<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>

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

<body>

    <?php include("../includes/header.php") ?>

    <main>
        <div class="px-16 pt-10 pb-16">
            <h2 class="text-3xl text-center mb-10 font-semibold text-[#1C5B8F]">Connexion :</h2>

            <div class="border border-[#1C5B8F] rounded-[2.5rem] py-10 px-10 grid gap-x-6 gap-y-8 sm:grid-cols-6">
                <div class="sm:col-span-6">
                    <label class="small-text">Adresse mail</label>
                    <div class="mt-2">
                        <input id="signup_email" type="email" class="form-input" required />
                    </div>
                </div>

                <div class="sm:col-span-6">
                    <label class="small-text">Mot de passe</label>
                    <div class="mt-2">
                        <input id="signup_password" type="password" class="form-input" required />
                    </div>
                </div>

                <a class="sm:col-span-6 justify-self-center">
                    <button id="btn_login" class=" px-14 rounded-md button-blue">Je me connecte</button>
                </a>
            </div>
        </div>
    </main>

    <?php include("../includes/footer.php") ?>

    <script>
        const btnLogin = document.getElementById('btn_login');

        btnLogin.addEventListener('click', async (e) => {
            e.preventDefault();

            const emailInput = document.getElementById('signup_email').value;
            const passwordInput = document.getElementById('signup_password').value;

            if (!emailInput || !passwordInput) {
                alert("Veuillez remplir votre email et votre mot de passe.");
                return;
            }

            const data = {
                email: emailInput,
                mdp: passwordInput
            };

            try {
                const response = await fetch('http://localhost:8082/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'include',
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.statut === "admin") {
                        window.location.replace("../../back/dashboard.php");
                    } else if (result.statut === "user") {
                        window.location.replace("../index.php"); 
                    } else {
                        alert("Statut de compte non reconnu ou banni.");
                    }

                } else {
                    const errorText = await response.text();
                    alert(errorText); 
                }
            } catch (error) {
                console.error("Erreur de connexion :", error);
                alert("Impossible de joindre le serveur de connexion.");
            }
        });
    </script>

</body>