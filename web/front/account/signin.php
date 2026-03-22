<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

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
                        <input id="email" type="email" class="form-input" required />
                    </div>
                </div>

                <div class="sm:col-span-6">
                    <label class="small-text">Mot de passe</label>
                    <div class="mt-2">
                        <input id="password" type="password" class="form-input" required />
                    </div>
                </div>

                <div class="sm:col-span-6 justify-self-center">
                    <div class="cf-turnstile" data-sitekey="0x4AAAAAACpHSvX9fEtYZZTy" data-theme="light"></div>
                </div>

                <a class="sm:col-span-6 justify-self-center">
                    <button id="btn_login" class="px-14 rounded-md button-blue">Je me connecte</button>
                </a>
            </div>
        </div>
    </main>

    <?php include("../includes/footer.php") ?>

    <script>
        document.getElementById('btn_login').addEventListener('click', async (e) => {
            e.preventDefault();

            const emailInput = document.getElementById('email').value;
            const passwordInput = document.getElementById('password').value;

            const turnstileResponse = document.querySelector('[name="cf-turnstile-response"]')?.value;

            if (!emailInput || !passwordInput || !turnstileResponse) {
                return alert("Veuillez remplir vos identifiants et valider le Captcha.");
            }

            if (!turnstileResponse) {
                messageBox.textContent = "Veuillez valider la vérification de sécurité (Captcha).";
                messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold bg-red-100 border-red-400 text-red-700";
                messageBox.classList.remove('hidden');
                return;
            }

            try {
                const response = await fetch('http://localhost:8082/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'include',
                    body: JSON.stringify({
                        email: emailInput,
                        mdp: passwordInput,
                        "cf-turnstile-response": turnstileResponse
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.statut === "admin") {
                        window.location.replace("../../back/dashboard.php");
                    } else if (result.statut === "user") {
                        const urlParams = new URLSearchParams(window.location.search);
                        const redirectUrl = urlParams.get('redirect');

                        if (redirectUrl) {
                            window.location.replace(redirectUrl);
                        } else {
                            window.location.replace("../index.php");
                        }
                    } else alert("Statut de compte non reconnu ou banni.");
                } else {
                    alert(await response.text());
                }
            } catch (error) {
                alert("Impossible de joindre le serveur de connexion.");
            }
        });
    </script>

</body>