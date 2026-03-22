<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits</title>
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
    <?php include("../includes/header.php") ?>
    <main class="mb-10 bg-white">
        <div class="max-w-6xl mx-auto">
            <div class="max-w-6xl mx-auto px-4">
                <div class="w-full pt-8">
                    <h1 class="mb-5 text-center big-text">Nos Produits</h1>
                    <h2 class="text-lg text-gray-600 text-center mb-10">
                        Découvrez notre large gamme de produits Silver Happy.
                    </h2>
                </div>

                <div id="produit-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-8">
                </div>

                <div id="pagination-controls"></div>
            </div>
        </div>

    </main>
    <?php include("../includes/footer.php") ?>

    <script>
        const API_BASE = "http://localhost:8082";
        let currentPage = 1;
        const limit = 12;

        window.addEventListener('auth_ready', () => {
            fetchProduits();
            setInterval(fetchProduits, 2000);
        });

        async function fetchProduits(page = 1) {
            try {
                currentPage = page;
                const response = await fetch(`${API_BASE}/produit/read?page=${currentPage}&limit=${limit}`);
                const result = await response.json();

                const produits = result.data || [];
                const grid = document.getElementById('produit-grid');
                grid.innerHTML = '';

                if (produits.length === 0) {
                    grid.innerHTML = '<p class="col-span-full text-center text-gray-400 py-20">Aucun produit disponible.</p>';
                    renderPagination(0, 0);
                    return;
                }

                produits.forEach(p => {
                    const imgSrc = p.image ? `${API_BASE}/${p.image}` : 'https://via.placeholder.com/150';

                    grid.innerHTML += `
                    <div class="group flex flex-col bg-white overflow-hidden">
                        <a href="/front/services/detail_product.php?id=${p.id}">
                            <div class="w-64 h-64 overflow-hidden rounded-2xl border border-gray-100 shadow-sm mb-4">
                                <img src="${imgSrc}" 
                                    class="w-full h-full object-cover group-hover:scale-105" 
                                    alt="${p.nom}">
                            </div>

                            <div class="px-1">
                                <div class="flex justify-between items-center mb-2">
                                    <h2 class="text-2xl font-semibold text-[#1C5B8F] group-hover:text-[#E1AB2B]">${p.nom}</h2>
                                    <div class="text-2xl font-semibold text-[#1C5B8F] group-hover:text-[#E1AB2B] flex-shrink-0 italic">
                                        ${parseFloat(p.prix).toFixed(2)} €
                                    </div>
                                </div>
                                
                                    <p class="text-[#1C5B8F] text-lg group-hover:text-[#E1AB2B] text-justify">
                                        Voir le produit
                                    </p>
                                
                            </div>
                        </a>
                    </div>
                    `;
                });

                renderPagination(result.totalPages, result.total);
            } catch (err) {
                showAlert("Erreur lors de la récupération des produits.", false);
            }
        }

        function renderPagination(totalPages, totalItems) {
            let paginationContainer = document.getElementById('pagination-controls');

            if (!paginationContainer) {
                const tableContainer = document.querySelector('.table-container');
                paginationContainer = document.createElement('div');
                paginationContainer.id = 'pagination-controls';
                tableContainer.parentNode.insertBefore(paginationContainer, tableContainer.nextSibling);
            }

            if (totalItems === 0) {
                paginationContainer.innerHTML = '';
                return;
            }

            let html = `
                <div class="flex justify-between items-center mt-6 px-4 text-sm">
                    <span class="text-gray-500 font-semibold">Total : ${totalItems} produits</span>
                    <div class="flex gap-2">
                        <button ${currentPage === 1 ? 'disabled' : ''} onclick="fetchProduits(${currentPage - 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Précédent</button>
            `;

            for (let i = 1; i <= totalPages; i++) {
                const activeClass = i === currentPage ? 'bg-[#1C5B8F] text-white' : 'text-[#1C5B8F] hover:bg-blue-50';
                html += `<button onclick="fetchProduits(${i})" class="px-3 py-1 border border-[#1C5B8F] rounded transition ${activeClass}">${i}</button>`;
            }

            html += `
                        <button ${currentPage === totalPages ? 'disabled' : ''} onclick="fetchProduits(${currentPage + 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Suivant</button>
                    </div>
                </div>
            `;
            paginationContainer.innerHTML = html;
        }

        window.onload = () => fetchProduits(1);
    </script>
</body>

</html>