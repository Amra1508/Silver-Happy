<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Évènements</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: { fontFamily: { sans: ['Alata', 'sans-serif'] } }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="flex min-h-screen relative">

        <?php include("../includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto relative">
            <main class="p-8">
                
                <div id="main-content-valide" class="hidden space-y-8 max-w-7xl mx-auto">
                    
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                        <div>
                            <h1 class="text-3xl font-semibold text-[#1C5B8F]">Mes Évènements & Services</h1>
                            <p class="text-gray-500 mt-1">Gérez vos prestations, ateliers et interventions à venir.</p>
                        </div>
                        <button onclick="openModal()" class="bg-[#E1AB2B] hover:bg-yellow-500 text-[#1C5B8F] font-bold py-3 px-6 rounded-xl shadow-md transition-all flex items-center gap-2">
                            Créer un évènement
                        </button>
                    </div>

                    <div id="page-alert" class="hidden p-4 rounded-xl font-semibold text-sm"></div>

                    <div id="events-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <div class="col-span-full py-10 text-center text-gray-500 flex flex-col items-center">
                            Chargement de vos évènements...
                        </div>
                    </div>
                </div>

            </main>
        </div>

        <div id="event-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4 fade-in">
            <div class="bg-white rounded-3xl shadow-xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
                
                <div class="bg-[#1C5B8F] px-6 py-4 flex justify-between items-center text-white shrink-0">
                    <h3 class="text-xl font-bold flex items-center gap-2">
                        Nouvel évènement
                    </h3>
                    <button onclick="closeModal()" class="text-white hover:text-red-300 transition-colors text-2xl leading-none">&times;</button>
                </div>

                <div class="p-6 overflow-y-auto">
                    <div id="modal-alert" class="hidden p-3 mb-4 rounded-xl font-semibold text-sm"></div>
                    
                    <form id="form-event" class="space-y-5" enctype="multipart/form-data">
                        
                        <div>
                            <label class="text-sm text-gray-600 font-semibold block mb-1">Image de l'évènement</label>
                            <input type="file" id="evt-image" accept="image/*" class="w-full border border-gray-300 rounded-xl p-2 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F] file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-[#1C5B8F] hover:file:bg-blue-100 cursor-pointer">
                        </div>

                        <div>
                            <label class="text-sm text-gray-600 font-semibold block mb-1">Nom de l'évènement <span class="text-red-500">*</span></label>
                            <input type="text" id="evt-nom" class="w-full border border-gray-300 rounded-xl p-2.5 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required>
                        </div>
                        
                        <div>
                            <label class="text-sm text-gray-600 font-semibold block mb-1">Description <span class="text-red-500">*</span></label>
                            <textarea id="evt-desc" rows="3" class="w-full border border-gray-300 rounded-xl p-2.5 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="text-sm text-gray-600 font-semibold block mb-1">Lieu de l'intervention <span class="text-red-500">*</span></label>
                                <input type="text" id="evt-lieu" placeholder="Adresse ou Ville" class="w-full border border-gray-300 rounded-xl p-2.5 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-600 font-semibold block mb-1">Places <span class="text-red-500">*</span></label>
                                    <input type="number" id="evt-places" min="1" value="1" class="w-full border border-gray-300 rounded-xl p-2.5 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600 font-semibold block mb-1">Prix (€) <span class="text-red-500">*</span></label>
                                    <input type="number" id="evt-prix" step="0.01" min="0" value="0.00" class="w-full border border-gray-300 rounded-xl p-2.5 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="text-sm text-gray-600 font-semibold block mb-1">Date de début <span class="text-red-500">*</span></label>
                                <input type="datetime-local" id="evt-debut" class="w-full border border-gray-300 rounded-xl p-2.5 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600 font-semibold block mb-1">Date de fin</label>
                                <input type="datetime-local" id="evt-fin" class="w-full border border-gray-300 rounded-xl p-2.5 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200 shrink-0">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 text-gray-600 font-bold hover:bg-gray-200 rounded-xl transition-colors">Annuler</button>
                    <button type="button" id="btn-save-event" class="bg-[#1C5B8F] hover:bg-blue-800 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition-colors flex items-center gap-2">
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        let currentProviderId = null;
        const API_URL = 'http://localhost:8082';

        function openModal() {
            document.getElementById('event-modal').classList.remove('hidden');
            document.getElementById('form-event').reset();
            document.getElementById('modal-alert').classList.add('hidden');
        }

        function closeModal() {
            document.getElementById('event-modal').classList.add('hidden');
        }

        function formatDateTime(dateString) {
            if (!dateString) return "Non défini";
            const options = { weekday: 'long', day: 'numeric', month: 'short', hour: '2-digit', minute:'2-digit' };
            return new Date(dateString).toLocaleDateString('fr-FR', options);
        }

        async function loadEvents(providerId) {
            const container = document.getElementById('events-container');
            try {
                const res = await fetch(`${API_URL}/prestataire/${providerId}/profile`, { method: 'GET' });
                if (res.ok) {
                    const data = await res.json();
                    const evenements = data.evenements || [];
                    
                    container.innerHTML = '';

                    if (evenements.length === 0) {
                        container.innerHTML = `
                            <div class="col-span-full bg-white rounded-3xl p-10 text-center border border-gray-100 shadow-sm mt-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Aucun évènement pour le moment</h3>
                                <p class="text-gray-500 mb-6">Vous n'avez pas encore créé de prestation ou d'évènement.</p>
                                <button onclick="openModal()" class="text-[#1C5B8F] font-bold hover:underline">Créer mon premier évènement</button>
                            </div>
                        `;
                    } else {
                        evenements.forEach(evt => {
                            const card = document.createElement('div');
                            card.className = "bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col fade-in hover:shadow-md transition-shadow relative";
                            
                            const badgePrice = evt.prix > 0 ? `${evt.prix} €` : 'Gratuit';
                            
                            let imageBlock = '';
                            if (evt.image) {
                                imageBlock = `<img src="${API_URL}${evt.image}" alt="${evt.nom}" class="w-full h-40 object-cover">`;
                            } else {
                                const headerColor = evt.prix > 0 ? "from-[#1C5B8F] to-blue-600" : "from-green-500 to-green-400";
                                imageBlock = `<div class="h-40 w-full bg-gradient-to-r ${headerColor}"></div>`;
                            }

                            card.innerHTML = `
                                ${imageBlock}
                                <div class="p-6 flex-1 flex flex-col">
                                    <div class="flex justify-between items-start mb-3">
                                        <span class="inline-block px-3 py-1 bg-gray-100 text-gray-800 text-xs font-bold rounded-full shadow-sm">
                                            ${badgePrice}
                                        </span>
                                        <span class="text-xs font-bold text-gray-400 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">${evt.nombre_place} places</span>
                                    </div>
                                    
                                    <h3 class="text-lg font-bold text-[#1C5B8F] mb-2 leading-tight">${evt.nom}</h3>
                                    <p class="text-sm text-gray-500 mb-6 flex-1 line-clamp-3">${evt.description}</p>
                                    
                                    <div class="space-y-3 mt-auto pt-4 border-t border-gray-50">
                                        <div class="flex items-start text-sm text-gray-600">
                                            <span class="line-clamp-2">📍 ${evt.lieu}</span>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600">
                                            <span class="truncate font-medium">📅 ${formatDateTime(evt.date_debut)}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                            container.appendChild(card);
                        });
                    }
                }
            } catch (err) {
                console.error("Erreur de chargement :", err);
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            
            try {
                const meRes = await fetch(`${API_URL}/auth/me-provider`, {
                    method: 'GET',
                    credentials: 'include' 
                });

                if (meRes.ok) {
                    const data = await meRes.json();
                    
                    if (data.status && (data.status.toLowerCase() === 'validé' || data.status.toLowerCase() === 'valide')) {
                        document.getElementById('main-content-valide').classList.remove('hidden');
                        currentProviderId = data.id_prestataire || data.id || data.ID;
                        
                        loadEvents(currentProviderId);
                    }
                } else {
                    window.location.href = "/front/providers/account/signin.php";
                }
            } catch (err) {
                console.error("Erreur auth :", err);
            }

            const btnSaveEvent = document.getElementById('btn-save-event');
            const formEvent = document.getElementById('form-event');
            const modalAlert = document.getElementById('modal-alert');

            btnSaveEvent.addEventListener('click', async () => {
                
                if(!formEvent.checkValidity()) {
                    formEvent.reportValidity();
                    return;
                }

                btnSaveEvent.disabled = true;
                btnSaveEvent.innerHTML = "Création...";

                const formData = new FormData();
                formData.append('nom', document.getElementById('evt-nom').value.trim());
                formData.append('description', document.getElementById('evt-desc').value.trim());
                formData.append('lieu', document.getElementById('evt-lieu').value.trim());
                formData.append('nombre_place', document.getElementById('evt-places').value);
                formData.append('prix', document.getElementById('evt-prix').value);
                formData.append('date_debut', document.getElementById('evt-debut').value);
                
                const dateFin = document.getElementById('evt-fin').value;
                if(dateFin) {
                    formData.append('date_fin', dateFin);
                }

                const fileInput = document.getElementById('evt-image');
                if (fileInput.files.length > 0) {
                    formData.append('image', fileInput.files[0]);
                }

                try {
                    const createRes = await fetch(`${API_URL}/prestataire/evenement/create`, {
                        method: 'POST',
                        credentials: 'include',
                        body: formData
                    });

                    if (createRes.ok) {
                        closeModal();
                        
                        const pageAlert = document.getElementById('page-alert');
                        pageAlert.textContent = "Évènement créé avec succès !";
                        pageAlert.className = "p-4 rounded-xl font-bold text-green-700 bg-green-100 border border-green-400 block fade-in";
                        
                        loadEvents(currentProviderId);

                        setTimeout(() => { pageAlert.classList.add('hidden'); }, 4000);

                    } else {
                        const errorMsg = await createRes.text();
                        modalAlert.textContent = "Erreur : " + errorMsg;
                        modalAlert.className = "p-3 mb-4 rounded-xl font-bold text-red-700 bg-red-100 border border-red-400 block";
                    }
                } catch (err) {
                    modalAlert.textContent = "Impossible de joindre le serveur.";
                    modalAlert.className = "p-3 mb-4 rounded-xl font-bold text-red-700 bg-red-100 border border-red-400 block";
                } finally {
                    btnSaveEvent.disabled = false;
                    btnSaveEvent.innerHTML = "Enregistrer";
                }
            });
        });
    </script>
</body>
</html>