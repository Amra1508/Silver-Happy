<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>
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
            }
        }
    </script>
</head>

<body class="bg-gray-50 text-gray-800">

    <div class="flex min-h-screen relative">

        <?php include("../includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto relative">

            <main class="p-8">

                <div id="main-content-valide" class="hidden space-y-6 max-w-7xl mx-auto">

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                        <div>
                            <h1 class="text-3xl font-semibold text-[#1C5B8F]">Historique de mes prestations</h1>
                            <p class="text-gray-500 mt-1">Consultez vos évènements et services passés.</p>
                        </div>
                    </div>

                    <div class="flex gap-6 border-b border-gray-200 mt-6">
                        <button id="tab-events" onclick="switchTab('events')" class="pb-3 px-2 text-[#1C5B8F] font-bold border-b-2 border-[#1C5B8F] transition-all">
                            Évènements
                        </button>
                        <button id="tab-services" onclick="switchTab('services')" class="pb-3 px-2 text-gray-500 font-semibold border-b-2 border-transparent hover:text-[#1C5B8F] transition-all">
                            Services
                        </button>
                    </div>

                    <div id="page-alert" class="hidden p-4 rounded-xl font-semibold text-sm text-center"></div>

                    <div id="events-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <div class="col-span-full py-10 text-center text-gray-500 flex flex-col items-center">
                            Chargement des évènements...
                        </div>
                    </div>

                    <div id="services-container" class="hidden grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <div class="col-span-full py-10 text-center text-gray-500 flex flex-col items-center">
                            Chargement des services...
                        </div>
                    </div>
                </div>

            </main>
        </div>

        <div id="details-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-3xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
                <div id="details-header" class="h-40 w-full bg-gradient-to-r from-[#1C5B8F] to-blue-600 bg-cover bg-center relative">
                    <button onclick="toggleModal('details-modal')" class="absolute top-4 right-4 bg-white bg-opacity-50 hover:bg-opacity-100 text-gray-800 rounded-full w-8 h-8 flex items-center justify-center font-bold transition">&times;</button>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-2">
                        <span id="details-prix" class="inline-block px-3 py-1 bg-[#E1AB2B] text-white text-xs font-bold rounded-full shadow-sm"></span>
                        <span id="details-places" class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded-md border border-gray-200"></span>
                    </div>
                    <h3 id="details-nom" class="text-2xl font-bold text-[#1C5B8F] mb-4"></h3>
                    <p id="details-desc" class="text-gray-600 mb-6 whitespace-pre-wrap"></p>
                    <div class="space-y-2 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <p class="text-sm text-gray-700"><strong>Lieu :</strong> <span id="details-lieu"></span></p>
                        <p class="text-sm text-gray-700"><strong>Début :</strong> <span id="details-debut"></span></p>
                        <p class="text-sm text-gray-700"><strong>Fin :</strong> <span id="details-fin"></span></p>
                    </div>
                </div>
            </div>
        </div>

        <div id="participants-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-3xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[80vh]">
                <div class="bg-[#1C5B8F] px-6 py-4 flex justify-between items-center text-white shrink-0">
                    <h3 class="text-xl font-bold flex items-center gap-2">
                        Seniors Inscrits
                    </h3>
                    <button onclick="toggleModal('participants-modal')" class="text-white hover:text-red-300 transition-colors text-2xl leading-none">&times;</button>
                </div>
                <div class="p-6 overflow-y-auto bg-gray-50">
                    <ul id="participants-list" class="space-y-3">
                        <li class="text-center text-gray-500 py-4">Chargement...</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <script>
        let currentProviderId = null;
        let allEvents = [];
        let allServices = [];
        const API_URL = window.API_BASE_URL || 'http://localhost:8082';

        function switchTab(type) {
            const tabEvents = document.getElementById('tab-events');
            const tabServices = document.getElementById('tab-services');
            const contEvents = document.getElementById('events-container');
            const contServices = document.getElementById('services-container');

            if (type === 'events') {
                tabEvents.className = "pb-3 px-2 text-[#1C5B8F] font-bold border-b-2 border-[#1C5B8F] transition-all";
                tabServices.className = "pb-3 px-2 text-gray-500 font-semibold border-b-2 border-transparent transition-all";
                contEvents.classList.remove('hidden');
                contServices.classList.add('hidden');
                loadHistoryEvents(currentProviderId);
            } else {
                tabServices.className = "pb-3 px-2 text-[#1C5B8F] font-bold border-b-2 border-[#1C5B8F] transition-all";
                tabEvents.className = "pb-3 px-2 text-gray-500 font-semibold border-b-2 border-transparent transition-all";
                contServices.classList.remove('hidden');
                contEvents.classList.add('hidden');
                loadHistoryServices(currentProviderId);
            }
        }

        async function loadHistoryEvents(providerId) {
            const container = document.getElementById('events-container');
            container.innerHTML = '<div class="col-span-full py-10 text-center text-gray-500 flex flex-col items-center">Chargement de l\'historique...</div>';
            try {
                const res = await fetch(`${API_URL}/prestataire/${providerId}/past-events`, {
                    credentials: 'include'
                });
                if (res.ok) {
                    allEvents = await res.json();
                    renderHistoryEvents();
                }
            } catch (err) {
                console.error("Erreur :", err);
            }
        }

        function renderHistoryEvents() {
            const container = document.getElementById('events-container');
            container.innerHTML = '';
            if (allEvents.length === 0) {
                container.innerHTML = `<div class="col-span-full bg-white rounded-3xl p-10 text-center border border-gray-100 shadow-sm mt-6">...</div>`;
                return;
            }

            allEvents.forEach(evt => {
                const evtId = evt.id_evenement || evt.id || evt.ID;
                const card = document.createElement('div');
                card.className = "bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col fade-in hover:shadow-md transition-shadow relative opacity-80";

                const badgePrice = evt.prix > 0 ? `${evt.prix} €` : 'Gratuit';
                const imgSrc = evt.image ? `${API_URL}${evt.image}` : null;
                let imageBlock = imgSrc ?
                    `<div class="relative"><img src="${imgSrc}" class="w-full h-40 object-cover cursor-pointer filter grayscale-[30%]" onclick="openDetailsModal(${evtId})"></div>` :
                    `<div class="relative h-40 w-full bg-gradient-to-r from-gray-500 to-gray-400 cursor-pointer" onclick="openDetailsModal(${evtId})"></div>`;

                card.innerHTML = `
                ${imageBlock}
                <div class="p-6 flex-1 flex flex-col">
                    <div class="flex justify-between items-start mb-3">
                        <span class="inline-block px-3 py-1 bg-gray-500 text-white text-xs font-bold rounded-full shadow-sm">${badgePrice}</span>
                        <span class="text-xs font-bold text-gray-500 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">${evt.nombre_place} places</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2 leading-tight cursor-pointer hover:text-[#1C5B8F] transition" onclick="openDetailsModal(${evtId})">${evt.nom}</h3>
                    <p class="text-sm text-gray-500 mb-6 flex-1 line-clamp-2">${evt.description || 'Aucune description'}</p>
                    <div class="space-y-2 mt-auto text-sm text-gray-600">
                        <div class="flex items-start"><span class="truncate">${evt.lieu}</span></div>
                        <div class="flex items-center"><span class="truncate text-red-600 font-medium">Terminé le ${formatDisplayDate(evt.date_debut)}</span></div>
                    </div>
                    <div class="mt-5 pt-4 border-t border-gray-100 flex justify-between items-center gap-2">
                        <div class="flex gap-2 flex-wrap justify-end items-center w-full">
                            <span class="text-xs text-gray-400 font-bold px-2">Évènement passé</span>
                            <button onclick="openParticipantsModal(${evtId})" class="text-[#1C5B8F] font-bold text-xs hover:underline flex items-center gap-1 px-1">Inscrits</button>
                        </div>
                    </div>
                </div>`;
                container.appendChild(card);
            });
        }

        async function loadHistoryServices(providerId) {
            const container = document.getElementById('services-container');
            container.innerHTML = '<div class="col-span-full py-10 text-center text-gray-500 flex flex-col items-center">Chargement des prestations...</div>';
            try {
                const res = await fetch(`${API_URL}/prestataire/${providerId}/past-services`, {
                    credentials: 'include'
                });
                if (res.ok) {
                    const data = await res.json();
                    allServices = data || [];
                    renderHistoryServices();
                }
            } catch (err) {
                console.error(err);
            }
        }

        function renderHistoryServices() {
            const container = document.getElementById('services-container');
            container.innerHTML = '';
            if (allServices.length === 0) {
                container.innerHTML = `<div class="col-span-full bg-white rounded-3xl p-10 text-center border border-gray-100 shadow-sm mt-6"><p class="text-gray-500">Aucun service effectué pour le moment.</p></div>`;
                return;
            }

            allServices.forEach(ser => {
                const card = document.createElement('div');
                card.className = "bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col fade-in hover:shadow-md transition-shadow relative opacity-80";

                card.innerHTML = `
                <div class="p-6 flex-1 flex flex-col">
                    <div class="flex justify-between items-start mb-3">
                        <span class="inline-block px-3 py-1 bg-gray-500 text-white text-xs font-bold rounded-full shadow-sm">${ser.prix_final} €</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2 leading-tight">${ser.service_nom}</h3>
                    <p class="text-sm text-gray-500 mb-6 flex-1">Prestation réalisée pour <strong>${ser.client_prenom} ${ser.client_nom}</strong>.</p>
                    <div class="space-y-2 mt-auto text-sm text-gray-600">
                        <div class="flex items-center"><span class="truncate text-red-600 font-medium">Réalisé le ${formatDisplayDate(ser.date_heure)}</span></div>
                    </div>
                    <div class="mt-5 pt-4 border-t border-gray-100 flex justify-end">
                        <a href="/providers/communication/messaging.php/${ser.client_prenom}/${ser.client_nom}/${ser.id_utilisateur}/senior" class="text-[#1C5B8F] font-bold text-xs hover:underline flex items-center gap-1 px-1">Contacter l'adhérent</a>
                    </div>
                </div>`;
                container.appendChild(card);
            });
        }

        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const meRes = await fetch(`${API_URL}/auth/me-provider`, {
                    method: 'GET',
                    credentials: 'include'
                });
                if (meRes.ok) {
                    const data = await meRes.json();
                    if (data.status && (data.status.toLowerCase() === 'validé' || data.status.toLowerCase() === 'valide')) {
                        document.getElementById('main-content-valide').classList.remove('hidden');
                        currentProviderId = data.id_prestataire || data.id || data.ID;
                        window.currentUserId = currentProviderId;

                        loadHistoryEvents(currentProviderId);
                    }
                } else {
                    window.location.href = "/providers/account/signin.php";
                }
            } catch (err) {
                console.error("Erreur auth :", err);
            }
        });

        function formatDisplayDate(dateStr) {
            if (!dateStr) return "-";
            const d = new Date(dateStr.replace(' ', 'T'));
            if (isNaN(d)) return "-";
            return d.toLocaleString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function formatDateForInput(dateStr) {
            if (!dateStr) return "";
            const d = new Date(dateStr.replace(' ', 'T'));
            if (isNaN(d)) return "";
            return d.toISOString().slice(0, 16);
        }

        function showAlert(msg, type = "success") {
            const pageAlert = document.getElementById('page-alert');
            pageAlert.textContent = msg;
            pageAlert.className = `p-4 mb-6 rounded-xl font-bold block fade-in ${type === 'success' ? 'text-green-700 bg-green-100 border border-green-400' : 'text-red-700 bg-red-100 border border-red-400'}`;
            pageAlert.classList.remove('hidden');
            setTimeout(() => pageAlert.classList.add('hidden'), 5000);
        }

        async function openParticipantsModal(eventId) {
            toggleModal('participants-modal');
            const listContainer = document.getElementById('participants-list');
            listContainer.innerHTML = '<li class="text-center text-gray-500 py-4 font-semibold">Chargement des inscrits...</li>';

            try {
                const res = await fetch(`${API_URL}/prestataire/evenement/${eventId}/participants`, {
                    method: 'GET',
                    credentials: 'include'
                });

                if (res.ok) {
                    const participants = await res.json();
                    if (participants.length === 0) {
                        listContainer.innerHTML = '<li class="text-center text-gray-500 py-6">Aucun senior ne s\'était inscrit à cet évènement.</li>';
                    } else {
                        listContainer.innerHTML = '';
                        participants.forEach(p => {
                            const seniorId = p.id || p.ID || p.id_utilisateur;

                            const li = document.createElement('li');
                            li.className = 'flex justify-between items-center p-4 bg-white shadow-sm rounded-xl border border-gray-100';

                            li.innerHTML = `
                                <div class="flex items-center gap-3">
                                    <div class="bg-blue-100 text-[#1C5B8F] w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg shrink-0">
                                        ${p.prenom.charAt(0)}${p.nom.charAt(0)}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-800 truncate">${p.prenom} ${p.nom}</p>
                                        <a href="mailto:${p.email}" class="text-sm text-[#1C5B8F] hover:underline truncate block">${p.email}</a>
                                    </div>
                                </div>
                                <a href="/providers/communication/messaging.php/${p.prenom}/${p.nom}/${seniorId}/senior" class="flex items-center gap-2 bg-[#1C5B8F] text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-blue-800 transition-colors shadow-sm shrink-0">
                                    Message
                                </a>
                            `;
                            listContainer.appendChild(li);
                        });
                    }
                } else {
                    listContainer.innerHTML = '<li class="text-center text-red-500 py-4">Erreur lors de la récupération des inscrits.</li>';
                }
            } catch (err) {
                listContainer.innerHTML = '<li class="text-center text-red-500 py-4">Serveur injoignable.</li>';
            }
        }
    </script>
</body>

</html>