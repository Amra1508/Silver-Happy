<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Prestataire</title>

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

<body class="flex flex-col min-h-screen bg-gray-50">

    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center">
                    <a href="/index.php" class="text-3xl font-bold tracking-wider text-[#1C5B8F]">
                        Silver<span class="text-[#E1AB2B]">Happy</span>
                    </a>
                </div>
                <nav class="hidden md:flex space-x-8 items-center">
                    <a href="/front/index.php" class="text-gray-600 hover:text-[#1C5B8F] font-medium transition-colors">Espace Senior</a>
                    <a href="providers/account/signup.php" class="px-5 py-2 bg-[#E1AB2B]/10 text-[#E1AB2B] rounded-full font-bold hover:bg-[#E1AB2B]/20 transition-colors">Devenir Pro</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center py-16 px-4">
        
        <div class="w-full max-w-md bg-white border border-gray-200 rounded-[2.5rem] p-10 shadow-xl">
            
            <h2 class="text-3xl text-center mb-2 font-semibold text-[#1C5B8F]">Espace Pro</h2>
            <p class="text-center text-gray-500 mb-8 text-sm">Connectez-vous pour gérer vos prestations</p>

            <div id="login_message" class="hidden mb-6 p-4 rounded-lg border text-center font-bold text-sm"></div>

            <form id="login_form" class="space-y-6">
                
                <div>
                    <label for="email" class="block text-sm text-gray-600 font-semibold mb-2">Adresse mail pro</label>
                    <input type="email" id="email" class="w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:border-[#1C5B8F]" required>
                </div>

                <div>
                    <label for="password" class="block text-sm text-gray-600 font-semibold mb-2">Mot de passe</label>
                    <input type="password" id="password" class="w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:border-[#1C5B8F]" required>
                </div>

                <button type="submit" class="w-full py-3 bg-[#1C5B8F] text-white font-semibold rounded-md hover:bg-blue-800 transition-colors shadow-md">
                    Me connecter
                </button>

            </form>

            <div class="mt-8 text-center pt-6 border-t border-gray-100">
                <span class="text-gray-600 text-sm">Vous n'avez pas encore de compte pro ?</span><br>
                <a href="signup.php" class="text-[#E1AB2B] font-semibold hover:underline transition-all mt-2 inline-block">
                    Déposer mon dossier d'inscription
                </a>
            </div>

        </div>

    </main>

    <footer class="bg-[#1C5B8F] text-white py-10 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="text-center md:text-left">
                <span class="text-2xl font-bold tracking-wider">
                    Silver<span class="text-[#E1AB2B]">Happy</span>
                </span>
                <p class="text-sm text-blue-200 mt-2">Accompagner nos aînés au quotidien.</p>
                <p class="text-sm text-blue-300 mt-1">&copy; 2026 Silver Happy. Tous droits réservés.</p>
            </div>
            <div class="flex flex-wrap justify-center gap-x-8 gap-y-2 text-sm text-blue-200">
                <a href="#" class="hover:text-[#E1AB2B] transition-colors">Mentions légales</a>
                <a href="#" class="hover:text-[#E1AB2B] transition-colors">Politique de confidentialité</a>
                <a href="#" class="hover:text-[#E1AB2B] transition-colors">CGU Prestataires</a>
                <a href="#" class="hover:text-[#E1AB2B] transition-colors">Nous contacter</a>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('login_form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const messageBox = document.getElementById('login_message');
            const email = document.getElementById('email').value;
            const mdp = document.getElementById('password').value;

            if (!email || !mdp) {
                messageBox.textContent = "Veuillez saisir vos identifiants.";
                messageBox.className = "mb-6 p-3 rounded-lg border text-center font-bold bg-red-100 border-red-400 text-red-700 text-sm";
                messageBox.classList.remove('hidden');
                return;
            }

            try {
                const response = await fetch('http://localhost:8082/auth/login-provider', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email: email, mdp: mdp })
                });

                if (response.ok) {
                    messageBox.textContent = "Connexion réussie. Redirection...";
                    messageBox.className = "mb-6 p-3 rounded-lg border text-center font-bold bg-green-100 border-green-400 text-green-700 text-sm";
                    messageBox.classList.remove('hidden');

                    setTimeout(() => {
                        window.location.href = "dashboard.php"; 
                    }, 1500);
                } else {
                    const errorText = await response.text();
                    
                    messageBox.textContent = errorText.length < 50 ? errorText : "Identifiants incorrects ou compte non validé.";
                    messageBox.className = "mb-6 p-3 rounded-lg border text-center font-bold bg-red-100 border-red-400 text-red-700 text-sm";
                    messageBox.classList.remove('hidden');
                }
            } catch (error) {
                messageBox.textContent = "Impossible de joindre le serveur.";
                messageBox.className = "mb-6 p-3 rounded-lg border text-center font-bold bg-red-100 border-red-400 text-red-700 text-sm";
                messageBox.classList.remove('hidden');
            }
        });
    </script>

</body>
</html>