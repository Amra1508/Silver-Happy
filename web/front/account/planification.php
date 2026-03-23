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
        
        :root {
            --fc-button-bg-color: #1C5B8F;
            --fc-button-border-color: #1C5B8F;
            --fc-button-hover-bg-color: #154670;
            --fc-button-hover-border-color: #154670;
            --fc-button-active-bg-color: #154670;
            --fc-button-active-border-color: #154670;
        }
        
        .fc-toolbar-title {
            text-transform: capitalize;
            color: #1C5B8F;
        }

        .fc-event {
            cursor: pointer;
            padding: 2px 4px;
            font-weight: bold;
            border-radius: 4px;
            color: white !important;
        }
    </style>

    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Alata', 'sans-serif'] } } }
        }
    </script>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">

    <?php include("../includes/header.php") ?>

    <main class="flex-grow">
        <div class="max-w-6xl mx-auto px-6 pt-10 pb-16">

            <div class="max-w-6xl mx-auto px-6 pt-10 pb-16">
                <div class="p-3 flex justify-between items-center mx-8">
                <a href="/front/services/menu_activity.php">
                    <button class="flex items-center rounded-full px-6 button-blue">
                        <img src="/front/icons/fleche_gauche.svg" alt="fleche" class="w-7 h-7 mr-2"> Revenir à la liste
                    </button>
                </a>
            </div>
            
            <div class="flex items-center justify-between mb-10">
                <h2 class="text-3xl font-semibold text-[#1C5B8F]">
                    Mon Calendrier
                </h2>
            </div>

            <div class="flex gap-4 mb-6">
                <span class="flex items-center text-sm font-bold text-gray-600"><div class="w-4 h-4 rounded-full bg-[#E1AB2B] mr-2"></div> Événements</span>
                <span class="flex items-center text-sm font-bold text-gray-600"><div class="w-4 h-4 rounded-full bg-[#1C5B8F] mr-2"></div> Services (RDV)</span>
            </div>

            <div id="status_message" class="text-center text-gray-500 font-bold py-10">
                Chargement de votre planning...
            </div>

            <div id="calendar" class="hidden bg-white p-6 rounded-2xl shadow-sm border border-gray-200 min-h-[600px]"></div>

        </div>
    </main>

    <?php include("../includes/footer.php") ?>

    <div id="eventModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-xl">
            <h3 id="modalTitle" class="text-2xl font-bold text-[#1C5B8F] mb-4"></h3>
            <div class="space-y-3 text-gray-700">
                <p><strong>Début :</strong> <span id="modalStart"></span></p>
                <p><strong>Fin :</strong> <span id="modalEnd"></span></p>
                <p><strong>Lieu :</strong> <span id="modalLocation"></span></p>
                <p class="pt-2 border-t text-sm mt-4" id="modalDescription"></p>
            </div>
            <div class="mt-6 flex justify-end">
                <button onclick="document.getElementById('eventModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-full font-bold hover:bg-gray-300 transition-colors">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const statusMessage = document.getElementById('status_message');
            const calendarEl = document.getElementById('calendar');

            try {
                const authResponse = await fetch('http://localhost:8082/auth/me', {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include'
                });

                if (!authResponse.ok) {
                    window.location.href = "/front/account/signin.php";
                    return;
                }

                const user = await authResponse.json();
                const userId = user.id_utilisateur || user.id; 

                const planningResponse = await fetch(`http://localhost:8082/planning?user_id=${userId}`, {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include'
                });

                if (planningResponse.ok) {
                    const planningData = await planningResponse.json();
                    
                    statusMessage.classList.add('hidden');
                    calendarEl.classList.remove('hidden');

                    const eventsData = planningData.map(item => ({
                        id: item.id,
                        title: item.title,
                        start: item.start,
                        end: item.end || undefined,
                        backgroundColor: item.color,
                        borderColor: item.color,
                        extendedProps: {
                            description: item.description || 'Aucune description.',
                            lieu: item.location || 'Non spécifié'
                        }
                    }));

                    const calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        locale: 'fr',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay' 
                        },
                        events: eventsData, 
                        height: 'auto',
                        firstDay: 1,
                        
                        eventClick: function(info) {
                            const eventObj = info.event;
                            const options = { weekday: 'long', day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit' };
                            
                            document.getElementById('modalTitle').textContent = eventObj.title;
                            document.getElementById('modalStart').textContent = eventObj.start ? eventObj.start.toLocaleDateString('fr-FR', options).replace(':', 'h') : 'Inconnu';
                            
                            if (eventObj.end) {
                                document.getElementById('modalEnd').textContent = eventObj.end.toLocaleDateString('fr-FR', options).replace(':', 'h');
                            } else {
                                document.getElementById('modalEnd').textContent = 'Non spécifiée (ou rendez-vous ponctuel)';
                            }
                            
                            document.getElementById('modalLocation').textContent = eventObj.extendedProps.lieu;
                            document.getElementById('modalDescription').textContent = eventObj.extendedProps.description;
                            
                            document.getElementById('eventModal').classList.remove('hidden');
                        }
                    });

                    calendar.render();

                } else {
                    statusMessage.textContent = "Impossible de récupérer le planning. Veuillez réessayer.";
                    statusMessage.classList.add("text-red-500");
                }
            } catch (error) {
                console.error("Erreur de récupération :", error);
                statusMessage.textContent = "Erreur de connexion au serveur.";
                statusMessage.classList.add("text-red-500");
            }
        });
    </script>
</body>
</html>