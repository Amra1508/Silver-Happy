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

            if (!data.email || !data.mdp || !data.prenom || !data.nom) {
                alert("Veuillez au moins remplir les champs principaux (Prénom, Nom, Email, Mot de passe).");
                return;
            }

            try {
                const response = await fetch('http://localhost:8082/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    const result = await response.json();
                    alert("Inscription réussie ! Bienvenue " + result.prenom);
                    window.location.href = "signin.php";
                } else {
                    const errorText = await response.text();
                    alert("Erreur lors de l'inscription : " + errorText);
                }
            } catch (error) {
                console.error("Erreur Fetch :", error);
                alert("Impossible de joindre le serveur.");
            }
        });
    </script>

</body>