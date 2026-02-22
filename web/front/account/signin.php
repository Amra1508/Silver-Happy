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
                <a href="/back/dashboard.php" class="sm:col-span-6 justify-self-center">
                    <button class=" px-14 rounded-md button-blue">Je me connecte</button>
                </a>
            </div>
        </div>
    </main>

    <?php include("../includes/footer.php") ?>

</body>