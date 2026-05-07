<?php include("includes/login.php"); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche Admin</title>
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

<body>
    <div class="flex min-h-screen bg-gray-50">

        <?php include("includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col">
            <?php include("includes/header.php"); ?>

            <main class="p-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-[#1C5B8F]">
                        Résultats pour : <span id="search-query" class="text-gray-500">...</span>
                    </h1>
                    <p class="text-gray-500">Recherche globale dans toute la base de données (incluant les éléments archivés/passés)</p>
                </div>

                <div id="loading" class="text-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#1C5B8F] mx-auto"></div>
                    <p class="mt-4 text-gray-500">Extraction des données en cours...</p>
                </div>

                <div id="results-wrapper" class="hidden space-y-12">
                    <section id="section-seniors" class="hidden">
                        <h2 class="text-xl font-bold text-[#1C5B8F] mb-4 flex items-center gap-2 border-b pb-2">Seniors</h2>
                        <div id="grid-seniors" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6"></div>
                    </section>

                    <section id="section-prestataires" class="hidden">
                        <h2 class="text-xl font-bold text-[#1C5B8F] mb-4 flex items-center gap-2 border-b pb-2">Prestataires</h2>
                        <div id="grid-prestataires" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6"></div>
                    </section>

                    <section id="section-produits" class="hidden">
                        <h2 class="text-xl font-bold text-[#1C5B8F] mb-4 flex items-center gap-2 border-b pb-2">Produits</h2>
                        <div id="grid-produits" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6"></div>
                    </section>

                    <section id="section-evenements" class="hidden">
                        <h2 class="text-xl font-bold text-[#1C5B8F] mb-4 flex items-center gap-2 border-b pb-2">Événements</h2>
                        <div id="grid-evenements" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6"></div>
                    </section>

                    <section id="section-services" class="hidden">
                        <h2 class="text-xl font-bold text-[#1C5B8F] mb-4 flex items-center gap-2 border-b pb-2">Services & Catégories</h2>
                        <div id="grid-services" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6"></div>
                    </section>

                    <section id="section-conseils" class="hidden">
                        <h2 class="text-xl font-bold text-[#1C5B8F] mb-4 flex items-center gap-2 border-b pb-2">Conseils</h2>
                        <div id="grid-conseils" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6"></div>
                    </section>
                </div>

                <div id="no-results" class="hidden text-center text-gray-500 text-xl py-12 bg-white rounded-[2.5rem] shadow-xl shadow-[#1C5B8F]/10 border border-gray-100">
                    Aucun résultat trouvé pour cette recherche.
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const query = urlParams.get('q');

            if (query) {
                document.getElementById('search-query').textContent = `"${query}"`;
                fetchSearchResults(query);
            } else {
                document.getElementById('loading').classList.add('hidden');
                document.getElementById('no-results').classList.remove('hidden');
            }
        });

        async function fetchSearchResults(query) {
            try {
                const response = await fetch(`${window.API_BASE_URL}/admin/search?q=${encodeURIComponent(query)}`);
                const data = await response.json();

                document.getElementById('loading').classList.add('hidden');

                const hasResults = Object.values(data).some(arr => arr && arr.length > 0);

                if (!hasResults) {
                    document.getElementById('no-results').classList.remove('hidden');
                    return;
                }

                document.getElementById('results-wrapper').classList.remove('hidden');

                const renderAdminCards = (items, gridId, sectionId, color = "#1C5B8F") => {
                    if (items && items.length > 0) {
                        document.getElementById(sectionId).classList.remove('hidden');
                        const grid = document.getElementById(gridId);

                        items.forEach(item => {
                            grid.innerHTML += `
                                <div class="bg-white p-6 rounded-[1.5rem] shadow-lg shadow-[#1C5B8F]/5 border border-gray-100 hover:border-[#1C5B8F] transition-all flex flex-col h-full">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-bold text-[#1C5B8F] line-clamp-1">${item.titre}</h3>
                                        <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-1 rounded-full uppercase">ID: ${item.id}</span>
                                    </div>
                                    <p class="text-gray-500 text-xs mb-4 line-clamp-2 flex-grow">
                                        ${item.description || 'Pas de détails supplémentaires.'}
                                    </p>
                                    <div class="flex justify-between items-center pt-3 border-t border-gray-50">
                                        <span class="text-[#1C5B8F] font-bold text-sm">${item.prix ? item.prix + '€' : ''}</span>
                                        <a href="${item.lien}" class="text-white bg-[#1C5B8F] hover:text-[#1C5B8F] hover:bg-[#D9D9D9]/40 px-4 py-1 rounded-full text-xs transition-colors">
                                            Gérer
                                        </a>
                                    </div>
                                </div>
                            `;
                        });
                    }
                };

                renderAdminCards(data.seniors, 'grid-seniors', 'section-seniors');
                renderAdminCards(data.prestataires, 'grid-prestataires', 'section-prestataires');
                renderAdminCards(data.produits, 'grid-produits', 'section-produits');
                renderAdminCards(data.evenements, 'grid-evenements', 'section-evenements');
                renderAdminCards(data.services, 'grid-services', 'section-services');
                renderAdminCards(data.categories, 'grid-services', 'section-services');
                renderAdminCards(data.conseils, 'grid-conseils', 'section-conseils');

            } catch (error) {
                console.error(error);
                document.getElementById('loading').innerHTML = `<p class="text-red-500 font-bold">Erreur de connexion au serveur API.</p>`;
            }
        }
    </script>
</body>

</html>