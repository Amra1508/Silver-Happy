<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentions Légales</title>
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
    <main>
        <body class="bg-gray-50 text-gray-800">

            <?php include("../includes/header.php") ?>

            <main class="max-w-4xl mx-auto px-6 py-12 md:py-16 bg-white shadow-sm my-8 rounded-2xl">
                <h1 class="text-3xl md:text-4xl font-bold text-[#1C5B8F] mb-8">Mentions Légales</h1>

        <div class="space-y-6 text-lg leading-relaxed">
            <section>
                <h2 class="text-2xl font-bold text-[#E1AB2B] mb-3">1. Éditeur du site</h2>
                <p>Le site <strong>Silver Happy</strong> est édité par la société RobOut.Inc</p>
                <ul class="list-disc ml-6 mt-2">
                    <li><strong>Siège social :</strong> 15 allée Jean Bart 93190 Livry-Gargan</li> 
                    <li><strong>Email :</strong> contact@silverhappy.fr</li>
                    <li><strong>Téléphone :</strong> 01 02 03 04 05</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-[#E1AB2B] mb-3">2. Directeur de la publication</h2>
                <p>Le directeur de la publication est <strong> Romario VELE</strong>, en sa qualité de Président.</p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-[#E1AB2B] mb-3">3. Hébergement du site</h2>
                <p>Le site Silver Happy est hébergé par la société <strong> Hetzner</strong>.</p>
                <ul class="list-disc ml-6 mt-2">
                    <li><strong>Siège social de l'hébergeur :</strong> Gunzenhausen, Allemagne</li>
                    <li><strong>Contact de l'hébergeur :</strong> +49 (0)9831 505-0</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-[#E1AB2B] mb-3">4. Propriété intellectuelle</h2>
                <p>L'ensemble de ce site relève de la législation française et internationale sur le droit d'auteur et la propriété intellectuelle. Tous les droits de reproduction sont réservés, y compris pour les documents téléchargeables et les représentations iconographiques et photographiques.</p>
            </section>
        </div>
        </main>

    <?php include("../includes/footer.php") ?>

</body>
</html>

