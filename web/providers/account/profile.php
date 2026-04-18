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
                extend: { fontFamily: { sans: ['Alata', 'sans-serif'] } }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="flex min-h-screen">
        <?php include("../includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto">
            <main class="p-8">
                <div class="max-w-4xl mx-auto space-y-8">
                    
                    <div>
                        <h1 class="text-3xl font-semibold text-[#1C5B8F]">Mon Profil</h1>
                        <p class="text-gray-500 mt-1">Consultez et modifiez toutes vos informations.</p>
                    </div>

                    <div id="alert-box" class="hidden p-4 rounded-xl font-semibold text-sm"></div>

                    <form id="form-profil" class="space-y-6">
                        
                        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                            <h2 class="text-xl font-bold text-[#1C5B8F] mb-6 flex items-center gap-2">
                                Informations Personnelles
                            </h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-sm text-gray-600 font-semibold block mb-2">Prénom</label>
                                    <input type="text" id="prenom" class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600 font-semibold block mb-2">Nom</label>
                                    <input type="text" id="nom" class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600 font-semibold block mb-2">Adresse E-mail</label>
                                    <input type="email" id="email" class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600 font-semibold block mb-2">Téléphone</label>
                                    <input type="tel" id="telephone" class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-sm text-gray-600 font-semibold block mb-2">Nouveau mot de passe</label>
                                    <input type="password" id="mdp" placeholder="Laissez vide pour conserver votre mot de passe actuel" class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]">
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                            <h2 class="text-xl font-bold text-[#1C5B8F] mb-6 flex items-center gap-2">
                                Gestion de l'abonnement & Visibilité
                            </h2>
                            
                            <div class="flex flex-col md:flex-row md:items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600 font-semibold">Abonnement Pro</p>
                                    <div id="subscription-status" class="mt-1 flex items-center gap-2">
                                        <span class="inline-block w-3 h-3 rounded-full bg-gray-300 animate-pulse"></span>
                                        <span class="text-gray-500 italic">Vérification...</span>
                                    </div>
                                </div>
                                <button type="button" id="btn-cancel-sub" class="hidden text-sm font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-4 py-2 rounded-xl transition-all border border-red-200">
                                    Résilier l'abonnement
                                </button>
                            </div>
                            <p id="sub-info-text" class="text-xs text-gray-400 mt-2 mb-6"></p>

                            <div class="flex flex-col md:flex-row md:items-center justify-between p-4 bg-yellow-50/50 rounded-2xl border border-yellow-100 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600 font-semibold flex items-center gap-2">
                                        Visibilité Boostée
                                    </p>
                                    <div id="boost-status" class="mt-1 flex items-center gap-2">
                                        <span class="inline-block w-3 h-3 rounded-full bg-gray-300 animate-pulse"></span>
                                        <span class="text-gray-500 italic">Vérification...</span>
                                    </div>
                                </div>
                                <button type="button" id="btn-buy-boost" onclick="acheterBoost('compte')" class="hidden text-sm font-bold text-[#1C5B8F] hover:text-blue-800 bg-blue-100 hover:bg-blue-200 px-4 py-2 rounded-xl transition-all border border-blue-300 shadow-sm">
                                    ⭐ Booster mon profil (10€)
                                </button>
                            </div>
                            <p id="boost-info-text" class="text-xs text-gray-400 mt-2"></p>
                        </div>

                        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                            <h2 class="text-xl font-bold text-[#1C5B8F] mb-6 flex items-center gap-2">
                                Activité Professionnelle
                            </h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-sm text-gray-600 font-semibold block mb-2">Numéro SIRET</label>
                                    <input type="text" id="siret" maxlength="14" class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600 font-semibold block mb-2">Catégorie</label>
                                    <select id="id_categorie" class="w-full border border-gray-300 rounded-xl p-3 bg-white focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required>
                                        <option value="" disabled selected>Chargement...</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-sm text-gray-600 font-semibold block mb-2">Tarif de base (€/h)</label>
                                    <div class="relative w-full">
                                        <input type="number" id="tarifs" step="0.01" min="0" class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required>
                                        <span class="absolute right-4 top-3 text-gray-400 font-bold">€</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                            <h2 class="text-xl font-bold text-[#635BFF] mb-6 flex items-center gap-2">
                                Paiements & Versements (Stripe)
                            </h2>
                            <div class="flex flex-col md:flex-row md:items-center justify-between p-6 bg-[#635BFF]/5 rounded-2xl border border-[#635BFF]/20 gap-4">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-700 font-semibold mb-1">Compte bancaire / Versements</p>
                                    <p id="stripe-status-text" class="text-xs text-gray-500">
                                        Vérification de votre compte Stripe en cours...
                                    </p>
                                </div>
                                <button type="button" id="btn-stripe-connect" onclick="connectStripe()" class="hidden bg-[#635BFF] hover:bg-[#5249ea] text-white font-bold py-2.5 px-6 rounded-xl shadow-md transition-all whitespace-nowrap">
                                    Lier mon compte bancaire
                                </button>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" id="btn-save" class="bg-[#E1AB2B] hover:bg-yellow-500 text-[#1C5B8F] font-bold py-3 px-8 rounded-xl shadow-md transition-all flex items-center gap-2">
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>

                </div>
            </main>
        </div>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('stripe') === 'success') {
            alert("Votre compte Stripe a été lié avec succès ! Les versements sont maintenant activés.");
            window.history.replaceState({}, document.title, window.location.pathname);
        } else if (urlParams.get('stripe') === 'error') {
            alert("L'association du compte Stripe n'a pas pu aboutir. Veuillez réessayer.");
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        async function connectStripe() {
            const providerId = window.currentUserId; 
            if (!providerId) return alert("Vous devez être connecté.");

            const btn = document.getElementById('btn-stripe-connect');
            btn.innerHTML = "Redirection...";
            btn.disabled = true;

            try {
                const res = await fetch(`${window.API_BASE_URL}/prestataire/stripe-connect`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ provider_id: parseInt(providerId) })
                });

                if (res.ok) {
                    const result = await res.json();
                    window.location.href = result.url; 
                } else {
                    const errorText = await res.text();
                    console.error("Erreur renvoyée par le serveur :", errorText);
                    alert("Erreur Serveur : \n" + errorText);
                    
                    btn.innerHTML = "Lier mon compte bancaire";
                    btn.disabled = false;
                }
            } catch (err) {
                console.error(err);
                alert("Serveur inaccessible.");
                btn.innerHTML = "Lier mon compte bancaire";
                btn.disabled = false;
            }
        }

        async function acheterBoost(typeBoost, targetId = 0) {
            const providerId = window.currentUserId; 
            if (!providerId) return alert("Vous devez être connecté.");

            const data = {
                provider_id: parseInt(providerId),
                type_boost: typeBoost,
                target_id: parseInt(targetId)
            };

            try {
                const apiBase = window.API_BASE_URL;
                const response = await fetch(`${apiBase}/paiement-boost`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    const result = await response.json();
                    window.location.href = result.url;
                } else {
                    alert("Erreur lors de l'initialisation du paiement du boost.");
                }
            } catch (err) {
                console.error(err);
                alert("Serveur inaccessible.");
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            let providerData = null;
            const selectCategorie = document.getElementById('id_categorie');
            const alertBox = document.getElementById('alert-box');
            const subStatusDiv = document.getElementById('subscription-status');
            const btnCancelSub = document.getElementById('btn-cancel-sub');
            const subInfoText = document.getElementById('sub-info-text');

            const boostStatusDiv = document.getElementById('boost-status');
            const btnBuyBoost = document.getElementById('btn-buy-boost');
            const boostInfoText = document.getElementById('boost-info-text');

            const btnStripeConnect = document.getElementById('btn-stripe-connect');
            const stripeStatusText = document.getElementById('stripe-status-text');

            const getLastDayOfMonth = () => {
                const now = new Date();
                const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                return lastDay.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
            };

            const updateSubscriptionUI = (isSubscribed) => {
                if (isSubscribed) {
                    subStatusDiv.innerHTML = `
                        <span class="w-3 h-3 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></span>
                        <span class="font-bold text-green-700">Abonnement Actif (Premium)</span>
                    `;
                    btnCancelSub.classList.remove('hidden');
                    subInfoText.textContent = "Profitez de votre accès Premium complet.";
                } else {
                    subStatusDiv.innerHTML = `
                        <span class="w-3 h-3 rounded-full bg-gray-400"></span>
                        <span class="font-bold text-gray-600">Aucun abonnement actif</span>
                    `;
                    btnCancelSub.classList.add('hidden');
                    subInfoText.textContent = "Abonnez-vous pour profiter d'outils exclusifs.";
                }
            };

            const updateBoostUI = (dateFinBoost) => {
                if (dateFinBoost && new Date(dateFinBoost) > new Date()) {
                    const d = new Date(dateFinBoost);
                    const dateStr = d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute:'2-digit' });
                    
                    boostStatusDiv.innerHTML = `
                        <span class="w-3 h-3 rounded-full bg-[#E1AB2B] shadow-[0_0_8px_rgba(225,171,43,0.6)]"></span>
                        <span class="font-bold text-yellow-700">Boost Actif</span>
                    `;
                    btnBuyBoost.classList.add('hidden');
                    boostInfoText.textContent = `Votre profil est mis en avant jusqu'au ${dateStr}.`;
                } else {
                    boostStatusDiv.innerHTML = `
                        <span class="w-3 h-3 rounded-full bg-gray-400"></span>
                        <span class="font-bold text-gray-600">Non boosté</span>
                    `;
                    btnBuyBoost.classList.remove('hidden');
                    boostInfoText.textContent = "Améliorez votre visibilité et remontez dans les recherches de nos Seniors.";
                }
            };

            try {
                const catRes = await fetch(`${window.API_BASE_URL}/categorie/read`);
                if (catRes.ok) {
                    const jsonResponse = await catRes.json();
                    const categories = Array.isArray(jsonResponse) ? jsonResponse : (jsonResponse.data || []);
                    selectCategorie.innerHTML = '<option value="" disabled>Sélectionnez une catégorie</option>';
                    categories.forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.id_categorie || cat.id || cat.ID; 
                        option.textContent = cat.nom || cat.Nom;
                        selectCategorie.appendChild(option);
                    });
                }

                const res = await fetch(`${window.API_BASE_URL}/auth/me-provider`, {
                    method: 'GET',
                    credentials: 'include'
                });

                if (res.ok) {
                    providerData = await res.json();
                    window.currentUserId = providerData.id_prestataire || providerData.id || providerData.ID;

                    document.getElementById('prenom').value = providerData.prenom || '';
                    document.getElementById('nom').value = providerData.nom || '';
                    document.getElementById('email').value = providerData.email || '';
                    document.getElementById('telephone').value = providerData.num_telephone || '';
                    document.getElementById('siret').value = providerData.siret || '';
                    document.getElementById('tarifs').value = providerData.tarifs || 0;
                    
                    if(providerData.id_categorie || providerData.IdCategorie) {
                        selectCategorie.value = providerData.id_categorie || providerData.IdCategorie;
                    }

                    const stripeId = providerData.stripe_account_id;
                    btnStripeConnect.classList.remove('hidden');

                    if (stripeId) {
                        stripeStatusText.innerHTML = `Compte Stripe connecté (ID: <span class="font-mono text-[10px] text-gray-400">${stripeId}</span>)<br/>Vous êtes prêt à recevoir vos versements automatiques.`;
                        btnStripeConnect.innerHTML = "Mettre à jour mes infos bancaires";
                        btnStripeConnect.classList.replace('bg-[#635BFF]', 'bg-white');
                        btnStripeConnect.classList.replace('text-white', 'text-[#635BFF]');
                        btnStripeConnect.classList.add('border', 'border-[#635BFF]');
                    } else {
                        stripeStatusText.innerHTML = "Vous devez lier un compte bancaire pour recevoir l'argent de vos services et événements.";
                        btnStripeConnect.innerHTML = "Lier mon compte bancaire";
                    }

                    const isSub = providerData.id_abonnement != 0;
                    updateSubscriptionUI(isSub);

                    const boostDate = providerData.date_fin_boost || providerData.DateFinBoost;
                    updateBoostUI(boostDate);

                } else {
                    window.location.href = "/front/providers/account/signin.php";
                }
            } catch (err) {
                console.error("Erreur initialisation :", err);
            }

            btnCancelSub.addEventListener('click', async () => {
                const dateFin = getLastDayOfMonth();
                if (confirm(`Confirmez-vous la résiliation ? Votre accès Premium restera actif jusqu'au ${dateFin}.`)) {
                    try {
                        const res = await fetch(`${window.API_BASE_URL}/cancel-subscription-prestataire`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            credentials: 'include',
                            body: JSON.stringify({ id_prestataire: providerData.id_prestataire || providerData.id || providerData.ID })
                        });

                        if (res.ok) {
                            alert(`Résiliation enregistrée. Fin d'abonnement : ${dateFin}.`);
                            providerData.id_abonnement = 0;
                            updateSubscriptionUI(false);
                        } else {
                            alert("Erreur lors de la résiliation.");
                        }
                    } catch (error) {
                        console.error("Erreur API résiliation :", error);
                    }
                }
            });

            const form = document.getElementById('form-profil');
            const btnSave = document.getElementById('btn-save');

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                btnSave.disabled = true;
                btnSave.innerHTML = "Sauvegarde...";

                const pwd = document.getElementById('mdp').value.trim();
                const updatedData = {
                    ...providerData,
                    prenom: document.getElementById('prenom').value.trim(),
                    nom: document.getElementById('nom').value.trim(),
                    email: document.getElementById('email').value.trim(),
                    num_telephone: document.getElementById('telephone').value.trim(),
                    siret: document.getElementById('siret').value.trim(),
                    tarifs: parseFloat(document.getElementById('tarifs').value),
                    id_categorie: parseInt(selectCategorie.value)
                };

                if (pwd !== "") updatedData.mdp = pwd;

                try {
                    const updateRes = await fetch(`${window.API_BASE_URL}/auth/update-provider`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        credentials: 'include',
                        body: JSON.stringify(updatedData)
                    });

                    if (updateRes.ok) {
                        alertBox.textContent = "Profil mis à jour avec succès !";
                        alertBox.className = "p-4 rounded-xl font-bold text-green-700 bg-green-100 border border-green-400";
                        alertBox.classList.remove('hidden');
                        document.getElementById('mdp').value = "";
                        providerData = updatedData;
                    } else {
                        const errorMsg = await updateRes.text();
                        alertBox.textContent = "Erreur : " + errorMsg;
                        alertBox.className = "p-4 rounded-xl font-bold text-red-700 bg-red-100 border border-red-400";
                        alertBox.classList.remove('hidden');
                    }
                } catch (err) {
                    alertBox.textContent = "Erreur de connexion au serveur.";
                    alertBox.classList.remove('hidden');
                } finally {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    btnSave.disabled = false;
                    btnSave.innerHTML = "Enregistrer les modifications";
                }
            });
        });
    </script>
</body>
</html>