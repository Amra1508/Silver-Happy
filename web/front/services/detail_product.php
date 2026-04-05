<?php
$is_logged_in = isset($_COOKIE['session_token']);
?>

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
    <main class="p-8 bg-white">
        <div class="max-w-6xl mx-auto">
            <?php if ($is_logged_in): ?>
                <div id="api-message" class="hidden"></div>
                <div class="w-full pt-8 relative">
                    <div class="flex justify-between items-center mx-8">
                        <a href="/front/services/products.php">
                            <button class="flex items-center rounded-md px-6 button-blue">
                                <img src="/front/icons/fleche_gauche.svg" alt="fleche" class="w-7 h-7 mr-2"> Revenir aux produits
                            </button>
                        </a>
                    </div>
                    <div class="absolute right-8 top-10">
                        <a href="/front/services/basket.php" class="flex items-center gap-2 px-4 rounded-md button-blue">
                            Mon Panier
                            <img src="/front/icons/panier.svg" alt="panier" class="w-5 h-5 filter brightness-0 invert">
                        </a>
                    </div>
                </div>
                <div class="max-w-6xl mx-auto px-4">
                    <div id="detail-container" class="bg-white rounded-[2.5rem] shadow-xl shadow-blue-900/10 overflow-hidden p-8">

                    </div>

                    <div id="pagination-controls"></div>
                </div>
                <div id="add-modal" class="hidden fixed inset-0 z-50 bg-black/50 items-center justify-center">
                    <div class="add-modal flex flex-col items-center">
                        <h3 class="text-2xl font-semibold text-[#1C5B8F] mb-6">Quantité souhaitée</h3>

                        <div class="flex items-center justify-center gap-8 mb-8">
                            <button onclick="updateQuantite(-1)" type="button" class="w-12 h-12 rounded-full border-2 border-[#1C5B8F] text-[#1C5B8F] text-2xl font-bold hover:bg-[#1C5B8F] hover:text-white transition-colors">-</button>
                            <span id="quantite" class="text-4xl font-bold w-12 text-center text-[#1C5B8F]">1</span>
                            <button onclick="updateQuantite(1)" type="button" class="w-12 h-12 rounded-full border-2 border-[#1C5B8F] text-[#1C5B8F] text-2xl font-bold hover:bg-[#1C5B8F] hover:text-white transition-colors">+</button>
                        </div>

                        <div class="w-full flex flex-col gap-3">
                            <button id="confirm-add-btn" class="rounded-full px-4 button-blue text-lg">
                                Ajouter au panier
                            </button>
                            <button onclick="toggleModal('add-modal')" class="text-gray-400 hover:underline">
                                Annuler
                            </button>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="flex flex-col items-center justify-center py-20 rounded-[2.5rem] shadow-xl shadow-blue-900/10">
                    <p class="text-center font-semibold text-[#1C5B8F] text-2xl mb-8">
                        Vous devez être connecté(e) pour consulter ce produit Silver Happy.</p>
                    <a class="rounded-full px-4 py-2 button-blue" href="/front/account/signin.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>"> Je me connecte </a>
                </div>
            <?php endif; ?>
        </div>

    </main>
    <?php include("../includes/footer.php") ?>

    <script>
        const API_BASE = window.API_BASE_URL;
        const messageBox = document.getElementById('api-message');

        function showAlert(msg, isSuccess) {
            messageBox.textContent = msg;
            messageBox.className = `fixed top-24 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-xl p-4 rounded-lg border text-center font-bold shadow-2xl transition-all duration-300 ${isSuccess ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'}`;
            messageBox.classList.remove('hidden');
            setTimeout(() => {
                messageBox.classList.add('opacity-0');
                setTimeout(() => {
                    messageBox.classList.add('hidden');
                    messageBox.classList.remove('opacity-0');
                }, 300);
            }, 4000);
        }

        let isModalOpen = false;

        window.addEventListener('auth_ready', () => {
            fetchOneProduit();
            setInterval(() => {
                if (!isModalOpen) fetchOneProduit();
            }, 2000);
        });

        let produit = null;
        let quantite = 1;
        let maxStock = 0;
        let quantitePanier = 0;

        async function openAddModal(id, stock) {
            produit = id;
            maxStock = Number(stock);
            quantite = 1;
            isModalOpen = true;

            try {
                const response = await fetch(`${API_BASE}/panier/get?id_utilisateur=${window.currentUserId}`);
                const items = await response.json();

                const existingItem = items.find(item => Number(item.id_produit) === Number(id));

                quantitePanier = existingItem ? Number(existingItem.quantite) : 0;

            } catch (e) {
                console.error("Erreur panier:", e);
                quantitePanier = 0;
            }

            document.getElementById('quantite').innerText = quantite;
            document.getElementById('confirm-add-btn').onclick = () => ajouter(produit, quantite);
            toggleModal('add-modal');
        }

        function showStockError() {
            console.log("Debug Stock - Panier:", quantitePanier, "Max:", maxStock);

            const msg = quantitePanier > 0 ?
                `Vous avez déjà ${quantitePanier} articles au panier. Stock max : ${maxStock}.` :
                "Limite de stock atteinte.";
            showAlert(msg, false);
        }

        function updateQuantite(nb) {
            const ajout = Number(quantite) + Number(nb) + Number(quantitePanier);

            if (nb > 0) {
                if (ajout <= maxStock) {
                    quantite += nb;
                    document.getElementById('quantite').innerText = quantite;
                    document.getElementById('confirm-add-btn').onclick = () => ajouter(produit, quantite);
                } else {
                    showStockError();
                }
            } else if (nb < 0 && quantite > 1) {
                quantite += nb;
                document.getElementById('quantite').innerText = quantite;
                document.getElementById('confirm-add-btn').onclick = () => ajouter(produit, quantite);
            }
        }

        async function ajouter(idProduit, quantite) {
            const idUtilisateur = window.currentUserId;

            const donnees = new FormData();
            donnees.append("id_produit", idProduit);
            donnees.append("id_utilisateur", idUtilisateur);
            donnees.append("quantite", quantite);

            const response = await fetch(`${API_BASE}/panier/add`, {
                method: 'POST',
                body: donnees
            });

            if (response.ok) {
                toggleModal('add-modal');
                showAlert(`Produit ajouté au panier !`, true);
                fetchOneProduit();
            } else {
                showStockError();
            }
        }

        async function fetchOneProduit() {
            const urlParams = new URLSearchParams(window.location.search);
            const productId = urlParams.get('id');

            if (!productId) {
                document.getElementById('detail-container').innerHTML = `
            <div class="text-center py-10">
                <p class="text-red-500 font-bold">Produit introuvable ou ID manquant.</p>
                <a href="produits.php" class="text-blue-500 underline">Retour aux produits</a>
            </div>`;
                return;
            }

            try {
                const response = await fetch(`${API_BASE}/produit/read-one/${productId}`);

                if (!response.ok) {
                    throw new Error("Produit non trouvé côté serveur");
                }

                const p = await response.json();

                const imgSrc = p.image ? `${API_BASE}/${p.image}` : 'https://via.placeholder.com/150';

                const rupture = p.stock <= 0;

                document.getElementById('detail-container').innerHTML = `
                <div class="flex flex-col md:flex-row gap-12">
                    <div>
                        <div class="w-96 h-96 overflow-hidden rounded-3xl border border-gray-100 shadow-sm">
                            <img src="${imgSrc}" class="w-full h-full object-cover" alt="${p.nom}">
                        </div>
                    </div>

                    <div class="flex flex-col flex-1">
                        <h2 class="big-text mb-4 text-3xl font-bold">${p.nom}</h2>
                        <div class="text-3xl font-bold text-[#E1AB2B] mb-6">
                            ${parseFloat(p.prix).toFixed(2)} €
                        </div>
                        
                        <div class="border-t border-gray-100 pt-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-400 mb-2">DESCRIPTION</h3>
                            <p class="text-gray-600 text-xl leading-relaxed text-justify">
                                ${p.description}
                            </p>
                        </div>

                        <div class="flex items-center gap-4 mt-auto">
                            <span class="px-4 py-2 ${rupture ? 'bg-[#AA1114]/20 text-[#AA1114]' : 'bg-[#E1AB2B]/30 text-[#1C5B8F]'} rounded-md font-semibold text-xl">
                                ${rupture ? 'En rupture de stock' : p.stock + ' produit(s) restant(s)'}
                            </span>

                            ${rupture ? 
                                ` ` 
                                : 
                                `<button onclick="openAddModal(${p.id}, ${p.stock})" class="px-4 rounded-md button-blue">
                                    Ajouter au panier
                                </button>`
                            }
                        </div>
                    </div>
                </div>
            `;
            } catch (err) {
                console.error("Erreur detail:", err);
                document.getElementById('detail-container').innerHTML = `
            <div class="text-center py-20">
                <p class="text-red-500 font-bold">Erreur : ${err.message}</p>
            </div>`;
            }
        }

        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                const isHidden = modal.classList.contains('hidden');
                modal.classList.toggle('hidden');
                modal.classList.toggle('flex');
                if (!isHidden) isModalOpen = false;
            }
        }
    </script>
</body>

</html>