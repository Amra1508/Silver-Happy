<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Silver Happy</title>

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
            
            <h2 class="text-3xl text-center mb-10 font-semibold text-[#1C5B8F]">Mes informations personnelles :</h2>

            <div id="response_message" class="hidden max-w-4xl mx-auto mb-6 p-4 rounded-lg border"></div>

            <div class="border border-[#1C5B8F] rounded-[2.5rem] py-10 px-10 grid gap-x-6 gap-y-8 sm:grid-cols-6">
                <div class="sm:col-span-3">
                    <label class="small-text">Prénom</label>
                    <div class="mt-2">
                        <input id="first_name" type="text" class="form-input w-full border rounded-md px-3 py-2" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Nom</label>
                    <div class="mt-2">
                        <input id="last_name" type="text" class="form-input w-full border rounded-md px-3 py-2" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Date de naissance</label>
                    <div class="mt-2">
                        <input id="birth_date" type="date" class="form-input w-full border rounded-md px-3 py-2" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Numéro de téléphone</label>
                    <div class="mt-2">
                        <input id="phone_number" type="tel" class="form-input w-full border rounded-md px-3 py-2" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Adresse mail</label>
                    <div class="mt-2">
                        <input id="profile_email" type="email" class="form-input w-full border rounded-md px-3 py-2" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text font-bold text-gray-500">Nouveau mot de passe (optionnel)</label>
                    <div class="mt-2">
                        <input id="profile_password" type="password" placeholder="Laisser vide pour ne pas changer" class="form-input w-full border rounded-md px-3 py-2" />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Pays</label>
                    <div class="mt-2">
                        <input id="country" type="text" class="form-input w-full border rounded-md px-3 py-2" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Adresse</label>
                    <div class="mt-2">
                        <input id="address" type="text" class="form-input w-full border rounded-md px-3 py-2" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Ville</label>
                    <div class="mt-2">
                        <input id="city" type="text" class="form-input w-full border rounded-md px-3 py-2" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="small-text">Code postal</label>
                    <div class="mt-2">
                        <input id="zip_code" type="text" class="form-input w-full border rounded-md px-3 py-2" required />
                    </div>
                </div>
                
                <button id="btn_update" class="sm:col-span-6 justify-self-center px-14 py-3 rounded-full bg-[#1C5B8F] text-white font-bold hover:bg-[#154670] transition-colors">
                    Mettre à jour mes informations
                </button>
            </div>
        </div>
    </main>

    <?php include("../includes/footer.php") ?>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const messageBox = document.getElementById('response_message');

            try {
                const response = await fetch('http://localhost:8082/auth/me', {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include'
                });

                if (response.ok) {
                    const user = await response.json();
                    
                    document.getElementById('first_name').value = user.prenom || '';
                    document.getElementById('last_name').value = user.nom || '';
                    document.getElementById('phone_number').value = user.num_telephone || '';
                    document.getElementById('profile_email').value = user.email || '';
                    document.getElementById('country').value = user.pays || '';
                    document.getElementById('address').value = user.adresse || '';
                    document.getElementById('city').value = user.ville || '';
                    document.getElementById('zip_code').value = user.code_postal || '';

                    if (user.date_naissance) {
                        document.getElementById('birth_date').value = user.date_naissance.split('T')[0];
                    }
                } else {
                    window.location.href = "/front/account/signin.php";
                }
            } catch (error) {
                console.error("Erreur de récupération des données :", error);
            }

            const btnSubmit = document.getElementById('btn_update');

            btnSubmit.addEventListener('click', async (e) => {
                e.preventDefault();

                const data = {
                    prenom: document.getElementById('first_name').value,
                    nom: document.getElementById('last_name').value,
                    date_naissance: document.getElementById('birth_date').value,
                    num_telephone: document.getElementById('phone_number').value,
                    email: document.getElementById('profile_email').value,
                    pays: document.getElementById('country').value,
                    adresse: document.getElementById('address').value,
                    ville: document.getElementById('city').value,
                    code_postal: document.getElementById('zip_code').value
                };

                const passwordInput = document.getElementById('profile_password').value;
                if (passwordInput.trim() !== '') {
                    data.mdp = passwordInput;
                }

                if (!data.prenom || !data.nom || !data.email) {
                    messageBox.textContent = "Veuillez remplir au moins votre nom, prénom et email.";
                    messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold bg-red-100 border-red-400 text-red-700 block";
                    return;
                }

                try {
                    const response = await fetch('http://localhost:8082/auth/update', { 
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        credentials: 'include',
                        body: JSON.stringify(data)
                    });

                    if (response.ok) {
                        messageBox.textContent = "Vos informations ont bien été mises à jour !";
                        messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold bg-green-100 border-green-400 text-green-700 block";
                        
                        document.getElementById('profile_password').value = '';
                    } else {
                        const errorText = await response.text();
                        messageBox.textContent = "Erreur : " + errorText;
                        messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold bg-red-100 border-red-400 text-red-700 block";
                    }
                } catch (error) {
                    messageBox.textContent = "Impossible de joindre le serveur.";
                    messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold bg-red-100 border-red-400 text-red-700 block";
                }
            });
        });
    </script>
</body>
</html>