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
                Nos Prestataires
            </h1>
            <p class="text-xl md:text-2xl text-gray-700 max-w-2xl mb-6">
                Découvrez les professionnels classés par note selon notre communauté pour vos besoins et services.
            </p>

            <select id="category-filter" onchange="applyCategoryFilter()" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#E1AB2B] bg-white shadow-sm cursor-pointer">
                <option value="all">Toutes les professions</option>
            </select>
        </div>

        <div id="prestataires-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 px-6 md:px-16 pt-8 pb-4">
            <div class="w-full text-center py-10 col-span-full">
                <p class="text-xl text-gray-500 animate-pulse">Chargement des prestataires...</p>
            </div>
        </div>

        <div id="pagination-controls" class="flex justify-center items-center gap-4 py-12"></div>

    </main>

    <?php include("../includes/footer.php") ?>

    <script>
        const API_BASE = window.API_BASE_URL;
        let currentPage = 1;
        let currentCategory = "all";
        const limit = 6;

        window.addEventListener('DOMContentLoaded', () => {
            fetchPrestataires(1);
            loadCategories();
        });

        async function fetchPrestataires(page = 1) {
            try {
                currentPage = page;

                const url = `${API_BASE}/prestataires/top?page=${currentPage}&limit=${limit}&category=${currentCategory}`;

                const response = await fetch(url);
                if (!response.ok) throw new Error("Erreur de récupération");

                const result = await response.json();

                renderPrestataireCards(result.data || []);

                renderPagination(result.totalPages);

            } catch (err) {
                console.error("Erreur fetch:", err);
                document.getElementById('prestataires-container').innerHTML = `<p class="text-red-500 text-center col-span-full">Erreur de connexion au serveur.</p>`;
            }
        }

        function renderPrestataireCards(list) {
            const container = document.getElementById('prestataires-container');
            container.innerHTML = '';

            if (list.length === 0) {
                container.innerHTML = '<p class="text-xl text-gray-500 py-10 italic col-span-full text-center">Aucun prestataire trouvé pour cette catégorie.</p>';
                return;
            }

            list.forEach(p => {
                const id = p.id_prestataire;
                const nomComplet = `${p.prenom} ${p.nom}`;
                const categorie = p.categorie || 'Non spécifié';
                const noteMoyenne = parseFloat(p.moyenne);
                const noteTexte = noteMoyenne > 0 ? `${noteMoyenne.toFixed(1)} / 5` : "Nouveau";
                const stars = "★".repeat(Math.round(noteMoyenne)) + "☆".repeat(5 - Math.round(noteMoyenne));
                const nbAvis = p.nombre_avis;

                const borderClass = p.is_boosted ? "border-2 border-[#E1AB2B] shadow-xl" : "border-l-8 border-[#1C5B8F] shadow-md";
                const badgeBoost = p.is_boosted ? `<span class="absolute -top-3 -left-3 bg-[#E1AB2B] text-white p-2 rounded-full z-10">⭐</span>` : "";

                container.innerHTML += `
                        <div class="bg-white ${borderClass} rounded-xl p-6 flex flex-col hover:shadow-lg transition-all relative h-full">
                            
                            ${badgeBoost}
                            
                            <div class="absolute top-4 right-4 flex flex-col items-end z-10">
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
        }

        function applyCategoryFilter() {
            currentCategory = document.getElementById('category-filter').value;
            fetchPrestataires(1);
        }

        function renderPagination(totalPages) {
            const container = document.getElementById('pagination-controls');
            if (!container) return;
            container.innerHTML = '';

            if (totalPages <= 1) return;

            const btnClass = "px-6 py-2 border-2 border-[#1C5B8F] text-[#1C5B8F] rounded-full font-bold transition-all";

            const prevDisabled = currentPage === 1;
            container.innerHTML += `
        <button onclick="fetchPrestataires(${currentPage - 1})" 
            class="${btnClass} ${prevDisabled ? 'opacity-30 cursor-not-allowed' : 'hover:bg-[#1C5B8F] hover:text-white'}"
            ${prevDisabled ? 'disabled' : ''}>← Précédent</button>`;

            container.innerHTML += `<span class="text-gray-600 font-medium">Page <strong class="text-[#1C5B8F] text-xl">${currentPage}</strong> sur ${totalPages}</span>`;

            const nextDisabled = currentPage === totalPages;
            container.innerHTML += `
        <button onclick="fetchPrestataires(${currentPage + 1})" 
            class="${btnClass} ${nextDisabled ? 'opacity-30 cursor-not-allowed' : 'hover:bg-[#1C5B8F] hover:text-white'}"
            ${nextDisabled ? 'disabled' : ''}>Suivant →</button>`;
        }

        async function loadCategories() {
            try {
                const response = await fetch(`${API_BASE}/categorie/read`);
                if (!response.ok) throw new Error("Erreur");

                const result = await response.json();
                const select = document.getElementById('category-filter');

                if (result.data && Array.isArray(result.data)) {
                    result.data.forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.nom;
                        option.textContent = cat.nom;
                        select.appendChild(option);
                    });
                }
            } catch (e) {
                console.error("Impossible de charger les catégories :", e);
            }
        }
    </script>
</body>

</html>