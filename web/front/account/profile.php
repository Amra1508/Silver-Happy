<?php
if (!isset($_COOKIE['session_token'])) {
    header("Location: /front/account/signin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>

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

<body class="bg-gray-50">

    <?php include("../includes/header.php") ?>

    <main>
        <div class="px-16 pt-10 pb-16">

            <h2 class="text-3xl text-center mb-10 font-semibold text-[#1C5B8F] flex items-center justify-center gap-4">
                Mes informations personnelles
                <span id="auth_status_badge" class="text-sm px-4 py-1 bg-gray-200 text-gray-500 rounded-full border font-bold transition-colors">
                    Vérification...
                </span>
            </h2>

            <div id="response_message" class="hidden max-w-4xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold shadow-sm"></div>

            <div class="border border-[#1C5B8F] rounded-[2.5rem] py-10 px-10 grid gap-x-6 gap-y-8 sm:grid-cols-6 bg-white shadow-sm max-w-5xl mx-auto">
                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-bold">Prénom</label>
                    <div class="mt-2">
                        <input id="first_name" type="text" class="form-input w-full border border-gray-300 focus:border-[#1C5B8F] rounded-md px-3 py-2 outline-none" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-bold">Nom</label>
                    <div class="mt-2">
                        <input id="last_name" type="text" class="form-input w-full border border-gray-300 focus:border-[#1C5B8F] rounded-md px-3 py-2 outline-none" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-bold">Date de naissance</label>
                    <div class="mt-2">
                        <input id="birth_date" type="date" class="form-input w-full border border-gray-300 focus:border-[#1C5B8F] rounded-md px-3 py-2 outline-none" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-bold">Numéro de téléphone</label>
                    <div class="mt-2">
                        <input id="phone_number" type="tel" class="form-input w-full border border-gray-300 focus:border-[#1C5B8F] rounded-md px-3 py-2 outline-none" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-bold">Adresse mail</label>
                    <div class="mt-2">
                        <input id="profile_email" type="email" class="form-input w-full border border-gray-300 focus:border-[#1C5B8F] rounded-md px-3 py-2 outline-none" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-bold">Nouveau mot de passe (optionnel)</label>
                    <div class="mt-2">
                        <input id="profile_password" type="password" placeholder="Laisser vide pour ne pas changer" class="form-input w-full border border-gray-300 focus:border-[#1C5B8F] rounded-md px-3 py-2 outline-none" />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-bold">Pays</label>
                    <div class="mt-2">
                        <input id="country" type="text" class="form-input w-full border border-gray-300 focus:border-[#1C5B8F] rounded-md px-3 py-2 outline-none" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-bold">Adresse</label>
                    <div class="mt-2 relative">
                        <input id="address" type="text" class="form-input w-full border border-gray-300 focus:border-[#1C5B8F] rounded-md px-3 py-2 outline-none" required />
                        <ul id="address_suggestions" class="absolute w-full bg-white rounded-md shadow-lg max-h-60 overflow-y-auto mt-1"></ul>
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-bold">Ville</label>
                    <div class="mt-2">
                        <input id="city" type="text" class="form-input w-full border border-gray-300 focus:border-[#1C5B8F] rounded-md px-3 py-2 outline-none" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-bold">Code postal</label>
                    <div class="mt-2">
                        <input id="zip_code" type="text" class="form-input w-full border border-gray-300 focus:border-[#1C5B8F] rounded-md px-3 py-2 outline-none" required />
                    </div>
                </div>

                <div class="sm:col-span-6 flex flex-col sm:flex-row justify-center gap-4 mt-4">
                    <a href="/front/account/planification.php" class="text-center px-10 py-3 rounded-full border-2 border-[#1C5B8F] text-[#1C5B8F] font-bold hover:bg-[#1C5B8F] hover:text-white transition-colors shadow-sm">
                        Consulter mon planning
                    </a>
                    <a href="/front/account/invoice.php" class="text-center px-10 py-3 rounded-full border-2 border-[#1C5B8F] text-[#1C5B8F] font-bold hover:bg-[#1C5B8F] hover:text-white transition-colors shadow-sm">
                        Consulter mes factures
                    </a>
                    <button id="btn_update" class="px-10 py-3 rounded-full bg-[#1C5B8F] text-white font-bold hover:bg-[#154670] transition-colors shadow-md">
                        Mettre à jour mes informations
                    </button>
                </div>

                <div class="sm:col-span-6 flex justify-center mt-2">
                    <button type="button" id="btn_delete_account_trigger" class="text-red-500 hover:text-red-700 underline text-sm font-bold transition-colors">
                        Supprimer définitivement mon compte
                    </button>
                </div>

            </div>
        </div>
    </main>

    <div id="delete_modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full mx-4 border border-red-100">
            <h3 class="text-2xl font-bold text-red-600 mb-4 text-center">Supprimer le compte ?</h3>
            <p class="text-gray-700 mb-6 text-center">
                Êtes-vous sûr de vouloir supprimer définitivement votre compte ? <br><br>
                <span class="font-bold">Cette action est irréversible</span> et toutes vos données seront perdues.
            </p>
            <div class="flex flex-col-reverse sm:flex-row justify-center gap-4">
                <button id="btn_cancel_delete" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-full font-bold hover:bg-gray-300 transition-colors">
                    Annuler
                </button>
                <button id="btn_confirm_delete" class="px-6 py-2 bg-red-600 text-white rounded-full font-bold hover:bg-red-700 transition-colors shadow-md">
                    Oui, supprimer
                </button>
            </div>
        </div>
    </div>

    <?php include("../includes/footer.php") ?>

    <script>
        let currentUserId = null;

        document.addEventListener('DOMContentLoaded', async () => {
            const messageBox = document.getElementById('response_message');
            const authBadge = document.getElementById('auth_status_badge');

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) {
                const type = urlParams.get('success');
                messageBox.classList.remove('hidden');
                messageBox.classList.add('bg-green-100', 'border-green-400', 'text-green-700', 'block');

                if (type === 'abonnement_valide') {
                    messageBox.textContent = "Félicitations ! Votre abonnement a bien été activé.";
                } else if (type === 'resiliation_validee') {
                    messageBox.textContent = "Votre abonnement a bien été résilié.";
                } else {
                    messageBox.textContent = "Opération réussie !";
                }

                window.history.replaceState({}, document.title, window.location.pathname);
            } else if (urlParams.has('error')) {
                const type = urlParams.get('error');
                messageBox.classList.remove('hidden');
                messageBox.classList.add('bg-red-100', 'border-red-400', 'text-red-700', 'block');

                if (type === 'paiement_echoue') {
                    messageBox.textContent = "Le paiement a échoué ou a été annulé. Vous n'avez pas été débité.";
                } else {
                    messageBox.textContent = "Une erreur est survenue.";
                }

                window.history.replaceState({}, document.title, window.location.pathname);
            }

            try {
                const response = await fetch('http://localhost:8082/auth/me', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'include'
                });

                if (response.ok) {
                    const user = await response.json();
                    
                    currentUserId = user.id_utilisateur || user.id;

                    if (user.id_abonnement && user.id_abonnement !== null && user.id_abonnement !== 0) {
                        let isExpired = false;
                        let dateFinFormatee = "";

                        if (user.debut_abonnement) {
                            const debutDate = new Date(user.debut_abonnement);

                            if (user.type_paiement === 'mensuel') {
                                debutDate.setMonth(debutDate.getMonth() + 1);
                            } else if (user.type_paiement === 'annuel') {
                                debutDate.setFullYear(debutDate.getFullYear() + 1);
                            }

                            const aujourdHui = new Date();
                            if (debutDate < aujourdHui) {
                                isExpired = true;
                            }

                            const options = { year: 'numeric', month: 'long', day: 'numeric' };
                            dateFinFormatee = debutDate.toLocaleDateString('fr-FR', options);
                        }

                        if (isExpired) {
                            authBadge.innerHTML = "Abonnement expiré <span class='ml-1 text-[#1C5B8F] underline text-xs'>Renouveler</span>";
                            authBadge.className = "cursor-pointer text-sm px-4 py-1 bg-red-100 border border-red-300 text-red-600 rounded-full font-bold shadow-sm hover:bg-red-200 transition-colors";
                            authBadge.onclick = () => window.location.href = "/front/services/subscription.php";
                        } else {
                            authBadge.innerHTML = `
                                Abonné(e) jusqu'au ${dateFinFormatee} 
                                <span onclick="cancelSubscription()" class="ml-3 text-red-500 hover:text-red-700 underline text-xs cursor-pointer transition-colors">
                                    Résilier
                                </span>
                            `;
                            authBadge.className = "text-sm pl-4 pr-3 py-1 bg-[#E1AB2B]/20 border border-[#E1AB2B] text-yellow-700 rounded-full font-bold shadow-sm flex items-center";
                        }
                    } else {
                        authBadge.innerHTML = "Non abonné(e) <span class='ml-1 text-[#1C5B8F] underline text-xs'>S'abonner</span>";
                        authBadge.className = "cursor-pointer text-sm px-4 py-1 bg-gray-100 border border-gray-300 text-gray-500 rounded-full font-bold shadow-sm hover:bg-gray-200 transition-colors";
                        authBadge.onclick = () => window.location.href = "/front/services/subscription.php";
                    }

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
                authBadge.textContent = "Erreur serveur";
                authBadge.className = "text-sm px-4 py-1 bg-red-100 border border-red-400 text-red-700 rounded-full font-bold shadow-sm";
            }

            const btnSubmit = document.getElementById('btn_update');
            btnSubmit.addEventListener('click', async (e) => {
                e.preventDefault();
                messageBox.className = "hidden max-w-4xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold shadow-sm";

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
                    messageBox.classList.add('bg-red-100', 'border-red-400', 'text-red-700', 'block');
                    messageBox.classList.remove('hidden');
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
                        messageBox.classList.add('bg-green-100', 'border-green-400', 'text-green-700', 'block');
                        messageBox.classList.remove('hidden');
                        document.getElementById('profile_password').value = '';
                    } else {
                        const errorText = await response.text();
                        messageBox.textContent = "Erreur : " + errorText;
                        messageBox.classList.add('bg-red-100', 'border-red-400', 'text-red-700', 'block');
                        messageBox.classList.remove('hidden');
                    }
                } catch (error) {
                    messageBox.textContent = "Impossible de joindre le serveur.";
                    messageBox.classList.add('bg-red-100', 'border-red-400', 'text-red-700', 'block');
                    messageBox.classList.remove('hidden');
                }
            });
            
            const btnDeleteTrigger = document.getElementById('btn_delete_account_trigger');
            const btnCancelDelete = document.getElementById('btn_cancel_delete');
            const btnConfirmDelete = document.getElementById('btn_confirm_delete');
            const deleteModal = document.getElementById('delete_modal');

            btnDeleteTrigger.addEventListener('click', () => {
                deleteModal.classList.remove('hidden');
            });

            btnCancelDelete.addEventListener('click', () => {
                deleteModal.classList.add('hidden');
            });

            btnConfirmDelete.addEventListener('click', async () => {
                if (!currentUserId) {
                    alert("Erreur : Impossible d'identifier l'utilisateur pour la suppression.");
                    return;
                }

                try {
                    const deleteRes = await fetch(`http://localhost:8082/seniors/delete/${currentUserId}`, {
                        method: 'DELETE', 
                        credentials: 'include'
                    });

                    if (!deleteRes.ok) {
                        const errText = await deleteRes.text();
                        alert("Erreur lors de la suppression : " + errText);
                        deleteModal.classList.add('hidden');
                        return;
                    }

                    await fetch('http://localhost:8082/auth/logout', {
                        method: 'POST', 
                        credentials: 'include'
                    });

                    window.location.href = "/front/account/signin.php";

                } catch (error) {
                    console.error("Erreur de suppression :", error);
                    alert("Impossible de joindre le serveur pour la suppression.");
                    deleteModal.classList.add('hidden');
                }
            });
        });

        window.cancelSubscription = async function() {
            if (!confirm("Êtes-vous sûr de vouloir annuler le renouvellement automatique de votre abonnement ?")) {
                return;
            }

            try {
                const responseMe = await fetch('http://localhost:8082/auth/me', { credentials: 'include' });
                const user = await responseMe.json();

                const response = await fetch('http://localhost:8082/abonnement/cancel', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_utilisateur: user.id_utilisateur || user.id })
                });

                if (response.ok) {
                    window.location.href = "/front/account/profile.php?success=resiliation_validee";
                } else {
                    const errText = await response.text();
                    alert("Erreur : " + errText);
                }
            } catch (err) {
                alert("Erreur de connexion au serveur.");
            }
        };


        const addressInput = document.getElementById('address');
        const resultBox = document.getElementById('address_suggestions');

            addressInput.addEventListener('input', async () => {
                const typedText = addressInput.value;

            if (typedText.length < 3) {
                resultBox.innerHTML = '';
                return;
            }

            const response = await fetch(`https://data.geopf.fr/geocodage/search?q=${typedText}&limit=5`);            
            const data = await response.json();

            resultBox.innerHTML = '';

            data.features.forEach(foundAddress => {
                
                const clickableItem = document.createElement('li'); 
                
                clickableItem.className = "px-4 py-2 cursor-pointer hover:bg-[#1C5B8F] hover:text-white border-b text-sm";
                clickableItem.textContent = foundAddress.properties.label; 
                
                clickableItem.onclick = () => {
                    addressInput.value = foundAddress.properties.name;
                    document.getElementById('city').value = foundAddress.properties.city;
                    document.getElementById('zip_code').value = foundAddress.properties.postcode;
                    document.getElementById('country').value = "France";
                    
                    resultBox.innerHTML = ''; 
                };
                
                resultBox.appendChild(clickableItem);
            });
        });

    </script>
</body>

</html>