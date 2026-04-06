<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonnement Pro</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>

        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Alata', 'sans-serif']
                    }
                }
            }
        }

        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            if (modal) {
                modal.classList.toggle('hidden');
                modal.classList.toggle('flex');
            }
        }
    </script>
</head>

<body class="bg-gray-50 text-gray-800">

    <div class="flex min-h-screen">
        <?php include("../includes/sidebar.php") ?>

        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto relative">
            <main class="p-8 flex-1">
                    <div id="api-message" class="hidden absolute top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-xl"></div>

                    <div class="w-full px-6 pt-8">
                        <h1 class="mb-5 text-center big-text text-3xl font-bold text-[#1C5B8F]">Devenez un Prestataire Silver Happy</h1>
                        <h2 class="text-lg text-gray-600 text-center mb-10">
                            Rejoignez notre réseau de professionnels qualifiés et développez votre activité.<br>
                            Choisissez la formule qui vous correspond le mieux.
                        </h2>
                    </div>

                    <div class="flex justify-center items-center gap-4 mb-8 pb-8">
                        <span class="text-xl font-bold text-[#1C5B8F] transition-colors" id="label-mensuel">Mensuel</span>
                        <button id="toggle-billing" class="relative inline-flex h-8 w-16 items-center rounded-full bg-[#1C5B8F] transition-colors focus:outline-none shadow-inner">
                            <span id="toggle-knob" class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform translate-x-1 shadow-md"></span>
                        </button>
                        <span class="text-xl font-bold text-gray-400 transition-colors" id="label-annuel">Annuel</span>
                    </div>

                    <div class="flex flex-wrap gap-8 px-6 pb-16 justify-center pt-8">
                        <div class="md:max-w-[450px] w-full bg-white border border-[#1C5B8F] flex flex-col items-center py-10 px-8 rounded-[2.5rem] shadow-lg hover:-translate-y-2 transition-transform duration-300">
                            <h3 class="text-2xl md:text-3xl font-bold mb-6 text-[#1C5B8F] text-center">Abonnement Pro</h3>
                            <div class="flex items-baseline gap-2 mb-2">
                                <span class="text-6xl font-bold text-black" id="price-normal">3</span>
                                <span class="text-3xl font-bold text-black">€</span>
                            </div>
                            <p class="text-gray-500 mb-10 text-center" id="period-normal">/ mois</p>
                            
                            <ul class="text-gray-600 mb-10 space-y-3 w-full px-4 text-center text-lg">
                                <li>✔️ Visibilité maximale de vos prestations</li>
                                <li>✔️ Mise en avant sur notre catalogue</li>
                                <li>✔️ Messagerie directe avec les seniors</li>
                                <li>✔️ Outils de planification et gestion</li>
                                <li class="italic text-sm text-[#1C5B8F] pt-4">Renouvellement automatique, sans engagement.</li>
                                <li id="frais-initiaux" class="italic text-sm text-[#E1AB2B]">Frais de dossier initiaux : 1 €</li>
                            </ul>
                            <button onclick="openSubModal()" class="w-full rounded-full py-4 px-6 bg-[#1C5B8F] text-white font-bold text-xl mt-auto hover:bg-[#154670] transition-colors shadow-md">
                                M'abonner
                            </button>
                        </div>
                    </div>

                    <div id="sub-modal" class="hidden fixed inset-0 bg-black/60 z-[100] items-center justify-center p-4">
                        <div class="bg-white rounded-2xl w-full max-w-md p-8 shadow-2xl">
                            <h3 class="text-2xl font-semibold text-[#1C5B8F] mb-2">Récapitulatif</h3>
                            <p id="modal-summary" class="text-gray-600 mb-6 pb-4 border-b">Chargement...</p>

                            <form id="sub-form" class="space-y-6">
                                <input type="hidden" id="sub-tarif">
                                <input type="hidden" id="sub-periode">

                                <p class="text-sm text-gray-500 italic text-center">
                                    Vous allez être redirigé vers l'interface de paiement sécurisée de Stripe.
                                </p>

                                <div class="flex justify-end gap-4 mt-8 pt-4">
                                    <button type="button" onclick="toggleModal('sub-modal')" class="text-gray-500 hover:text-gray-800 font-medium">Annuler</button>
                                    <button type="submit" id="btn-stripe" class="bg-[#1C5B8F] text-white px-6 py-2 rounded-full font-bold hover:bg-[#154670] transition-colors">
                                        Payer en toute sécurité
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
            </main>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('toggle-billing');
        const toggleKnob = document.getElementById('toggle-knob');
        const labelMensuel = document.getElementById('label-mensuel');
        const labelAnnuel = document.getElementById('label-annuel');
        const priceNormal = document.getElementById('price-normal');
        const periodNormal = document.getElementById('period-normal');
        const fraisInitiaux = document.getElementById('frais-initiaux');

        let isAnnual = false;

        if(toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                isAnnual = !isAnnual;
                if (isAnnual) {
                    toggleKnob.classList.replace('translate-x-1', 'translate-x-9');
                    labelMensuel.classList.replace('text-[#1C5B8F]', 'text-gray-400');
                    labelAnnuel.classList.replace('text-gray-400', 'text-[#1C5B8F]');
                    priceNormal.textContent = '35';
                    periodNormal.textContent = '/ an';
                    fraisInitiaux.textContent = 'Frais de dossier initiaux : 5 €';
                } else {
                    toggleKnob.classList.replace('translate-x-9', 'translate-x-1');
                    labelMensuel.classList.replace('text-gray-400', 'text-[#1C5B8F]');
                    labelAnnuel.classList.replace('text-[#1C5B8F]', 'text-gray-400');
                    priceNormal.textContent = '3';
                    periodNormal.textContent = '/ mois';
                    fraisInitiaux.textContent = 'Frais de dossier initiaux : 1 €';
                }
            });
        }

        const messageBox = document.getElementById('api-message');

        function showAlert(msg, isSuccess) {
            messageBox.textContent = msg;
            messageBox.className = `p-4 rounded-lg border text-center font-bold shadow-lg ${isSuccess ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'}`;
            messageBox.classList.remove('hidden');
            setTimeout(() => messageBox.classList.add('hidden'), 5000);
        }

        function openSubModal() {
            const tarif = priceNormal.textContent;
            const periodeText = isAnnual ? "Annuel" : "Mensuel";
            const fraisText = isAnnual ? "5" : "1";

            document.getElementById('modal-summary').innerHTML = `
                Offre : <strong class="text-[#1C5B8F]">Abonnement Pro (${periodeText})</strong><br>
                Tarif récurrent : <strong class="text-[#E1AB2B] text-xl">${tarif}€</strong><br>
                <span class="text-sm text-gray-500">+ ${fraisText}€ de frais de première souscription</span>
            `;
            document.getElementById('sub-tarif').value = tarif;
            document.getElementById('sub-periode').value = isAnnual ? 'annuel' : 'mensuel';
            toggleModal('sub-modal');
        }

        const subForm = document.getElementById('sub-form');
        if (subForm) {
            subForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btnStripe = document.getElementById('btn-stripe');
                btnStripe.innerText = "Redirection...";
                btnStripe.disabled = true;

                const providerId = window.currentUserId || 1; 
                console.log(providerId)

                const data = {
                    user_id: parseInt(providerId),
                    type_abonnement: "Abonnement Pro",
                    periode: document.getElementById('sub-periode').value,
                    tarif: parseInt(document.getElementById('sub-tarif').value)
                };

                try {
                    const response = await fetch(`${window.API_BASE_URL}/prestataire/paiement-abonnement`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        credentials: 'include', 
                        body: JSON.stringify(data)
                    });

                    if (response.ok) {
                        const result = await response.json();
                        window.location.href = result.url;
                    } else {
                        const errorText = await response.text();
                        toggleModal('sub-modal');
                        showAlert(errorText || "Erreur lors de l'initialisation du paiement.", false);
                        btnStripe.innerText = "Payer en toute sécurité";
                        btnStripe.disabled = false;
                    }
                } catch (err) {
                    toggleModal('sub-modal');
                    showAlert("Erreur serveur, impossible de joindre l'API.", false);
                    btnStripe.innerText = "Payer en toute sécurité";
                    btnStripe.disabled = false;
                }
            });
        }
    </script>
</body>
</html>