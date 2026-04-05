<?php
$is_logged_in = isset($_COOKIE['session_token']);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail du Conseil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');

        body {
            font-family: 'Alata', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">

    <?php include("../includes/header.php") ?>

    <main class="flex-1 container mx-auto px-6 py-12 max-w-4xl">
        <div class="p-3 flex justify-between items-center mx-8">
            <a href="/front/services/advice.php">
                <button class="flex items-center rounded-full px-6 button-blue">
                    <img src="/front/icons/fleche_gauche.svg" alt="fleche" class="w-7 h-7 mr-2"> Revenir aux conseils
                </button>
            </a>
        </div>

        <?php if ($is_logged_in): ?>
            <div id="detail-container">
                <div class="animate-pulse space-y-4">
                    <div class="h-10 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                    <div class="h-64 bg-gray-200 rounded w-full"></div>
                </div>
            </div>
        <?php else: ?>
            <div class="flex flex-col items-center justify-center py-20 rounded-[2.5rem] bg-white shadow-xl shadow-blue-900/10 px-6 mt-10">
                <p class="text-center font-semibold text-[#1C5B8F] text-2xl mb-8 px-4">
                    Vous devez être connecté(e) pour lire ce conseil.
                </p>
                <a class="rounded-full px-4 py-2 button-blue" href="/front/account/signin.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">
                    Je me connecte
                </a>
            </div>
        <?php endif; ?>
    </main>

    <?php include("../includes/footer.php") ?>

    <script>
        const API_BASE = window.API_BASE_URL;

        const urlParams = new URLSearchParams(window.location.search);
        const conseilId = urlParams.get('id');

        async function fetchOneConseil() {
            try {
                const userId = window.currentUserId || 1;

                const response = await fetch(`${API_BASE}/conseil/read-one/${conseilId}?user_id=${userId}`);
                const result = await response.json();

                const c = result.data || result;

                if (!c || Object.keys(c).length === 0) {
                    throw new Error("Données vides");
                }

                renderDetail(c);
            } catch (err) {
                console.error("Erreur détaillée :", err);
                document.getElementById('detail-container').innerHTML = `
                    <p class="text-red-500 font-bold">Erreur : Impossible de charger les détails (ID: ${conseilId}).</p>
                `;
            }
        }

        function renderDetail(c) {
            const container = document.getElementById('detail-container');
            if (!container) return;

            const rawDate = c.date_publication || c.Date || c.date;
            let dateStr = "Date inconnue";

            if (rawDate) {
                const safeDateStr = String(rawDate).replace(' ', 'T');
                const d = new Date(safeDateStr);
                if (!isNaN(d)) {
                    dateStr = d.toLocaleDateString('fr-FR', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                }
            }

            const titre = c.titre || "Titre absent";
            const description = c.description || "";
            const categorie = c.categorie || "Général";

            const likes = c.likes || 0;
            const isAlreadyLikedByCurrentUser = (c.is_liked === true || c.is_liked === 1);
            const heartIcon = isAlreadyLikedByCurrentUser ? "❤️" : "🤍";

            container.innerHTML = `
                <article class="bg-white rounded-2xl shadow-xl overflow-hidden relative">
                    <div class="h-64 bg-[#1C5B8F] flex items-center justify-center">
                        <span class="text-white text-7xl font-bold">💡</span> 
                    </div>
                    
                    <div class="p-8 md:p-12 relative">
                        <div class="absolute top-8 right-8 flex items-center gap-2 bg-gray-100 rounded-full px-4 py-2 cursor-pointer hover:bg-red-50 transition-colors shadow-md" onclick="toggleLike(this)">
                            <span class="text-red-500 text-xl like-icon">${heartIcon}</span>
                            <span class="font-bold text-gray-700 text-lg like-count">${likes}</span>
                        </div>

                        <div class="flex items-center gap-4 mb-6 mt-6 md:mt-0">
                            <span class="bg-[#E1AB2B] text-white px-4 py-1 rounded-full text-sm font-bold uppercase">
                                ${categorie}
                            </span>
                            <span class="text-gray-400 font-medium">Publié le ${dateStr}</span>
                        </div>

                        <h1 class="text-4xl md:text-5xl font-bold text-[#1C5B8F] mb-8 leading-tight pr-24">
                            ${titre}
                        </h1>

                        <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                            <p class="text-xl mb-6 font-semibold text-gray-800">${description || "Aucune description détaillée disponible."}</p>
                        </div>
                    </div>
                </article>
            `;
        }

        async function toggleLike(element) {
            const countSpan = element.querySelector('.like-count');
            const iconSpan = element.querySelector('.like-icon');
            let currentCount = parseInt(countSpan.innerText);

            const userId = window.currentUserId || 1;

            const isLiked = iconSpan.innerText === '❤️';
            const method = isLiked ? 'DELETE' : 'POST';
            const endpoint = isLiked ? `/conseil/unlike/${conseilId}` : `/conseil/like/${conseilId}`;

            try {
                const response = await fetch(`${API_BASE}${endpoint}`, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_utilisateur: userId
                    })
                });

                if (response.ok) {
                    if (isLiked) {
                        iconSpan.innerText = '🤍';
                        countSpan.innerText = currentCount - 1;
                    } else {
                        iconSpan.innerText = '❤️';
                        countSpan.innerText = currentCount + 1;
                    }
                } else {
                    const errorData = await response.json();
                    alert("Erreur: " + (errorData.message || "Impossible de modifier le like."));
                }
            } catch (error) {
                console.error("Erreur réseau lors du like", error);
            }
        }

        window.onload = () => {
            if (document.getElementById('detail-container')) {
                fetchOneConseil();
            }
        };
    </script>
</body>

</html>