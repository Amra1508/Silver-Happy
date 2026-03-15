<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue</title>

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

<body class="bg-gray-50 flex flex-col min-h-screen">

    <?php include("../includes/header.php") ?>

    <main class="flex-1">
        
        <div class="relative h-[400px] w-full overflow-hidden">
            <img src="/front/images/background.webp" alt="" class="absolute inset-0 w-full h-full object-cover opacity-60">

            <div class="absolute inset-0 flex flex-col items-center justify-center px-16 bg-white/30 backdrop-blur-sm">
                <h2 class="text-4xl md:text-5xl leading-tight mb-4 text-[#1C5B8F] font-bold text-center drop-shadow-md">
                    Notre Catalogue
                </h2>
                <p class="text-xl md:text-2xl text-gray-800 text-center max-w-3xl font-medium">
                    Découvrez toutes nos offres conçues spécialement pour votre bien-être, votre confort et vos loisirs.
                </p>
            </div>
        </div>

        <div class="w-full px-6 md:px-16 mt-12 mb-4 bg-gray-50 text-center">
            <h2 class="text-2xl font-bold mb-4 text-[#1C5B8F]">
                Que recherchez-vous aujourd'hui ?
            </h2>
            <h2 class="text-lg max-w-4xl mx-auto text-gray-600">
                Sélectionnez la catégorie de votre choix pour découvrir nos propositions.
            </h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 px-6 md:px-16 py-12 max-w-7xl mx-auto">
            
            <a href="/front/services/events.php" class="group w-full bg-white border border-gray-200 flex flex-col p-10 rounded-[2.5rem] shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-[#1C5B8F] group-hover:bg-[#E1AB2B] transition-colors"></div>
                
                <h3 class="text-3xl text-[#1C5B8F] font-bold mb-4 text-center">Événements</h3>
                <p class="text-gray-600 text-center text-lg flex-grow mb-8">
                    Participez à nos sorties, ateliers et conférences pour partager des moments de convivialité avec la communauté.
                </p>
                <div class="w-full rounded-full py-4 px-6 bg-[#1C5B8F] text-white font-bold text-xl text-center group-hover:bg-[#154670] transition-colors shadow-md">
                    Voir l'agenda
                </div>
            </a>

            <a href="/front/services/catalog.php" class="group w-full bg-white border border-gray-200 flex flex-col p-10 rounded-[2.5rem] shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-[#E1AB2B] group-hover:bg-[#1C5B8F] transition-colors"></div>
                
                <h3 class="text-3xl text-[#1C5B8F] font-bold mb-4 text-center">Services</h3>
                <p class="text-gray-600 text-center text-lg flex-grow mb-8">
                    Découvrez notre gamme d'accompagnement à domicile : aide aux repas, entretien, et assistance quotidienne.
                </p>
                <div class="w-full rounded-full py-4 px-6 bg-[#E1AB2B] text-black font-bold text-xl text-center group-hover:bg-[#c79624] transition-colors shadow-md">
                    Consulter les offres
                </div>
            </a>

            <a href="#" class="group w-full bg-white border border-gray-200 flex flex-col p-10 rounded-[2.5rem] shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-[#1C5B8F] group-hover:bg-[#E1AB2B] transition-colors"></div>
                
                <h3 class="text-3xl text-[#1C5B8F] font-bold mb-4 text-center">Prestataires</h3>
                <p class="text-gray-600 text-center text-lg flex-grow mb-8">
                    Faites connaissance avec nos professionnels de confiance, rigoureusement sélectionnés pour vous.
                </p>
                <div class="w-full rounded-full py-4 px-6 bg-[#1C5B8F] text-white font-bold text-xl text-center group-hover:bg-[#154670] transition-colors shadow-md">
                    Voir les profils
                </div>
            </a>

        </div>
    </main>

    <?php include("../includes/footer.php") ?>

</body>
</html>