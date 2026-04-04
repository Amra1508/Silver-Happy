<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Prestataire</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Alata', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="flex min-h-screen">

        <?php include("includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col min-w-0">
            
            <main class="p-8">
                <div id="main-content-valide" class="hidden">
                    <h1 class="text-3xl font-semibold text-[#1C5B8F] mb-8">Mon Tableau de bord</h1>
                    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100">
                        <p>Bienvenue dans votre espace pro ! Vous pouvez maintenant gérer vos prestations, votre planning et vos factures.</p>
                        </div>
                </div>

                <div id="main-content-attente" class="hidden max-w-3xl mx-auto mt-10">
                    <div class="bg-white p-10 rounded-[2.5rem] shadow-md border-t-4 border-[#E1AB2B] text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-yellow-100 mb-6">
                            <svg class="w-8 h-8 text-[#E1AB2B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-4">Votre compte est en cours d'examen</h1>
                        <p class="text-gray-600 mb-6">
                            Merci d'avoir rejoint SilverHappy. Notre équipe vérifie actuellement vos documents (Identité, KBIS, etc.). 
                            Cette étape est nécessaire pour garantir la sécurité de nos seniors.
                        </p>
                        <p class="text-sm text-gray-500">
                            Vous recevrez un e-mail dès que votre profil sera validé. En attendant, les fonctionnalités de votre espace pro sont restreintes.
                        </p>
                    </div>
                </div>

                <div id="main-content-refuse" class="hidden max-w-3xl mx-auto mt-10">
                    <div class="bg-white p-10 rounded-[2.5rem] shadow-md border-t-4 border-red-500 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-6">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-4">Demande refusée</h1>
                        <p class="text-gray-600 mb-2">Malheureusement, votre demande d'inscription n'a pas pu être validée.</p>
                        <p id="motif-refus-text" class="text-red-600 font-semibold mb-6"></p>
                        <a href="mailto:contact@silverhappy.fr" class="text-[#1C5B8F] underline font-semibold">Contacter le support</a>
                    </div>
                </div>

            </main>

        </div>
    </div>

</body>
</html>