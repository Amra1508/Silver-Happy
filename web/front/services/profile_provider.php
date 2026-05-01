<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil du Prestataire</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Alata', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">

    <?php include("../includes/header.php"); ?>

    <main class="flex-grow container mx-auto px-6 py-12 max-w-5xl">

        <div id="alert-box" class="hidden max-w-2xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold shadow-sm"></div>

        <div class="flex justify-between items-center mx-8">
            <a href="/front/services/providers.php" id="back-button-link">
                <button class="flex items-center rounded-md px-6 button-blue">
                    <img src="/front/icons/fleche_gauche.svg" alt="fleche" class="w-7 h-7 mr-2">
                    <span id="back-button-text">Revenir aux prestataires</span>
                </button>
            </a>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-sm border border-[#1C5B8F]/20 mb-10 flex flex-col md:flex-row gap-8 items-center relative overflow-hidden">
            <div class="absolute top-0 right-0 bg-[#E1AB2B] text-white px-6 py-2 rounded-bl-2xl font-bold shadow-sm">
                Prestataire Silver Happy
            </div>

            <div class="w-32 h-32 bg-[#1C5B8F]/10 rounded-full flex items-center justify-center text-[#1C5B8F] text-5xl font-bold flex-shrink-0">
                <span id="prestataire-initials">...</span>
            </div>

            <div class="flex-grow">
                <h1 id="prestataire-name" class="text-4xl font-bold text-[#1C5B8F] mb-1">Chargement...</h1>
                <p id="prestataire-type" class="text-lg text-gray-500 mb-4 font-semibold">...</p>

                <div class="flex flex-col sm:flex-row gap-6 text-sm font-semibold text-gray-700">
                    <span id="prestataire-email" class="flex items-center gap-2">...</span>
                    <span id="prestataire-phone" class="flex items-center gap-2">...</span>
                    <span id="prestataire-tarif" class="flex items-center gap-2 text-[#E1AB2B]">...</span>
                </div>
            </div>
        </div>

        <h2 class="text-2xl font-bold text-[#1C5B8F] mb-6 flex items-center gap-3">
            Événements à venir animés par ce prestataire
        </h2>

        <div id="events-container" class="grid md:grid-cols-2 gap-6">
        </div>

    </main>

    <?php include("../includes/footer.php"); ?>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const prestataireId = urlParams.get('id');
        let currentUserId = null;
        let isUserSubscribed = false;

        function formatDate(dateString) {
            if (!dateString) return "Date non définie";
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateString).toLocaleDateString('fr-FR', options).replace(':', 'h');
        }

        function showAlert(msg, type = 'success') {
            const box = document.getElementById('alert-box');
            box.textContent = msg;
            box.className = `max-w-2xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold shadow-sm block ${
                type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 
                type === 'warning' ? 'bg-yellow-100 border-yellow-400 text-yellow-700' : 
                'bg-red-100 border-red-400 text-red-700'
            }`;
            setTimeout(() => {
                box.classList.add('hidden');
            }, 4000);
        }

        async function fetchCurrentUser() {
            try {
                const res = await fetch(`${API_BASE}/auth/me`, {
                    credentials: 'include'
                });
                if (res.ok) {
                    const user = await res.json();
                    currentUserId = user.id_utilisateur || user.id;

                    if (user.id_abonnement && user.id_abonnement !== null && user.id_abonnement !== 0 && user.debut_abonnement) {
                        const debutDate = new Date(user.debut_abonnement);
                        if (user.type_paiement === 'mensuel') {
                            debutDate.setMonth(debutDate.getMonth() + 1);
                        } else if (user.type_paiement === 'annuel') {
                            debutDate.setFullYear(debutDate.getFullYear() + 1);
                        }

                        if (new Date() <= debutDate) {
                            isUserSubscribed = true;
                        }
                    }
                }
            } catch (err) {
                console.log("Non connecté.");
            }
        }

        async function loadProfile() {
            if (!prestataireId) {
                showAlert("Aucun prestataire sélectionné.", "error");
                return;
            }

            try {
                const response = await fetch(`${window.API_BASE_URL}/prestataire/${prestataireId}/profile`);
                if (!response.ok) throw new Error("Prestataire introuvable");

                const data = await response.json();
                const p = data.prestataire;

                document.getElementById('prestataire-name').textContent = `${p.prenom} ${p.nom}`;
                document.getElementById('prestataire-type').textContent = p.type_prestation || "Service Général";
                document.getElementById('prestataire-initials').textContent = `${p.prenom.charAt(0)}${p.nom.charAt(0)}`.toUpperCase();
                document.getElementById('prestataire-email').innerHTML = `<a href="mailto:${p.email}" class="hover:underline">${p.email}</a>`;
                document.getElementById('prestataire-phone').innerHTML = `${p.num_telephone}`;

                const container = document.getElementById('events-container');
                container.innerHTML = '';

                if (data.evenements.length === 0) {
                    container.innerHTML = `<div class="col-span-2 text-center text-gray-500 py-10 bg-white rounded-2xl border border-gray-200 font-medium">Aucun événement prévu avec ce prestataire pour le moment.</div>`;
                    return;
                }

                data.evenements.forEach(ev => {
                    const prixAffiche = ev.prix > 0 ? `${ev.prix.toFixed(2)} €` : 'Gratuit';
                    const placesText = ev.nombre_place > 0 ? `${ev.nombre_place} places restantes` : '<span class="text-red-500">Complet</span>';

                    container.innerHTML += `
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition flex flex-col h-full">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-bold text-gray-800">${ev.nom}</h3>
                                <span class="bg-[#E1AB2B]/20 text-yellow-700 px-3 py-1 rounded-full text-sm font-bold whitespace-nowrap">${prixAffiche}</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-4 flex-grow line-clamp-3">${ev.description}</p>
                            
                            <div class="space-y-2 mb-6 text-sm text-gray-700 bg-gray-50 p-4 rounded-xl">
                                <p>📍 <strong>Lieu :</strong> ${ev.lieu}</p>
                                <p>🗓️ <strong>Début :</strong> ${formatDate(ev.date_debut)}</p>
                                <p>👥 <strong>Disponibilité :</strong> ${placesText}</p>
                            </div>

                            <button onclick="checkoutEvent(${ev.id_evenement}, ${ev.nombre_place})" 
                                class="w-full ${ev.nombre_place > 0 ? 'bg-[#1C5B8F] hover:bg-[#154670]' : 'bg-gray-400 cursor-not-allowed'} text-white font-bold py-3 rounded-xl transition shadow-sm"
                                ${ev.nombre_place <= 0 ? 'disabled' : ''}>
                                ${ev.nombre_place > 0 ? "S'inscrire à l'événement" : "Événement complet"}
                            </button>
                        </div>
                    `;
                });

            } catch (err) {
                showAlert("Erreur lors du chargement du profil.", "error");
            }
        }

        window.checkoutEvent = async function(eventId, placesDispo) {
            if (placesDispo <= 0) return;

            if (!currentUserId) {
                showAlert("Vous devez être connecté pour vous inscrire.", "error");
                setTimeout(() => window.location.href = '/front/account/signin.php', 2000);
                return;
            }

            if (!isUserSubscribed) {
                showAlert("Vous devez avoir un abonnement actif pour participer aux événements.", "alert");
                setTimeout(() => window.location.href = '/front/services/subscription.php', 2500);
                return;
            }

            try {
                const response = await fetch(`${window.API_BASE_URL}/evenement/checkout/${eventId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'include',
                    body: JSON.stringify({
                        id_utilisateur: currentUserId
                    })
                });

                if (response.ok) {
                    const resData = await response.json();

                    if (resData.isFree) {
                        showAlert("Inscription gratuite confirmée avec succès !", "success");
                        setTimeout(() => loadProfile(), 1500);
                    } else if (resData.url) {
                        window.location.href = resData.url;
                    }
                } else if (response.status === 409) {
                    showAlert("Vous êtes déjà inscrit à cet événement.", "warning");
                } else if (response.status === 403) {
                    showAlert("Désolé, cet événement est complet.", "error");
                } else {
                    showAlert("Une erreur est survenue.", "error");
                }
            } catch (err) {
                showAlert("Impossible de joindre le serveur.", "error");
            }
        };

        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const fromPage = urlParams.get('from');

            const backLink = document.getElementById('back-button-link');
            const backText = document.getElementById('back-button-text');

            if (fromPage === 'services') {
                backLink.href = "/front/services/catalog.php";
                backText.textContent = "Revenir aux services";
            } else {
                backLink.href = "/front/services/providers.php";
                backText.textContent = "Revenir aux prestataires";
            }
        });

        document.addEventListener('DOMContentLoaded', async () => {
            await fetchCurrentUser();
            loadProfile();
        });
    </script>
</body>

</html>