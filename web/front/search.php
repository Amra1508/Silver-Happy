<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de recherche</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

    <?php include("includes/header.php"); ?>

    <main class="flex-grow container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-[#1C5B8F] mb-8 border-b pb-4">
            Résultats pour : <span id="search-query" class="text-[#E1AB2B]">...</span>
        </h1>

        <div id="loading" class="text-center text-gray-500 text-xl py-12">Recherche en cours...</div>

        <div id="results-wrapper" class="hidden space-y-12">
            
            <section id="section-produits" class="hidden">
                <h2 class="text-2xl font-bold text-[#1C5B8F] mb-4 flex items-center gap-2">🛒 Produits</h2>
                <div id="grid-produits" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6"></div>
            </section>

            <section id="section-evenements" class="hidden">
                <h2 class="text-2xl font-bold text-[#1C5B8F] mb-4 flex items-center gap-2">📅 Événements</h2>
                <div id="grid-evenements" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6"></div>
            </section>

            <section id="section-services" class="hidden">
                <h2 class="text-2xl font-bold text-[#1C5B8F] mb-4 flex items-center gap-2">🤝 Prestations & Services</h2>
                <div id="grid-services" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6"></div>
            </section>

            <section id="section-avis" class="hidden">
                <h2 class="text-2xl font-bold text-[#1C5B8F] mb-4 flex items-center gap-2">⭐ Avis Clients</h2>
                <div id="grid-avis" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6"></div>
            </section>

            <section id="section-conseils" class="hidden">
                <h2 class="text-2xl font-bold text-[#1C5B8F] mb-4 flex items-center gap-2">💡 Conseils & Astuces</h2>
                <div id="grid-conseils" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6"></div>
            </section>

        </div>

        <div id="no-results" class="hidden text-center text-gray-500 text-xl py-12 bg-white rounded-lg shadow border border-gray-200">
            Aucun résultat trouvé pour cette recherche dans nos produits, événements, conseils, services ou avis.
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const urlParams = new URLSearchParams(window.location.search);
            const query = urlParams.get('q');

            if (query) {
                document.getElementById('search-query').textContent = `"${query}"`;
                fetchSearchResults(query);
            } else {
                document.getElementById('search-query').textContent = "Recherche vide";
                document.getElementById('loading').classList.add('hidden');
                document.getElementById('no-results').classList.remove('hidden');
            }
        });

        async function fetchSearchResults(query) {
            try {
                const response = await fetch(`http://localhost:8082/search?q=${encodeURIComponent(query)}`);
                if (!response.ok) throw new Error("Erreur serveur");
                
                const data = await response.json();
                
                document.getElementById('loading').classList.add('hidden');
                document.getElementById('results-wrapper').classList.remove('hidden');

                if (data.produits.length === 0 && 
                    data.evenements.length === 0 && 
                    data.services.length === 0 && 
                    (!data.avis || data.avis.length === 0) && 
                    (!data.conseils || data.conseils.length === 0)) {
                    
                    document.getElementById('no-results').classList.remove('hidden');
                    return;
                }

                const renderCards = (items, gridId, sectionId, isAvis = false) => {
                    if (items && items.length > 0) {
                        document.getElementById(sectionId).classList.remove('hidden');
                        const grid = document.getElementById(gridId);
                        items.forEach(item => {
                            let priceHTML = '';
                            if (!isAvis) {
                                const prixAffiche = item.prix > 0 ? `${item.prix} €` : 'Gratuit / Sur devis';
                                priceHTML = `<span class="font-bold text-[#E1AB2B]">${prixAffiche}</span>`;
                            } else {
                                priceHTML = `<span class="italic text-gray-400 text-sm">Avis</span>`;
                            }

                            grid.innerHTML += `
                                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow flex flex-col h-full">
                                    <h3 class="text-lg font-bold text-[#1C5B8F] mb-2 line-clamp-1" title="${item.titre}">${item.titre}</h3>
                                    <p class="text-gray-600 mb-4 text-sm line-clamp-3 flex-grow">${item.description || 'Aucune description disponible.'}</p>
                                    <div class="flex justify-between items-center mt-auto pt-4 border-t border-gray-100">
                                        ${priceHTML}
                                        <a href="${item.lien}" class="bg-[#1C5B8F] text-white px-4 py-1.5 rounded-full text-sm hover:bg-blue-800 transition-colors">Voir</a>
                                    </div>
                                </div>
                            `;
                        });
                    }
                };

                renderCards(data.produits, 'grid-produits', 'section-produits');
                renderCards(data.evenements, 'grid-evenements', 'section-evenements');
                renderCards(data.services, 'grid-services', 'section-services');
                renderCards(data.avis, 'grid-avis', 'section-avis', true);
                renderCards(data.conseils, 'grid-conseils', 'section-conseils', true);

            } catch (error) {
                console.error(error);
                document.getElementById('loading').classList.add('hidden');
                document.getElementById('results-wrapper').innerHTML = '<p class="text-red-500 text-center text-xl font-bold bg-red-50 p-6 rounded-lg">Une erreur est survenue lors de la communication avec le serveur.</p>';
            }
        }
    </script>
</body>
</html>