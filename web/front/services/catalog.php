<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
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
    </script>
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">

    <?php include("../includes/header.php") ?>

    <main class="flex-1 relative">
        <div class="p-3 flex justify-between items-center mx-8">
            <a href="/front/services/menu_activity.php">
                <button class="flex items-center rounded-full px-6 button-blue text-white border border-[#1C5B8F] hover:bg-[#1C5B8F] py-2 transition-colors">
                    <img src="/front/icons/fleche_gauche.svg" alt="fleche" class="w-7 h-7 mr-2"> Revenir à la liste
                </button>
            </a>
        </div>

        <div id="api-message" class="hidden fixed top-24 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-xl p-4 rounded-lg border text-center font-bold shadow-2xl transition-all duration-300"></div>

        <div id="my-services-section" class="hidden w-full px-6 md:px-16 mt-12">
            <h2 class="text-2xl font-bold text-[#E1AB2B] mb-6 flex items-center gap-2">
                Mes rendez-vous à venir
            </h2>
            <div id="my-services-container" class="flex flex-wrap gap-6 pb-10 border-b border-gray-200">
            </div>
        </div>

        <div class="w-full px-6 md:px-16 mt-12 mb-8 bg-gray-50 text-center">
            <h2 class="text-4xl font-bold mb-4 text-[#1C5B8F]">Notre catalogue de services</h2>
            <h2 class="text-lg max-w-4xl mx-auto text-gray-600 mb-8">
                Parcourez ci-dessous l'ensemble des prestations proposées et réservez le créneau qui vous convient.
            </h2>

            <div class="flex justify-center items-center gap-4">
                <select id="price-sort" onchange="applyPriceSort()" class="p-2 border rounded-lg">
                    <option value="none">Trier par...</option>
                    <option value="asc">Prix : Croissant</option>
                    <option value="desc">Prix : Décroissant</option>
                </select>
            </div>
        </div>

        <div id="services-container" class="flex flex-wrap gap-8 px-6 md:px-16 py-10 justify-center">
            <div class="w-full text-center py-10">
                <p class="text-xl text-gray-500 animate-pulse">Chargement des services en cours...</p>
            </div>
        </div>

        <div id="pagination-controls" class="flex justify-center items-center gap-4 pb-16"></div>

    </main>

    <?php include("../includes/footer.php") ?>

    <script>
        const API_BASE = window.API_BASE_URL || "http://localhost:8082";
        let currentPage = 1;
        const limit = 6;
        const messageBox = document.getElementById('api-message');
        let currentServicesData = [];

        window.serviceSlots = {};

        function showAlert(msg, isSuccess) {
            messageBox.textContent = msg;
            messageBox.className = `fixed top-24 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-xl p-4 rounded-lg border text-center font-bold shadow-2xl transition-all duration-300 ${isSuccess ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'}`;
            messageBox.classList.remove('hidden');
            setTimeout(() => {
                messageBox.classList.add('opacity-0');
                setTimeout(() => {
                    messageBox.classList.add('hidden');
                    messageBox.classList.remove('opacity-0');
                }, 300);
            }, 4000);
        }

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success') && urlParams.get('success') === 'reservation_validee') {
            showAlert("Paiement validé ! Votre réservation est confirmée.", true);
            window.history.replaceState({}, document.title, window.location.pathname);
        } else if (urlParams.has('error') && urlParams.get('error') === 'paiement_echoue') {
            showAlert("Le paiement a échoué. Veuillez réessayer.", false);
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        async function fetchMyServices() {
            const userId = window.currentUserId;
            if (!userId) return;

            try {
                const response = await fetch(`${API_BASE}/service/user/${userId}`);
                if (!response.ok) return;

                const myServices = await response.json();
                const section = document.getElementById('my-services-section');
                const container = document.getElementById('my-services-container');
                container.innerHTML = '';

                if (myServices.length > 0) {
                    section.classList.remove('hidden');
                    myServices.forEach(s => {
                        const dateObj = new Date(s.date_heure);
                        const dateString = dateObj.toLocaleString('fr-FR', {
                            weekday: 'long',
                            day: 'numeric',
                            month: 'long',
                            hour: '2-digit',
                            minute: '2-digit'
                        }).replace(/^\w/, c => c.toUpperCase());

                        const card = `
                            <div class="flex items-center bg-white border border-[#E1AB2B] rounded-2xl shadow-sm p-5 w-full md:w-[400px]">
                                <div class="flex-1">
                                    <h4 class="font-bold text-[#1C5B8F] text-xl leading-tight line-clamp-1">${s.nom}</h4>
                                    <p class="text-md font-bold text-[#E1AB2B] mt-2">📅 ${dateString}</p>
                                    <p class="text-sm text-gray-500 mb-4 line-clamp-2 mt-2">${s.description}</p>
                                    <button onclick="cancelService(${s.id_reservation})" class="text-sm text-red-500 hover:text-white hover:bg-red-500 font-bold transition-colors border border-red-500 rounded-full px-4 py-2">
                                        Annuler le RDV
                                    </button>
                                </div>
                            </div>
                        `;
                        container.innerHTML += card;
                    });
                } else {
                    section.classList.add('hidden');
                }
            } catch (err) {
                console.error("Erreur", err);
            }
        }

        async function loadDisposForService(serviceId, prestataireId) {
            const timeGrid = document.getElementById(`time-grid-${serviceId}`);
            const daySelect = document.getElementById(`day-select-${serviceId}`);

            if (!prestataireId) {
                timeGrid.innerHTML = '<p class="text-sm text-red-500 col-span-3 text-center py-4">Prestataire inconnu</p>';
                return;
            }

            try {
                const res = await fetch(`${API_BASE}/prestataire/planning/${prestataireId}/available`);
                if (!res.ok) throw new Error('Erreur réseau');

                const slots = await res.json();

                if (!slots || slots.length === 0) {
                    timeGrid.innerHTML = '<p class="text-sm text-gray-500 col-span-3 text-center italic py-4">Aucun créneau disponible</p>';
                    return;
                }

                const grouped = {};
                const now = new Date();

                slots.forEach(slot => {
                    const formattedDate = slot.date_heure.replace(" ", "T");
                    const dateObj = new Date(formattedDate);

                    if (dateObj <= now) {
                        return;
                    }

                    if (isNaN(dateObj.getTime())) {
                        console.error("Date invalide reçue :", slot.date_heure);
                        return;
                    }

                    const dateOnly = new Date(dateObj.getFullYear(), dateObj.getMonth(), dateObj.getDate());
                    const key = dateOnly.toISOString();

                    if (!grouped[key]) {
                        grouped[key] = {
                            label: dateObj.toLocaleDateString('fr-FR', {
                                weekday: 'long',
                                day: 'numeric',
                                month: 'short'
                            }),
                            slots: []
                        };
                    }
                    grouped[key].slots.push({
                        id: slot.id_disponibilite,
                        datetime: slot.date_heure,
                        timeLabel: dateObj.toLocaleTimeString('fr-FR', {
                            hour: '2-digit',
                            minute: '2-digit'
                        })
                    });
                });

                if (Object.keys(grouped).length === 0) {
                    timeGrid.innerHTML = '<p class="text-sm text-gray-500 col-span-3 text-center italic py-4">Aucun créneau futur disponible</p>';
                    return;
                }

                window.serviceSlots[serviceId] = grouped;

                daySelect.innerHTML = '<option value="" disabled selected>1. Choisissez un jour</option>';
                Object.keys(grouped).forEach(key => {
                    daySelect.innerHTML += `<option value="${key}" class="capitalize">${grouped[key].label}</option>`;
                });

                daySelect.classList.remove('hidden');
                timeGrid.innerHTML = '<p class="text-sm text-gray-500 col-span-3 text-center italic py-4">Veuillez sélectionner un jour ci-dessus.</p>';

            } catch (err) {
                timeGrid.innerHTML = '<p class="text-sm text-red-500 col-span-3 text-center py-4">Erreur de chargement</p>';
            }
        }

        function renderTimeSlots(serviceId) {
            const daySelect = document.getElementById(`day-select-${serviceId}`);
            const timeGrid = document.getElementById(`time-grid-${serviceId}`);
            const selectedKey = daySelect.value;

            document.getElementById(`selected-dispo-id-${serviceId}`).value = "";
            document.getElementById(`selected-dispo-date-${serviceId}`).value = "";

            if (!selectedKey || !window.serviceSlots[serviceId][selectedKey]) return;

            const dayData = window.serviceSlots[serviceId][selectedKey];
            timeGrid.innerHTML = '';

            dayData.slots.forEach(s => {
                const btn = document.createElement('button');
                btn.className = `time-slot-btn-${serviceId} w-full py-2 rounded-lg border-2 border-[#1C5B8F] text-[#1C5B8F] bg-white font-bold text-sm hover:bg-[#1C5B8F] hover:text-white transition-all focus:outline-none`;
                btn.textContent = s.timeLabel;
                btn.onclick = () => selectTimeSlot(serviceId, s.id, s.datetime, btn);
                timeGrid.appendChild(btn);
            });
        }

        function selectTimeSlot(serviceId, dispoId, datetime, btnElement) {
            document.getElementById(`selected-dispo-id-${serviceId}`).value = dispoId;
            document.getElementById(`selected-dispo-date-${serviceId}`).value = datetime;

            const grid = document.getElementById(`time-grid-${serviceId}`);
            grid.querySelectorAll(`.time-slot-btn-${serviceId}`).forEach(b => {
                b.classList.remove('bg-[#1C5B8F]', 'text-white', 'scale-105');
                b.classList.add('bg-white', 'text-[#1C5B8F]');
            });

            btnElement.classList.remove('bg-white', 'text-[#1C5B8F]');
            btnElement.classList.add('bg-[#1C5B8F]', 'text-white', 'scale-105');
        }


        async function bookService(serviceId) {
            const userId = window.currentUserId;

            if (!userId) {
                showAlert("Vous devez être connecté pour prendre RDV.", false);
                setTimeout(() => window.location.href = "/front/account/signin.php?redirect=" + encodeURIComponent(window.location.pathname), 2000);
                return;
            }

            const user = window.userData;
            const hasSubscription = user && user.id_abonnement && user.id_abonnement > 0;
            if (!hasSubscription) {
                showAlert("Vous devez posséder un abonnement Silver Happy pour réserver un service.", false);
                setTimeout(() => window.location.href = "/front/services/subscription.php", 3000);
                return;
            }

            const idDispoRaw = document.getElementById(`selected-dispo-id-${serviceId}`).value;
            const dateInput = document.getElementById(`selected-dispo-date-${serviceId}`).value;

            if (idDispoRaw === "" || !dateInput) {
                showAlert("Veuillez choisir un jour et cliquer sur une heure !", false);
                return;
            }

            const idDisponibilite = parseInt(idDispoRaw);

            try {
                const response = await fetch(`${API_BASE}/service/checkout/${serviceId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_utilisateur: parseInt(userId),
                        date_heure: dateInput,
                        id_disponibilite: idDisponibilite
                    }),
                });

                let data = null;
                const contentType = response.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    const text = await response.text();
                    data = {
                        error: text.trim()
                    };
                }

                if (!response.ok) {
                    if (response.status === 409) {
                        showAlert("Ce créneau est déjà pris pour ce prestataire. Choisissez un autre horaire.", false);

                        const timeGrid = document.getElementById(`time-grid-${serviceId}`);
                        if (timeGrid) {
                            const allBtns = timeGrid.querySelectorAll(`.time-slot-btn-${serviceId}`);
                            allBtns.forEach(btn => {
                                if (btn.classList.contains('bg-[#1C5B8F]') && btn.classList.contains('text-white')) {
                                    btn.remove();
                                }
                            });
                        }

                        document.getElementById(`selected-dispo-id-${serviceId}`).value = "";
                        document.getElementById(`selected-dispo-date-${serviceId}`).value = "";

                    } else {
                        showAlert("Erreur : " + (data.error || "Une erreur est survenue."), false);
                    }
                    return;
                }

                if (data.url) {
                    window.location.href = data.url;
                } else if (data.isFree) {
                    showAlert(data.message || "Réservation confirmée !", true);
                    fetchMyServices();
                    const daySelect = document.getElementById(`day-select-${serviceId}`);
                    const prestataireId = daySelect.getAttribute('data-prestataire');
                    loadDisposForService(serviceId, prestataireId);
                }

            } catch (err) {
                console.error("Erreur fetch bookService:", err);
                showAlert("Impossible de contacter le serveur. Vérifiez votre connexion.", false);
            }
        }

        async function cancelService(reservationId) {
            const userId = window.currentUserId;
            if (!userId) return;

            if (!confirm("Êtes-vous sûr de vouloir annuler ce rendez-vous ? (Si vous avez payé, vous serez remboursé)")) {
                return;
            }

            try {
                const response = await fetch(`${API_BASE}/service/unregister/${reservationId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_utilisateur: parseInt(userId)
                    }),
                });

                if (response.ok) {
                    const data = await response.json();
                    showAlert(data.message || "Rendez-vous annulé avec succès.", true);
                    fetchMyServices();
                } else {
                    const errText = await response.text();
                    showAlert("Erreur : " + errText, false);
                }
            } catch (err) {
                showAlert("Impossible de joindre le serveur pour l'annulation.", false);
            }
        }

        async function fetchServices(page = 1) {
            try {
                currentPage = page;
                const response = await fetch(`${API_BASE}/service/read?statut=accepte&page=${currentPage}&limit=${limit}`);
                const result = await response.json();
                currentServicesData = result.data || [];

                const container = document.getElementById('services-container');
                container.innerHTML = '';

                if (currentServicesData.length === 0) {
                    container.innerHTML = '<p class="text-xl text-gray-500 py-10 italic">Aucun service disponible.</p>';
                    renderPagination(0);
                    return;
                }
                renderServiceCards(currentServicesData);
                renderPagination(result.totalPages);
            } catch (err) {
                showAlert("Erreur de connexion avec le catalogue.", false);
            }
        }

        function applyPriceSort() {
            const sortOrder = document.getElementById('price-sort').value;

            let sortedServices = [...currentServicesData];

            if (sortOrder === 'asc') {
                sortedServices.sort((a, b) => a.prix - b.prix);
            } else if (sortOrder === 'desc') {
                sortedServices.sort((a, b) => b.prix - a.prix);
            }

            renderServiceCards(sortedServices);
        }

        function renderServiceCards(services) {
            const container = document.getElementById('services-container');
            let htmlContent = '';

            if (services.length === 0) {
                container.innerHTML = '<p class="text-lg text-gray-500 py-10 italic">Aucun service de cette catégorie sur cette page.</p>';
                return;
            }

            const formatDuree = (minutes) => {
                if (!minutes || minutes <= 0) return 'Durée non spécifiée';
                const h = Math.floor(minutes / 60);
                const m = minutes % 60;
                
                if (h > 0 && m > 0) return `${h}h${m.toString().padStart(2, '0')}`;
                if (h > 0) return `${h}h`;
                return `${m} min`;
            };

            services.forEach(s => {
                console.log(s);
                const id = s.id_service || s.ID;
                const nom = s.nom || 'Service sans nom';
                const description = s.description || '';
                const idPrestataire = s.id_prestataire;

                const prixNum = parseFloat(s.prix || 0);
                const prixHtml = prixNum > 0 ?
                    `<p class="text-xl font-extrabold text-[#E1AB2B] mb-1">${prixNum.toFixed(2)} €</p>` :
                    `<p class="text-xl font-extrabold text-green-600 mb-1">Gratuit</p>`;

                const isBoosted = s.is_boosted === 1 || s.is_boosted === true || s.IsBoosted === true;
                const borderClass = isBoosted ? "border-[#E1AB2B] border-2 shadow-[#E1AB2B]/20 shadow-xl" : "border-gray-200 shadow-lg";
                const badgeBoost = isBoosted ? `<span class="absolute -top-3 -right-3 bg-[#E1AB2B] text-white p-2 rounded-full shadow-md text-xl" title="Prestataire Recommandé">⭐</span>` : "";

                const dureeFormatee = formatDuree(s.duree);

                htmlContent += `
                <div class="md:max-w-[400px] w-full bg-white ${borderClass} flex flex-col p-8 rounded-[2rem] hover:-translate-y-1 transition-all relative mt-4">
                    ${badgeBoost}
                    <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-1/3 h-1.5 bg-[#1C5B8F] rounded-b-md"></div>
                    
                    <div class="mt-2 mb-1 flex justify-between items-center">
                        <span class="bg-[#E1AB2B]/10 text-[#E1AB2B] border border-[#E1AB2B]/30 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                        ${s.prestataire_prenom} ${s.prestataire_nom}  
                        </span>
                        <a href="profile_provider.php?id=${idPrestataire}&from=services" class="text-[#1C5B8F] hover:text-[#E1AB2B] text-xs font-bold normal-case">
                            Voir le profil complet
                        </a>
                    </div>

                    <h3 class="text-2xl text-[#1C5B8F] font-bold mt-3 mb-1">${nom}</h3>
                    
                    <!-- Affichage du Prix et de la Durée -->
                    <div class="flex flex-col mb-4">
                        ${prixHtml}
                        <div class="flex items-center text-sm font-semibold text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            ${dureeFormatee}
                        </div>
                    </div>

                    <p class="text-gray-600 mb-6 flex-grow leading-relaxed line-clamp-3">${description}</p>
                    
                    <div class="mt-auto bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <label class="block text-sm font-bold text-gray-700 mb-3">
                            Choisissez votre créneau :
                        </label>
                        
                        <div id="dispo-wrapper-${id}" class="mb-5">
                            <select id="day-select-${id}" data-prestataire="${idPrestataire}" onchange="renderTimeSlots(${id})" class="w-full p-2.5 border border-gray-300 rounded-lg mb-3 outline-none focus:border-[#1C5B8F] font-semibold text-gray-700 capitalize hidden cursor-pointer">
                            </select>
                            
                            <div id="time-grid-${id}" class="grid grid-cols-3 gap-2 max-h-[140px] overflow-y-auto pr-1">
                                <p class="text-sm text-gray-500 col-span-3 text-center py-4 animate-pulse">Recherche des créneaux...</p>
                            </div>
                            
                            <input type="hidden" id="selected-dispo-id-${id}">
                            <input type="hidden" id="selected-dispo-date-${id}">
                        </div>

                        <button onclick="bookService(${id})" class="w-full rounded-full py-3 px-4 bg-[#1C5B8F] text-white font-bold hover:bg-[#154670] transition-colors shadow-md mb-3">
                            ${prixNum > 0 ? 'Payer & Réserver' : 'Confirmer le RDV'}
                        </button>
                        <button onclick="negocierOffre(${s.id_service}, ${s.id_prestataire}, '${s.nom.replace(/'/g, "\\'")}', '${s.prestataire_nom}', '${s.prestataire_prenom}')" 
                            class="w-full py-2 border-2 border-[#E1AB2B] text-[#E1AB2B] rounded-full font-bold hover:bg-[#E1AB2B] hover:text-white transition-all">
                                Faire une offre
                        </button>
                    </div>
                </div>
                `;
            });

            container.innerHTML = htmlContent;

            services.forEach(s => {
                const id = s.id_service || s.ID;
                loadDisposForService(id, s.id_prestataire);
            });
        }

        function negocierOffre(idService, idPresta, nomSvc, nomPresta, prenomPresta) {
            const url = `/front/communication/messaging.php/${prenomPresta}/${nomPresta}/${idPresta}/presta?prefill_service=${idService}`;
            window.location.href = url;
        }

        function renderPagination(totalPages) {
            const paginationContainer = document.getElementById('pagination-controls');
            paginationContainer.innerHTML = '';
            if (totalPages <= 1) return;

            const prevDisabled = currentPage === 1 ? 'disabled opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 text-[#1C5B8F]';
            paginationContainer.innerHTML += `<button onclick="fetchServices(${currentPage - 1})" class="px-4 py-2 border-2 border-[#1C5B8F] text-[#1C5B8F] rounded-full font-bold transition-colors ${prevDisabled}" ${currentPage === 1 ? 'disabled' : ''}>← Précédent</button>`;
            paginationContainer.innerHTML += `<span class="text-gray-500 font-medium px-4">Page <strong class="text-[#1C5B8F]">${currentPage}</strong> sur ${totalPages}</span>`;

            const nextDisabled = currentPage === totalPages ? 'disabled opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 text-[#1C5B8F]';
            paginationContainer.innerHTML += `<button onclick="fetchServices(${currentPage + 1})" class="px-4 py-2 border-2 border-[#1C5B8F] text-[#1C5B8F] rounded-full font-bold transition-colors ${nextDisabled}" ${currentPage === totalPages ? 'disabled' : ''}>Suivant →</button>`;
        }

        window.addEventListener('auth_ready', () => {
            fetchMyServices();
        });

        window.onload = () => {
            fetchServices(1);
            setTimeout(() => {
                if (window.currentUserId) fetchMyServices();
            }, 500);
        };
    </script>
</body>

</html>