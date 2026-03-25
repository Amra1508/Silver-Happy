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
                <div class="flex justify-between items-center mx-8">
                    <a href="/front/services/products.php">
                        <button class="flex items-center rounded-full px-6 button-blue">
                            <img src="/front/icons/fleche_gauche.svg" alt="fleche" class="w-7 h-7 mr-2"> Revenir aux produits
                        </button>
                    </a>
                </div>
                <div class="max-w-6xl mx-auto px-4">
                    <div id="detail-container" class="bg-white rounded-[2.5rem] shadow-xl shadow-blue-900/10 overflow-hidden p-8">

                    </div>

                    <div id="pagination-controls"></div>
                </div>
                <!-- <div id="qty-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm">
                    <div class="bg-white rounded-[2rem] p-8 max-w-sm w-full shadow-2xl scale-95 transition-transform duration-300">
                        <h3 class="text-2xl font-bold text-[#1C5B8F] mb-4 text-center">Quantité souhaitée</h3>

                        <div class="flex items-center justify-center gap-6 mb-8">
                            <button onclick="changeQty(-1)" class="w-12 h-12 rounded-full border-2 border-[#1C5B8F] text-[#1C5B8F] text-2xl font-bold hover:bg-[#1C5B8F] hover:text-white transition-colors">-</button>
                            <span id="modal-qty-display" class="text-4xl font-bold w-12 text-center">1</span>
                            <button onclick="changeQty(1)" class="w-12 h-12 rounded-full border-2 border-[#1C5B8F] text-[#1C5B8F] text-2xl font-bold hover:bg-[#1C5B8F] hover:text-white transition-colors">+</button>
                        </div>

                        <div class="flex flex-col gap-3">
                            <button id="confirm-add-btn" class="w-full py-3 rounded-full button-blue font-bold text-lg">
                                Confirmer l'ajout
                            </button>
                            <button onclick="closeModal()" class="w-full py-2 text-gray-500 font-semibold hover:underline">
                                Annuler
                            </button>
                        </div>
                    </div>
                </div> -->
            <?php else: ?>
                <div class="flex flex-col items-center justify-center py-20 rounded-[2.5rem] shadow-xl shadow-blue-900/10">
                    <p class="text-center font-semibold text-[#1C5B8F] text-2xl mb-8">
                        Vous devez être connecté(e) pour consulter nos produits Silver Happy.</p>
                    <a class="rounded-full px-4 py-2 button-blue" href="/front/account/signin.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>"> Je me connecte </a>
                </div>
            <?php endif; ?>
        </div>

    </main>
    <?php include("../includes/footer.php") ?>

    <script>
        const API_BASE = "http://localhost:8082";
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

        window.addEventListener('auth_ready', () => {
            panierExpire();
            fetchOneProduit();
        });

        async function panierExpire() {
            try {
                await fetch(`${API_BASE}/panier/autodelete`);
            } catch (err) {}
        }

        async function ajouterAuPanier(idProduit) {
            const idUtilisateur = window.currentUserId;

            if (!idUtilisateur) {
                showAlert("Vous devez être connecté", false);
                return;
            }

            const donnees = new FormData();
            donnees.append("id_produit", idProduit);
            donnees.append("id_utilisateur", idUtilisateur);

            try {
                const reponse = await fetch(`${API_BASE}/panier/add`, {
                    method: 'POST',
                    body: donnees
                });

                if (reponse.ok) {
                    showAlert("Votre panier expire dans 15 min !", true);
                    fetchOneProduit();
                } else {
                    showAlert("Impossible d'ajouter le produit.");
                }
            } catch (err) {
                showAlert("Erreur de connexion au serveur.");
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
                                `<button onclick="ajouterAuPanier(${p.id})" class="px-4 rounded-md button-blue">
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
    </script>
</body>

</html>