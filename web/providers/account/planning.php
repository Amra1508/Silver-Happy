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
        
        .fc-theme-standard .fc-scrollgrid { border-color: #f3f4f6; }
        .fc-header-toolbar { margin-bottom: 2rem !important; }
        .fc-button-primary { background-color: #1C5B8F !important; border-color: #1C5B8F !important; border-radius: 0.5rem !important; }
        .fc-button-primary:hover { background-color: #154670 !important; }
        .fc-button-active { background-color: #E1AB2B !important; border-color: #E1AB2B !important; }
    </style>

    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Alata', 'sans-serif'] } } }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="flex min-h-screen relative">

        <?php include("../includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto relative">
            

            <main class="p-8">
                <div class="max-w-7xl mx-auto">
                    
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
                        <div>
                            <h1 class="text-3xl font-semibold text-[#1C5B8F]">Mon Planning</h1>
                            <p class="text-gray-500 mt-1">Gérez vos disponibilités et consultez votre historique d'interventions.</p>
                        </div>
                    </div>

                    <div id="status_message" class="py-20 text-center text-xl text-gray-500 font-bold animate-pulse bg-white rounded-3xl border border-gray-100 shadow-sm">
                        Chargement de votre planning...
                    </div>

                    <div id="no-sub-container" class="hidden flex-col items-center justify-center py-20 bg-white rounded-3xl border border-gray-100 shadow-sm fade-in">
                        <div class="text-yellow-500 text-6xl mb-4">⏳</div>
                        <p class="text-center font-bold text-gray-700 text-xl mb-2">
                            Votre compte est en attente de validation.
                        </p>
                        <p class="text-gray-500 text-center px-4 max-w-md">
                            Vous pourrez consulter votre planning et recevoir des missions une fois que nos administrateurs auront validé votre profil.
                        </p>
                    </div>

                    <div id="planning-content" class="hidden fade-in">
                        
                        <div class="flex flex-wrap gap-4 mb-6">
                            <span class="flex items-center text-sm font-bold text-[#1C5B8F] bg-yellow-50 px-4 py-2 rounded-xl border border-yellow-200">
                                <div class="w-3 h-3 rounded-full bg-[#E1AB2B] mr-2"></div> À venir
                            </span>
                            <span class="flex items-center text-sm font-bold text-gray-600 bg-gray-50 px-4 py-2 rounded-xl border border-gray-200">
                                <div class="w-3 h-3 rounded-full bg-gray-400 mr-2"></div> Terminé
                            </span>
                        </div>

                        <div id="calendar" class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 min-h-[600px]"></div>
                    </div>

                </div>
            </main>
        </div>

        <div id="eventModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 fade-in">
            <div class="bg-white rounded-3xl shadow-xl w-full max-w-md overflow-hidden flex flex-col">
                <div class="bg-[#1C5B8F] px-6 py-4 flex justify-between items-center text-white shrink-0">
                    <h3 id="modalTitle" class="text-xl font-bold truncate pr-4"></h3>
                    <button onclick="document.getElementById('eventModal').classList.add('hidden')" class="text-white hover:text-red-300 transition-colors text-2xl leading-none">&times;</button>
                </div>
                <div class="p-6 space-y-4">
                    <div id="modalStatusContainer" class="hidden mb-2">
                        <span id="modalStatusBadge" class="text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider"></span>
                    </div>
                    <div class="flex items-center text-gray-700 bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <span class="text-xl mr-3">📅</span>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase">Début</p>
                            <p id="modalStart" class="font-bold text-[#1C5B8F]"></p>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-700 bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <span class="text-xl mr-3">🏁</span>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase">Fin</p>
                            <p id="modalEnd" class="font-bold text-[#1C5B8F]"></p>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-700 bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <span class="text-xl mr-3">📍</span>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 font-semibold uppercase">Lieu</p>
                            <p id="modalLocation" class="font-bold text-gray-800 truncate"></p>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-700 bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <span class="text-xl mr-3">🎟️</span>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase">Places limitées</p>
                            <p id="modalPlaces" class="font-bold text-gray-800"></p>
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

    </div>

    <script>
        const API_URL = window.API_BASE_URL || "http://localhost:8082";
        const ROUTE_PLANNING = "/prestataire/planning"; 
        
        let calendarInstance = null;

        async function loadPlanning() {
            const statusMessage = document.getElementById('status_message');
            const calendarEl = document.getElementById('calendar');
            const planningContent = document.getElementById('planning-content');

            statusMessage.classList.remove('hidden');
            planningContent.classList.add('opacity-50');

            try {
                const planningResponse = await fetch(`${API_URL}${ROUTE_PLANNING}`, {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include'
                });

                if (planningResponse.ok) {
                    const result = await planningResponse.json();
                    const planningData = result.data || [];
                    
                    statusMessage.classList.add('hidden');
                    planningContent.classList.remove('hidden', 'opacity-50');

                    const currentDate = new Date();

                    const eventsData = planningData.map(item => {
                        const startStr = item.date_debut.replace(' ', 'T');
                        const endStr = item.date_fin ? item.date_fin.replace(' ', 'T') : null;
                        
                        const startDate = new Date(startStr);
                        const endDate = endStr ? new Date(endStr) : new Date(startDate.getTime() + (2 * 3600 * 1000));
                        
                        const isPast = endDate < currentDate;

                        return {
                            id: item.id_evenement,
                            title: item.nom,
                            start: startStr, 
                            end: endStr || undefined,
                            backgroundColor: isPast ? '#9CA3AF' : '#E1AB2B', 
                            borderColor: isPast ? '#9CA3AF' : '#E1AB2B',
                            textColor: isPast ? '#ffffff' : '#1C5B8F', 
                            extendedProps: {
                                lieu: item.lieu || 'Non spécifié',
                                places: item.nombre_place || 0,
                                isPast: isPast
                            }
                        };
                    });

                    if (calendarInstance !== null) {
                        calendarInstance.destroy();
                    }

                    calendarInstance = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        locale: 'fr',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth' 
                        },
                        events: eventsData,
                        height: 'auto',
                        firstDay: 1, 
                        
                        eventClick: function(info) {
                            const evt = info.event;
                            const options = { weekday: 'long', day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit' };
                            
                            document.getElementById('modalTitle').textContent = evt.title;
                            
                            document.getElementById('modalStart').textContent = evt.start 
                                ? evt.start.toLocaleDateString('fr-FR', options).replace(':', 'h') 
                                : 'Non définie';
                            
                            document.getElementById('modalEnd').textContent = evt.end 
                                ? evt.end.toLocaleDateString('fr-FR', options).replace(':', 'h')
                                : 'Ponctuel';
                            
                            document.getElementById('modalLocation').textContent = evt.extendedProps.lieu;
                            document.getElementById('modalPlaces').textContent = evt.extendedProps.places + ' personnes max';
                            
                            const statusContainer = document.getElementById('modalStatusContainer');
                            const statusBadge = document.getElementById('modalStatusBadge');
                            
                            statusContainer.classList.remove('hidden');
                            if (evt.extendedProps.isPast) {
                                statusBadge.textContent = "Terminé";
                                statusBadge.className = "text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider bg-gray-200 text-gray-700";
                            } else {
                                statusBadge.textContent = "À venir";
                                statusBadge.className = "text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider bg-yellow-100 text-yellow-800";
                            }

                            document.getElementById('eventModal').classList.remove('hidden');
                        }
                    });

                    calendarInstance.render();

                } else {
                    statusMessage.textContent = "Impossible de charger le planning.";
                    statusMessage.classList.replace("text-gray-500", "text-red-500");
                    statusMessage.classList.remove("animate-pulse");
                }
            } catch (error) {
                statusMessage.textContent = "Erreur de connexion au serveur.";
                statusMessage.classList.replace("text-gray-500", "text-red-500");
                statusMessage.classList.remove("animate-pulse");
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            const statusMessage = document.getElementById('status_message');
            const noSubContainer = document.getElementById('no-sub-container');

            try {
                const authResponse = await fetch(`${API_URL}/auth/me-provider`, {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include'
                });

                if (!authResponse.ok) {
                    window.location.href = "/providers/account/signin.php";
                    return;
                }

                const user = await authResponse.json();
                
                if (!user.status || (user.status.toLowerCase() !== 'validé' && user.status.toLowerCase() !== 'valide')) {
                    statusMessage.classList.add('hidden');
                    noSubContainer.classList.remove('hidden');
                    noSubContainer.classList.add('flex'); 
                    return;
                }

                loadPlanning();

            } catch (error) {
                statusMessage.textContent = "Erreur de vérification du compte.";
                statusMessage.classList.replace("text-gray-500", "text-red-500");
                statusMessage.classList.remove("animate-pulse");
            }
        });
    </script>
</body>
</html>