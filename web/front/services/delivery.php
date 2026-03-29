<?php
$is_logged_in = isset($_COOKIE['session_token']);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier - Silver Happy</title>
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
    <main class="p-8 max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-[#1C5B8F]">Finaliser ma commande</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-lg border">
                    <h2 class="font-bold mb-4">Adresse de livraison</h2>
                    <input type="text" id="adresse" placeholder="Rue..." class="w-full p-2 border rounded mb-2">
                    <div class="flex gap-2">
                        <input type="text" id="cp" placeholder="Code Postal" class="w-1/3 p-2 border rounded">
                        <input type="text" id="ville" placeholder="Ville" class="w-2/3 p-2 border rounded">
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-lg border">
                    <h2 class="font-bold mb-4">Code de réduction</h2>
                    <div class="flex gap-2">
                        <input type="text" id="coupon" class="flex-1 p-2 border rounded" placeholder="MONCODE10">
                        <button onclick="appliquerPromo()" class="bg-gray-200 px-4 py-2 rounded">Appliquer</button>
                    </div>
                    <p id="promo-message" class="text-xs mt-2"></p>
                </div>
            </div>

            <div class="bg-[#F8FAFC] p-6 rounded-2xl border">
                <h2 class="font-bold mb-4">Résumé</h2>
                <div id="recap-articles" class="text-sm space-y-2 mb-4">
                </div>
                <hr class="my-4">
                <div class="flex justify-between font-bold text-xl">
                    <span>Total à régler</span>
                    <span id="total-final">0.00 €</span>
                </div>

                <button onclick="payerStripe()" class="w-full mt-6 bg-[#1C5B8F] text-white py-4 rounded-full font-bold text-lg hover:bg-green-600 transition-all">
                    Payer avec Stripe
                </button>
                <a href="/front/services/basket.php">
                    <button class="w-full mt-6 bg-[#1C5B8F] text-white py-4 rounded-full font-bold text-lg hover:bg-green-600 transition-all">
                        Retour au panier
                    </button>
                </a>
            </div>
        </div>
    </main>
    <?php include("../includes/footer.php") ?>

    <script>
        const API_BASE = "http://localhost:8082";

        let reductionAppliquee = {
            valeur: 0,
            type: 'pourcentage',
            code: ''
        };

        window.addEventListener('auth_ready', () => {
            const savedPromo = localStorage.getItem('current_promo');
            if (savedPromo) {
                reductionAppliquee = JSON.parse(savedPromo);
                document.getElementById('coupon').value = reductionAppliquee.code || "";
            }
            if (window.userData) {
                document.getElementById('adresse').value = window.userData.adresse || "";
                document.getElementById('cp').value = window.userData.code_postal || "";
                document.getElementById('ville').value = window.userData.ville || "";
            }
            fetchPanier();
        });

        async function appliquerPromo() {
            const inputCoupon = document.getElementById('coupon');
            const msgCoupon = document.getElementById('promo-message');
            const code = inputCoupon.value.trim();
            const userid = window.currentUserId;

            if (!code) return;

            try {
                const response = await fetch(`${API_BASE}/panier/check?code=${code}&id_utilisateur=${userid}`);
                if (response.ok) {
                    const data = await response.json();

                    const ancienCode = reductionAppliquee.code;

                    reductionAppliquee = {
                        valeur: data.valeur,
                        type: data.type,
                        code: code
                    };

                    localStorage.setItem('current_promo', JSON.stringify(reductionAppliquee));

                    if (ancienCode && ancienCode !== code) {
                        msgCoupon.textContent = `Le code "${code}" a remplacé le code précédent.`;
                        msgCoupon.className = "text-xs mt-2 text-blue-600 font-semibold";
                    } else {
                        msgCoupon.textContent = "Code promo appliqué avec succès !";
                        msgCoupon.className = "text-xs mt-2 text-green-600 font-semibold";
                    }

                    inputCoupon.value = "";
                    fetchPanier();
                } else {
                    reductionAppliquee = {
                        valeur: 0,
                        type: 'pourcentage',
                        code: ''
                    };
                    localStorage.removeItem('current_promo');
                    msgCoupon.textContent = "Ce code promo n'existe pas ou est expiré.";
                    msgCoupon.className = "text-xs mt-2 text-red-500";
                    fetchPanier();
                }
            } catch (err) {
                console.error(err);
                msgCoupon.textContent = "Erreur lors de la vérification du code.";
            }
        }

        async function fetchPanier() {
            const userId = window.currentUserId;
            try {
                const response = await fetch(`${API_BASE}/panier/get?id_utilisateur=${userId}`);
                const items = await response.json();
                const resumeContainer = document.getElementById('recap-articles');

                if (!items || items.length === 0) {
                    resumeContainer.innerHTML = "<p class='text-gray-500'>Votre panier est vide.</p>";
                    return;
                }

                let html = '';
                let totalBrut = 0;

                items.forEach(item => {
                    const subtotal = item.prix * item.quantite;
                    totalBrut += subtotal;

                    html += `
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <div class="flex flex-col">
                        <span class="font-bold text-[#1C5B8F]">${item.nom}</span>
                        <span class="text-xs text-gray-400">Qté: ${item.quantite}</span>
                    </div>
                    <span class="font-semibold">${subtotal.toFixed(2)} €</span>
                </div>`;
                });

                let totalApresReduc = totalBrut;
                if (reductionAppliquee.valeur > 0) {
                    if (reductionAppliquee.type === 'pourcentage') {
                        totalApresReduc = totalBrut * (1 - (reductionAppliquee.valeur / 100));
                    } else {
                        totalApresReduc = totalBrut - reductionAppliquee.valeur;
                    }
                    html += `<div class="text-green-600 text-sm mt-2 font-bold italic">Réduction appliquée (${reductionAppliquee.code})</div>`;
                }

                resumeContainer.innerHTML = html;
                document.getElementById('total-final').textContent = `${Math.max(0, totalApresReduc).toFixed(2)} €`;

            } catch (err) {
                console.error("Erreur chargement récap:", err);
            }
        }

        async function payerStripe() {
            const userId = window.currentUserId;
            const adresse = document.getElementById('adresse').value.trim();
            const cp = document.getElementById('cp').value.trim();
            const ville = document.getElementById('ville').value.trim();

            if (!adresse || !cp || !ville) {
                alert("Veuillez remplir tous les champs de livraison avant de payer.");
                return;
            }

            if (!userId) return;

            const user = window.userData;
            const hasSubscription = user && user.id_abonnement && user.id_abonnement > 0;

            if (!hasSubscription) {
                alert("Vous devez posséder un abonnement Silver Happy pour valider votre commande.");
                window.location.href = "/front/services/subscription.php";
                return;
            }

            try {
                const response = await fetch(`${API_BASE}/paiement-panier`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        user_id: parseInt(userId),
                        code: reductionAppliquee.code,
                        adresse: adresse,
                        cp: cp,
                        ville: ville
                    })
                });

                const data = await response.json();
                if (data.url) {
                    localStorage.removeItem('current_promo');
                    window.location.href = data.url;
                } else {
                    alert("Erreur lors de la création de la session de paiement.");
                }
            } catch (err) {
                console.error("Erreur:", err);
                alert("Impossible de contacter le serveur de paiement.");
            }
        }
    </script>

</body>