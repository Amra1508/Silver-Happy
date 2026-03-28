<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos - Silver Happy</title>

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

    <main class="flex-grow max-w-5xl mx-auto w-full px-6 py-16">
        
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold text-[#1C5B8F] mb-6">À propos de Silver Happy</h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Découvrez l'histoire et l'équipe qui se cachent derrière votre plateforme dédiée à l'épanouissement.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-16">
            <div class="space-y-6">
                <h2 class="text-3xl font-bold text-[#1C5B8F] border-l-4 border-[#E1AB2B] pl-4">Notre histoire</h2>
                <p class="text-gray-700 leading-relaxed text-lg">
                    L'idée de <strong>Silver Happy</strong> est née d'un constat simple : la retraite est une nouvelle vie qui mérite d'être vécue pleinement. Nous voulions créer un espace unique, chaleureux et sécurisant, où les seniors peuvent trouver à la fois de l'inspiration, des conseils pratiques, et des occasions de se réunir.
                </p>
                <p class="text-gray-700 leading-relaxed text-lg">
                    Aujourd'hui, Silver Happy est bien plus qu'un site internet : c'est une véritable communauté qui célèbre le bien-être, le partage et la curiosité à tout âge.
                </p>
            </div>
            <div class="bg-gray-200 rounded-3xl h-64 md:h-full w-full flex items-center justify-center shadow-inner">
                <img src="/front/images/team.jpg"/>
            </div>
        </div>

        <div class="bg-white p-10 rounded-3xl shadow-sm border border-gray-100 mb-16">
            <h2 class="text-3xl font-bold text-[#1C5B8F] text-center mb-10">Nos valeurs</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div>
                    <h3 class="text-xl font-bold text-[#1C5B8F] mb-2">Bienveillance</h3>
                    <p class="text-gray-600">Un environnement respectueux où chaque membre compte et se sent écouté.</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-[#1C5B8F] mb-2">Vitalité</h3>
                    <p class="text-gray-600">Nous encourageons une vie active, curieuse et pleine de découvertes.</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-[#1C5B8F] mb-2">Sécurité</h3>
                    <p class="text-gray-600">Des paiements sécurisés et des événements encadrés pour une tranquillité d'esprit totale.</p>
                </div>
            </div>
        </div>

        <div class="text-center">
            <h2 class="text-3xl font-bold text-[#1C5B8F] mb-6">L'équipe</h2>
            <p class="text-lg text-gray-700 max-w-2xl mx-auto leading-relaxed mb-8">
                Derrière Silver Happy, c're une équipe de passionnés, des techniciens aux conseillers, en passant par notre <strong>Happiness Manager</strong>, tous dévoués à vous offrir la meilleure expérience possible chaque jour.
            </p>
        </div>

    </main>

    <?php include("../includes/footer.php") ?>

</body>
</html>