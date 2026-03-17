<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silver Happy</title>

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

    <?php include("./includes/header.php") ?>

    <main>
        <div class="relative h-[600px] w-full overflow-hidden">
            <img src="./images/background.webp"
                alt="background"
                class="absolute inset-0 w-full h-full object-cover opacity-60">

            <div class="absolute inset-0 flex flex-col items-start px-16 pt-40">
                <h2 class="big-text leading-tight mb-8">
                    Bien vivre après 60 ans,<br>
                    c’est possible chez Silver Happy !
                </h2>
                <img src="./images/SilverHappy_illustration.png"
                    alt="illustration"
                    class="absolute bottom-2 w-60 h-60 object-contain">

                <div class="absolute bottom-10 right-10">
                    <a href="/front/services/advice.php" class="rounded-full px-6 button-blue">
                        Découvrez nos conseils
                    </a>
                </div>
            </div>
        </div>
        <div class="w-full px-16 mt-7 bg-white">
            <h2 class="big-text mb-8">
                Nos prestataires
            </h2>
        </div>
        <div class="w-full px-16 bg-white">
            <h2 class="small-text">
                Afin de vous proposer des activités, services et évènements à la hauteur de vos attentes, nos prestataires sont
                rigoureusement sélectionnés. <br>
                Pour garantir votre sérénité, chaque prestataire fait l'objet d'un contrôle approfondi
                avant d'intégrer notre société :
            </h2>
        </div>
        <div class="flex flex-wrap gap-6 px-6 md:px-16 py-10 justify-between">
            <div class="md:max-w-[600px] bg-white index-components">
                <h3 class="big-text md:text-3xl mb-4">
                    Diplômes & Certifications
                </h3>
                <p class="small-text md:text-lg leading-relaxed">
                    Vérification systématique des diplômes<br>
                    et des certifications professionnelles.
                </p>
            </div>
            <div class="md:max-w-[600px] bg-white index-components">
                <h3 class="big-text md:text-3xl font-bold mb-4">
                    Contrôle des antécédents
                </h3>
                <p class="small-text md:text-lg leading-relaxed">
                    Contrôle rigoureux incluant l'extrait de casier judiciaire pour assurer votre sécurité et tranquillité d'esprit.
                </p>
            </div>
        </div>
        <div class="flex flex-wrap gap-6 px-6 md:px-16 py-10 justify-start">
            <div class="bg-[#E1AB2B]/60 index-components">
                <h3 class="text-center big-text md:text-3xl mb-4">
                    Chez Silver Happy, nous ne laissons rien au hasard.
                </h3>
                <p class="text-center small-text md:text-lg leading-relaxed">
                    Cette exigence nous permet de vous offrir un accompagnement humain de qualité, sécurisé<br>
                    et totalement adapté à vos besoins spécifiques, pour que votre seule priorité reste votre plaisir et votre confort.
                </p>
            </div>
        </div>
        <div class="flex flex-wrap gap-6 px-6 md:px-16 py-10 justify-between items-center">
            <div class="md:max-w-[900px] bg-white index-components">
                <h3 class="big-text md:text-3xl font-bold mb-4">
                    Vous avez aimé votre première expérience ?
                </h3>
                <p class="small-text md:text-lg leading-relaxed">
                    Partagez votre ressenti avec la communauté Silver Happy !
                    Votre avis compte et aide d'autres seniors à découvrir nos services.
                </p>
            </div>
            <div class="py-10 flex flex-col items-center gap-2 pr-4 md:pr-16 md:pl-16">
                <a href="/front/communication/review.php" class="rounded-full px-6 button-blue whitespace-nowrap">
                    Laisser mon avis
                </a>
                <div class="flex gap-4 text-[#E1AB2B] text-5xl">
                    <span>★</span>
                    <span>★</span>
                    <span>★</span>
                    <span>★</span>
                    <span>★</span>
                </div>
            </div>
        </div>

        <?php include("./includes/footer.php") ?>

        <div id="tour-overlay" class="hidden fixed inset-0 bg-gray-900/70 z-[80] transition-opacity duration-300"></div>

        <div id="tour-dialog" class="hidden fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-[100] bg-white p-8 w-[90%] max-w-lg border border-gray-300 shadow-2xl rounded-sm">
            <div class="mb-6">
                <p id="tour-step-counter" class="text-sm font-bold text-gray-400 mb-2 uppercase tracking-wide">Étape 1 sur 7</p>
                <h3 id="tour-title" class="text-2xl font-bold text-[#1C5B8F] mb-3">Guide de navigation</h3>
                <p id="tour-text" class="text-gray-700 text-lg leading-relaxed">Texte explicatif classique.</p>
            </div>

            <div class="flex justify-between items-center border-t border-gray-200 pt-6 mt-2">
                <button type="button" id="tour-prev" class="text-[#1C5B8F] underline hover:text-blue-800 px-2 py-1 invisible">
                    Retour
                </button>

                <button type="button" id="tour-next" class="bg-[#1C5B8F] text-white px-6 py-2 font-medium hover:bg-[#154670] transition-colors">
                    Étape suivante
                </button>
                <button type="button" id="tour-close" class="hidden bg-[#E1AB2B] text-white px-6 py-2 font-medium hover:bg-[#c99723] transition-colors">
                    Terminer la visite
                </button>
            </div>
        </div>

    </main>

</body>

</html>