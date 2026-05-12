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
    <title>Mon Planning</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.11/locales/fr.global.min.js'></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');

        .fc-theme-standard .fc-scrollgrid {
            border-color: #f3f4f6;
        }

        .fc-header-toolbar {
            margin-bottom: 2rem !important;
        }

        .fc-button-primary {
            background-color: #1C5B8F !important;
            border-color: #1C5B8F !important;
            border-radius: 0.5rem !important;
        }

        .fc-button-primary:hover {
            background-color: #154670 !important;
        }

        .fc-button-active {
            background-color: #E1AB2B !important;
            border-color: #E1AB2B !important;
        }
    </style>

    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Alata', 'sans-serif'] } } }
        }
    </script>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col text-gray-800">

    <?php include("../includes/header.php") ?>

    <main class="flex-grow">
        <div class="max-w-7xl mx-auto px-6 pt-10 pb-16">

            <div class="flex justify-between items-center mb-6">
                <a href="/front/account/profile.php">
                    <button class="flex items-center rounded-full px-6 py-2 bg-[#1C5B8F] text-white font-bold hover:bg-[#154670] transition">
                        <img src="/front/icons/fleche_gauche.svg" alt="fleche" class="w-7 h-7 mr-2 brightness-0 invert"> Revenir à mon profil
                    </button>
                </a>
            </div>
            
            <div class="flex flex-col justify-between items-start mb-8">
                <h2 class="text-3xl font-bold text-[#1C5B8F]">Mon Planning</h2>
                <p class="text-gray-500 mt-1">Consultez vos événements et vos prestations de services réservées.</p>
            </div>

            <div id="no-sub-container" class="hidden flex flex-col items-center justify-center py-20 rounded-3xl shadow-sm border border-gray-100 bg-white fade-in">
                <p class="text-center font-semibold text-[#1C5B8F] text-2xl mb-8 px-4">
                    Vous devez être abonné(e) pour consulter votre planning d'activités.
                </p>
                <a class="rounded-xl px-8 py-3 bg-[#1C5B8F] text-white font-bold text-lg hover:bg-[#154670] transition" href="/front/services/subscription.php">
                    Je m'abonne
                </a>
            </div>

            <div id="planning-content" class="hidden fade-in">
                
                <div class="flex flex-wrap gap-4 mb-6">
                    <span class="flex items-center text-sm font-bold text-yellow-800 bg-yellow-50 px-4 py-2 rounded-xl border border-yellow-200">
                        <div class="w-3 h-3 rounded-full bg-[#E1AB2B] mr-2"></div> Événements
                    </span>
                    <span class="flex items-center text-sm font-bold text-[#1C5B8F] bg-blue-50 px-4 py-2 rounded-xl border border-blue-200">
                        <div class="w-3 h-3 rounded-full bg-[#1C5B8F] mr-2"></div> Prestations de services
                    </span>
                    <span class="flex items-center text-sm font-bold text-gray-600 bg-gray-50 px-4 py-2 rounded-xl border border-gray-200">
                        <div class="w-3 h-3 rounded-full bg-gray-400 mr-2"></div> Terminé
                    </span>
                </div>

                <div id="calendar" class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 min-h-[600px]"></div>
            </div>

            <div id="status_message" class="text-center text-xl text-gray-500 font-bold py-20 bg-white rounded-3xl border border-gray-100 shadow-sm animate-pulse">
                Chargement de votre planning...
            </div>

        </div>
    </main>

    <?php include("../includes/footer.php") ?>

    <div id="eventModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 fade-in">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-md overflow-hidden flex flex-col">
            <div class="bg-[#1C5B8F] px-6 py-4 flex justify-between items-center text-white shrink-0">
                <h3 id="modalTitle" class="text-xl font-bold truncate pr-4"></h3>
                <button onclick="document.getElementById('eventModal').classList.add('hidden')" class="text-white hover:text-red-300 transition-colors text-3xl leading-none">&times;</button>
            </div>
            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div id="modalStatusContainer" class="hidden mb-2">
                    <span id="modalStatusBadge" class="text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider"></span>
                </div>
                
                <div class="flex items-center text-gray-700 bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <span class="text-2xl mr-4"></span>
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase">Début</p>
                        <p id="modalStart" class="font-bold text-[#1C5B8F]"></p>
                    </div>
                </div>
                
                <div class="flex items-center text-gray-700 bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <span class="text-2xl mr-4"></span>
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase">Fin</p>
                        <p id="modalEnd" class="font-bold text-[#1C5B8F]"></p>
                    </div>
                </div>
                
                <div class="flex items-center text-gray-700 bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <span class="text-2xl mr-4"></span>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Lieu</p>
                        <p id="modalLocation" class="font-bold text-gray-800 break-words"></p>
                    </div>
                </div>

                <div class="flex items-start text-gray-700 bg-gray-50 p-3 rounded-xl border border-gray-100 mt-2">
                    <span class="text-2xl mr-4"></span>
                    <div class="min-w-0 w-full">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Description</p>
                        <p id="modalDescription" class="font-medium text-gray-600 text-sm mt-1 leading-relaxed"></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200 shrink-0">
                <button onclick="document.getElementById('eventModal').classList.add('hidden')" class="bg-[#1C5B8F] text-white font-bold px-6 py-2.5 rounded-xl hover:bg-blue-800 transition shadow-md w-full">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const statusMessage = document.getElementById('status_message');
            const calendarEl = document.getElementById('calendar');
            const planningContent = document.getElementById('planning-content');
            const noSubContainer = document.getElementById('no-sub-container');

            try {
                const authResponse = await fetch(`${window.API_BASE_URL}/auth/me`, {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include'
                });

                if (!authResponse.ok) {
                    window.location.href = "/front/account/signin.php";
                    return;
                }

                const user = await authResponse.json();
                const isSubscribed = (user.id_abonnement !== null && user.id_abonnement !== undefined && user.id_abonnement !== 0);

                if (!isSubscribed) {
                    statusMessage.classList.add('hidden');
                    noSubContainer.classList.remove('hidden');
                    return;
                }

                const userId = user.id_utilisateur || user.id; 

                const planningResponse = await fetch(`${window.API_BASE_URL}/planning?user_id=${userId}`, {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include'
                });

                if (planningResponse.ok) {
                    const planningData = await planningResponse.json();
                    
                    statusMessage.classList.add('hidden');
                    planningContent.classList.remove('hidden');

                    const currentDate = new Date();

                    const eventsData = planningData.map(item => {
                        const startDate = new Date(item.debut);
                        const endDate = item.fin ? new Date(item.fin) : new Date(startDate.getTime() + (60 * 60 * 1000));
                        
                        const isPast = endDate < currentDate;
                        
                        let bgColor = item.couleur || '#1C5B8F';
                        let borderColor = item.couleur || '#154670';
                        let textColor = '#ffffff';

                        if (isPast) {
                            bgColor = '#F3F4F6';
                            borderColor = '#D1D5DB';
                            textColor = '#6B7280';
                        }

                        return {
                            id: item.id,
                            title: item.titre,
                            start: item.debut, 
                            end: item.fin || undefined,
                            backgroundColor: bgColor,
                            borderColor: borderColor,
                            textColor: textColor,
                            extendedProps: {
                                description: item.description || 'Aucune description fournie.',
                                lieu: item.lieu || 'Lieu non spécifié',
                                isPast: isPast
                            }
                        };
                    });

                    const calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'timeGridWeek',
                        slotDuration: '00:15:00',
                        slotLabelInterval: '01:00',
                        eventOverlap: false,
                        slotEventOverlap: false,
                        eventMinHeight: 20,
                        locale: 'fr',
                        slotMinTime: '08:00:00',
                        slotMaxTime: '20:00:00',
                        allDaySlot: false,
                        expandRows: true,
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'timeGridWeek,timeGridDay,dayGridMonth,listMonth' 
                        },
                        events: eventsData,
                        height: 'auto',
                        firstDay: 1, 
                        
                        eventClick: function(info) {
                            const eventObj = info.event;
                            const options = { weekday: 'long', day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit' };
                            
                            document.getElementById('modalTitle').textContent = eventObj.title;
                            
                            document.getElementById('modalStart').textContent = eventObj.start 
                                ? eventObj.start.toLocaleDateString('fr-FR', options).replace(':', 'h') 
                                : 'Inconnu';
                            
                            if (eventObj.end) {
                                document.getElementById('modalEnd').textContent = eventObj.end.toLocaleDateString('fr-FR', options).replace(':', 'h');
                            } else {
                                document.getElementById('modalEnd').textContent = 'Ponctuel (Pas de fin spécifiée)';
                            }
                            
                            document.getElementById('modalLocation').textContent = eventObj.extendedProps.lieu;
                            document.getElementById('modalDescription').textContent = eventObj.extendedProps.description;

                            const statusContainer = document.getElementById('modalStatusContainer');
                            const statusBadge = document.getElementById('modalStatusBadge');

                            statusContainer.classList.remove('hidden');

                            if (eventObj.extendedProps.isPast) {
                                statusBadge.textContent = "Terminé";
                                statusBadge.className = "text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider bg-gray-200 text-gray-700";
                            } else {
                                statusBadge.textContent = "À venir";
                                statusBadge.className = "text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider bg-yellow-100 text-yellow-800";
                            }
                            
                            document.getElementById('eventModal').classList.remove('hidden');
                        }
                    });

                    calendar.render();

                } else {
                    statusMessage.textContent = "Impossible de récupérer votre planning. Veuillez réessayer.";
                    statusMessage.classList.replace("text-gray-500", "text-red-500");
                    statusMessage.classList.remove("animate-pulse");
                }
            } catch (error) {
                statusMessage.textContent = "Erreur de connexion au serveur.";
                statusMessage.classList.replace("text-gray-500", "text-red-500");
                statusMessage.classList.remove("animate-pulse");
            }
        });
    </script>
</body>
</html>