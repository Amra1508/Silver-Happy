<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Prestataire - Silver Happy</title>
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
<body class="bg-gray-50 text-gray-800">

    <div class="flex min-h-screen">

        <?php include("includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col min-w-0">
            
            <main class="p-8">
                <h1 class="text-3xl font-semibold text-[#1C5B8F] mb-8">Mon Tableau de bord</h1>
                
                <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <p>Bienvenue dans votre espace pro !</p>
                </div>
            </main>

        </div>
    </div>

</body>
</html>