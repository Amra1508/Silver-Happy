<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conseils</title>
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

        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            if (modal) {
                modal.classList.toggle('hidden');
                modal.classList.toggle('flex');
            }
        }
    </script>
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="flex min-h-screen">

        <?php include("../includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col">
            <?php include("../includes/header.php"); ?>

            <main class="p-8">
                <div class="max-w-4xl mx-auto py-5">
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
                        <div class="bg-white px-6 py-4 border-b border-gray-200 text-center">
                            <h4 class="text-xl font-semibold text-gray-800">Cette page n'est pas disponible pour le moment...</h4>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>