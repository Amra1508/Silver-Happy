<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestataires</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Alata', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">

    <?php include("../includes/header.php") ?>

    <main class="flex-1 relative">

        <div class="w-full px-6 md:px-16 mt-16 mb-8">
            <h1 class="text-4xl md:text-5xl font-bold text-[#1C5B8F] leading-tight mb-4">
                Nos Meilleurs Prestataires
            </h1>
            <p class="text-xl md:text-2xl text-gray-700 max-w-2xl mb-12">
                Découvrez les professionnels les mieux notés par notre communauté pour vos besoins et services.
            </p>

            <h2 class="text-3xl font-bold text-[#1C5B8F] border-b-4 border-[#E1AB2B] inline-block pb-2">
                Classement par note moyenne
            </h2>
        </div>

        <div id="prestataires-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 px-6 md:px-16 py-4 overflow-hidden">
            <div class="w-full text-center py-10 col-span-full">
                <p class="text-xl text-gray-500 animate-pulse">Chargement des prestataires...</p>
            </div>
        </div>

        <div id="pagination-controls" class="flex justify-center items-center gap-4 py-12"></div>

    </main>

    <?php include("../includes/footer.php") ?>

    <script>
        const API_BASE = "http://localhost:8082";
        let currentPage = 1;
        const limit = 6;

        window.addEventListener('DOMContentLoaded', () => {
            fetchPrestataires(1);
        });

        async function fetchPrestataires(page = 1) {
            try {
                currentPage = page;
                const response = await fetch(`${API_BASE}/prestataires/top?page=${currentPage}&limit=${limit}&status=valide`);

                if (!response.ok) throw new Error("Erreur de récupération des données");

                const result = await response.json();
                const prestataires = result.data || [];
                const container = document.getElementById('prestataires-container');
                container.innerHTML = '';

                if (prestataires.length === 0) {
                    container.innerHTML = '<p class="text-xl text-gray-500 py-10 italic col-span-full text-center">Aucun prestataire trouvé.</p>';
                    return;
                }

                prestataires.forEach(p => {
                    const id = p.id_prestataire;
                    const nomComplet = `${p.prenom} ${p.nom}`;
                    const categorie = p.type_prestation || 'Non spécifié';

                    const noteMoyenne = parseFloat(p.moyenne).toFixed(1);
                    const nbAvis = p.nombre_avis;

                    const stars = "★".repeat(Math.round(noteMoyenne)) + "☆".repeat(5 - Math.round(noteMoyenne));

                    container.innerHTML += `
                        <div class="bg-white border-l-8 border-[#1C5B8F] rounded-xl shadow-md p-6 flex flex-col hover:shadow-lg transition-all relative overflow-hidden h-full">
                            
                            <div class="absolute top-4 right-4 flex flex-col items-end">
                                <div class="bg-[#E1AB2B] text-white font-bold px-3 py-1 rounded-lg shadow-sm">
                                    ${noteMoyenne} / 5
                                </div>
                                <span class="text-xs text-gray-400 mt-1 font-semibold">${nbAvis} avis</span>
                            </div>

                            <div class="mt-4 flex flex-col flex-grow">
                                <span class="text-xs font-bold text-[#E1AB2B] uppercase tracking-wider mb-2 block pr-20">${categorie}</span>
                                <h3 class="text-2xl text-[#1C5B8F] font-bold mb-2 leading-snug pr-20">${nomComplet}</h3>
                                
                                <div class="text-[#E1AB2B] text-xl mb-4">
                                    ${stars}
                                </div>
                                
                                <a href="profile_provider.php?id=${id}" class="self-start text-[#1C5B8F] font-bold hover:text-[#E1AB2B] transition-colors flex items-center gap-2 mt-auto pt-4 border-t w-full">
                                    Voir le profil complet <span class="text-xl">→</span>
                                </a>
                            </div>
                        </div>
                    `;
                });

                renderPagination(result.totalPages);

            } catch (err) {
                console.error(err);
                document.getElementById('prestataires-container').innerHTML = `
                    <div class="w-full text-center py-10 col-span-full">
                        <p class="text-xl text-red-500 font-bold">Impossible de charger les prestataires.</p>
                        <p class="text-gray-500 mt-2">Vérifiez que votre API Go est bien lancée.</p>
                    </div>`;
            }
        }

        function renderPagination(totalPages) {
            const paginationContainer = document.getElementById('pagination-controls');
            if (!paginationContainer) return;
            paginationContainer.innerHTML = '';

            if (totalPages <= 1) return;

            const prevDisabled = currentPage === 1 ? 'disabled opacity-50 cursor-not-allowed' : 'hover:bg-[#1C5B8F] hover:text-white';
            paginationContainer.innerHTML += `
                <button onclick="fetchPrestataires(${currentPage - 1})" 
                    class="px-6 py-2 border-2 border-[#1C5B8F] text-[#1C5B8F] rounded-full font-bold transition-all ${prevDisabled}" 
                    ${currentPage === 1 ? 'disabled' : ''}>
                    ← Précédent
                </button>`;

            paginationContainer.innerHTML += `
                <span class="text-gray-600 font-medium px-4">
                    Page <strong class="text-[#1C5B8F] text-xl">${currentPage}</strong> sur ${totalPages}
                </span>`;

            const nextDisabled = currentPage === totalPages ? 'disabled opacity-50 cursor-not-allowed' : 'hover:bg-[#1C5B8F] hover:text-white';
            paginationContainer.innerHTML += `
                <button onclick="fetchPrestataires(${currentPage + 1})" 
                    class="px-6 py-2 border-2 border-[#1C5B8F] text-[#1C5B8F] rounded-full font-bold transition-all ${nextDisabled}" 
                    ${currentPage === totalPages ? 'disabled' : ''}>
                    Suivant →
                </button>`;
        }
    </script>
</body>

</html>