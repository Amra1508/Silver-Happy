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

<body>

    <?php include("../includes/header.php") ?>

    <main>
        <div class="px-16 pt-10 pb-16">

            <h2 class="text-3xl text-center mb-10 font-semibold text-[#1C5B8F]">Devenir Prestataire :</h2>

            <div id="response_message" class="hidden max-w-4xl mx-auto mb-6 p-4 rounded-lg border"></div>

            <div class="border border-[#1C5B8F] rounded-[2.5rem] py-10 px-10 grid gap-x-6 gap-y-8 sm:grid-cols-6">
                
                <div class="sm:col-span-3">
                    <label class="small-text">Prénom</label>
                    <div class="mt-2">
                        <input id="first_name" type="text" class="form-input w-full border rounded-md p-2" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Nom</label>
                    <div class="mt-2">
                        <input id="last_name" type="text" class="form-input w-full border rounded-md p-2" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Date de naissance</label>
                    <div class="mt-2">
                        <input id="birth_date" type="date" class="form-input w-full border rounded-md p-2" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Numéro de téléphone</label>
                    <div class="mt-2">
                        <input id="phone_number" type="tel" class="form-input w-full border rounded-md p-2" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Adresse mail pro</label>
                    <div class="mt-2">
                        <input id="signup_email" type="email" class="form-input w-full border rounded-md p-2" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Mot de passe</label>
                    <div class="mt-2">
                        <input id="signup_password" type="password" class="form-input w-full border rounded-md p-2" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Numéro SIRET (14 chiffres)</label>
                    <div class="mt-2">
                        <input id="siret" type="text" maxlength="14" class="form-input w-full border rounded-md p-2" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Catégorie de prestation</label>
                    <div class="mt-2">
                        <select id="id_categorie" class="form-input w-full border rounded-md p-2 bg-white" required>
                            <option value="" disabled selected>Chargement des catégories...</option>
                        </select>
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Tarif horaire/forfait (€)</label>
                    <div class="mt-2">
                        <input id="tarif" type="number" step="0.01" min="0" placeholder="Ex: 25.50" class="form-input w-full border rounded-md p-2" required />
                    </div>
                </div>

                <div class="sm:col-span-6 justify-self-center mt-4">
                    <div class="cf-turnstile" data-sitekey="0x4AAAAAACpHSvX9fEtYZZTy" data-theme="light"></div>
                </div>

                <button id="btn_register" class="sm:col-span-6 justify-self-center px-14 py-3 bg-[#1C5B8F] text-white rounded-md hover:bg-blue-800 transition-colors">Envoyer ma demande</button>
            </div>
        </div>

        <div class="mt-8 text-center">
            <span class="text-gray-600">Vous êtes un senior cherchant de l'aide ?</span>
            <a href="/front/users/signup.php" class="text-[#1C5B8F] font-semibold hover:underline ml-1 transition-all">
                Je m'inscris ici
            </a>
        </div>

    </main>
    <?php include("../includes/footer.php") ?>

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
                messageBox.textContent = "Veuillez remplir tous les champs.";
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