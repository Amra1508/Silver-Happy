<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confidentialité</title>
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
        <h1 class="text-3xl md:text-4xl font-bold text-[#1C5B8F] mb-8">Politique de Confidentialité</h1>

        <div class="space-y-6 text-lg leading-relaxed">
            <p class="mb-4">Chez Silver Happy, nous accordons une importance primordiale à la protection de vos données personnelles et au respect de votre vie privée.</p>

            <section>
                <h2 class="text-2xl font-bold text-[#E1AB2B] mb-3">1. Données collectées</h2>
                <p>Dans le cadre de l'utilisation de nos services, nous sommes amenés à collecter les données suivantes :</p>
                <ul class="list-disc ml-6 mt-2">
                    <li>Informations d'identité (Nom, Prénom, Date de naissance)</li>
                    <li>Coordonnées (Adresse postale, Email, Numéro de téléphone)</li>
                    <li>Données de connexion (Identifiants, adresses IP)</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-[#E1AB2B] mb-3">2. Finalité de la collecte</h2>
                <p>Vos données sont collectées pour :</p>
                <ul class="list-disc ml-6 mt-2">
                    <li>Gérer votre compte utilisateur et vos abonnements.</li>
                    <li>Vous mettre en relation avec les prestataires pour les activités choisies.</li>
                    <li>Assurer votre sécurité (notamment via le bouton d'urgence).</li>
                    <li>Vous envoyer des communications relatives à nos services.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-[#E1AB2B] mb-3">3. Vos droits (RGPD)</h2>
                <p>Conformément à la réglementation européenne (RGPD), vous disposez d'un droit d'accès, de rectification, de suppression et de portabilité de vos données. Vous pouvez exercer ces droits en nous contactant à l'adresse suivante : <strong> contact.silver.happy@gmail.com</strong>.</p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-[#E1AB2B] mb-3">4. Conservation des données</h2>
                <p>Vos données personnelles sont conservées le temps nécessaire à l'exécution de nos services. En cas d'inactivité de votre compte, ou en cas de bannissement, vos données seront conservées ou supprimées conformément aux durées légales en vigueur.</p>
            </section>
        </div>
        </main>

    <?php include("../includes/footer.php") ?>

</body>
</html>

