<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Avis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Alata', 'sans-serif']
                    }
                }
            }
        }

        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            if (modal) {
                modal.classList.toggle('hidden');
            }
        }
    </script>
</head>

<body class="bg-gray-50 text-gray-800">

    <div class="flex min-h-screen relative">

        <?php include("../includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto relative">

            <main class="p-8">

                <div id="main-content-valide" class="hidden space-y-8 max-w-7xl mx-auto">

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                        <div>
                            <h1 class="text-3xl font-semibold text-[#1C5B8F]">Mes avis</h1>
                            <p class="text-gray-500 mt-1">Découvrez ce que les seniors ont pensé de vos prestations.</p>
                        </div>
                    </div>

                    <div id="page-alert" class="hidden p-4 rounded-xl font-semibold text-sm text-center"></div>

                    <div id="reviews-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <div class="col-span-full py-10 text-center text-gray-500 flex flex-col items-center">
                            Chargement de vos avis...
                        </div>
                    </div>

                </div>

            </main>
        </div>

    </div>

    <script>
        const API_BASE = window.API_BASE_URL;

        function getUserId() {
            return window.currentUserId || null;
        }

        async function fetchMyAvis() {
            const userId = getUserId();

            if (!userId) {
                if (!window.retryCount) window.retryCount = 0;
                if (window.retryCount < 30) {
                    window.retryCount++;
                    setTimeout(fetchMyAvis, 100);
                }
                return;
            }

            try {
                const response = await fetch(`${API_BASE}/prestataire/${userId}/read-avis`);
                const reviews = await response.json();

                const container = document.getElementById('reviews-container');
                container.innerHTML = '';

                if (!reviews || reviews.length === 0) {
                    container.innerHTML = `<p class="text-gray-400 italic col-span-full text-center">Vous n'avez pas encore reçu d'avis.</p>`;
                    return;
                }

                reviews.forEach(a => {
                    const stars = "★".repeat(a.note) + "☆".repeat(5 - a.note);
                    container.innerHTML += `
                <div class="bg-white border border-gray-100 p-6 rounded-[2rem] shadow-lg">
                    <div class="flex justify-between mb-4">
                        <span class="bg-blue-50 text-[#1C5B8F] px-3 py-1 rounded-full text-[10px] font-bold uppercase">${a.categorie}</span>
                        <span class="text-[10px] text-gray-400">${new Date(a.date).toLocaleDateString()}</span>
                    </div>
                    <h3 class="text-xl font-bold mb-1">${a.titre}</h3>
                    <div class="text-[#E1AB2B] mb-3">${stars}</div>
                    <p class="text-gray-500 text-sm mb-2 italic line-clamp-3">"${a.description}"</p>
                </div>`;
                });
            } catch (err) {
                console.error("Erreur chargement avis:", err);
            }
        }

        window.addEventListener('load', fetchMyAvis);
    </script>
</body>

</html>