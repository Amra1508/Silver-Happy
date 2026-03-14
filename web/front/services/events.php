<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Alata', 'sans-serif'] } } }
        }
    </script>
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">

    <?php include("../includes/header.php") ?>

    <main class="flex-1 relative">
        <div id="api-message" class="hidden fixed top-24 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-xl p-4 rounded-lg border text-center font-bold shadow-2xl transition-all duration-300"></div>

        <div class="relative h-[400px] w-full overflow-hidden">
            <img src="/front/images/background.webp" alt="" class="absolute inset-0 w-full h-full object-cover opacity-60">
            <div class="absolute inset-0 flex flex-col items-center justify-center px-16 bg-white/30 backdrop-blur-sm">
                <h2 class="big-text text-4xl md:text-5xl leading-tight mb-4 text-[#1C5B8F] font-bold text-center drop-shadow-md">Nos prochains événements</h2>
                <p class="text-xl md:text-2xl text-gray-800 text-center max-w-3xl font-medium">Sorties, conférences, ateliers... Partagez des moments uniques avec la communauté.</p>
            </div>
        </div>

        <div id="my-events-section" class="hidden w-full px-6 md:px-16 mt-12">
            <h2 class="text-2xl font-bold text-[#E1AB2B] mb-6 flex items-center gap-2">
                Mes événements réservés
            </h2>
            <div id="my-events-container" class="flex flex-wrap gap-6 pb-10 border-b border-gray-200">
                </div>
        </div>

        <div class="w-full px-6 md:px-16 mt-12 mb-8 text-center">
            <h2 class="big-text mb-4 text-[#1C5B8F]">L'agenda complet</h2>
            <p class="text-gray-600 max-w-4xl mx-auto">Découvrez le programme et réservez votre place.</p>
        </div>

        <div id="events-container" class="flex flex-wrap gap-8 px-6 md:px-16 py-4 justify-center">
            <div class="w-full text-center py-10"><p class="text-xl text-gray-500 animate-pulse">Chargement de l'agenda...</p></div>
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

        function formatDisplayDate(dateStr) {
            if (!dateStr) return "Date à définir";
            const d = new Date(dateStr);
            if (isNaN(d)) return "Date invalide";
            return d.toLocaleString('fr-FR', {
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
            }).replace(/^\w/, c => c.toUpperCase());
        }

        async function fetchMyEvenements() {
            const userId = window.currentUserId;
            if (!userId) return;

            try {
                const response = await fetch(`${API_BASE}/evenement/user/${userId}`);
                if (!response.ok) return;

                const myEvents = await response.json();
                const section = document.getElementById('my-events-section');
                const container = document.getElementById('my-events-container');
                container.innerHTML = '';

                if (myEvents.length > 0) {
                    section.classList.remove('hidden');
                    myEvents.forEach(e => {
                        const id = e.id_evenement || e.ID;
                        const dateText = formatDisplayDate(e.date_debut);
                        const imgSrc = e.image ? `${API_BASE}/${e.image.replace(/\\/g, '/')}` : 'https://via.placeholder.com/150?text=SH';

                        const card = `
                            <div class="flex items-center bg-white border border-[#E1AB2B] rounded-2xl shadow-sm p-4 w-full md:w-[400px] hover:shadow-md transition">
                                <img src="${imgSrc}" class="w-20 h-20 rounded-xl object-cover mr-4">
                                <div class="flex-1">
                                    <h4 class="font-bold text-[#1C5B8F] text-lg leading-tight line-clamp-1">${e.nom}</h4>
                                    <p class="text-sm text-gray-500 font-semibold mt-1">📅 ${dateText}</p>
                                    <p class="text-sm text-gray-500 mb-2">📍 ${e.lieu}</p>
                                    <button onclick="unregisterEvent(${id})" class="text-sm text-red-500 hover:text-red-700 font-bold transition-colors">
                                        ❌ Se désinscrire
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
                console.error("Impossible de charger les événements personnels", err);
            }
        }

        async function registerEvent(eventId) {
            const userId = window.currentUserId; 

            if (!userId) {
                showAlert("Vous devez être connecté pour vous inscrire.", false);
                setTimeout(() => window.location.href = "/front/account/signin.php", 2000);
                return;
            }

            try {
                const response = await fetch(`${API_BASE}/evenement/register/${eventId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_utilisateur: parseInt(userId) }),
                });

                if (response.ok) {
                    showAlert("🎉 Inscription confirmée ! Votre place est réservée.", true);
                    fetchEvenements(currentPage); 
                    fetchMyEvenements(); 
                } else {
                    const errText = await response.text();
                    showAlert("Erreur : " + errText, false);
                }
            } catch (err) {
                showAlert("Impossible de joindre le serveur.", false);
            }
        }

        async function unregisterEvent(eventId) {
            const userId = window.currentUserId; 
            if (!userId) return;

            if (!confirm("Êtes-vous sûr de vouloir annuler votre inscription à cet événement ?")) {
                return;
            }

            try {
                const response = await fetch(`${API_BASE}/evenement/unregister/${eventId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_utilisateur: parseInt(userId) }),
                });

                if (response.ok) {
                    showAlert("Votre inscription a bien été annulée.", true);
                    fetchEvenements(currentPage); 
                    fetchMyEvenements(); 
                } else {
                    const errText = await response.text();
                    showAlert("Erreur : " + errText, false);
                }
            } catch (err) {
                showAlert("Impossible de joindre le serveur.", false);
            }
        }

        async function fetchEvenements(page = 1) {
            try {
                currentPage = page;
                const response = await fetch(`${API_BASE}/evenement/read?page=${currentPage}&limit=${limit}`);
                if (!response.ok) throw new Error("Erreur de récupération");
                
                const result = await response.json();
                const evenements = result.data || [];
                const container = document.getElementById('events-container');
                container.innerHTML = '';

                if (evenements.length === 0) {
                    container.innerHTML = '<p class="text-xl text-gray-500 py-10 italic">Aucun événement prévu.</p>';
                    return;
                }

                evenements.forEach(e => {
                    const id = e.id_evenement || e.ID;
                    const nom = e.nom || e.Nom || 'Événement sans nom';
                    const description = e.description || e.Description || '';
                    const lieu = e.lieu || e.Lieu || 'Lieu à définir';
                    const places = e.nombre_place !== undefined ? parseInt(e.nombre_place || e.NombrePlace) : 0;
                    const displayDebut = formatDisplayDate(e.date_debut);
                    const imgSrc = e.image ? `${API_BASE}/${e.image.replace(/\\/g, '/')}` : 'https://via.placeholder.com/400x250?text=Silver+Happy';

                    let badgeHTML = places > 0 
                        ? `<span class="bg-[#E1AB2B]/20 text-yellow-800 border border-[#E1AB2B] text-xs px-3 py-1 rounded-full font-bold">Il reste ${places} place(s)</span>`
                        : `<span class="bg-red-100 text-red-700 border border-red-300 text-xs px-3 py-1 rounded-full font-bold">Complet</span>`;
                    
                    let btnHTML = places > 0 
                        ? `<button onclick="registerEvent(${id})" class="w-full rounded-full py-3 px-6 bg-[#1C5B8F] text-white font-bold text-lg mt-4 hover:bg-[#154670] transition-colors">Je m'inscris</button>`
                        : `<button class="w-full rounded-full py-3 px-6 bg-gray-300 text-gray-500 font-bold text-lg mt-4 cursor-not-allowed" disabled>Complet</button>`;

                    container.innerHTML += `
                        <div class="md:max-w-[400px] w-full bg-white border border-gray-200 flex flex-col rounded-[2rem] shadow-lg hover:-translate-y-1 transition-all overflow-hidden">
                            <div class="h-48 w-full overflow-hidden relative">
                                <img src="${imgSrc}" class="w-full h-full object-cover">
                                <div class="absolute top-4 right-4 shadow-md">${badgeHTML}</div>
                            </div>
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="text-2xl text-[#1C5B8F] font-bold mb-2">${nom}</h3>
                                <div class="flex items-center text-sm text-gray-500 mb-1 font-semibold">📅 ${displayDebut}</div>
                                <div class="flex items-center text-sm text-gray-500 mb-4 font-semibold">📍 ${lieu}</div>
                                <p class="text-gray-600 mb-4 flex-grow line-clamp-3">${description}</p>
                                ${btnHTML}
                            </div>
                        </div>
                    `;
                });

                renderPagination(result.totalPages);
            } catch (err) {
                showAlert("Erreur réseau.", false);
            }
        }

        function renderPagination(totalPages) {
            const paginationContainer = document.getElementById('pagination-controls');
            paginationContainer.innerHTML = '';
            if (totalPages <= 1) return;

            const prevDisabled = currentPage === 1 ? 'disabled opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 text-[#1C5B8F]';
            paginationContainer.innerHTML += `<button onclick="fetchEvenements(${currentPage - 1})" class="px-4 py-2 border-2 border-[#1C5B8F] text-[#1C5B8F] rounded-full font-bold transition-colors ${prevDisabled}" ${currentPage === 1 ? 'disabled' : ''}>← Précédent</button>`;
            paginationContainer.innerHTML += `<span class="text-gray-500 font-medium px-4">Page <strong class="text-[#1C5B8F]">${currentPage}</strong> sur ${totalPages}</span>`;
            
            const nextDisabled = currentPage === totalPages ? 'disabled opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 text-[#1C5B8F]';
            paginationContainer.innerHTML += `<button onclick="fetchEvenements(${currentPage + 1})" class="px-4 py-2 border-2 border-[#1C5B8F] text-[#1C5B8F] rounded-full font-bold transition-colors ${nextDisabled}" ${currentPage === totalPages ? 'disabled' : ''}>Suivant →</button>`;
        }

        window.addEventListener('auth_ready', () => {
            fetchMyEvenements();
        });

        window.onload = () => {
            fetchEvenements(1);
            setTimeout(() => { if (window.currentUserId) fetchMyEvenements(); }, 500);
        };
    </script>
</body>
</html>