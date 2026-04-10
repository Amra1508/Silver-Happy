<?php
$is_logged_in = isset($_COOKIE['session_token']);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');

        body {
            font-family: 'Alata', sans-serif;
        }
    </style>
</head>

<body class="bg-white">
    <?php include("../includes/header.php") ?>

    <main class="p-8">
        <div class="max-w-6xl mx-auto">
            <?php if ($is_logged_in): ?>
                <div id="api-message" class="hidden"></div>
                <div class="relative flex items-center justify-center w-full my-8 px-8">
                    <div class="absolute left-8">
                        <a href="/front/services/products.php">
                            <button class="flex items-center rounded-md px-6 py-2 button-blue">
                                <img src="/front/icons/fleche_gauche.svg" alt="fleche" class="w-7 h-7 mr-2">
                                Revenir à la boutique
                            </button>
                        </a>
                    </div>
                    <h1 class="big-text text-center">Mon Panier</h1>
                </div>
                <div id="panier-container" class="bg-white rounded-[2.5rem] shadow-xl shadow-blue-900/10 overflow-hidden p-8 border border-gray-50">
                    <div class="text-center py-20">
                        <p class="text-gray-400">Chargement de votre panier...</p>
                    </div>
                </div>

                <div id="panier-resume" class="hidden mt-8 flex flex-col items-end gap-4">
                    <div id="frais-port-affichage" class="text-lg text-gray-600 font-semibold text-right"></div>
    
                    <div class="text-2xl font-bold text-[#1C5B8F]">
                        Total : <span id="total-price" class="text-[#E1AB2B]">0.00</span> €
                    </div>  
                    <a href="/front/services/delivery.php">
                        <button class="px-10 py-3 rounded-full bg-[#1C5B8F] text-white text-xl font-bold hover:bg-green-600 transition-all shadow-lg">
                            Valider ma commande
                        </button>
                    </a>
                </div>
            <?php else: ?>
                <div class="flex flex-col items-center justify-center py-20 rounded-[2.5rem] shadow-xl shadow-blue-900/10">
                    <p class="text-center font-semibold text-[#1C5B8F] text-2xl mb-8">
                        Vous devez être connecté(e) pour consulter votre panier.</p>
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

        window.addEventListener('auth_ready', () => {
            fetchPanier();
        });

        let quantite = 1;
        let maxStock = 0;
        let quantitePanier = 0;

        function showStockError(stock) {
            maxStock = Number(stock);
            const msg = quantitePanier > 0 ?
                `Vous avez déjà ${quantitePanier} articles au panier. Stock max : ${maxStock}.` :
                "Limite de stock atteinte.";
            showAlert(msg, false);
        }

        async function changeQuantity(idPanier, nouvelleQuantite, idProduit) {
            if (nouvelleQuantite <= 0) {
                removeProduit(idPanier);
                return;
            }

            const idUtilisateur = window.currentUserId;
            const donnees = new FormData();
            donnees.append("id_panier", idPanier);
            donnees.append("id_produit", idProduit);
            donnees.append("id_utilisateur", idUtilisateur);
            donnees.append("quantite", nouvelleQuantite);
            donnees.append("action", "update");

            try {
                const response = await fetch(`${API_BASE}/panier/add`, {
                    method: 'POST',
                    body: donnees
                });

                if (response.ok) {
                    const data = await response.json();
                    fetchPanier();
                } else {
                    const errorText = await response.text();
                    showAlert(errorText, false);
                }
            } catch (err) {
                console.error("Erreur Fetch:", err);
                showAlert("Impossible de joindre le serveur", false);
            }
        }

        async function fetchPanier() {
            const userId = window.currentUserId;
            try {
                const response = await fetch(`${API_BASE}/panier/get?id_utilisateur=${userId}`);
                const items = await response.json();

                const container = document.getElementById('panier-container');
                const resume = document.getElementById('panier-resume');

                if (!items || items.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-20">
                            <p class="text-2xl text-gray-400 mb-6">Votre panier est vide.</p>
                            <a href="products.php" class="text-[#1C5B8F] underline">Continuer mes achats</a>
                        </div>`;
                    resume.classList.add('hidden');
                    return;
                }

                let html = '<div class="flex flex-col gap-6">';
                let total = 0;

                items.forEach(item => {
                    const subtotal = item.prix * item.quantite;
                    total += subtotal;
                    const imgSrc = item.image ? `${API_BASE}/${item.image}` : 'https://via.placeholder.com/150';

                    html += `
                        <div class="flex items-center gap-6 p-4 border-b border-gray-100 last:border-0">
                            <img src="${imgSrc}" class="w-24 h-24 object-cover rounded-2xl border border-gray-100" alt="${item.nom}">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-[#1C5B8F]">${item.nom}</h3>
                                <p class="text-gray-500">Prix unitaire : ${parseFloat(item.prix).toFixed(2)} €</p>
                            </div>
                            <div class="flex items-center gap-4 rounded-lg p-1">
                                <button onclick="changeQuantity('${item.id_panier}', ${item.quantite - 1}, ${item.id_produit})" 
                                        class="w-10 h-10 text-xl font-bold hover:bg-gray-200 rounded-full transition-colors">-</button>
                                
                                <span class="font-semibold text-lg w-8 text-center">${item.quantite}</span>
                                
                                <button onclick="changeQuantity('${item.id_panier}', ${item.quantite + 1}, ${item.id_produit})" 
                                        class="w-10 h-10 text-xl font-bold hover:bg-gray-200 rounded-full transition-colors">+</button>
                            </div>
                            <div class="w-32 text-right">
                                <span class="text-xl font-bold text-[#E1AB2B]">${subtotal.toFixed(2)} €</span>
                            </div>
                            <button onclick="removeProduit(${item.id_panier})" class="text-red-500 hover:text-red-700 font-bold ml-4">
                                ✕
                            </button>
                        </div>
                    `;
                });

                html += '</div>';
                container.innerHTML = html;

                let fraisPort = 0;
                const affichageFrais = document.getElementById('frais-port-affichage');

                if (total > 0 && total <= 100) {
                    fraisPort = 4.99;
                        affichageFrais.innerHTML = `Frais de livraison : 4.99 € (inclus dans le prix total)<br><span class="text-sm text-green-500">Encore ${(100 - total).toFixed(2)} € pour la livraison gratuite !</span>`;                } else if (total > 100) {
                    fraisPort = 0;
                        affichageFrais.innerHTML = `<span class="text-green-500">Frais de livraison : OFFERTS</span>`;
                }

                const totalFinal = total + fraisPort;

            document.getElementById('total-price').textContent = totalFinal.toFixed(2);  
            resume.classList.remove('hidden');

            } catch (err) {
                console.error(err);
            }
        }

        async function removeProduit(idPanier) {
            if (!confirm("Supprimer cet article ?")) return;
            await fetch(`${API_BASE}/panier/delete?id=${idPanier}`, {
                method: 'DELETE'
            });
            fetchPanier();
        }
    </script>
</body>

</html>