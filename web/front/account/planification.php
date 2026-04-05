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

            <div class="p-3 flex justify-between items-center mb-6">
                <a href="/front/account/profile.php">
                    <button class="flex items-center rounded-full px-6 py-2 bg-[#1C5B8F] text-white font-bold hover:bg-[#154670] transition">
                        <img src="/front/icons/fleche_gauche.svg" alt="fleche" class="w-7 h-7 mr-2"> Revenir a mon profil
                    </button>
                </a>
            </div>
            
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-4xl font-bold text-[#1C5B8F]">
                    Mon Calendrier
                </h2>
            </div>

            <div id="no-sub-container" class="hidden flex flex-col items-center justify-center py-20 rounded-[2.5rem] shadow-xl shadow-blue-900/10 bg-white">
                <p class="text-center font-semibold text-[#1C5B8F] text-2xl mb-8 px-4">
                    Vous devez être abonné(e) pour consulter votre planning d'activités.
                </p>
                <a class="rounded-full px-8 py-3 bg-[#1C5B8F] text-white font-bold text-lg hover:bg-[#154670] transition" href="/front/services/subscription.php">
                    Je m'abonne
                </a>
            </div>

            <div id="planning-content" class="hidden">
                <div class="flex gap-6 mb-8 bg-white p-4 rounded-xl shadow-sm border border-gray-100 inline-flex">
                    <span class="flex items-center text-sm font-bold text-gray-700">
                        <div class="w-5 h-5 rounded-full bg-[#E1AB2B] mr-3 shadow-inner"></div> Événements
                    </span>
                    <span class="flex items-center text-sm font-bold text-gray-700">
                        <div class="w-5 h-5 rounded-full bg-[#1C5B8F] mr-3 shadow-inner"></div> Prestations de services
                    </span>
                </div>

                <div id="calendar" class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 min-h-[600px]"></div>
            </div>

            <div id="status_message" class="text-center text-xl text-gray-500 font-bold py-20 animate-pulse">
                Chargement de votre planning...
            </div>

        </div>
    </main>

    <?php include("../includes/footer.php") ?>

    <div id="eventModal" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white rounded-[2rem] p-8 max-w-md w-full mx-4 shadow-2xl transform transition-all">
            <h3 id="modalTitle" class="text-2xl font-bold text-[#1C5B8F] mb-6 border-b pb-4"></h3>
            <div class="space-y-4 text-gray-700">
                <p class="flex items-center"><span class="text-xl mr-3">📅</span> <strong class="w-20">Début :</strong> <span id="modalStart" class="font-medium"></span></p>
                <p class="flex items-center"><span class="text-xl mr-3">🏁</span> <strong class="w-20">Fin :</strong> <span id="modalEnd" class="font-medium"></span></p>
                <p class="flex items-start"><span class="text-xl mr-3">📍</span> <strong class="w-20">Lieu :</strong> <span id="modalLocation" class="font-medium"></span></p>
                <div class="pt-4 border-t mt-6">
                    <p class="text-sm text-gray-600 font-medium leading-relaxed" id="modalDescription"></p>
                </div>
            </div>
            <div class="mt-8 flex justify-center">
                <button onclick="document.getElementById('eventModal').classList.add('hidden')" class="px-8 py-3 bg-gray-100 text-gray-700 rounded-full font-bold hover:bg-gray-200 transition-colors w-full">
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

                    const eventsData = planningData.map(item => ({
                        id: item.id,
                        title: item.titre,
                        start: item.debut, 
                        end: item.fin || undefined,
                        backgroundColor: item.couleur,
                        borderColor: item.couleur,
                        extendedProps: {
                            description: item.description || 'Aucune description fournie.',
                            lieu: item.lieu || 'Lieu non spécifié' 
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