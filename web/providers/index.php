<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    </script>
</head>

<body class="bg-gray-50 text-gray-800">

    <div class="flex min-h-screen">

        <?php include("includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto">

            <main class="p-8">

                <div id="main-content-valide" class="hidden space-y-8 max-w-7xl mx-auto">

                    <div class="flex justify-between items-end">
                        <div>
                            <h1 class="text-3xl font-semibold text-[#1C5B8F]">Vue d'ensemble</h1>
                            <p class="text-gray-500 mt-1" id="welcome-text">Bienvenue dans votre espace de gestion.</p>
                        </div>
                        <a href="/providers/services/events.php" class="bg-[#E1AB2B] hover:bg-yellow-500 text-[#1C5B8F] font-bold py-2 px-6 rounded-xl shadow-sm transition-colors flex items-center gap-2">
                            Nouvelle Prestation
                        </a>
                    </div>

                    <div id="banner-boost" class="hidden bg-[#1C5B8F] rounded-3xl p-8 text-white flex flex-col lg:flex-row items-center justify-between shadow-lg relative overflow-hidden mt-2 mb-2">
                        <div class="relative z-10 mb-6 lg:mb-0 text-center lg:text-left">
                            <h2 class="text-2xl font-bold mb-2">Manque de visibilité ?</h2>
                            <p class="text-blue-100 text-sm">Démarquez-vous dans l'Espace Senior et obtenez plus de réservations.</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 relative z-10">
                            <button id="btn-boost-services" onclick="acheterBoost('services')" class="hidden bg-[#E1AB2B] hover:bg-yellow-500 text-[#1C5B8F] font-bold py-3 px-5 rounded-xl transition-all shadow-md flex items-center justify-center gap-2 text-sm">
                                Booster mes services (10€)
                            </button>
                            <button id="btn-boost-profil" onclick="acheterBoost('profil')" class="hidden bg-purple-500 hover:bg-purple-600 text-white font-bold py-3 px-5 rounded-xl transition-all shadow-md flex items-center justify-center gap-2 text-sm border border-purple-400">
                                Booster mon profil (15€)
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                            <div class="bg-blue-100 p-4 rounded-full text-[#1C5B8F]"></div>
                            <div>
                                <p class="text-sm text-gray-500 font-semibold">Prochaines interventions</p>
                                <p class="text-2xl font-bold text-gray-800" id="stat-events-count">-</p>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                            <div class="bg-yellow-100 p-4 rounded-full text-[#E1AB2B]"></div>
                            <div>
                                <p class="text-sm text-gray-500 font-semibold">Nouveaux messages</p>
                                <p id="messages" class="text-2xl font-bold text-gray-800"></p>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                            <div class="bg-green-100 p-4 rounded-full text-green-600"></div>
                            <div>
                                <p class="text-sm text-gray-500 font-semibold">Note moyenne</p>
                                <p id="note" class="text-2xl font-bold text-gray-800">
                                    <span id="note-valeur">--</span>
                                    <span class="text-sm font-normal text-gray-400">/5</span>
                                </p>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                            <div class="bg-purple-100 p-4 rounded-full text-purple-600"></div>
                            <div>
                                <p class="text-sm text-gray-500 font-semibold">Votre Tarif (Moy)</p>
                                <p class="text-2xl font-bold text-gray-800" id="stat-tarif">- € <span class="text-sm font-normal text-gray-400">/h</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-[#1C5B8F]">Vos revenus (30 derniers jours)</h2>
                            <span class="text-sm text-gray-500 italic">Net (99%) de vos prestations</span>
                        </div>
                        <div class="relative w-full h-72">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                        <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-xl font-bold text-[#1C5B8F]">Vos prochaines interventions</h2>
                                <a href="/providers/account/planning.php" class="text-sm text-[#E1AB2B] font-semibold hover:underline">Voir tout le planning</a>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="text-gray-400 text-sm border-b border-gray-100">
                                            <th class="pb-3 font-medium">Date & Heure</th>
                                            <th class="pb-3 font-medium">Prestation</th>
                                            <th class="pb-3 font-medium">Lieu</th>
                                            <th class="pb-3 font-medium">Places</th>
                                        </tr>
                                    </thead>
                                    <tbody id="events-table-body">
                                        <tr>
                                            <td colspan="4" class="py-6 text-center text-gray-500 text-sm">Chargement du planning...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-[#1C5B8F] to-blue-800 rounded-3xl shadow-md p-8 text-white flex flex-col justify-between">
                            <div>
                                <h2 class="text-xl font-bold mb-2">Besoin d'aide ?</h2>
                                <p class="text-blue-100 text-sm mb-6">L'équipe Silver Happy est disponible pour vous accompagner dans vos démarches ou en cas d'urgence avec un senior.</p>

                                <div class="space-y-3">
                                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to=contact@silver-happy.fr" target="_blank" class="w-full bg-white/10 hover:bg-white/20 transition-colors py-3 px-4 rounded-xl flex items-center justify-between text-sm font-semibold">
                                        Nous contacter
                                    </a>
                                    <button class="w-full bg-white/10 hover:bg-white/20 transition-colors py-3 px-4 rounded-xl flex items-center justify-between text-sm font-semibold">
                                        Consulter la FAQ Pro
                                    </button>
                                </div>
                            </div>
                            <div class="mt-8 pt-6 border-t border-white/20">
                                <p class="text-xs text-blue-200">Connecté en tant que prestataire vérifié.</p>
                            </div>
                        </div>

                    </div>
                </div>

                <div id="main-content-attente" class="hidden max-w-3xl mx-auto mt-10">
                    <div class="bg-white p-10 rounded-[2.5rem] shadow-md border-t-4 border-[#E1AB2B] text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-yellow-100 mb-6"></div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-4">Votre compte est en cours d'examen</h1>
                        <p class="text-gray-600 mb-6">
                            Merci d'avoir rejoint SilverHappy. Notre équipe vérifie actuellement vos documents.
                            Cette étape est nécessaire pour garantir la sécurité de nos aînés.
                        </p>
                        <p class="text-sm text-gray-500">
                            Vous recevrez un e-mail dès que votre profil sera validé.
                        </p>
                    </div>
                </div>

                <div id="main-content-refuse" class="hidden max-w-3xl mx-auto mt-10">
                    <div class="bg-white p-10 rounded-[2.5rem] shadow-md border-t-4 border-red-500 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-6"></div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-4">Demande refusée</h1>
                        <p class="text-gray-600 mb-2">Malheureusement, votre demande d'inscription n'a pas pu être validée.</p>
                        <p id="motif-refus-text" class="text-red-600 font-semibold mb-6"></p>
                        <a href="mailto:contact@silverhappy.fr" class="text-[#1C5B8F] underline font-semibold">Contacter le support</a>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        const API_BASE = window.API_BASE_URL;

        window.addEventListener('auth_ready', () => {
            providerId = window.currentUserId;
            noteMoyenne();
            setInterval(noteMoyenne, 2000);
            countMessages();
            setInterval(countMessages, 2000);
        });

        async function noteMoyenne() {
            try {
                const response = await fetch(`${API_BASE}/prestataire/${providerId}/note-moyenne`);
                const stats = await response.json();

                const noteElement = document.getElementById('note-valeur');
                if (noteElement) {
                    noteElement.textContent = stats.moyenne.toFixed(1);
                }

            } catch (err) {
                console.error("Erreur stats:", err);
            }
        }

        async function countMessages() {
            try {
                const response = await fetch(`${API_BASE}/prestataire/${providerId}/count`);
                const data = await response.json();

                const count = document.getElementById('messages');
                if (count) {
                    count.textContent = data.count || 0;
                }

            } catch (err) {
                console.error("Erreur messages:", err);
            }
        }

        async function acheterBoost(typeBoost, targetId = 0) {
            if (!window.currentUserId) return alert("Vous devez être connecté pour effectuer cette action.");

            const data = {
                provider_id: parseInt(window.currentUserId),
                type_boost: typeBoost,
                target_id: parseInt(targetId)
            };

            try {
                const response = await fetch(`${API_BASE}/prestataire/paiement-boost`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    const result = await response.json();
                    window.location.href = result.url;
                } else {
                    alert("Erreur lors de l'initialisation du paiement du boost.");
                }
            } catch (err) {
                console.error(err);
                alert("Serveur inaccessible.");
            }
        }

        const updateBannerUI = (dateServices, dateProfil) => {
            const now = new Date();
            
            const isServicesActive = dateServices && new Date(dateServices) > now;
            const isProfilActive = dateProfil && new Date(dateProfil) > now;

            const banner = document.getElementById('banner-boost');
            const btnServices = document.getElementById('btn-boost-services');
            const btnProfil = document.getElementById('btn-boost-profil');

            if (!banner || !btnServices || !btnProfil) return;

            if (isServicesActive && isProfilActive) {
                banner.classList.add('hidden');
                return;
            }

            banner.classList.remove('hidden');

            if (!isServicesActive) {
                btnServices.classList.remove('hidden');
            } else {
                btnServices.classList.add('hidden');
            }

            if (!isProfilActive) {
                btnProfil.classList.remove('hidden');
            } else {
                btnProfil.classList.add('hidden');
            }
        };

        async function loadRevenueChart(providerId) {
            try {
                const res = await fetch(`${window.API_BASE_URL}/prestataire/${providerId}/revenues`);
                if (res.ok) {
                    const data = await res.json();

                    const labels = data.map(item => {
                        const d = new Date(item.date);
                        return d.toLocaleDateString('fr-FR', {
                            day: '2-digit',
                            month: 'short'
                        });
                    });
                    const totals = data.map(item => item.total);

                    const ctx = document.getElementById('revenueChart').getContext('2d');

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels.length > 0 ? labels : ['Aucune donnée'],
                            datasets: [{
                                label: 'Revenus nets (€)',
                                data: totals.length > 0 ? totals : [0],
                                borderColor: '#1C5B8F',
                                backgroundColor: 'rgba(28, 91, 143, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#E1AB2B',
                                pointBorderColor: '#fff',
                                pointHoverBackgroundColor: '#fff',
                                pointHoverBorderColor: '#E1AB2B',
                                pointRadius: 5,
                                pointHoverRadius: 7
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { 
                                    display: false 
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) { 
                                            return context.parsed.y.toFixed(2) + ' €'; 
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { 
                                        color: '#f3f4f6' 
                                    },
                                    ticks: {
                                        callback: function(value) { 
                                            return value + ' €'; 
                                        }
                                    }
                                },
                                x: {
                                    grid: { 
                                        display: false 
                                    }
                                }
                            }
                        }
                    });
                }
            } catch (err) {
                console.error("Erreur chargement du graphique :", err);
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const meRes = await fetch(`${window.API_BASE_URL}/auth/me-provider`, {
                    method: 'GET',
                    credentials: 'include'
                });

                if (meRes.ok) {
                    const meData = await meRes.json();
                    const providerId = meData.id_prestataire || meData.id || meData.ID;
                    window.currentUserId = providerId;

                    if (meData.status && (meData.status.toLowerCase() === 'validé' || meData.status.toLowerCase() === 'valide')) {

                        document.getElementById('welcome-text').textContent = `Bonjour ${meData.prenom}, voici l'activité de votre profil.`;

                        const dServ = meData.date_fin_boost || meData.DateFinBoost;
                        const dProf = meData.date_fin_boost_profil || meData.DateFinBoostProfil;
                        updateBannerUI(dServ, dProf);

                        loadRevenueChart(providerId);

                        const profileRes = await fetch(`${window.API_BASE_URL}/prestataire/${providerId}/profile`, {
                            method: 'GET'
                        });

                        if (profileRes.ok) {
                            const profileData = await profileRes.json();

                            const evenements = profileData.evenements || [];
                            document.getElementById('stat-events-count').textContent = evenements.length;

                            const tbody = document.getElementById('events-table-body');
                            tbody.innerHTML = '';

                            if (evenements.length === 0) {
                                tbody.innerHTML = `<tr><td colspan="4" class="py-6 text-center text-gray-500 text-sm">Aucune prestation prévue prochainement.</td></tr>`;
                            } else {
                                evenements.slice(0, 5).forEach(evt => {
                                    let dateText = "Non définie";
                                    if (evt.date_debut) {
                                        const d = new Date(evt.date_debut);
                                        dateText = d.toLocaleDateString('fr-FR', {
                                            weekday: 'short',
                                            day: 'numeric',
                                            month: 'short',
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        });
                                    }

                                    const tr = document.createElement('tr');
                                    tr.className = "border-b border-gray-50 hover:bg-gray-50 transition-colors";
                                    tr.innerHTML = `
                                        <td class="py-4 text-sm font-semibold text-gray-700">${dateText}</td>
                                        <td class="py-4 text-sm text-[#1C5B8F] font-bold">${evt.nom}</td>
                                        <td class="py-4 text-sm text-gray-600">${evt.lieu || '-'}</td>
                                        <td class="py-4">
                                            <span class="bg-green-100 text-green-700 py-1 px-3 rounded-full text-xs font-bold">${evt.nombre_place || 0} places</span>
                                        </td>
                                    `;
                                    tbody.appendChild(tr);
                                });
                            }
                        }
                    }
                }
            } catch (err) {
                console.error("Erreur chargement dashboard :", err);
            }
        });
    </script>
</body>

</html>