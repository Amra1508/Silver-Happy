<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte Suspendu - SilverHappy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        <?php include("front.css") ?>
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 md:p-12 rounded-2xl shadow-xl max-w-lg w-full text-center border-t-8 border-[#AA1114]">
        
        <div class="flex justify-center mb-6">
            <img class="w-40 object-contain" src="/front/images/SilverHappy_logo.png" alt="SilverHappy Logo">
        </div>

        <div class="bg-red-50 text-[#AA1114] rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-800 mb-4">Compte Suspendu</h1>
        
        <p class="text-gray-600 mb-8 text-lg">
            Votre compte a été suspendu par un administrateeur suite à un non-respect de nos conditions d'utilisation. Si vous pensez qu'il s'agit d'une erreur, veuillez contacter le support.
        </p>

        <div class="flex flex-col gap-4">
            <a href="https://mail.google.com/mail/?view=cm&fs=1&to=contact@silver-happy.fr" class="bg-[#1C5B8F] text-white py-3 px-6 rounded-full font-semibold text-lg hover:bg-[#154670] transition-colors">
                Contacter le support
            </a>
            
            <button id="btn_ban_logout" class="border-2 border-gray-300 text-gray-600 py-3 px-6 rounded-full font-semibold text-lg hover:bg-gray-100 transition-colors">
                Se déconnecter
            </button>
        </div>

    </div>

    <script>
        document.getElementById('btn_ban_logout').addEventListener('click', async (e) => {
            e.preventDefault();
            try {
                const response = await fetch(`${window.API_BASE_URL}/auth/logout`, {
                    method: 'POST',
                    credentials: 'include'
                });

                if (response.ok) {
                    window.location.href = "/front/index.php";
                } else {
                    alert("Erreur lors de la déconnexion.");
                }
            } catch (error) {
                console.error("Erreur réseau lors de la déconnexion :", error);
            }
        });
    </script>

</body>
</html>