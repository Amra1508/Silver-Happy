<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silver Happy - Abonnements</title>
    <style>@import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');</style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Alata', 'sans-serif'] } } }
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
<body class="bg-gray-50 flex flex-col min-h-screen">

    <?php include("../includes/header.php") ?>

    <main class="flex-1 relative">
        <div id="api-message" class="hidden absolute top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-xl"></div>

        <div class="w-full px-6 md:px-16 mt-10 bg-white pt-8">
            <h2 class="text-3xl md:text-4xl mb-4 text-center text-[#1C5B8F] font-bold">Nos Tarifs d'Abonnement</h2>
            <h2 class="text-lg text-gray-600 text-center mb-10">
                Rejoignez la communauté Silver Happy et profitez de tous nos services adaptés.<br>
                Choisissez la formule qui vous correspond le mieux.
            </h2>
        </div>

        <div class="flex justify-center items-center gap-4 mb-8 bg-white px-16 pb-8">
            <span class="text-xl font-bold text-[#1C5B8F] transition-colors" id="label-mensuel">Mensuel</span>
            <button id="toggle-billing" class="relative inline-flex h-8 w-16 items-center rounded-full bg-[#1C5B8F] transition-colors focus:outline-none shadow-inner">
                <span id="toggle-knob" class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform translate-x-1 shadow-md"></span>
            </button>
            <span class="text-xl font-bold text-gray-400 transition-colors" id="label-annuel">Annuel</span>
        </div>

        <div class="flex flex-wrap gap-8 px-6 md:px-16 pb-16 justify-center bg-gray-50 pt-8">
            <div class="md:max-w-[450px] w-full bg-white border border-[#1C5B8F] flex flex-col items-center py-10 px-8 rounded-[2.5rem] shadow-lg">
                <h3 class="text-2xl md:text-3xl font-bold mb-6 text-[#1C5B8F] text-center">Abonnement Normal</h3>
                <div class="flex items-baseline gap-2 mb-2">
                    <span class="text-6xl font-bold text-black" id="price-normal">4</span>
                    <span class="text-3xl font-bold text-black">€</span>
                </div>
                <p class="text-gray-500 mb-10 text-center" id="period-normal">/ mois</p>
                <ul class="text-gray-600 mb-10 space-y-3 w-full px-4 text-center text-lg">
                    <li>✓ Accès complet aux activités</li>
                    <li>✓ Messagerie incluse</li>
                    <li>✓ Support prioritaire</li>
                </ul>
                <button onclick="openSubModal(false)" class="w-full rounded-full py-4 px-6 bg-[#1C5B8F] text-white font-bold text-xl mt-auto hover:bg-[#154670] transition-colors">
                    M'abonner
                </button>
            </div>

            <div class="md:max-w-[450px] w-full bg-white border-2 border-[#E1AB2B] flex flex-col items-center py-10 px-8 rounded-[2.5rem] relative overflow-hidden shadow-lg">
                <div class="absolute top-0 w-full h-4 bg-[#E1AB2B]"></div>
                <h3 class="text-2xl md:text-3xl font-bold mb-6 text-[#1C5B8F] text-center">Renouvellement</h3>
                <div class="flex items-baseline gap-2 mb-2">
                    <span class="text-6xl font-bold text-black" id="price-renewal">3</span>
                    <span class="text-3xl font-bold text-black">€</span>
                </div>
                <p class="text-gray-500 mb-10 text-center" id="period-renewal">/ mois</p>
                <ul class="text-gray-600 mb-10 space-y-3 w-full px-4 text-center text-lg">
                    <li>✓ Continuité de vos avantages</li>
                    <li>✓ Tarif préférentiel fidélité</li>
                    <li>✓ Sans interruption</li>
                </ul>
                <button onclick="openSubModal(true)" class="w-full rounded-full py-4 px-6 bg-[#E1AB2B] text-black font-bold text-xl mt-auto hover:bg-[#c79624] transition-colors">
                    Renouveler mon offre
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
                    <input type="hidden" id="sub-type">

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

    <?php include("../includes/footer.php") ?>

    <script>
        const toggleBtn = document.getElementById('toggle-billing');
        const toggleKnob = document.getElementById('toggle-knob');
        const labelMensuel = document.getElementById('label-mensuel');
        const labelAnnuel = document.getElementById('label-annuel');
        const priceNormal = document.getElementById('price-normal');
        const periodNormal = document.getElementById('period-normal');
        const priceRenewal = document.getElementById('price-renewal');
        const periodRenewal = document.getElementById('period-renewal');

        let isAnnual = false;

        toggleBtn.addEventListener('click', () => {
            isAnnual = !isAnnual;
            if (isAnnual) {
                toggleKnob.classList.replace('translate-x-1', 'translate-x-9');
                labelMensuel.classList.replace('text-[#1C5B8F]', 'text-gray-400');
                labelAnnuel.classList.replace('text-gray-400', 'text-[#1C5B8F]');
                priceNormal.textContent = '40'; periodNormal.textContent = '/ annuel';
                priceRenewal.textContent = '35'; periodRenewal.textContent = '/ annuel';
            } else {
                toggleKnob.classList.replace('translate-x-9', 'translate-x-1');
                labelMensuel.classList.replace('text-gray-400', 'text-[#1C5B8F]');
                labelAnnuel.classList.replace('text-[#1C5B8F]', 'text-gray-400');
                priceNormal.textContent = '4'; periodNormal.textContent = '/ mois';
                priceRenewal.textContent = '3'; periodRenewal.textContent = '/ mois';
            }
        });

        const messageBox = document.getElementById('api-message');
        function showAlert(msg, isSuccess) {
            messageBox.textContent = msg;
            messageBox.className = `p-4 rounded-lg border text-center font-bold shadow-lg ${isSuccess ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'}`;
            messageBox.classList.remove('hidden');
            setTimeout(() => messageBox.classList.add('hidden'), 4000);
        }

        function openSubModal(isRenewal) {
            const tarif = isRenewal ? priceRenewal.textContent : priceNormal.textContent;
            const periodeText = isAnnual ? "Annuel" : "Mensuel";
            const typeAbo = isRenewal ? "Renouvellement" : "Abonnement Normal";

            document.getElementById('modal-summary').innerHTML = `Offre : <strong class="text-[#1C5B8F]">${typeAbo} (${periodeText})</strong><br>Tarif : <strong class="text-[#E1AB2B] text-xl">${tarif}€</strong>`;
            document.getElementById('sub-tarif').value = tarif;
            document.getElementById('sub-periode').value = isAnnual ? 'annuel' : 'mensuel';
            document.getElementById('sub-type').value = typeAbo;
            toggleModal('sub-modal');
        }

        document.getElementById('sub-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btnStripe = document.getElementById('btn-stripe');
            btnStripe.innerText = "Redirection...";
            btnStripe.disabled = true;

            const userId = window.currentUserId || 1; 

            const data = {
                user_id: parseInt(userId),
                type_abonnement: document.getElementById('sub-type').value,
                periode: document.getElementById('sub-periode').value,
                tarif: parseInt(document.getElementById('sub-tarif').value)
            };

            try {
                const response = await fetch('http://localhost:8082/create-checkout', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    const result = await response.json();
                    window.location.href = result.url; 
                } else {
                    showAlert("Erreur lors de l'initialisation du paiement.", false);
                    btnStripe.innerText = "Payer en toute sécurité";
                    btnStripe.disabled = false;
                }
            } catch (err) {
                showAlert("Erreur serveur.", false);
                btnStripe.innerText = "Payer en toute sécurité";
                btnStripe.disabled = false;
            }
        });
    </script>
</body>
</html>