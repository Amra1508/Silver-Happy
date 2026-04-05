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

        <div id="my-events-section" class="hidden w-full px-6 md:px-16 mt-12">
            <h2 class="text-2xl font-bold text-[#E1AB2B] mb-6 flex items-center gap-2">
                Mes événements réservés
            </h2>
            <div id="my-events-container" class="flex flex-wrap gap-6 pb-10 border-b border-gray-200">
            </div>
        </div>

        <div class="w-full px-6 md:px-16 mt-12 mb-8 text-center">
            <h2 class="big-text mb-4 text-[#1C5B8F] text-4xl font-bold">L'agenda complet</h2>
            <p class="text-gray-600 max-w-4xl mx-auto mb-6">Découvrez le programme et réservez votre place.</p>

            <div class="max-w-xs mx-auto">
                <select id="filter-category" class="w-full p-3 border border-gray-300 text-gray-700 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-[#1C5B8F]" onchange="fetchEvenements(1)">
                    <option value="">Toutes les catégories</option>
                </select>
            </div>
        </div>

        <div id="events-container" class="flex flex-wrap gap-8 px-6 md:px-16 py-4 justify-center">
            <div class="w-full text-center py-10">
                <p class="text-xl text-gray-500 animate-pulse">Chargement de l'agenda...</p>
            </div>
        </div>

        <div id="pagination-controls" class="flex justify-center items-center gap-4 pb-16 mt-4"></div>

    </main>

    <?php include("../includes/footer.php") ?>

    <script>
        const API_BASE = window.API_BASE_URL;
        let currentPage = 1;
        const limit = 6;
        const messageBox = document.getElementById('api-message');

        let categoriesData = [];

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
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).replace(/^\w/, c => c.toUpperCase());
        }

        function getTimeRemaining(dateStr) {
            if (!dateStr) return {
                isPast: false,
                text: "Date inconnue"
            };

            const now = new Date();
            const evDate = new Date(dateStr);
            if (isNaN(evDate)) return {
                isPast: false,
                text: ""
            };

            const diffMs = evDate - now;

            if (diffMs < 0) {
                return {
                    isPast: true,
                    text: "Événement terminé"
                };
            }

            const days = Math.floor(diffMs / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

            let timeParts = [];
            if (days > 0) timeParts.push(`${days}j`);
            if (hours > 0) timeParts.push(`${hours}h`);
            if (days === 0 && hours === 0 && minutes > 0) timeParts.push(`${minutes}m`);

            return {
                isPast: false,
                text: timeParts.length > 0 ? `⏳ Dans ${timeParts.join(' ')}` : "⏳ Commence bientôt"
            };
        }

        async function fetchCategories() {
            try {
                const response = await fetch(`${API_BASE}/categorie/read`);
                if (!response.ok) return;
                const result = await response.json();
                categoriesData = result.data || result || [];

                const select = document.getElementById('filter-category');
                categoriesData.forEach(cat => {
                    const id = cat.id_categorie || cat.id || cat.ID;
                    const nom = cat.nom || cat.Nom;
                    select.innerHTML += `<option value="${id}">${nom}</option>`;
                });
            } catch (err) {
                console.error("Erreur de chargement des catégories:", err);
            }
        }

        function getCategoryName(id) {
            if (!id) return null;
            const cat = categoriesData.find(c => (c.id_categorie || c.id || c.ID) == id);
            return cat ? (cat.nom || cat.Nom) : null;
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
                        const timeStatus = getTimeRemaining(e.date_debut);
                        const imgSrc = e.image ? `${API_BASE}/${e.image.replace(/\\/g, '/')}` : 'https://via.placeholder.com/150?text=SH';

                        const card = `
                            <div class="flex items-center bg-white border border-[#E1AB2B] rounded-2xl shadow-sm p-4 w-full md:w-[400px] hover:shadow-md transition ${timeStatus.isPast ? 'opacity-75 bg-gray-50' : ''}">
                                <img src="${imgSrc}" class="w-20 h-20 rounded-xl object-cover mr-4 ${timeStatus.isPast ? 'grayscale' : ''}">
                                <div class="flex-1">
                                    <h4 class="font-bold text-[#1C5B8F] text-lg leading-tight line-clamp-1">${e.nom}</h4>
                                    <p class="text-sm text-gray-500 font-semibold mt-1">📅 ${dateText}</p>
                                    <p class="text-sm ${timeStatus.isPast ? 'text-gray-400' : 'text-[#E1AB2B]'} font-bold mb-1">${timeStatus.text}</p>
                                    <p class="text-sm text-gray-500 mb-2">📍 ${e.lieu}</p>
                                    ${timeStatus.isPast 
                                        ? `<span class="text-sm text-gray-400 italic">Désinscription indisponible</span>` 
                                        : `<button onclick="unregisterEvent(${id})" class="text-sm text-red-500 hover:text-red-700 font-bold transition-colors">❌ Se désinscrire</button>`
                                    }
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

        async function unregisterEvent(eventId) {
            const userId = window.currentUserId;
            if (!userId) return;

            if (!confirm("Êtes-vous sûr de vouloir annuler votre inscription à cet événement ?")) {
                return;
            }

            try {
                const response = await fetch(`${API_BASE}/evenement/unregister/${eventId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_utilisateur: parseInt(userId)
                    }),
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

        async function registerEvent(eventId) {
            const userId = window.currentUserId;

            if (!userId) {
                showAlert("Vous devez être connecté pour vous inscrire.", false);
                setTimeout(() => window.location.href = "/front/account/signin.php?redirect=" + encodeURIComponent(window.location.href), 2000);
                return;
            }

            const user = window.userData;
            const hasSubscription = user && user.id_abonnement && user.id_abonnement > 0;

            if (!hasSubscription) {
                showAlert("Vous devez posséder un abonnement pour participer à cet événement.", false);
                setTimeout(() => window.location.href = "/front/services/subscription.php", 3000);
                return;
            }

            try {
                const response = await fetch(`${API_BASE}/evenement/checkout/${eventId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_utilisateur: parseInt(userId)
                    }),
                });

                if (response.status === 409) {
                    showAlert("Vous êtes déjà inscrit à un autre événement sur ce créneau horaire.", false);
                    return;
                }

                if (!response.ok) {
                    const errText = await response.text();
                    showAlert("Erreur : " + errText, false);
                    return;
                }

                const data = await response.json();

                if (data.isFree) {
                    showAlert("Inscription gratuite confirmée !", true);
                    fetchEvenements(currentPage);
                    fetchMyEvenements();
                } else if (data.url) {
                    window.location.href = data.url;
                } else {
                    showAlert("Erreur lors de la génération du paiement.", false);
                }

            } catch (err) {
                showAlert("Impossible de joindre le serveur.", false);
            }
        }

        async function fetchEvenements(page = 1) {
            try {
                currentPage = page;
                const categoryId = document.getElementById('filter-category').value;

                let url = `${API_BASE}/evenement/read?page=${currentPage}&limit=${limit}`;

                if (categoryId) {
                    url = `${API_BASE}/evenement/filter?categorie=${categoryId}`;
                }

                const response = await fetch(url);
                if (!response.ok) throw new Error("Erreur de récupération");

                const result = await response.json();

                const evenementsAPI = Array.isArray(result) ? result : (result.data || []);

                const evenements = evenementsAPI.filter(e => {
                    if (!e.date_debut) return true;
                    return new Date(e.date_debut) >= new Date();
                });

                const container = document.getElementById('events-container');
                container.innerHTML = '';

                if (evenements.length === 0) {
                    container.innerHTML = '<p class="text-xl text-gray-500 py-10 italic">Aucun événement prévu dans cette catégorie.</p>';
                    renderPagination(Array.isArray(result) ? 0 : (result.totalPages || 0));
                    return;
                }

                evenements.forEach(e => {
                    const id = e.id_evenement || e.ID;
                    const nom = e.nom || e.Nom || 'Événement sans nom';
                    const description = e.description || e.Description || '';
                    const lieu = e.lieu || e.Lieu || 'Lieu à définir';
                    const places = e.nombre_place !== undefined ? parseInt(e.nombre_place || e.NombrePlace) : 0;
                    const displayDebut = formatDisplayDate(e.date_debut);
                    const timeStatus = getTimeRemaining(e.date_debut);
                    const imgSrc = e.image ? `${API_BASE}/${e.image.replace(/\\/g, '/')}` : 'https://via.placeholder.com/400x250?text=Silver+Happy';

                    const catName = getCategoryName(e.id_categorie || e.IDCategorie);
                    const catBadge = catName ? `<span class="text-xs bg-[#1C5B8F]/10 text-[#1C5B8F] px-3 py-1 rounded-full mb-3 inline-block font-bold border border-[#1C5B8F]/20">${catName}</span>` : '';

                    const prix = parseFloat(e.prix || e.Prix || 0);
                    const displayPrix = prix > 0 ? `${prix.toFixed(2)} €` : 'Gratuit';
                    const prixBadge = `<span class="text-xs ${prix > 0 ? 'bg-green-100 text-green-800 border-green-200' : 'bg-gray-100 text-gray-800 border-gray-200'} px-3 py-1 rounded-full mb-3 inline-block font-bold border ml-2">${displayPrix}</span>`;

                    let badgeHTML = places > 0 ?
                        `<span class="bg-[#E1AB2B]/90 text-white shadow-md text-sm px-4 py-2 rounded-full font-bold backdrop-blur-sm">Il reste ${places} place(s)</span>` :
                        `<span class="bg-red-500/90 text-white shadow-md text-sm px-4 py-2 rounded-full font-bold backdrop-blur-sm">Complet</span>`;

                    let btnHTML = places > 0 ?
                        `<button onclick="registerEvent(${id})" class="w-full rounded-full py-3 px-6 bg-[#1C5B8F] text-white font-bold text-lg mt-4 hover:bg-[#154670] transition-colors shadow-md">Je m'inscris</button>` :
                        `<button class="w-full rounded-full py-3 px-6 bg-gray-200 text-gray-500 font-bold text-lg mt-4 cursor-not-allowed border border-gray-300" disabled>Complet</button>`;

                    container.innerHTML += `
                        <div class="md:max-w-[400px] w-full bg-white border border-gray-200 flex flex-col rounded-[2rem] shadow-lg hover:-translate-y-2 transition-all duration-300 overflow-hidden">
                            <div class="h-56 w-full overflow-hidden relative">
                                <img src="${imgSrc}" class="w-full h-full object-cover">
                                <div class="absolute top-4 right-4">${badgeHTML}</div>
                            </div>
                            <div class="p-6 flex flex-col flex-grow">
                                ${catBadge}
                                ${prixBadge}
                                <h3 class="text-2xl text-[#1C5B8F] font-bold mb-3">${nom}</h3>
                                <div class="flex items-center text-sm text-gray-600 mb-2 font-semibold">📅 ${displayDebut}</div>
                                <div class="flex items-center text-sm text-[#E1AB2B] mb-2 font-bold">${timeStatus.text}</div>
                                <div class="flex items-center text-sm text-gray-500 mb-4 font-semibold">📍 ${lieu}</div>
                                <p class="text-gray-600 mb-4 flex-grow line-clamp-3 leading-relaxed">${description}</p>
                                ${btnHTML}
                            </div>
                        </div>
                    `;
                });

                if (Array.isArray(result) && !result.totalPages) {
                    renderPagination(0);
                } else {
                    renderPagination(result.totalPages);
                }

            } catch (err) {
                console.error(err);
                showAlert("Erreur réseau.", false);
            }
        }

        function renderPagination(totalPages) {
            const paginationContainer = document.getElementById('pagination-controls');
            paginationContainer.innerHTML = '';
            if (!totalPages || totalPages <= 1) return;

            const prevDisabled = currentPage === 1 ? 'disabled opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 text-[#1C5B8F]';
            paginationContainer.innerHTML += `<button onclick="fetchEvenements(${currentPage - 1})" class="px-4 py-2 border-2 border-[#1C5B8F] text-[#1C5B8F] rounded-full font-bold transition-colors ${prevDisabled}" ${currentPage === 1 ? 'disabled' : ''}>← Précédent</button>`;

            paginationContainer.innerHTML += `<span class="text-gray-500 font-medium px-4">Page <strong class="text-[#1C5B8F] text-lg">${currentPage}</strong> sur ${totalPages}</span>`;

            const nextDisabled = currentPage === totalPages ? 'disabled opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 text-[#1C5B8F]';
            paginationContainer.innerHTML += `<button onclick="fetchEvenements(${currentPage + 1})" class="px-4 py-2 border-2 border-[#1C5B8F] text-[#1C5B8F] rounded-full font-bold transition-colors ${nextDisabled}" ${currentPage === totalPages ? 'disabled' : ''}>Suivant →</button>`;
        }

        window.addEventListener('auth_ready', () => {
            fetchMyEvenements();
        });

        window.onload = async () => {
            await fetchCategories();
            fetchEvenements(1);

            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.get('success') === 'inscription_validee') {
                showAlert("Paiement réussi ! Votre inscription est validée.", true);
            } else if (urlParams.get('error') === 'paiement_echoue') {
                showAlert("Le paiement a échoué ou a été annulé.", false);
            }

            setTimeout(() => {
                if (window.currentUserId) fetchMyEvenements();
            }, 500);
        };
    </script>
</body>

</html>