<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CGU</title>
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
        <h1 class="text-3xl md:text-4xl font-bold text-[#1C5B8F] mb-8">Conditions Générales d'Utilisation (CGU)</h1>

        <div class="space-y-6 text-lg leading-relaxed">
            <section>
                <h2 class="text-2xl font-bold text-[#E1AB2B] mb-3">1. Objet</h2>
                <p>Les présentes CGU ont pour objet de définir les modalités et conditions dans lesquelles Silver Happy met à la disposition de ses utilisateurs le site, et la manière dont les utilisateurs accèdent au site et utilisent ses services (mise en relation avec des prestataires, messagerie, etc.).</p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-[#E1AB2B] mb-3">2. Accès aux services</h2>
                <p>L'accès à certains services (messagerie, réservation d'activités) nécessite la création d'un compte utilisateur. L'utilisateur s'engage à fournir des informations véridiques et exactes lors de son inscription. Le site est destiné à un public senior, la bienveillance et le respect sont exigés lors des interactions sur la plateforme.</p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-[#E1AB2B] mb-3">3. Responsabilité de l'utilisateur</h2>
                <p>L'utilisateur est responsable de la protection du mot de passe qu'il utilise pour accéder au site. En cas d'utilisation frauduleuse de son compte, il s'engage à en informer Silver Happy dans les plus brefs délais.</p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-[#E1AB2B] mb-3">4. Prestataires et activités</h2>
                <p>Silver Happy agit en tant qu'intermédiaire de mise en relation. Bien que nos prestataires soient rigoureusement sélectionnés (vérification des diplômes et antécédents), la responsabilité de l'exécution des activités incombe directement aux prestataires concernés.</p>
            </section>
        </div>
        </main>

    <?php include("../includes/footer.php") ?>

</body>
</html>

