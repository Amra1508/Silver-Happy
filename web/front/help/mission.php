<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notre mission - Silver Happy</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>

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

    <main class="flex-grow max-w-4xl mx-auto w-full px-6 py-16">
        
        <div class="mb-12 border-b border-gray-200 pb-10">
            <h1 class="text-4xl md:text-5xl font-bold text-[#1C5B8F] mb-6">Notre mission</h1>
            <p class="text-2xl text-[#E1AB2B] font-semibold leading-relaxed">
                "Réinventer le quotidien des seniors en créant des moments de joie, d'apprentissage et de partage."
            </p>
        </div>

        <div class="space-y-12">
            
            <section>
                <h2 class="text-2xl font-bold text-[#1C5B8F] mb-4">Briser l'isolement et favoriser le lien social</h2>
                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                    Notre mission première est de lutter contre l'isolement. À travers notre plateforme, nous souhaitons faciliter les rencontres et les échanges. C'est pour cela que nous organisons régulièrement des <strong>événements exclusifs</strong> : ateliers créatifs, sorties culturelles ou simples moments de convivialité.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-[#1C5B8F] mb-4">Accompagner avec des conseils de qualité</h2>
                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                    Nous savons que chaque étape de la vie apporte ses propres questions. Notre rubrique <strong>Conseils</strong> est conçue pour vous apporter des réponses claires sur la santé, le bien-être, les loisirs, et l'utilisation des nouvelles technologies. Une information fiable pour aborder le quotidien en toute sérénité.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-[#1C5B8F] mb-4">Faciliter l'accès aux produits adaptés</h2>
                <p class="text-gray-700 text-lg leading-relaxed mb-4">
                    Notre <strong>Boutique</strong> a été pensée pour rassembler des produits utiles, ergonomiques et de qualité, spécialement sélectionnés pour répondre à vos besoins. De l'achat jusqu'à la facture sécurisée, nous rendons l'expérience simple et transparente.
                </p>
            </section>

            <div class="bg-[#1C5B8F] text-white rounded-3xl p-10 text-center mt-12 shadow-lg">
                <h3 class="text-3xl font-bold mb-4">Rejoignez l'aventure Silver Happy</h3>
                <p class="text-lg mb-8 opacity-90">Devenez membre aujourd'hui et profitez de tous nos services, événements et conseils personnalisés.</p>
                <a href="/front/services/subscription.php" class="inline-block bg-[#E1AB2B] text-white font-bold text-lg px-8 py-3 rounded-full hover:bg-yellow-600 transition shadow-md">
                    Je découvre les abonnements
                </a>
            </div>

        </div>

    </main>

    <?php include("../includes/footer.php") ?>

</body>
</html>