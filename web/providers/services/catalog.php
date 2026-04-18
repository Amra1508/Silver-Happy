<?php
session_start();
$is_logged_in = isset($_SESSION['provider_id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer mes Services et Disponibilités</title>
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
                <?php if ($is_logged_in): ?>
                    <div id="main-content" class="space-y-12 max-w-6xl mx-auto">
                        
                        <div id="alert-box" class="hidden p-4 rounded-xl font-semibold text-sm transition-all"></div>

                        <div>
                            <div class="flex justify-between items-center mb-8">
                                <div>
                                    <h1 class="text-3xl font-semibold text-[#1C5B8F]">Mes Services</h1>
                                    <p class="text-gray-500 mt-1">Gérez les prestations que vous proposez à vos clients.</p>
                                </div>
                                <button onclick="toggleModal('serviceModal')" class="rounded-full px-6 py-2.5 bg-[#1C5B8F] text-white font-bold hover:bg-[#154670] transition-colors shadow-md flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Ajouter un service
                                </button>
                            </div>

                            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="text-gray-400 text-sm border-b border-gray-100">
                                                <th class="pb-4 font-medium px-4">Nom du Service</th>
                                                <th class="pb-4 font-medium px-4">Description</th>
                                                <th class="pb-4 font-medium px-4">Prix</th>
                                                <th class="pb-4 font-medium text-right px-4">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="services-table-body">
                                            <tr>
                                                <td colspan="4" class="py-8 text-center text-gray-500 text-sm">
                                                    <span class="animate-pulse">Chargement de vos services...</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-8">
                                <div>
                                    <h2 class="text-3xl font-semibold text-[#1C5B8F]">Mon Planning</h2>
                                    <p class="text-gray-500 mt-1">Vos créneaux ouverts à la réservation pour les 3 prochains mois.</p>
                                </div>
                                <button onclick="toggleModal('dispoModal')" class="rounded-full px-6 py-2.5 bg-[#E1AB2B] text-white font-bold hover:bg-[#c99723] transition-colors shadow-md flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    Générer des créneaux
                                </button>
                            </div>

                            <div id="dispos-container" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 max-h-[800px] overflow-y-auto space-y-6">
                                <div class="py-10 text-center text-gray-500 text-sm">
                                    <span class="animate-pulse">Chargement de votre planning...</span>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center py-20 rounded-[2.5rem] shadow-xl shadow-blue-900/10 m-8 bg-white border border-gray-100">
                        <p class="text-center font-semibold text-[#1C5B8F] text-2xl mb-8">Vous devez être connecté(e).</p>
                        <a class="rounded-full px-8 py-3 bg-[#1C5B8F] text-white font-bold hover:bg-[#154670] transition-colors shadow-md" href="/providers/account/signin.php">Je me connecte</a>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <div id="serviceModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 transition-opacity">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-lg p-8 m-4">
            <h2 class="text-2xl font-bold text-[#1C5B8F] mb-6">Ajouter un nouveau service</h2>
            <form id="add-service-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du service</label>
                    <input type="text" id="nom_service" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#1C5B8F] outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description courte</label>
                    <textarea id="desc_service" rows="3" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#1C5B8F] outline-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix unitaire (€)</label>
                    <input type="number" id="prix_service" step="0.01" min="0" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#1C5B8F] outline-none">
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" onclick="toggleModal('serviceModal')" class="px-6 py-2.5 rounded-full font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">Annuler</button>
                    <button type="submit" class="px-6 py-2.5 rounded-full font-bold text-white bg-[#1C5B8F] hover:bg-[#154670] transition-colors shadow-md">Créer le service</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editServiceModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 transition-opacity">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-lg p-8 m-4">
            <h2 class="text-2xl font-bold text-[#E1AB2B] mb-6">Modifier le service</h2>
            <form id="edit-service-form" class="space-y-4">
                <input type="hidden" id="edit_id_service">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du service</label>
                    <input type="text" id="edit_nom_service" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#E1AB2B] outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description courte</label>
                    <textarea id="edit_desc_service" rows="3" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#E1AB2B] outline-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix unitaire (€)</label>
                    <input type="number" id="edit_prix_service" step="0.01" min="0" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#E1AB2B] outline-none">
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" onclick="toggleModal('editServiceModal')" class="px-6 py-2.5 rounded-full font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">Annuler</button>
                    <button type="submit" class="px-6 py-2.5 rounded-full font-bold text-white bg-[#E1AB2B] hover:bg-[#c99723] transition-colors shadow-md">Sauvegarder</button>
                </div>
            </form>
        </div>
    </div>

    <div id="dispoModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 transition-opacity">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-lg p-8 m-4">
            <h2 class="text-2xl font-bold text-[#E1AB2B] mb-6">Ajouter des disponibilités</h2>
            <form id="add-dispo-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jour de la semaine</label>
                    <select id="jour_semaine" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#E1AB2B] outline-none">
                        <option value="1">Lundi</option>
                        <option value="2">Mardi</option>
                        <option value="3">Mercredi</option>
                        <option value="4">Jeudi</option>
                        <option value="5">Vendredi</option>
                        <option value="6">Samedi</option>
                        <option value="7">Dimanche</option>
                    </select>
                </div>
                <div class="flex gap-4">
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Heure de début</label>
                        <input type="time" id="heure_debut" value="09:00" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#E1AB2B] outline-none">
                    </div>
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Heure de fin</label>
                        <input type="time" id="heure_fin" value="17:00" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#E1AB2B] outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durée d'un rendez-vous</label>
                    <select id="duree_minutes" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#E1AB2B] outline-none">
                        <option value="30">30 minutes</option>
                        <option value="60" selected>1 heure</option>
                        <option value="90">1 heure 30</option>
                        <option value="120">2 heures</option>
                    </select>
                </div>
                <div class="bg-blue-50 text-[#1C5B8F] p-3 rounded-lg text-xs font-semibold mt-2 border border-blue-100">
                    ℹ️ Cela génèrera automatiquement tous les créneaux pour ce jour sur les 3 prochains mois. 
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" onclick="toggleModal('dispoModal')" class="px-6 py-2.5 rounded-full font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">Annuler</button>
                    <button type="submit" class="px-6 py-2.5 rounded-full font-bold text-white bg-[#E1AB2B] hover:bg-[#c99723] transition-colors shadow-md">Générer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentProviderId = null;

        function toggleModal(modalID) {
            document.getElementById(modalID).classList.toggle('hidden');
        }

        function showAlert(msg, isSuccess = false) {
            const alertBox = document.getElementById('alert-box');
            alertBox.textContent = msg;
            alertBox.className = `p-4 mb-6 rounded-xl font-bold block ${isSuccess ? 'text-green-700 bg-green-100 border border-green-400' : 'text-red-700 bg-red-100 border border-red-400'}`;
            alertBox.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
            setTimeout(() => alertBox.classList.add('hidden'), 5000); 
        }

        async function loadServices() {
            const tbody = document.getElementById('services-table-body');
            try {
                const res = await fetch(`${window.API_BASE_URL}/prestataire/services/${currentProviderId}/get`, { credentials: 'include' });
                if (!res.ok) throw new Error('Erreur');
                const services = await res.json();
                tbody.innerHTML = '';
                if (!services || services.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="4" class="py-10 text-center text-gray-500 font-medium">Vous n'avez pas encore de services.</td></tr>`;
                    return;
                }
                services.forEach(s => {
                    const tr = document.createElement('tr');
                    tr.className = "border-b border-gray-50 hover:bg-gray-50 transition-colors";
                    tr.innerHTML = `
                        <td class="py-4 px-4 text-sm font-semibold text-[#1C5B8F]">${s.nom}</td>
                        <td class="py-4 px-4 text-sm text-gray-600 max-w-xs truncate" title="${s.description}">${s.description}</td>
                        <td class="py-4 px-4 text-sm font-bold text-[#E1AB2B]">${parseFloat(s.prix).toFixed(2)} €</td>
                        <td class="py-4 px-4 text-right flex justify-end gap-2">
                            <button onclick="openEditModal(${s.id_service}, '${s.nom.replace(/'/g, "\\'")}', '${s.description.replace(/'/g, "\\'")}', ${s.prix})" class="text-[#E1AB2B] hover:text-[#c99723] font-bold text-sm bg-yellow-50 px-3 py-1 rounded-lg transition-colors">Modifier</button>
                            <button onclick="deleteService(${s.id_service})" class="text-red-500 hover:text-red-700 font-bold text-sm bg-red-50 px-3 py-1 rounded-lg transition-colors">Supprimer</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } catch (err) {
                tbody.innerHTML = `<tr><td colspan="4" class="py-8 text-center text-red-500 font-medium">Erreur lors du chargement.</td></tr>`;
            }
        }

        document.getElementById('add-service-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                nom: document.getElementById('nom_service').value,
                description: document.getElementById('desc_service').value,
                prix: parseFloat(document.getElementById('prix_service').value),
                id_categorie: null
            };
            try {
                const res = await fetch(`${window.API_BASE_URL}/prestataire/services/${currentProviderId}/create`, {
                    method: 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'include',
                    body: JSON.stringify(payload)
                });
                if (res.ok) {
                    toggleModal('serviceModal'); e.target.reset(); 
                    showAlert("Service ajouté !", true); loadServices(); 
                } else showAlert("Erreur lors de l'ajout.");
            } catch (err) { showAlert("Erreur serveur."); }
        });

        function openEditModal(id, nom, desc, prix) {
            document.getElementById('edit_id_service').value = id;
            document.getElementById('edit_nom_service').value = nom;
            document.getElementById('edit_desc_service').value = desc;
            document.getElementById('edit_prix_service').value = prix;
            toggleModal('editServiceModal');
        }

        document.getElementById('edit-service-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const idService = document.getElementById('edit_id_service').value;
            const payload = {
                nom: document.getElementById('edit_nom_service').value,
                description: document.getElementById('edit_desc_service').value,
                prix: parseFloat(document.getElementById('edit_prix_service').value)
            };
            try {
                const res = await fetch(`${window.API_BASE_URL}/prestataire/services/${currentProviderId}/${idService}/update`, {
                    method: 'PUT', headers: { 'Content-Type': 'application/json' }, credentials: 'include',
                    body: JSON.stringify(payload)
                });
                if (res.ok) {
                    toggleModal('editServiceModal');
                    showAlert("Service modifié !", true); loadServices();
                } else showAlert("Erreur modification.");
            } catch (err) { showAlert("Erreur serveur."); }
        });

        async function deleteService(idService) {
            if (!confirm("Voulez-vous vraiment supprimer ce service ?")) return;
            try {
                const res = await fetch(`${window.API_BASE_URL}/prestataire/services/${currentProviderId}/${idService}/delete`, { method: 'DELETE', credentials: 'include' });
                if (res.ok) { showAlert("Service supprimé.", true); loadServices(); } 
                else showAlert("Erreur suppression.");
            } catch (err) { showAlert("Erreur serveur."); }
        }

        async function loadDispos() {
            const container = document.getElementById('dispos-container');
            try {
                const res = await fetch(`${window.API_BASE_URL}/prestataire/disponibilites/${currentProviderId}/get`, { credentials: 'include' });
                if (!res.ok) throw new Error('Erreur réseau');
                
                const dispos = await res.json();
                container.innerHTML = '';

                if (!dispos || dispos.length === 0) {
                    container.innerHTML = `<div class="py-10 text-center text-gray-500 font-medium">Vous n'avez pas encore généré de disponibilités.</div>`;
                    return;
                }

                const grouped = {};
                dispos.forEach(d => {
                    const dateObj = new Date(d.date_heure);
                    const dateKey = dateObj.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                    
                    if (!grouped[dateKey]) grouped[dateKey] = [];
                    grouped[dateKey].push({
                        id: d.id_disponibilite,
                        time: dateObj.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
                        isReserved: d.est_reserve
                    });
                });

                Object.keys(grouped).forEach(dateStr => {
                    const dayDiv = document.createElement('div');
                    dayDiv.className = "bg-gray-50 rounded-2xl p-5 border border-gray-100";
                    
                    let pillsHtml = '';
                    grouped[dateStr].forEach(slot => {
                        const statusColor = slot.isReserved ? 'bg-red-500' : 'bg-green-500';
                        const tooltip = slot.isReserved ? 'Réservé' : 'Libre';
                        
                        pillsHtml += `
                            <div class="flex items-center bg-white border border-gray-200 rounded-full pl-4 pr-1 py-1.5 shadow-sm hover:shadow-md transition-all">
                                <span class="font-bold text-gray-700 text-sm mr-3">${slot.time}</span>
                                <span class="w-2.5 h-2.5 rounded-full ${statusColor} mr-3" title="${tooltip}"></span>
                                <button onclick="deleteDispo(${slot.id})" class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-1.5 rounded-full transition-colors outline-none" title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        `;
                    });

                    dayDiv.innerHTML = `
                        <h3 class="font-bold text-[#1C5B8F] mb-4 capitalize text-lg flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#E1AB2B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            ${dateStr}
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            ${pillsHtml}
                        </div>
                    `;
                    container.appendChild(dayDiv);
                });
            } catch (err) {
                container.innerHTML = `<div class="py-8 text-center text-red-500 font-medium">Erreur lors du chargement de votre planning.</div>`;
            }
        }

        document.getElementById('add-dispo-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                jour_semaine: parseInt(document.getElementById('jour_semaine').value),
                heure_debut: document.getElementById('heure_debut').value,
                heure_fin: document.getElementById('heure_fin').value,
                duree_minutes: parseInt(document.getElementById('duree_minutes').value)
            };

            try {
                const res = await fetch(`${window.API_BASE_URL}/prestataire/disponibilites/${currentProviderId}/create`, {
                    method: 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'include',
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (res.ok) {
                    toggleModal('dispoModal'); e.target.reset(); 
                    showAlert(data.message || "Créneaux générés avec succès !", true); loadDispos(); 
                } else showAlert(data.message || "Erreur de génération.");
            } catch (err) { showAlert("Erreur serveur."); }
        });

        async function deleteDispo(idDispo) {
            if (!confirm("Supprimer ce créneau unique ?")) return;
            try {
                const res = await fetch(`${window.API_BASE_URL}/prestataire/disponibilites/${currentProviderId}/${idDispo}/delete`, { method: 'DELETE', credentials: 'include' });
                if (res.ok) { showAlert("Créneau supprimé.", true); loadDispos(); } 
                else showAlert("Erreur suppression.");
            } catch (err) { showAlert("Erreur serveur."); }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const meRes = await fetch(`${window.API_BASE_URL}/auth/me-provider`, { method: 'GET', credentials: 'include' });
                if (meRes.ok) {
                    const data = await meRes.json();
                    currentProviderId = data.id_prestataire || data.id || data.ID;
                    loadServices(); loadDispos();
                } else window.location.href = "/providers/account/signin.php";
            } catch (err) { console.error("Non connecté"); }
        });
    </script>
</body>
</html>