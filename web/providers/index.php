<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Alata', 'sans-serif'] }
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
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Nouvelle Prestation
                        </a>
                    </div>

                    <div class="bg-gradient-to-r from-[#1C5B8F] to-blue-800 rounded-[2rem] p-8 shadow-lg flex flex-col md:flex-row items-center justify-between border border-blue-400">
                        <div class="text-white mb-4 md:mb-0">
                            <h3 class="text-2xl font-bold flex items-center gap-2">
                                <span class="text-[#E1AB2B]">⭐</span> Manque de visibilité ?
                            </h3>
                            <p class="text-blue-100 mt-2">Faites apparaître votre profil en tête de liste dans l'Espace Senior pendant 7 jours.</p>
                        </div>
                        <button onclick="acheterBoost('compte')" class="bg-[#E1AB2B] hover:bg-yellow-500 text-[#1C5B8F] font-bold py-3 px-6 rounded-full shadow-md transition-transform hover:scale-105 whitespace-nowrap">
                            Booster mon profil (10€)
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                            <div class="bg-blue-100 p-4 rounded-full text-[#1C5B8F]">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-semibold">Prochaines interventions</p>
                                <p class="text-2xl font-bold text-gray-800" id="stat-events-count">-</p>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                            <div class="bg-yellow-100 p-4 rounded-full text-[#E1AB2B]">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-semibold">Nouveaux messages</p>
                                <p class="text-2xl font-bold text-gray-800">3</p>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                            <div class="bg-green-100 p-4 rounded-full text-green-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-semibold">Note moyenne</p>
                                <p class="text-2xl font-bold text-gray-800">4.8 <span class="text-sm font-normal text-gray-400">/5</span></p>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                            <div class="bg-purple-100 p-4 rounded-full text-purple-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-semibold">Votre Tarif (Moy)</p>
                                <p class="text-2xl font-bold text-gray-800" id="stat-tarif">- € <span class="text-sm font-normal text-gray-400">/h</span></p>
                            </div>
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
                                        <tr><td colspan="4" class="py-6 text-center text-gray-500 text-sm">Chargement du planning...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-[#1C5B8F] to-blue-800 rounded-3xl shadow-md p-8 text-white flex flex-col justify-between">
                            <div>
                                <h2 class="text-xl font-bold mb-2">Besoin d'aide ?</h2>
                                <p class="text-blue-100 text-sm mb-6">L'équipe Silver Happy est disponible pour vous accompagner dans vos démarches ou en cas d'urgence avec un senior.</p>
                                
                                <div class="space-y-3">
                                    <button class="w-full bg-white/10 hover:bg-white/20 transition-colors py-3 px-4 rounded-xl flex items-center justify-between text-sm font-semibold">
                                        Contacter le support client
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                    <button class="w-full bg-white/10 hover:bg-white/20 transition-colors py-3 px-4 rounded-xl flex items-center justify-between text-sm font-semibold">
                                        Consulter la FAQ Pro
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
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
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-yellow-100 mb-6">
                            <svg class="w-8 h-8 text-[#E1AB2B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
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
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-6">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </div>
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
        async function acheterBoost(typeBoost, targetId = 0) {
            const providerId = window.currentUserId; 
            if (!providerId) return alert("Vous devez être connecté pour effectuer cette action.");

            const data = {
                provider_id: parseInt(providerId),
                type_boost: typeBoost,
                target_id: parseInt(targetId)
            };

            try {
                const apiBase = window.API_BASE_URL || 'http://localhost:8082';
                const response = await fetch(`${apiBase}/prestataire/paiement-boost`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
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

        document.addEventListener('DOMContentLoaded', async () => {
            
            try {
                const meRes = await fetch(`${window.API_BASE_URL}/auth/me-provider`, {
                    method: 'GET',
                    credentials: 'include' 
                });

                if (meRes.ok) {
                    const meData = await meRes.json();
                    
                    window.currentUserId = meData.id_prestataire || meData.id || meData.ID;
                    
                    if (meData.status && (meData.status.toLowerCase() === 'validé' || meData.status.toLowerCase() === 'valide')) {
                        
                        document.getElementById('welcome-text').textContent = `Bonjour ${meData.prenom}, voici l'activité de votre activité de ${meData.categorie_nom || 'prestation'}.`;
                        
                        const profileRes = await fetch(`${window.API_BASE_URL}/prestataire/${meData.id_prestataire || meData.id || meData.ID}/profile`, {
                            method: 'GET'
                        });

                        if (profileRes.ok) {
                            const profileData = await profileRes.json();
                            
                            document.getElementById('stat-tarif').innerHTML = `${profileData.prestataire.Tarifs || profileData.prestataire.tarifs || 0} € <span class="text-sm font-normal text-gray-400">/h</span>`;

                            const evenements = profileData.evenements || [];
                            document.getElementById('stat-events-count').textContent = evenements.length;

                            const tbody = document.getElementById('events-table-body');
                            tbody.innerHTML = ''; 

                            if (evenements.length === 0) {
                                tbody.innerHTML = `<tr><td colspan="4" class="py-6 text-center text-gray-500 text-sm">Aucune prestation prévue prochainement.</td></tr>`;
                            } else {
                                evenements.slice(0, 5).forEach(evt => {
                                    
                                    let dateText = "Non définie";
                                    if(evt.date_debut) {
                                        const d = new Date(evt.date_debut);
                                        dateText = d.toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric', month: 'short', hour: '2-digit', minute:'2-digit' });
                                    }

                                    const tr = document.createElement('tr');
                                    tr.className = "border-b border-gray-50 hover:bg-gray-50 transition-colors";
                                    tr.innerHTML = `
                                        <td class="py-4 text-sm font-semibold text-gray-700">${dateText}</td>
                                        <td class="py-4 text-sm text-[#1C5B8F] font-bold">${evt.nom}</td>
                                        <td class="py-4 text-sm text-gray-600">${evt.lieu || '-'}</td>
                                        <td class="py-4">
                                            <span class="bg-green-100 text-green-700 py-1 px-3 rounded-full text-xs font-bold">${evt.nombre_place || 0} inscrits</span>
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