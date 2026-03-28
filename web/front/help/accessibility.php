<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accessibilité - Silver Happy</title>

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
        
        <div class="mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-[#1C5B8F] mb-6">Déclaration d'accessibilité</h1>
            <p class="text-lg text-gray-700 leading-relaxed">
                Chez <strong>Silver Happy</strong>, nous avons la conviction qu'internet doit être un espace ouvert et accessible à toutes et tous, sans exception. Compte tenu de notre mission dédiée à l'épanouissement des seniors, l'accessibilité numérique est au cœur de nos priorités.
            </p>
        </div>

        <div class="space-y-10">
            
            <section class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                <h2 class="text-2xl font-bold text-[#1C5B8F] mb-4 flex items-center">
                    <span class="w-2 h-8 bg-[#E1AB2B] rounded-full mr-4 inline-block"></span>
                    Notre engagement
                </h2>
                <p class="text-gray-600 leading-relaxed mb-4">
                    Nous nous engageons à rendre notre site internet accessible conformément à l'article 47 de la loi n° 2005-102 du 11 février 2005. Notre objectif est de proposer une expérience de navigation fluide, claire et intuitive, adaptée aux besoins spécifiques liés à la vision, à la motricité ou à la compréhension.
                </p>
            </section>

            <section class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                <h2 class="text-2xl font-bold text-[#1C5B8F] mb-4 flex items-center">
                    <span class="w-2 h-8 bg-[#E1AB2B] rounded-full mr-4 inline-block"></span>
                    Fonctionnalités mises en place
                </h2>
                <ul class="space-y-4 text-gray-600 leading-relaxed list-disc list-inside">
                    <li><strong>Contrastes de couleurs :</strong> Utilisation de couleurs fortement contrastées (Bleu marine <span class="text-[#1C5B8F] font-bold">#1C5B8F</span> et fond clair) pour assurer une lecture sans fatigue visuelle.</li>
                    <li><strong>Typographie lisible :</strong> Choix de la police "Alata", une typographie sans empattement, claire et espacée.</li>
                    <li><strong>Adaptabilité (Responsive) :</strong> Le site est conçu pour s'adapter à toutes les tailles d'écrans (ordinateurs, tablettes, smartphones) et permet le zoom natif du navigateur jusqu'à 200 % sans perte d'information.</li>
                    <li><strong>Navigation simplifiée :</strong> Des boutons d'action larges, des liens clairement identifiables et une structure de page logique.</li>
                </ul>
            </section>

            <section class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                <h2 class="text-2xl font-bold text-[#1C5B8F] mb-4 flex items-center">
                    <span class="w-2 h-8 bg-[#E1AB2B] rounded-full mr-4 inline-block"></span>
                    Retour d'information et contact
                </h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    L'accessibilité est un travail continu. Si vous rencontrez un défaut d'accessibilité vous empêchant d'accéder à un contenu ou à une fonctionnalité (par exemple, pour consulter un conseil, réserver un événement ou accéder à vos factures), nous vous invitons à nous le signaler.
                </p>
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                    <p class="text-gray-700 font-medium mb-2">Vous pouvez nous contacter :</p>
                    <ul class="space-y-2 text-[#1C5B8F]">
                        <li><strong class="text-gray-700">Par e-mail :</strong> <a href="mailto:contact@silverhappy.fr" class="hover:text-[#E1AB2B] underline transition">contact@silverhappy.fr</a></li>
                        <li><strong class="text-gray-700">Par téléphone :</strong> 01 23 45 67 89 (du lundi au vendredi, de 9h à 17h)</li>
                    </ul>
                </div>
            </section>

        </div>

    </main>

    <?php include("../includes/footer.php") ?>

</body>
</html>