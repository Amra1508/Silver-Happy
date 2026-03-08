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
                    <h1 class="title-text">Gestion des Événements</h1>
                    <button onclick="toggleModal('add-modal')" class="add-button" type="button">
                        + Ajouter un Événement
                    </button>
                </div>

                <div id="api-message" class="hidden max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold"></div>

                <div class="table-container">
                    <table class="w-full text-left">
                        <thead class="bg-[#1C5B8F] text-white">
                            <tr>
                                <th class="p-4 font-semibold">Image</th>
                                <th class="p-4 font-semibold">Nom</th>
                                <th class="p-4 font-semibold">Description</th>
                                <th class="p-4 font-semibold">Lieu</th>
                                <th class="p-4 font-semibold text-center">Places</th>
                                <th class="p-4 font-semibold">Début</th>
                                <th class="p-4 font-semibold">Fin</th>
                                <th class="p-4 font-semibold">Durée</th>
                                <th class="p-4 font-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="evenement-table-body" class="divide-y divide-gray-100">
                        </tbody>
                    </table>
                </div>

                <div id="add-modal" class="hidden modal">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-lg border border-[#1C5B8F] shadow-xl max-h-[90vh] overflow-y-auto">
                        <h3 class="text-2xl font-semibold text-[#1C5B8F] mb-6">Ajouter un Événement</h3>
                        <form id="add-form" class="space-y-4">
                            <div>
                                <label class="text-sm text-gray-500">Nom</label>
                                <input type="text" id="add-nom" class="w-full mt-1 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Description</label>
                                <textarea id="add-description" class="w-full mt-1 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Lieu</label>
                                    <input type="text" id="add-lieu" class="w-full mt-1 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Nombre de places</label>
                                    <input type="number" id="add-places" min="1" class="w-full mt-1 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Date de début</label>
                                    <input type="datetime-local" id="add-debut" class="w-full mt-1 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Date de fin</label>
                                    <input type="datetime-local" id="add-fin" class="w-full mt-1 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Image (Affiche)</label>
                                <input type="file" id="add-image" accept="image/*" class="w-full mt-1 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none">
                            </div>
                            <div class="flex justify-end gap-4 mt-8 pt-4">
                                <button type="button" onclick="toggleModal('add-modal')" class="text-gray-400">Annuler</button>
                                <button type="submit" class="add-button">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="edit-modal" class="hidden modal">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-lg border border-[#E1AB2B] shadow-xl max-h-[90vh] overflow-y-auto">
                        <h3 class="text-2xl font-semibold text-[#E1AB2B] mb-6">Modifier l'Événement</h3>
                        <form id="edit-form" class="space-y-4">
                            <input type="hidden" id="edit-id">
                            <div>
                                <label class="text-sm text-gray-500">Nom</label>
                                <input type="text" id="edit-nom" class="w-full mt-1 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Description</label>
                                <textarea id="edit-description" class="w-full mt-1 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Lieu</label>
                                    <input type="text" id="edit-lieu" class="w-full mt-1 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Nombre de places</label>
                                    <input type="number" id="edit-places" min="1" class="w-full mt-1 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Date de début</label>
                                    <input type="datetime-local" id="edit-debut" class="w-full mt-1 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Date de fin</label>
                                    <input type="datetime-local" id="edit-fin" class="w-full mt-1 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Nouvelle image (laisser vide pour conserver l'actuelle)</label>
                                <input type="file" id="edit-image" accept="image/*" class="w-full mt-1 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none">
                            </div>
                            <div class="flex justify-end gap-4 mt-8 pt-4">
                                <button type="button" onclick="toggleModal('edit-modal')" class="text-gray-400">Annuler</button>
                                <button type="submit" class="edit-button">Sauvegarder</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="delete-modal" class="hidden modal">
                    <div class="delete-modal">
                        <div class="text-red-500 text-6xl mb-4 font-bold">!</div>
                        <h3 class="text-2xl font-semibold mb-2">Supprimer l'événement ?</h3>
                        <p class="text-gray-400 mb-8 font-light">Cette action est irréversible.</p>
                        <input type="hidden" id="delete-id">
                        <div class="flex justify-center gap-6">
                            <button type="button" onclick="toggleModal('delete-modal')" class="text-gray-400">Annuler</button>
                            <button type="button" id="confirm-delete" class="delete-button">Oui, supprimer</button>
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

        async function fetchEvenements(page = 1) {
            try {
                currentPage = page;
                const response = await fetch(`${API_BASE}/evenement/read?page=${currentPage}&limit=${limit}`);

                if (!response.ok) {
                    await showErrorFromResponse(response, "Erreur lors de la récupération des événements.");
                    return;
                }

                const result = await response.json();
                const evenements = result.data || [];
                const tbody = document.getElementById('evenement-table-body');
                tbody.innerHTML = '';

                if (evenements.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="9" class="p-8 text-center text-gray-400">Aucun événement en base.</td></tr>';
                    renderPagination(0, 0);
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

                    const imgSrc = c.image ? `${API_BASE}/${c.image.replace(/\\/g, '/')}` : 'https://via.placeholder.com/150?text=Pas+d%27image';

                    const displayDebut = formatDisplayDate(date_debut);
                    const displayFin = formatDisplayDate(date_fin);
                    const duree = calculateDuration(date_debut, date_fin);

                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <img src="${imgSrc}" class="w-16 h-16 object-cover rounded-lg border border-gray-200" alt="Image event">
                            </td>
                            <td class="p-4 font-medium text-[#1C5B8F]">${nom}</td>
                            <td class="p-4 text-sm max-w-xs truncate" title="${description}">${description}</td>
                            <td class="p-4">${lieu}</td>
                            <td class="p-4 text-center font-bold">${places}</td>
                            <td class="p-4 text-sm">${displayDebut}</td>
                            <td class="p-4 text-sm">${displayFin}</td>
                            <td class="p-4 text-sm text-gray-600 font-medium">${duree}</td>
                            <td class="p-4 flex justify-center gap-4">
                                <button onclick="openEditModal(${id}, '${nom.replace(/'/g, "\\'")}', '${description.replace(/'/g, "\\'")}', '${lieu.replace(/'/g, "\\'")}', ${places}, '${date_debut}', '${date_fin}')" class="text-[#E1AB2B] font-bold hover:underline">Modifier</button>
                                <button onclick="openDeleteModal(${id})" class="text-red-500 font-bold hover:underline">Supprimer</button>
                            </td>
                        </tr>
                    `;
                });

                renderPagination(result.totalPages, result.total);
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

            if (totalItems === 0) {
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
            formData.append('date_debut', document.getElementById('add-debut').value);
            formData.append('date_fin', document.getElementById('add-fin').value);

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

        function openEditModal(id, nom, description, lieu, places, debut, fin) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nom').value = nom;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-lieu').value = lieu;
            document.getElementById('edit-places').value = places;
            document.getElementById('edit-debut').value = formatDateForInput(debut);
            document.getElementById('edit-fin').value = formatDateForInput(fin);
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
            formData.append('date_debut', document.getElementById('edit-debut').value);
            formData.append('date_fin', document.getElementById('edit-fin').value);

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

        window.onload = () => fetchEvenements(1);
    </script>
</body>

</html>