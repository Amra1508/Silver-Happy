<?php include("../includes/login.php"); ?>
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
                        sans: ['Alata', 'sans-serif'],
                    }
                }
            }
        }

        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            if (modal) {
                modal.classList.toggle('hidden');
                modal.classList.toggle('flex');
            }
        }
    </script>
</head>

<body>
    <div class="flex min-h-screen bg-gray-50">

        <?php include("../includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col">
            <?php include("../includes/header.php"); ?>

            <main class="p-8">

                <div class="flex justify-between items-center mb-8">
                    <h1 class="title-text text-3xl font-semibold text-[#1C5B8F]">Gestion des Événements</h1>
                    
                    <div class="flex items-center gap-4">
                        <label for="filter-category" class="font-semibold text-gray-700">Filtrer par :</label>
                        <select id="filter-category" class="border border-gray-300 rounded p-2 text-sm w-48" onchange="fetchEvenements(1)">
                            <option value="">Toutes les catégories</option>
                            </select>
                        <button onclick="toggleModal('add-modal')" class="bg-[#1C5B8F] text-white px-6 py-2 rounded-full font-semibold hover:bg-blue-800 transition" type="button">
                            + Ajouter un Événement
                        </button>
                    </div>
                </div>

                <div id="api-message" class="hidden max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold"></div>

                <div class="table-container bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-[#1C5B8F] text-white">
                            <tr>
                                <th class="p-4 font-semibold">Image</th>
                                <th class="p-4 font-semibold">Nom</th>
                                <th class="p-4 font-semibold">Catégorie</th>
                                <th class="p-4 font-semibold">Description</th>
                                <th class="p-4 font-semibold">Lieu</th>
                                <th class="p-4 font-semibold text-center">Prix</th>
                                <th class="p-4 font-semibold text-center">Places</th>
                                <th class="p-4 font-semibold">Début</th>
                                <th class="p-4 font-semibold">Fin</th>
                                <th class="p-4 font-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="evenement-table-body" class="divide-y divide-gray-100">
                        </tbody>
                    </table>
                </div>

                <div id="add-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-2xl shadow-xl shadow-[#1C5B8F]/20">
                        <h3 class="text-2xl font-semibold text-[#1C5B8F] mb-6">Ajouter un Événement</h3>
                        <form id="add-form" class="space-y-4">
                            <div>
                                <label class="text-sm text-gray-500">Nom</label>
                                <input type="text" id="add-nom" class="w-full p-2 border rounded" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Catégorie</label>
                                <select id="add-categorie" class="w-full p-2 border rounded">
                                    <option value="">-- Sans catégorie --</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Description</label>
                                <textarea id="add-description" class="w-full p-2 border rounded" required></textarea>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Prix (€)</label>
                                <input type="number" step="0.01" min="0" id="add-prix" class="w-full p-2 border rounded" required>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Lieu</label>
                                    <input type="text" id="add-lieu" class="w-full p-2 border rounded" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Nombre de places</label>
                                    <input type="number" id="add-places" min="1" class="w-full p-2 border rounded" required>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Date de début</label>
                                    <input type="datetime-local" id="add-debut" class="w-full p-2 border rounded" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Date de fin</label>
                                    <input type="datetime-local" id="add-fin" class="w-full p-2 border rounded" required>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Image (Affiche)</label>
                                <input type="file" id="add-image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-[#D9D9D9]/40 file:text-[#1C5B8F]">
                            </div>
                            <div class="flex justify-end gap-4 mt-8 pt-4">
                                <button type="button" onclick="toggleModal('add-modal')" class="text-gray-400 font-semibold">Annuler</button>
                                <button type="submit" class="bg-[#1C5B8F] text-white px-6 py-2 rounded-full font-semibold hover:bg-blue-800">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="edit-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-2xl shadow-xl shadow-[#1C5B8F]/20">
                        <h3 class="text-2xl font-semibold text-[#E1AB2B] mb-6">Modifier l'Événement</h3>
                        <form id="edit-form" class="space-y-4">
                            <input type="hidden" id="edit-id">
                            <div>
                                <label class="text-sm text-gray-500">Nom</label>
                                <input type="text" id="edit-nom" class="w-full p-2 border rounded" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Catégorie</label>
                                <select id="edit-categorie" class="w-full p-2 border rounded">
                                    <option value="">-- Sans catégorie --</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Description</label>
                                <textarea id="edit-description" class="w-full p-2 border rounded" required></textarea>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Prix (€)</label>
                                <input type="number" step="0.01" min="0" id="edit-prix" class="w-full p-2 border rounded" required>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Lieu</label>
                                    <input type="text" id="edit-lieu" class="w-full p-2 border rounded" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Nombre de places</label>
                                    <input type="number" id="edit-places" min="1" class="w-full p-2 border rounded" required>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Date de début</label>
                                    <input type="datetime-local" id="edit-debut" class="w-full p-2 border rounded" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Date de fin</label>
                                    <input type="datetime-local" id="edit-fin" class="w-full p-2 border rounded" required>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Nouvelle image (laisser vide pour conserver l'actuelle)</label>
                                <input type="file" id="edit-image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-[#E1AB2B]/10 file:text-[#E1AB2B]">
                            </div>
                            <div class="flex justify-end gap-4 mt-8 pt-4">
                                <button type="button" onclick="toggleModal('edit-modal')" class="text-gray-400 font-semibold">Annuler</button>
                                <button type="submit" class="bg-[#E1AB2B] text-white px-6 py-2 rounded-full font-semibold hover:bg-yellow-600">Sauvegarder</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="delete-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-sm text-center shadow-xl shadow-red-500/10">
                        <div class="text-red-500 text-6xl mb-4 font-bold">!</div>
                        <h3 class="text-2xl font-semibold mb-2">Supprimer l'événement ?</h3>
                        <p class="text-gray-400 mb-8 font-light">Cette action est irréversible.</p>
                        <input type="hidden" id="delete-id">
                        <div class="flex justify-center gap-6">
                            <button type="button" onclick="toggleModal('delete-modal')" class="text-gray-400 font-semibold">Annuler</button>
                            <button type="button" id="confirm-delete" class="text-red-500 font-bold hover:text-red-700">Oui, supprimer</button>
                        </div>
                    </div>
                </div>

                <div id="link-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-2xl shadow-xl shadow-[#1C5B8F]/20">
                        <h3 class="text-2xl font-semibold text-[#1C5B8F] mb-6">Prestataires de l'événement</h3>
                        <input type="hidden" id="link-event-id">

                        <div class="mb-6 flex gap-4">
                            <select id="link-provider-select" class="flex-1 p-2 border rounded text-sm">
                            </select>
                            <button type="button" onclick="linkProvider()" class="bg-[#1C5B8F] text-white px-4 py-2 rounded-xl font-semibold hover:bg-blue-800 text-sm whitespace-nowrap">Lier le prestataire</button>
                        </div>

                        <div class="table-container max-h-64 overflow-y-auto border border-gray-100 rounded-xl">
                            <table class="w-full text-left">
                                <thead class="bg-[#F5F5F5] text-[#1C5B8F] text-sm sticky top-0">
                                    <tr>
                                        <th class="p-3 font-semibold">Nom du Prestataire</th>
                                        <th class="p-3 font-semibold">Prestation</th>
                                        <th class="p-3 font-semibold text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="linked-providers-body" class="divide-y divide-gray-100 text-sm">
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-end mt-8 pt-4 border-t border-gray-100">
                            <button type="button" onclick="toggleModal('link-modal')" class="text-gray-400 hover:text-gray-600 font-semibold">Fermer</button>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        const API_BASE = "http://localhost:8082";
        let currentPage = 1;
        const limit = 10;
        const messageBox = document.getElementById('api-message');
        
        let categoriesData = [];

        function showAlert(msg, isSuccess) {
            messageBox.textContent = msg;
            messageBox.className = `max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold ${isSuccess ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'}`;
            messageBox.classList.remove('hidden');
            setTimeout(() => messageBox.classList.add('hidden'), 5000);
        }

        async function showErrorFromResponse(response, defaultMsg) {
            try {
                const text = await response.text();
                try {
                    const json = JSON.parse(text);
                    const errorMessage = json.message || json.error || text || defaultMsg;
                    showAlert(errorMessage, false);
                } catch {
                    showAlert(text || defaultMsg, false);
                }
            } catch {
                showAlert(defaultMsg, false);
            }
        }

        function calculateDuration(start, end) {
            if (!start || !end) return "-";
            const startDate = new Date(start);
            const endDate = new Date(end);

            if (isNaN(startDate) || isNaN(endDate)) return "-";

            let diffMs = endDate - startDate;
            if (diffMs <= 0) return "0h";

            const diffDays = Math.floor(diffMs / 86400000);
            const diffHrs = Math.floor((diffMs % 86400000) / 3600000);
            const diffMins = Math.round(((diffMs % 86400000) % 3600000) / 60000);

            let duration = [];
            if (diffDays > 0) duration.push(`${diffDays}j`);
            if (diffHrs > 0) duration.push(`${diffHrs}h`);
            if (diffMins > 0) duration.push(`${diffMins}m`);

            return duration.join(' ');
        }

        function formatDateForInput(dateStr) {
            if (!dateStr) return "";
            const d = new Date(dateStr);
            if (isNaN(d)) return "";
            return d.toISOString().slice(0, 16);
        }

        function formatDisplayDate(dateStr) {
            if (!dateStr) return "-";
            const d = new Date(dateStr);
            if (isNaN(d)) return "-";
            return d.toLocaleString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        const minDT = now.toISOString().slice(0, 16);

        document.getElementById('add-debut').min = minDT;
        document.getElementById('add-fin').min = minDT;
        document.getElementById('edit-debut').min = minDT;
        document.getElementById('edit-fin').min = minDT;

        async function fetchCategories() {
            try {
                const response = await fetch(`${API_BASE}/categorie/read`); 
                if(!response.ok) return;
                
                const result = await response.json();
                categoriesData = result.data || result || []; 
                
                populateCategorySelects();
            } catch (err) {
                console.error("Erreur chargement catégories:", err);
            }
        }

        function populateCategorySelects() {
            const filterSelect = document.getElementById('filter-category');
            const addSelect = document.getElementById('add-categorie');
            const editSelect = document.getElementById('edit-categorie');

            let optionsHTML = '';
            categoriesData.forEach(cat => {
                const id = cat.id_categorie || cat.id || cat.ID;
                const nom = cat.nom || cat.Nom;
                optionsHTML += `<option value="${id}">${nom}</option>`;
            });

            filterSelect.innerHTML = `<option value="">Toutes les catégories</option>` + optionsHTML;
            addSelect.innerHTML = `<option value="">-- Sans catégorie --</option>` + optionsHTML;
            editSelect.innerHTML = `<option value="">-- Sans catégorie --</option>` + optionsHTML;
        }

        function getCategoryName(id) {
            if(!id) return "-";
            const cat = categoriesData.find(c => (c.id_categorie || c.id || c.ID) == id);
            return cat ? (cat.nom || cat.Nom) : "-";
        }

        async function fetchEvenements(page = 1) {
            try {
                currentPage = page;
                const categoryId = document.getElementById('filter-category').value;
                let url = `${API_BASE}/evenement/read?page=${currentPage}&limit=${limit}`;

                if(categoryId) {
                    url = `${API_BASE}/evenement/filter?categorie=${categoryId}`;
                }

                const response = await fetch(url);

                if (!response.ok) {
                    await showErrorFromResponse(response, "Erreur lors de la récupération des événements.");
                    return;
                }

                const result = await response.json();
                const evenements = Array.isArray(result) ? result : (result.data || []);
                const tbody = document.getElementById('evenement-table-body');
                tbody.innerHTML = '';

                if (evenements.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="10" class="p-8 text-center text-gray-400">Aucun événement trouvé.</td></tr>';
                    renderPagination(result.totalPages || 0, result.total || 0);
                    return;
                }

                evenements.forEach(c => {
                    const id = c.id_evenement || c.ID;
                    const nom = c.nom || c.Nom || '';
                    const description = c.description || c.Description || '';
                    const lieu = c.lieu || c.Lieu || '';
                    const places = c.nombre_place || c.NombrePlace || 0;
                    const date_debut = c.date_debut || c.DateDebut || '';
                    const date_fin = c.date_fin || c.DateFin || '';
                    const idCat = c.id_categorie || c.IDCategorie || ''; 
                    const prix = parseFloat(c.prix || c.Prix || 0);

                    const imgSrc = c.image ? `${API_BASE}/${c.image.replace(/\\/g, '/')}` : 'https://via.placeholder.com/150?text=Pas+d%27image';

                    const displayDebut = formatDisplayDate(date_debut);
                    const displayFin = formatDisplayDate(date_fin);
                    const displayPrix = prix > 0 ? `${prix.toFixed(2)} €` : 'Gratuit';

                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <img src="${imgSrc}" class="w-16 h-16 object-cover rounded-lg border border-gray-200" alt="Image event">
                            </td>
                            <td class="p-4 font-medium text-[#1C5B8F]">${nom}</td>
                            <td class="p-4 text-sm text-gray-600">
                                <span class="bg-gray-100 px-2 py-1 rounded border border-gray-200">${getCategoryName(idCat)}</span>
                            </td>
                            <td class="p-4 text-sm max-w-[200px] truncate" title="${description}">${description}</td>
                            <td class="p-4">${lieu}</td>
                            <td class="p-4 text-center font-bold text-gray-700">${displayPrix}</td>
                            <td class="p-4 text-center font-bold">${places}</td>
                            <td class="p-4 text-sm">${displayDebut}</td>
                            <td class="p-4 text-sm">${displayFin}</td>
                            <td class="p-4 flex justify-center gap-2">
                                <button onclick="openLinkModal(${id})" class="text-[#1C5B8F] bg-[#1C5B8F]/10 hover:bg-[#1C5B8F]/20 px-2 py-1 rounded-lg font-bold text-xs">Prestas</button>
                                <button onclick="openEditModal(${id}, '${nom.replace(/'/g, "\\'")}', '${description.replace(/'/g, "\\'")}', '${lieu.replace(/'/g, "\\'")}', ${places}, '${date_debut}', '${date_fin}', '${idCat}', ${prix})" class="text-[#E1AB2B] bg-[#E1AB2B]/10 hover:bg-[#E1AB2B]/20 px-2 py-1 rounded-lg font-bold text-xs">Edit</button>
                                <button onclick="openDeleteModal(${id})" class="text-[#FF0000] bg-[#FF0000]/10 hover:bg-[#FF0000]/20 px-2 py-1 rounded-lg font-bold text-xs">Suppr</button>
                            </td>
                        </tr>
                    `;
                });

                if(Array.isArray(result) && !result.totalPages) {
                    renderPagination(0, 0); 
                } else {
                    renderPagination(result.totalPages, result.total);
                }

            } catch (err) {
                showAlert(err.message || "Erreur réseau lors du chargement des événements.", false);
            }
        }

        function renderPagination(totalPages, totalItems) {
            let paginationContainer = document.getElementById('pagination-controls');

            if (!paginationContainer) {
                const tableContainer = document.querySelector('.table-container');
                paginationContainer = document.createElement('div');
                paginationContainer.id = 'pagination-controls';
                tableContainer.parentNode.insertBefore(paginationContainer, tableContainer.nextSibling);
            }

            if (totalItems === 0 || totalPages === 0) {
                paginationContainer.innerHTML = '';
                return;
            }

            let html = `
                <div class="flex justify-between items-center mt-6 px-4 text-sm">
                    <span class="text-gray-500 font-semibold">Total : ${totalItems} événements</span>
                    <div class="flex gap-2">
                        <button ${currentPage === 1 ? 'disabled' : ''} onclick="fetchEvenements(${currentPage - 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Précédent</button>
            `;

            for (let i = 1; i <= totalPages; i++) {
                const activeClass = i === currentPage ? 'bg-[#1C5B8F] text-white' : 'text-[#1C5B8F] hover:bg-blue-50';
                html += `<button onclick="fetchEvenements(${i})" class="px-3 py-1 border border-[#1C5B8F] rounded transition ${activeClass}">${i}</button>`;
            }

            html += `
                        <button ${currentPage === totalPages ? 'disabled' : ''} onclick="fetchEvenements(${currentPage + 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Suivant</button>
                    </div>
                </div>
            `;
            paginationContainer.innerHTML = html;
        }

        document.getElementById('add-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData();
            formData.append('nom', document.getElementById('add-nom').value);
            formData.append('description', document.getElementById('add-description').value);
            formData.append('lieu', document.getElementById('add-lieu').value);
            formData.append('nombre_place', document.getElementById('add-places').value);
            formData.append('prix', document.getElementById('add-prix').value);
            formData.append('date_debut', document.getElementById('add-debut').value);
            formData.append('date_fin', document.getElementById('add-fin').value);
            
            const catValue = document.getElementById('add-categorie').value;
            if(catValue) formData.append('id_categorie', catValue);

            const fileInput = document.getElementById('add-image');
            if (fileInput.files[0]) {
                formData.append('image', fileInput.files[0]);
            }

            try {
                const response = await fetch(`${API_BASE}/evenement/create`, {
                    method: 'POST',
                    body: formData
                });
                if (response.ok) {
                    toggleModal('add-modal');
                    e.target.reset();
                    showAlert("Événement ajouté avec succès !", true);
                    fetchEvenements(1);
                } else {
                    await showErrorFromResponse(response, "Erreur lors de l'ajout");
                }
            } catch (err) {
                showAlert(err.message || "Erreur réseau", false);
            }
        });

        function openEditModal(id, nom, description, lieu, places, debut, fin, idCat, prix) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nom').value = nom;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-lieu').value = lieu;
            document.getElementById('edit-places').value = places;
            document.getElementById('edit-prix').value = prix;
            document.getElementById('edit-debut').value = formatDateForInput(debut);
            document.getElementById('edit-fin').value = formatDateForInput(fin);
            document.getElementById('edit-categorie').value = idCat || ""; 
            document.getElementById('edit-image').value = '';
            toggleModal('edit-modal');
        }

        document.getElementById('edit-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('edit-id').value;

            const formData = new FormData();
            formData.append('nom', document.getElementById('edit-nom').value);
            formData.append('description', document.getElementById('edit-description').value);
            formData.append('lieu', document.getElementById('edit-lieu').value);
            formData.append('nombre_place', document.getElementById('edit-places').value);
            formData.append('prix', document.getElementById('edit-prix').value);
            formData.append('date_debut', document.getElementById('edit-debut').value);
            formData.append('date_fin', document.getElementById('edit-fin').value);
            
            const catValue = document.getElementById('edit-categorie').value;
            if(catValue) formData.append('id_categorie', catValue);

            const fileInput = document.getElementById('edit-image');
            if (fileInput.files[0]) {
                formData.append('image', fileInput.files[0]);
            }

            try {
                const res = await fetch(`${API_BASE}/evenement/update/${id}`, {
                    method: 'PUT',
                    body: formData
                });
                if (res.ok) {
                    toggleModal('edit-modal');
                    showAlert("Modifications enregistrées", true);
                    fetchEvenements(currentPage);
                } else {
                    await showErrorFromResponse(res, "Erreur lors de la mise à jour");
                }
            } catch (err) {
                showAlert(err.message || "Erreur réseau", false);
            }
        });

        function openDeleteModal(id) {
            document.getElementById('delete-id').value = id;
            toggleModal('delete-modal');
        }

        document.getElementById('confirm-delete').addEventListener('click', async () => {
            const id = document.getElementById('delete-id').value;
            try {
                const res = await fetch(`${API_BASE}/evenement/delete/${id}`, {
                    method: 'DELETE'
                });
                if (res.ok) {
                    toggleModal('delete-modal');
                    showAlert("Événement supprimé", true);
                    fetchEvenements(currentPage);
                } else {
                    await showErrorFromResponse(res, "Erreur de suppression");
                }
            } catch (err) {
                showAlert(err.message || "Erreur réseau", false);
            }
        });

        async function openLinkModal(eventId) {
            document.getElementById('link-event-id').value = eventId;
            toggleModal('link-modal');
            await loadAvailableProviders();
            await loadLinkedProviders(eventId);
        }

        async function loadAvailableProviders() {
            try {
                const res = await fetch(`${API_BASE}/prestataires/read?limit=100&status=validé`);
                const data = await res.json();
                const select = document.getElementById('link-provider-select');
                select.innerHTML = '<option value="">-- Sélectionner un prestataire à ajouter --</option>';

                if (data.data && data.data.length > 0) {
                    data.data.forEach(p => {
                        select.innerHTML += `<option value="${p.id}">${p.nom.toUpperCase()} ${p.prenom} (${p.type_prestation})</option>`;
                    });
                }
            } catch (err) {
                console.error("Erreur chargement prestataires", err);
            }
        }

        async function loadLinkedProviders(eventId) {
            const tbody = document.getElementById('linked-providers-body');
            tbody.innerHTML = '<tr><td colspan="3" class="p-4 text-center text-gray-400 text-xs">Chargement...</td></tr>';

            try {
                const res = await fetch(`${API_BASE}/evenement/prestataires/read/${eventId}`);
                const providers = await res.json();

                tbody.innerHTML = '';
                if (providers.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="p-4 text-center text-gray-400 italic">Aucun prestataire lié pour le moment.</td></tr>';
                    return;
                }

                providers.forEach(p => {
                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 font-medium">${p.nom.toUpperCase()} ${p.prenom}</td>
                            <td class="p-3 text-gray-500">${p.type}</td>
                            <td class="p-3 text-center">
                                <button onclick="unlinkProvider(${eventId}, ${p.id})" class="text-[#FF0000] font-bold text-lg hover:text-red-700" title="Retirer ce prestataire">&times;</button>
                            </td>
                        </tr>
                    `;
                });
            } catch (err) {
                tbody.innerHTML = '<tr><td colspan="3" class="p-4 text-center text-red-400">Erreur lors du chargement.</td></tr>';
            }
        }

        async function linkProvider() {
            const eventId = document.getElementById('link-event-id').value;
            const providerId = document.getElementById('link-provider-select').value;

            if (!providerId) return;

            try {
                const res = await fetch(`${API_BASE}/evenement/prestataires/link/${eventId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_prestataire: parseInt(providerId) })
                });

                if (res.ok) {
                    showAlert("Prestataire lié avec succès !", true);
                    await loadLinkedProviders(eventId);
                    document.getElementById('link-provider-select').value = "";
                } else {
                    showAlert("Erreur lors de la liaison.", false);
                }
            } catch (err) {
                showAlert("Erreur réseau.", false);
            }
        }

        async function unlinkProvider(eventId, providerId) {
            try {
                const res = await fetch(`${API_BASE}/evenement/prestataires/unlink/${eventId}/${providerId}`, {
                    method: 'DELETE'
                });

                if (res.ok) {
                    showAlert("Prestataire retiré de l'événement.", true);
                    await loadLinkedProviders(eventId);
                } else {
                    showAlert("Erreur lors de la suppression.", false);
                }
            } catch (err) {
                showAlert("Erreur réseau.", false);
            }
        }

        window.onload = async () => {
            await fetchCategories();
            fetchEvenements(1);
        };
    </script>
</body>

</html>