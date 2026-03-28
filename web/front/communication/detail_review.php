<?php
$is_logged_in = isset($_COOKIE['session_token']);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail de l'Avis</title>
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
            <a href="/front/communication/review.php">
                <button class="flex items-center rounded-full px-6 button-blue">
                    <img src="/front/icons/fleche_gauche.svg" alt="fleche" class="w-7 h-7 mr-2"> Revenir aux avis
                </button>
            </a>
        </div>

        <?php if ($is_logged_in): ?>
            <div id="detail-container">
            </div>
        <?php else: ?>
            <div class="flex flex-col items-center justify-center py-20 rounded-[2.5rem] bg-white shadow-xl shadow-blue-900/10 px-6 mt-10">
                <p class="text-center font-semibold text-[#1C5B8F] text-2xl mb-8 px-4">
                    Vous devez être connecté(e) pour lire cet avis.
                </p>
                <a class="rounded-full px-4 py-2 button-blue" href="/front/account/signin.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">
                    Je me connecte
                </a>
            </div>
        <?php endif; ?>
    </main>

    <?php include("../includes/footer.php") ?>

    <script>
        const API_BASE = "http://localhost:8082";

        const urlParams = new URLSearchParams(window.location.search);
        const avisId = urlParams.get('id');

        async function fetchOneAvis() {
            try {
                const userId = window.currentUserId || 1;

                const response = await fetch(`${API_BASE}/avis/read-one/${avisId}?user_id=${userId}`);
                const result = await response.json();

                const c = result.data || result;

                if (!c || Object.keys(c).length === 0) {
                    throw new Error("Données vides");
                }

                renderDetail(c);
            } catch (err) {
                console.error("Erreur détaillée :", err);
                document.getElementById('detail-container').innerHTML = `
                    <p class="text-red-500 font-bold">Erreur : Impossible de charger les détails (ID: ${avisId}).</p>
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
            const note = c.note || 0;

            // Récupération et formatage du nom du prestataire
            const fullNom = `${c.prenom_prestataire || ''} ${c.nom_prestataire || ''}`.trim();
            const prestaHTML = (c.categorie === "Prestataire" && fullNom !== "") ?
                `<div class="mb-4 flex items-center gap-2">
            <span class="text-blue-600 font-bold">Prestataire concerné :</span>
            <span class="bg-blue-50 text-[#1C5B8F] px-3 py-1 rounded-lg border border-blue-100">${fullNom}</span>
           </div>` :
                "";

            container.innerHTML = `
                <article class="bg-white rounded-2xl shadow-xl overflow-hidden relative">
                    <div class="p-8 md:p-12 relative">

                        <div class="flex flex-wrap items-center gap-4 mb-6 mt-6 md:mt-0">
                            <span class="text-[#E1AB2B] border border-[#E1AB2B] text-sm px-3 py-1 rounded-full font-bold uppercase tracking-wider">
                                ${"★".repeat(note)}${"☆".repeat(5 - note)}
                            </span>
                            <span class="bg-[#E1AB2B] text-white px-4 py-1 rounded-full text-sm font-bold uppercase">
                                ${categorie}
                            </span>
                            <span class="text-gray-400 font-medium">Publié le ${dateStr}</span>
                        </div>

                        ${prestaHTML} <h1 class="text-4xl md:text-5xl font-bold text-[#1C5B8F] mb-8 leading-tight">
                            ${titre}
                        </h1>

                        <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                            <p class="leading-relaxed flex-grow mb-4 break-words text-xl font-semibold text-gray-800">
                                ${description || "Aucune description détaillée disponible."}
                            </p>
                        </div>
                    </div>
                </article>
            `;
        }

        window.onload = () => {
            if (document.getElementById('detail-container')) {
                fetchOneAvis();
            }
        };
    </script>
</body>

</html>