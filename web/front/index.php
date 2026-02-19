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
                <h2 class="text-4xl font-semibold text-[#1C5B8F] leading-tight mb-8">
                    Bien vivre après 60 ans,<br>
                    c’est possible chez Silver Happy !
                </h2>
                <img src="./images/SilverHappy_illustration.png"
                    alt="illustration"
                    class="absolute bottom-2 w-60 h-60 object-contain">

                <div class="absolute bottom-10 right-10">
                    <a href="#" class="bg-[#1C5B8F] text-white px-4 py-2 rounded-full font-semibold text-xl hover:bg-[#E1AB2B]/60 transition shadow-md">
                        Découvrez nos services
                    </a>
                </div>
            </div>
        </div>
        <div class="w-full px-16 mt-7 bg-white">
            <h2 class="text-4xl font-semibold text-[#1C5B8F] mb-8">
                Nos prestataires
            </h2>
        </div>
        <div class="w-full px-16 bg-white">
            <h2 class="text-xl font-semibold text-[#1C5B8F]">
                Afin de vous proposer des activités, services et évènements à la hauteur de vos attentes, nos prestataires sont
                rigoureusement sélectionnés. <br>
                Pour garantir votre sérénité, chaque prestataire fait l'objet d'un contrôle approfondi
                avant d'intégrer notre société :
            </h2>
        </div>
        <div class="flex flex-wrap gap-6 px-6 md:px-16 py-10 justify-between text-[#1C5B8F] font-semibold">
            <div class="flex-1 min-w-[400px] max-w-full md:max-w-[600px] bg-white border border-gray-100 rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-blue-900/10">
                <h3 class="text-4xl md:text-3xl mb-4">
                    Diplômes & Certifications
                </h3>
                <p class="text-xl md:text-lg leading-relaxed">
                    Vérification systématique des diplômes<br>
                    et des certifications professionnelles.
                </p>
            </div>
            <div class="flex-1 min-w-[400px] max-w-full md:max-w-[600px] bg-white border border-gray-100 rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-blue-900/10">
                <h3 class="text-4xl md:text-3xl font-bold mb-4">
                    Contrôle des antécédents
                </h3>
                <p class="text-xl md:text-lg leading-relaxed">
                    Contrôle rigoureux incluant l'extrait de casier judiciaire pour assurer votre sécurité et tranquillité d'esprit.
                </p>
            </div>
        </div>
        <div class="flex flex-wrap gap-6 px-6 md:px-16 py-10 justify-start text-[#1C5B8F] font-semibold">
            <div class="flex-1 min-w-[400px] max-w-full bg-[#E1AB2B]/60 border border-gray-100 rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-blue-900/10">
                <h3 class="text-center text-4xl text-[#1C5B8F] font-semibold md:text-3xl mb-4">
                    Chez Silver Happy, nous ne laissons rien au hasard.
                </h3>
                <p class="text-center text-xl text-[#1C5B8F] font-semibold md:text-lg leading-relaxed">
                    Cette exigence nous permet de vous offrir un accompagnement humain de qualité, sécurisé<br>
                    et totalement adapté à vos besoins spécifiques, pour que votre seule priorité reste votre plaisir et votre confort.
                </p>
            </div>
        </div>
        <div class="flex flex-wrap gap-6 px-6 md:px-16 py-10 justify-between items-center text-[#1C5B8F] font-semibold">
            <div class="flex-1 min-w-[400px] max-w-full md:max-w-[900px] bg-white border border-gray-100 rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-blue-900/10">
                <h3 class="text-4xl md:text-3xl font-bold mb-4">
                    Vous avez aimé votre première expérience ?
                </h3>
                <p class="text-xl md:text-lg leading-relaxed">
                    Partagez votre ressenti avec la communauté Silver Happy !
                    Votre avis compte et aide d'autres seniors à découvrir nos services.
                </p>
            </div>
            <div class="py-10 flex flex-col items-center gap-2 pr-4 md:pr-16 md:pl-16">
                <a href="#" class="bg-[#1C5B8F] text-white px-6 py-2 rounded-full font-semibold text-xl hover:bg-[#E1AB2B]/60 transition shadow-md whitespace-nowrap">
                    Laissez mon avis
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

    </main>

</body>

</html>