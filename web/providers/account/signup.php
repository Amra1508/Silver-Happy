<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Prestataire</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

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
                    <a href="/providers/account.signin.php" class="px-5 py-2 bg-[#1C5B8F]/10 text-[#1C5B8F] rounded-full font-bold hover:bg-[#1C5B8F]/20 transition-colors">Connexion Pro</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <div class="px-4 sm:px-16 pt-10 pb-16">

            <h2 class="text-3xl text-center mb-10 font-semibold text-[#1C5B8F]">Devenir Prestataire :</h2>

            <div id="response_message" class="hidden max-w-4xl mx-auto mb-6 p-4 rounded-lg border"></div>

            <div class="border border-[#1C5B8F] bg-white rounded-[2.5rem] py-10 px-10 grid gap-x-6 gap-y-8 sm:grid-cols-6 max-w-4xl mx-auto shadow-md">
                
                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-semibold">Prénom</label>
                    <div class="mt-2">
                        <input id="first_name" type="text" class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:border-[#1C5B8F]" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-semibold">Nom</label>
                    <div class="mt-2">
                        <input id="last_name" type="text" class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:border-[#1C5B8F]" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-semibold">Date de naissance</label>
                    <div class="mt-2">
                        <input id="birth_date" type="date" class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:border-[#1C5B8F]" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-semibold">Numéro de téléphone</label>
                    <div class="mt-2">
                        <input id="phone_number" type="tel" class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:border-[#1C5B8F]" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-semibold">Adresse mail pro</label>
                    <div class="mt-2">
                        <input id="signup_email" type="email" class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:border-[#1C5B8F]" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-semibold">Mot de passe</label>
                    <div class="mt-2">
                        <input id="signup_password" type="password" class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:border-[#1C5B8F]" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-semibold">Numéro SIRET (14 chiffres)</label>
                    <div class="mt-2">
                        <input id="siret" type="text" maxlength="14" class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:border-[#1C5B8F]" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-semibold">Catégorie de prestation</label>
                    <div class="mt-2">
                        <select id="id_categorie" class="w-full border border-gray-300 rounded-md p-2 bg-white focus:outline-none focus:border-[#1C5B8F]" required>
                            <option value="" disabled selected>Chargement des catégories...</option>
                        </select>
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-semibold">Tarif horaire/forfait (€)</label>
                    <div class="mt-2">
                        <input id="tarif" type="number" step="0.01" min="0" placeholder="Ex: 25.50" class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:border-[#1C5B8F]" required />
                    </div>
                </div>

                <div class="sm:col-span-6 flex justify-center mt-4">
                    <div class="cf-turnstile" data-sitekey="0x4AAAAAACpHSvX9fEtYZZTy" data-theme="light"></div>
                </div>

                <button id="btn_register" class="sm:col-span-6 mx-auto px-14 py-3 bg-[#1C5B8F] text-white font-semibold rounded-md hover:bg-blue-800 transition-colors shadow-md">Envoyer ma demande</button>
            </div>
            
            <div class="mt-8 text-center">
                <span class="text-gray-600">Vous avez déjà un compte validé ?</span>
                <a href="signin.php" class="text-[#E1AB2B] font-semibold hover:underline ml-1 transition-all">
                    Je me connecte
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
        const limitDate = new Date();
        limitDate.setFullYear(limitDate.getFullYear() - 18);
        const strMax = limitDate.toISOString().split('T')[0];
        document.getElementById('birth_date').max = strMax;

        const btnSubmit = document.getElementById('btn_register');

        btnSubmit.addEventListener('click', async (e) => {
            e.preventDefault();

            const messageBox = document.getElementById('response_message');
            const turnstileResponse = document.querySelector('[name="cf-turnstile-response"]')?.value;

            const inputBirth = document.getElementById('birth_date').value;
            if (inputBirth > strMax) {
                messageBox.textContent = "Désolé, vous devez avoir au moins 18 ans pour devenir prestataire.";
                messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold bg-red-100 border-red-400 text-red-700";
                messageBox.classList.remove('hidden');
                return;
            }

            const data = {
                prenom: document.getElementById('first_name').value,
                nom: document.getElementById('last_name').value,
                date_naissance: document.getElementById('birth_date').value,
                num_telephone: document.getElementById('phone_number').value,
                email: document.getElementById('signup_email').value,
                mdp: document.getElementById('signup_password').value,
                siret: document.getElementById('siret').value,
                id_categorie: parseInt(document.getElementById('id_categorie').value),
                tarifs: parseFloat(document.getElementById('tarif').value),
                "cf-turnstile-response": turnstileResponse
            };

            if (!data.prenom || !data.nom || !data.date_naissance || !data.email || !data.mdp || !data.siret || !data.id_categorie || isNaN(data.tarifs)) {
                messageBox.textContent = "Veuillez remplir tous les champs obligatoires.";
                messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold bg-red-100 border-red-400 text-red-700";
                messageBox.classList.remove('hidden');
                return;
            }

            if (data.siret.length !== 14 || isNaN(data.siret)) {
                messageBox.textContent = "Le numéro SIRET doit contenir exactement 14 chiffres.";
                messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold bg-red-100 border-red-400 text-red-700";
                messageBox.classList.remove('hidden');
                return;
            }

            if (!turnstileResponse) {
                messageBox.textContent = "Veuillez valider la vérification de sécurité (Captcha).";
                messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold bg-red-100 border-red-400 text-red-700";
                messageBox.classList.remove('hidden');
                return;
            }

            try {
                const response = await fetch('http://localhost:8082/auth/register-provider', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold";

                if (response.ok) {
                    const result = await response.json();

                    messageBox.textContent = "Demande envoyée ! Bienvenue " + (result.prenom || data.prenom) + ". Votre compte est en attente de validation.";
                    messageBox.classList.add('bg-green-100', 'border-green-400', 'text-green-700');
                    messageBox.classList.remove('hidden');

                    setTimeout(() => {
                        window.location.href = "signin.php";
                    }, 3000);
                } else {
                    const errorText = await response.text();

                    messageBox.textContent = "Erreur : " + errorText;
                    messageBox.classList.add('bg-red-100', 'border-red-400', 'text-red-700');
                    messageBox.classList.remove('hidden');
                }
            } catch (error) {
                messageBox.textContent = "Impossible de joindre le serveur.";
                messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold bg-red-100 border-red-400 text-red-700";
                messageBox.classList.remove('hidden');
            }
        });

        document.addEventListener('DOMContentLoaded', async () => {
            const selectCategorie = document.getElementById('id_categorie');
            
            try {
                const res = await fetch('http://localhost:8082/categorie/read');
                if (res.ok) {
                    const jsonResponse = await res.json();
                    
                    const categories = Array.isArray(jsonResponse) ? jsonResponse : (jsonResponse.data || []);
                    
                    selectCategorie.innerHTML = '<option value="" disabled selected>Sélectionnez une catégorie</option>';
                    
                    if (categories.length > 0) {
                        categories.forEach(cat => {
                            const option = document.createElement('option');
                            option.value = cat.id_categorie || cat.id || cat.ID; 
                            option.textContent = cat.nom || cat.Nom;
                            selectCategorie.appendChild(option);
                        });
                    } else {
                        selectCategorie.innerHTML = '<option value="" disabled>Aucune catégorie disponible</option>';
                    }
                } else {
                    selectCategorie.innerHTML = '<option value="" disabled>Erreur de chargement</option>';
                }
            } catch (err) {
                console.error("Erreur serveur:", err);
                selectCategorie.innerHTML = '<option value="" disabled>Serveur injoignable</option>';
            }
        });
    </script>

</body>
</html>