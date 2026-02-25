<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>

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

<body>

    <?php include("../includes/header.php") ?>

    <main>
        <div class="px-16 pt-10 pb-16">
            
            <h2 class="text-3xl text-center mb-10 font-semibold text-[#1C5B8F]">Inscription :</h2>

            <div id="response_message" class="hidden max-w-4xl mx-auto mb-6 p-4 rounded-lg border"></div>

            <div class="border border-[#1C5B8F] rounded-[2.5rem] py-10 px-10 grid gap-x-6 gap-y-8 sm:grid-cols-6">
                <div class="sm:col-span-3">
                    <label class="small-text">Prénom</label>
                    <div class="mt-2">
                        <input id="first_name" type="text" class="form-input" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Nom</label>
                    <div class="mt-2">
                        <input id="last_name" type="text" class="form-input" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Date de naissance</label>
                    <div class="mt-2">
                        <input id="birth_date" type="date" class="form-input" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Numéro de téléphone</label>
                    <div class="mt-2">
                        <input id="phone_number" type="tel" class="form-input" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Adresse mail</label>
                    <div class="mt-2">
                        <input id="signup_email" type="email" class="form-input" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Mot de passe</label>
                    <div class="mt-2">
                        <input id="signup_password" type="password" class="form-input" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Pays</label>
                    <div class="mt-2">
                        <input id="country" type="text" class="form-input" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Adresse</label>
                    <div class="mt-2">
                        <input id="address" type="text" class="form-input" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Ville</label>
                    <div class="mt-2">
                        <input id="city" type="text" class="form-input" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Code postal</label>
                    <div class="mt-2">
                        <input id="zip_code" type="text" class="form-input" required />
                    </div>
                </div>
                <button id="btn_register" class="sm:col-span-6 justify-self-center px-14 rounded-md button-blue">Je m'inscris</button>
            </div>
        </div>
    </main>
    <?php include("../includes/footer.php") ?>

        <script>
            const btnSubmit = document.getElementById('btn_register');

            btnSubmit.addEventListener('click', async (e) => {
                e.preventDefault();

                const messageBox = document.getElementById('response_message');

                const data = {
                    prenom: document.getElementById('first_name').value,
                    nom: document.getElementById('last_name').value,
                    date_naissance: document.getElementById('birth_date').value,
                    num_telephone: document.getElementById('phone_number').value,
                    email: document.getElementById('signup_email').value,
                    mdp: document.getElementById('signup_password').value,
                    pays: document.getElementById('country').value,
                    adresse: document.getElementById('address').value,
                    ville: document.getElementById('city').value,
                    code_postal: document.getElementById('zip_code').value
                };

                if (!data.prenom || !data.nom || !data.date_naissance || !data.email || !data.mdp || !data.pays || !data.adresse || !data.ville || !data.code_postal) {
                    messageBox.textContent = "Veuillez remplir tous les champs.";
                    messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold bg-red-100 border-red-400 text-red-700";
                    return;
                }

                try {
                    const response = await fetch('http://localhost:8082/auth/register', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });

                    messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold";

                    if (response.ok) {
                        const result = await response.json();
                        
                        messageBox.textContent = "Inscription réussie ! Bienvenue " + (result.prenom || data.prenom);
                        messageBox.classList.add('bg-green-100', 'border-green-400', 'text-green-700');
                        messageBox.classList.remove('hidden');

                        setTimeout(() => { window.location.href = "signin.php"; }, 2500);
                    } else {
                        const errorText = await response.text();
                        
                        messageBox.textContent = "Erreur : " + errorText;
                        messageBox.classList.add('bg-red-100', 'border-red-400', 'text-red-700');
                        messageBox.classList.remove('hidden');
                    }
                } catch (error) {
                    messageBox.textContent = "Impossible de joindre le serveur.";
                    messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold bg-red-100 border-red-400 text-red-700";
                }
            });
        </script>

</body>