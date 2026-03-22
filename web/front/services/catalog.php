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
                <button class="flex items-center rounded-full px-6 button-blue">
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
            <h2 class="text-4xl font-bold mb-4 text-[#1C5B8F]">Notre catalogue</h2>
            <h2 class="text-lg max-w-4xl mx-auto text-gray-600">
                Parcourez ci-dessous l'ensemble des services proposés et réservez le créneau qui vous convient.
            </h2>
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
        const API_BASE = "http://localhost:8082";
        let currentPage = 1;
        const limit = 6;
        const messageBox = document.getElementById('api-message');

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

        async function bookService(serviceId) {
            const userId = window.currentUserId;
            if (!userId) {
                showAlert("Vous devez être connecté pour prendre RDV.", false);
                setTimeout(() => window.location.href = "/front/account/signin.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>", 2000);
                return;
            }

            const dateInput = document.getElementById(`datetime-${serviceId}`).value;
            if (!dateInput) {
                showAlert("Veuillez choisir une date et une heure !", false);
                return;
            }

            if (new Date(dateInput) <= new Date()) {
                showAlert("Impossible de réserver dans le passé !", false);
                return;
            }

            try {
                const response = await fetch(`${API_BASE}/service/register/${serviceId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_utilisateur: parseInt(userId),
                        date_heure: dateInput
                    }),
                });

                if (response.ok) {
                    showAlert("Rendez-vous confirmé !", true);
                    document.getElementById(`datetime-${serviceId}`).value = "";
                    fetchMyServices();
                } else {
                    const errText = await response.text();
                    showAlert("Erreur : " + errText, false);
                }
            } catch (err) {
                showAlert("Impossible de joindre le serveur.", false);
            }
        }

        async function cancelService(reservationId) {
            const userId = window.currentUserId;
            if (!userId) return;

            if (!confirm("Êtes-vous sûr de vouloir annuler ce rendez-vous ?")) return;

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
                    showAlert("Rendez-vous annulé.", true);
                    fetchMyServices();
                } else {
                    const errText = await response.text();
                    showAlert("Erreur : " + errText, false);
                }
            } catch (err) {
                showAlert("Impossible de joindre le serveur.", false);
            }
        }

        async function fetchServices(page = 1) {
            try {
                currentPage = page;
                const response = await fetch(`${API_BASE}/service/read?page=${currentPage}&limit=${limit}`);
                const result = await response.json();
                const services = result.data || [];
                const container = document.getElementById('services-container');
                container.innerHTML = '';

                if (services.length === 0) {
                    container.innerHTML = '<p class="text-xl text-gray-500 py-10 italic">Aucun service disponible.</p>';
                    renderPagination(0);
                    return;
                }

                const now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                const minDateTime = now.toISOString().slice(0, 16);

                services.forEach(s => {
                    const id = s.id_service || s.ID;
                    const nom = s.nom || 'Service sans nom';
                    const description = s.description || '';

                    container.innerHTML += `
                        <div class="md:max-w-[400px] w-full bg-white border border-gray-200 flex flex-col p-8 rounded-[2rem] shadow-lg hover:-translate-y-1 transition-all relative">
                            <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-1/3 h-1.5 bg-[#1C5B8F] rounded-b-md"></div>
                            <h3 class="text-2xl text-[#1C5B8F] font-bold mb-2 mt-2">${nom}</h3>
                            <p class="text-gray-600 mb-6 flex-grow leading-relaxed">${description}</p>
                            
                            <div class="mt-auto bg-gray-50 p-4 rounded-xl border border-gray-200">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Choisissez votre créneau :</label>
                                <input type="datetime-local" id="datetime-${id}" min="${minDateTime}" class="w-full p-2 border border-gray-300 rounded-lg mb-4 outline-none focus:border-[#E1AB2B] focus:ring-1 focus:ring-[#E1AB2B]">
                                <button onclick="bookService(${id})" class="w-full rounded-full py-3 px-4 bg-[#1C5B8F] text-white font-bold hover:bg-[#154670] transition-colors shadow-md">
                                    Confirmer le RDV
                                </button>
                            </div>
                        </div>
                    `;
                });
                renderPagination(result.totalPages);
            } catch (err) {
                showAlert("Erreur de connexion", false);
            }
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