<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookies</title>
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
<body class="bg-gray-50 text-gray-800">

    <?php include("../includes/header.php") ?>

    <main class="max-w-4xl mx-auto px-6 py-12 md:py-16 bg-white shadow-sm my-8 rounded-2xl">
        <h1 class="text-3xl md:text-4xl font-bold text-[#1C5B8F] mb-8">Politique de gestion des Cookies</h1>

        <div class="space-y-6 text-lg leading-relaxed">
            <p class="mb-4">Lors de votre navigation sur Silver Happy, des informations peuvent être enregistrées dans des fichiers "Cookies" installés sur votre ordinateur, tablette ou smartphone.</p>

            <section>
                <h2 class="text-2xl font-bold text-[#E1AB2B] mb-3">1. Qu'est-ce qu'un cookie ?</h2>
                <p>Un cookie est un petit fichier texte déposé sur votre terminal lors de la visite d'un site ou de la consultation d'une publicité. Ils ont notamment pour but de collecter des informations relatives à votre navigation et de vous adresser des services personnalisés.</p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-[#E1AB2B] mb-3">2. Les cookies que nous utilisons</h2>
                <ul class="list-disc ml-6 mt-2 space-y-2">
                    <li><strong>Cookies strictement nécessaires :</strong> Indispensables au fonctionnement du site (ex: conservation de votre session de connexion, mémorisation de votre acceptation du tutoriel). Ils ne peuvent pas être désactivés.</li>
                    <li><strong>Cookies de fonctionnalité :</strong> Permettent d'optimiser votre expérience (ex: mémorisation de vos préférences de zoom pour la taille du texte).</li>
                    <li><strong>Cookies de mesure d'audience (si applicable) :</strong> Nous permettent de connaître l'utilisation et les performances de notre site pour en améliorer le fonctionnement.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-[#E1AB2B] mb-3">3. Vos choix concernant les cookies</h2>
                <p>Vous pouvez à tout moment choisir de désactiver ces cookies via les paramètres de votre navigateur web. Cependant, nous vous informons que le paramétrage est susceptible de modifier vos conditions d'accès à nos services nécessitant l'utilisation de cookies.</p>
            </section>
        </div>
        </main>

    <?php include("../includes/footer.php") ?>

</body>
</html>

