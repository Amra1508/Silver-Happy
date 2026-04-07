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

        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            if (modal) {
                modal.classList.toggle('hidden');
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
                        <button onclick="openCreateModal()" class="bg-[#E1AB2B] hover:bg-yellow-500 text-[#1C5B8F] font-bold py-3 px-6 rounded-xl shadow-md transition-all flex items-center gap-2">
                            + Créer un évènement
                        </button>
                    </div>

                    <div id="page-alert" class="hidden p-4 rounded-xl font-semibold text-sm text-center"></div>

                    <div id="events-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <div class="col-span-full py-10 text-center text-gray-500 flex flex-col items-center">
                            Chargement de vos évènements...
                        </div>
                    </div>
                </div>

            </main>
        </div>

        <div id="create-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-3xl shadow-xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="bg-[#1C5B8F] px-6 py-4 flex justify-between items-center text-white shrink-0">
                    <h3 class="text-xl font-bold">Nouvel évènement</h3>
                    <button onclick="toggleModal('create-modal')" class="text-white hover:text-red-300 transition-colors text-2xl leading-none">&times;</button>
                </div>
                <div class="p-6 overflow-y-auto">
                    <form id="form-create-event" class="space-y-5">
                        <div>
                            <label class="text-sm text-gray-600 font-semibold block mb-1">Nom de l'évènement <span class="text-red-500">*</span></label>
                            <input type="text" id="create-nom" class="w-full border border-gray-300 rounded-xl p-2.5" required>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-semibold block mb-1">Description <span class="text-red-500">*</span></label>
                            <textarea id="create-desc" rows="3" class="w-full border border-gray-300 rounded-xl p-2.5" required></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="md:col-span-1">
                                <label class="text-sm text-gray-600 font-semibold block mb-1">Lieu <span class="text-red-500">*</span></label>
                                <input type="text" id="create-lieu" class="w-full border border-gray-300 rounded-xl p-2.5" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600 font-semibold block mb-1">Places <span class="text-red-500">*</span></label>
                                <input type="number" id="create-places" min="1" value="1" class="w-full border border-gray-300 rounded-xl p-2.5" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600 font-semibold block mb-1">Prix (€) <span class="text-red-500">*</span></label>
                                <input type="number" id="create-prix" step="0.01" min="0" value="0.00" class="w-full border border-gray-300 rounded-xl p-2.5" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="text-sm text-gray-600 font-semibold block mb-1">Date de début <span class="text-red-500">*</span></label>
                                <input type="datetime-local" id="create-debut" class="w-full border border-gray-300 rounded-xl p-2.5" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600 font-semibold block mb-1">Date de fin</label>
                                <input type="datetime-local" id="create-fin" class="w-full border border-gray-300 rounded-xl p-2.5">
                            </div>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-semibold block mb-1">Image de l'évènement</label>
                            <input type="file" id="create-image" accept="image/*" class="w-full text-sm">
                        </div>
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" onclick="toggleModal('create-modal')" class="px-5 py-2.5 text-gray-600 font-bold hover:bg-gray-200 rounded-xl transition-colors">Annuler</button>
                            <button type="submit" id="btn-create-event" class="bg-[#1C5B8F] hover:bg-blue-800 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition-colors">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="details-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-3xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
                <div id="details-header" class="h-40 w-full bg-gradient-to-r from-[#1C5B8F] to-blue-600 bg-cover bg-center relative">
                    <button onclick="toggleModal('details-modal')" class="absolute top-4 right-4 bg-white bg-opacity-50 hover:bg-opacity-100 text-gray-800 rounded-full w-8 h-8 flex items-center justify-center font-bold transition">&times;</button>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-2">
                        <span id="details-prix" class="inline-block px-3 py-1 bg-[#E1AB2B] text-white text-xs font-bold rounded-full shadow-sm"></span>
                        <span id="details-places" class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded-md border border-gray-200"></span>
                    </div>
                    <h3 id="details-nom" class="text-2xl font-bold text-[#1C5B8F] mb-4"></h3>
                    <p id="details-desc" class="text-gray-600 mb-6 whitespace-pre-wrap"></p>
                    <div class="space-y-2 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <p class="text-sm text-gray-700"><strong>📍 Lieu :</strong> <span id="details-lieu"></span></p>
                        <p class="text-sm text-gray-700"><strong>📅 Début :</strong> <span id="details-debut"></span></p>
                        <p class="text-sm text-gray-700"><strong>🏁 Fin :</strong> <span id="details-fin"></span></p>
                    </div>
                </div>
            </div>
        </div>

        <div id="edit-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-3xl shadow-xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="bg-[#E1AB2B] px-6 py-4 flex justify-between items-center text-white shrink-0">
                    <h3 class="text-xl font-bold">Modifier l'évènement</h3>
                    <button onclick="toggleModal('edit-modal')" class="text-white hover:text-yellow-100 transition-colors text-2xl leading-none">&times;</button>
                </div>
                <div class="p-6 overflow-y-auto">
                    <form id="form-edit-event" class="space-y-5">
                        <input type="hidden" id="edit-id">
                        <div>
                            <label class="text-sm text-gray-600 font-semibold block mb-1">Nom de l'évènement <span class="text-red-500">*</span></label>
                            <input type="text" id="edit-nom" class="w-full border border-gray-300 rounded-xl p-2.5" required>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-semibold block mb-1">Description <span class="text-red-500">*</span></label>
                            <textarea id="edit-desc" rows="3" class="w-full border border-gray-300 rounded-xl p-2.5" required></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="md:col-span-1">
                                <label class="text-sm text-gray-600 font-semibold block mb-1">Lieu <span class="text-red-500">*</span></label>
                                <input type="text" id="edit-lieu" class="w-full border border-gray-300 rounded-xl p-2.5" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600 font-semibold block mb-1">Places <span class="text-red-500">*</span></label>
                                <input type="number" id="edit-places" min="1" class="w-full border border-gray-300 rounded-xl p-2.5" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600 font-semibold block mb-1">Prix (€) <span class="text-red-500">*</span></label>
                                <input type="number" id="edit-prix" step="0.01" min="0" class="w-full border border-gray-300 rounded-xl p-2.5" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="text-sm text-gray-600 font-semibold block mb-1">Date de début <span class="text-red-500">*</span></label>
                                <input type="datetime-local" id="edit-debut" class="w-full border border-gray-300 rounded-xl p-2.5" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600 font-semibold block mb-1">Date de fin</label>
                                <input type="datetime-local" id="edit-fin" class="w-full border border-gray-300 rounded-xl p-2.5">
                            </div>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-semibold block mb-1">Nouvelle image (laisser vide pour conserver l'actuelle)</label>
                            <input type="file" id="edit-image" accept="image/*" class="w-full text-sm">
                        </div>
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" onclick="toggleModal('edit-modal')" class="px-5 py-2.5 text-gray-600 font-bold hover:bg-gray-200 rounded-xl transition-colors">Annuler</button>
                            <button type="submit" id="btn-edit-event" class="bg-[#E1AB2B] hover:bg-yellow-600 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition-colors">Sauvegarder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="delete-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4">
            <div class="bg-white p-10 rounded-3xl w-full max-w-sm text-center shadow-xl">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Supprimer l'évènement ?</h3>
                <p class="text-gray-500 mb-8">Cette action est irréversible. Toutes les données liées seront perdues.</p>
                <input type="hidden" id="delete-id">
                <div class="flex justify-center gap-4">
                    <button type="button" onclick="toggleModal('delete-modal')" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">Annuler</button>
                    <button type="button" id="confirm-delete" class="px-5 py-2.5 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 transition shadow-md">Oui, supprimer</button>
                </div>
            </div>
        </div>

    </div>

    <script>
        let currentProviderId = null;
        let allEvents = [];
        const API_URL = window.API_BASE_URL;

        async function acheterBoost(typeBoost, targetId = 0) {
            const providerId = window.currentUserId || currentProviderId; 
            if (!providerId) return alert("Vous devez être connecté.");

            const data = {
                provider_id: parseInt(providerId),
                type_boost: typeBoost,
                target_id: parseInt(targetId)
            };

            try {
                const response = await fetch(`${API_URL}/paiement-boost`, {
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

        function showAlert(msg, type = "success") {
            const pageAlert = document.getElementById('page-alert');
            pageAlert.textContent = msg;
            pageAlert.className = `p-4 mb-6 rounded-xl font-bold block fade-in ${type === 'success' ? 'text-green-700 bg-green-100 border border-green-400' : 'text-red-700 bg-red-100 border border-red-400'}`;
            pageAlert.classList.remove('hidden');
            setTimeout(() => pageAlert.classList.add('hidden'), 5000);
        }

        function formatDateTime(dateString) {
            if (!dateString) return "Non défini";
            const options = { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute:'2-digit' };
            return new Date(dateString).toLocaleDateString('fr-FR', options);
        }

        function formatDateForInput(dateStr) {
            if (!dateStr) return "";
            const d = new Date(dateStr);
            if (isNaN(d)) return "";
            return d.toISOString().slice(0, 16);
        }

        async function loadEvents(providerId) {
            const container = document.getElementById('events-container');
            try {
                const res = await fetch(`${API_URL}/prestataire/${providerId}/profile`, { method: 'GET' });
                if (res.ok) {
                    const data = await res.json();
                    allEvents = data.evenements || [];
                    
                    container.innerHTML = '';

                    if (allEvents.length === 0) {
                        container.innerHTML = `
                            <div class="col-span-full bg-white rounded-3xl p-10 text-center border border-gray-100 shadow-sm mt-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Aucun évènement pour le moment</h3>
                                <p class="text-gray-500 mb-6">Vous n'avez pas encore créé de prestation ou d'évènement.</p>
                                <button onclick="openCreateModal()" class="text-[#1C5B8F] font-bold hover:underline">Créer mon premier évènement</button>
                            </div>
                        `;
                    } else {
                        allEvents.forEach(evt => {
                            const evtId = evt.id_evenement || evt.id || evt.ID;
                            const card = document.createElement('div');
                            card.className = "bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col fade-in hover:shadow-md transition-shadow relative";
                            
                            const badgePrice = evt.prix > 0 ? `${evt.prix} €` : 'Gratuit';
                            const imgSrc = evt.image ? `${API_URL}${evt.image}` : null;
                            
                            // NOUVEAU : Logique de vérification du boost
                            const boostDate = evt.date_fin_boost || evt.DateFinBoost;
                            const isBoosted = boostDate && new Date(boostDate) > new Date();

                            // Badge visuel sur l'image
                            let boostBadge = isBoosted 
                                ? `<div class="absolute top-3 right-3 bg-[#E1AB2B] text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-md flex items-center gap-1 border border-yellow-300">⭐ Boosté</div>` 
                                : '';

                            // Bouton d'action (Acheter vs Date d'expiration)
                            let boostActionBtn = isBoosted
                                ? `<span class="text-xs text-[#E1AB2B] font-bold px-2 flex items-center gap-1">⭐ Jusqu'au ${new Date(boostDate).toLocaleDateString('fr-FR')}</span>`
                                : `<button onclick="acheterBoost('evenement', ${evtId})" class="flex items-center gap-1 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-3 py-1 rounded-full text-xs font-bold transition-colors border border-yellow-300">⭐ Booster (5€)</button>`;

                            let imageBlock = imgSrc 
                                ? `<div class="relative"><img src="${imgSrc}" alt="${evt.nom}" class="w-full h-40 object-cover cursor-pointer" onclick="openDetailsModal(${evtId})">${boostBadge}</div>` 
                                : `<div class="relative h-40 w-full bg-gradient-to-r from-[#1C5B8F] to-blue-600 cursor-pointer" onclick="openDetailsModal(${evtId})">${boostBadge}</div>`;

                            card.innerHTML = `
                                ${imageBlock}
                                <div class="p-6 flex-1 flex flex-col">
                                    <div class="flex justify-between items-start mb-3">
                                        <span class="inline-block px-3 py-1 bg-[#1C5B8F] text-white text-xs font-bold rounded-full shadow-sm">${badgePrice}</span>
                                        <span class="text-xs font-bold text-gray-500 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">${evt.nombre_place} places</span>
                                    </div>
                                    
                                    <h3 class="text-lg font-bold text-gray-800 mb-2 leading-tight cursor-pointer hover:text-[#1C5B8F] transition" onclick="openDetailsModal(${evtId})">${evt.nom}</h3>
                                    <p class="text-sm text-gray-500 mb-6 flex-1 line-clamp-2">${evt.description}</p>
                                    
                                    <div class="space-y-2 mt-auto text-sm text-gray-600">
                                        <div class="flex items-start"><span class="truncate">📍 ${evt.lieu}</span></div>
                                        <div class="flex items-center"><span class="truncate">📅 ${formatDateTime(evt.date_debut)}</span></div>
                                    </div>

                                    <div class="mt-5 pt-4 border-t border-gray-100 flex justify-between items-center gap-2">
                                        <button onclick="openDetailsModal(${evtId})" class="text-gray-500 font-bold text-xs hover:underline">Détails</button>
                                        <div class="flex gap-2 flex-wrap justify-end items-center">
                                            ${boostActionBtn}
                                            <button onclick="openEditModal(${evtId})" class="text-[#1C5B8F] font-bold text-xs hover:underline flex items-center gap-1 px-1">Modifier</button>
                                            <button onclick="openDeleteModal(${evtId})" class="text-red-500 font-bold text-xs hover:underline flex items-center gap-1 px-1">Supprimer</button>
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
                showAlert("Impossible de charger les évènements.", "error");
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const meRes = await fetch(`${API_URL}/auth/me-provider`, { method: 'GET', credentials: 'include' });
                if (meRes.ok) {
                    const data = await meRes.json();
                    if (data.status && (data.status.toLowerCase() === 'validé' || data.status.toLowerCase() === 'valide')) {
                        document.getElementById('main-content-valide').classList.remove('hidden');
                        currentProviderId = data.id_prestataire || data.id || data.ID;
                        
                        window.currentUserId = currentProviderId; 
                        
                        loadEvents(currentProviderId);
                    }
                } else {
                    window.location.href = "/front/providers/account/signin.php";
                }
            } catch (err) {
                console.error("Erreur auth :", err);
            }
        });

        function openCreateModal() {
            document.getElementById('form-create-event').reset();
            toggleModal('create-modal');
        }

        document.getElementById('form-create-event').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btn-create-event');
            btn.disabled = true;
            btn.innerHTML = "Création...";

            const formData = new FormData();
            formData.append('nom', document.getElementById('create-nom').value.trim());
            formData.append('description', document.getElementById('create-desc').value.trim());
            formData.append('lieu', document.getElementById('create-lieu').value.trim());
            formData.append('nombre_place', document.getElementById('create-places').value);
            formData.append('prix', document.getElementById('create-prix').value);
            formData.append('date_debut', document.getElementById('create-debut').value);
            
            const dateFin = document.getElementById('create-fin').value;
            if(dateFin) formData.append('date_fin', dateFin);

            const fileInput = document.getElementById('create-image');
            if (fileInput.files.length > 0) formData.append('image', fileInput.files[0]);

            try {
                const res = await fetch(`${API_URL}/prestataire/evenement/create`, {
                    method: 'POST', credentials: 'include', body: formData
                });

                if (res.ok) {
                    toggleModal('create-modal');
                    showAlert("Évènement créé avec succès !", "success");
                    loadEvents(currentProviderId);
                } else {
                    const errorMsg = await res.text();
                    showAlert("Erreur : " + errorMsg, "error");
                }
            } catch (err) {
                showAlert("Impossible de joindre le serveur.", "error");
            } finally {
                btn.disabled = false;
                btn.innerHTML = "Enregistrer";
            }
        });

        function openDetailsModal(id) {
            const evt = allEvents.find(e => (e.id_evenement || e.id || e.ID) == id);
            if (!evt) return;

            document.getElementById('details-nom').textContent = evt.nom;
            document.getElementById('details-desc').textContent = evt.description;
            document.getElementById('details-lieu').textContent = evt.lieu;
            document.getElementById('details-debut').textContent = formatDateTime(evt.date_debut);
            document.getElementById('details-fin').textContent = evt.date_fin ? formatDateTime(evt.date_fin) : 'Non définie';
            document.getElementById('details-prix').textContent = evt.prix > 0 ? `${evt.prix} €` : 'Gratuit';
            document.getElementById('details-places').textContent = `${evt.nombre_place} places disponibles`;

            const header = document.getElementById('details-header');
            if (evt.image) {
                header.style.backgroundImage = `url('${API_URL}${evt.image}')`;
            } else {
                header.style.backgroundImage = `linear-gradient(to right, #1C5B8F, #2563EB)`;
            }

            toggleModal('details-modal');
        }

        function openEditModal(id) {
            const evt = allEvents.find(e => (e.id_evenement || e.id || e.ID) == id);
            if (!evt) return;

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nom').value = evt.nom;
            document.getElementById('edit-desc').value = evt.description;
            document.getElementById('edit-lieu').value = evt.lieu;
            document.getElementById('edit-places').value = evt.nombre_place;
            document.getElementById('edit-prix').value = evt.prix;
            document.getElementById('edit-debut').value = formatDateForInput(evt.date_debut);
            document.getElementById('edit-fin').value = evt.date_fin ? formatDateForInput(evt.date_fin) : '';
            document.getElementById('edit-image').value = ''; 

            toggleModal('edit-modal');
        }

        document.getElementById('form-edit-event').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btn-edit-event');
            const id = document.getElementById('edit-id').value;
            btn.disabled = true;
            btn.innerHTML = "Sauvegarde...";

            const formData = new FormData();
            formData.append('nom', document.getElementById('edit-nom').value.trim());
            formData.append('description', document.getElementById('edit-desc').value.trim());
            formData.append('lieu', document.getElementById('edit-lieu').value.trim());
            formData.append('nombre_place', document.getElementById('edit-places').value);
            formData.append('prix', document.getElementById('edit-prix').value);
            formData.append('date_debut', document.getElementById('edit-debut').value);
            
            const dateFin = document.getElementById('edit-fin').value;
            if(dateFin) formData.append('date_fin', dateFin);

            const fileInput = document.getElementById('edit-image');
            if (fileInput.files.length > 0) formData.append('image', fileInput.files[0]);

            try {
                const res = await fetch(`${API_URL}/evenement/update/${id}`, {
                    method: 'PUT', credentials: 'include', body: formData
                });

                if (res.ok) {
                    toggleModal('edit-modal');
                    showAlert("Modifications enregistrées avec succès !", "success");
                    loadEvents(currentProviderId);
                } else {
                    showAlert("Erreur lors de la mise à jour.", "error");
                }
            } catch (err) {
                showAlert("Erreur réseau.", "error");
            } finally {
                btn.disabled = false;
                btn.innerHTML = "Sauvegarder";
            }
        });

        function openDeleteModal(id) {
            document.getElementById('delete-id').value = id;
            toggleModal('delete-modal');
        }

        document.getElementById('confirm-delete').addEventListener('click', async () => {
            const id = document.getElementById('delete-id').value;
            const btn = document.getElementById('confirm-delete');
            btn.disabled = true;
            btn.innerHTML = "Suppression...";

            try {
                const res = await fetch(`${API_URL}/evenement/delete/${id}`, {
                    method: 'DELETE', credentials: 'include'
                });

                if (res.ok) {
                    toggleModal('delete-modal');
                    showAlert("Évènement supprimé avec succès.", "success");
                    loadEvents(currentProviderId);
                } else {
                    showAlert("Erreur lors de la suppression.", "error");
                }
            } catch (err) {
                showAlert("Erreur réseau.", "error");
            } finally {
                btn.disabled = false;
                btn.innerHTML = "Oui, supprimer";
            }
        });
    </script>
</body>
</html>