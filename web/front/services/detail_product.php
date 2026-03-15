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
    <main class="mb-10 bg-white">
        <div class="max-w-6xl mx-auto">
            <?php if ($is_logged_in): ?>
                <div class="max-w-6xl mx-auto px-4">
                    <div class="w-full pt-8">
                        <h1 class="mb-5 text-center big-text">Détails du Produit</h1>
                    </div>

                    <div id="detail-container" class="bg-white rounded-[2.5rem] shadow-xl shadow-blue-900/10 overflow-hidden p-8">
                        <div class="flex items-center justify-center py-20">
                            <p class="text-gray-400 italic animate-pulse">Chargement du produit...</p>
                        </div>
                    </div>

                    <div id="pagination-controls"></div>
                </div>
            <?php else: ?>
                <div class="flex flex-col items-center justify-center py-20 rounded-[2.5rem] shadow-xl shadow-blue-900/10">
                    <p class="text-center font-semibold text-[#1C5B8F] text-2xl mb-8">
                        Vous devez être connecté(e) pour consulter nos produits Silver Happy.</p>
                    <a href="/front/account/signin.php" class="rounded-full px-4 py-2 button-blue">
                        Je me connecte </a>
                </div>
            <?php endif; ?>
        </div>

    </main>
    <?php include("../includes/footer.php") ?>

    <script>
        const API_BASE = "http://localhost:8082";

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
                const response = await fetch(`${API_BASE}/produit/read-one?id=${productId}`);

                if (!response.ok) {
                    throw new Error("Produit non trouvé côté serveur");
                }

                const p = await response.json();

                const imgSrc = 'https://via.placeholder.com/500';

                document.getElementById('detail-container').innerHTML = `
            <div class="flex flex-col md:flex-row gap-12">
                <div class="w-full md:w-1/2">
                    <div class="aspect-square overflow-hidden rounded-3xl border border-gray-100 shadow-sm">
                        <img src="${imgSrc}" class="w-full h-full object-cover" alt="${p.nom}">
                    </div>
                </div>

                <div class="w-full md:w-1/2 flex flex-col justify-center">
                    <h2 class="text-4xl font-bold text-[#1C5B8F] mb-4">${p.nom}</h2>
                    <div class="text-3xl font-bold text-[#E1AB2B] mb-6 italic">
                        ${parseFloat(p.prix).toFixed(2)} €
                    </div>
                    
                    <div class="border-t border-gray-100 pt-6 mb-6">
                        <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-2">Description</h3>
                        <p class="text-gray-600 text-lg leading-relaxed text-justify">
                            ${p.description}
                        </p>
                    </div>

                    <div class="flex items-center gap-4 mt-auto">
                        <span class="px-4 py-2 bg-blue-50 text-[#1C5B8F] rounded-full text-sm font-bold">
                            Stock disponible : ${p.stock}
                        </span>
                        <button class="bg-[#1C5B8F] text-white px-8 py-3 rounded-full font-bold hover:bg-[#15466e] transition-all shadow-lg">
                            Ajouter au panier
                        </button>
                    </div>
                    
                    <a href="javascript:history.back()" class="mt-8 text-gray-400 hover:text-[#1C5B8F] text-sm flex items-center gap-2">
                        ← Retour à la liste
                    </a>
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

        window.onload = fetchOneProduit;
    </script>
</body>

</html>