<?php include("../includes/login.php"); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conseils</title>
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
    <div class="flex min-h-screen">

        <?php include("../includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col">
            <?php include("../includes/header.php"); ?>

            <main class="p-8">

                <div class="flex justify-between items-center mb-8">
                    <h1 class="title-text">Gestion des Conseils</h1>
                    <button onclick="toggleModal('add-modal')" class="add-button" type="button">
                        + Ajouter un Conseil
                    </button>
                </div>

                <div id="api-message" class="hidden"></div>

                <div class="table-container">
                    <table class="w-full text-left">
                        <thead class="bg-[#1C5B8F] text-white">
                            <tr>
                                <th class="p-4 font-semibold">ID</th>
                                <th class="p-4 font-semibold">Titre</th>
                                <th class="p-4 font-semibold">Description</th>
                                <th class="p-4 font-semibold">Date</th>
                                <th class="p-4 font-semibold">Catégorie</th>
                                <th class="p-4 font-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="conseil-table-body" class="divide-y divide-gray-100">
                        </tbody>
                    </table>
                </div>

                <div id="add-modal" class="hidden modal">
                    <div class="add-modal">
                        <h3 class="text-2xl font-semibold text-[#1C5B8F] mb-6">Ajouter un Conseil</h3>
                        <form id="add-form" class="space-y-6">
                            <div>
                                <label class="text-sm text-gray-500">Titre</label>
                                <input type="text" id="add-titre" class="add-input" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Description</label>
                                <textarea id="add-description" class="add-input" required></textarea>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Catégorie</label>
                                <input type="text" id="add-categorie" class="add-input" required>
                            </div>
                            <div class="flex justify-end gap-4 mt-8 pt-4">
                                <button type="button" onclick="toggleModal('add-modal')" class="text-gray-400">Annuler</button>
                                <button type="submit" class="add-button">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="edit-modal" class="hidden modal">
                    <div class="edit-modal">
                        <h3 class="text-2xl font-semibold text-[#E1AB2B] mb-6">Modifier le Conseil</h3>
                        <form id="edit-form" class="space-y-6">
                            <input type="hidden" id="edit-id">
                            <div>
                                <label class="text-sm text-gray-500">Titre</label>
                                <input type="text" id="edit-titre" class="edit-input" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Description</label>
                                <textarea id="edit-description" class="edit-input" required></textarea>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Catégorie</label>
                                <input type="text" id="edit-categorie" class="edit-input" required>
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
                        <h3 class="text-2xl font-semibold mb-2">Supprimer le conseil ?</h3>
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
        const API_BASE = "http://localhost:8082/conseil";
        let currentPage = 1;
        const limit = 10;
        const messageBox = document.getElementById('api-message');

        function showAlert(msg, isSuccess) {
            messageBox.textContent = msg;
            messageBox.className = `max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold ${isSuccess ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'}`;
            messageBox.classList.remove('hidden');
            setTimeout(() => messageBox.classList.add('hidden'), 3500);
        }

        async function fetchConseils(page = 1) {
            try {
                currentPage = page;
                const response = await fetch(`${API_BASE}/read?page=${currentPage}&limit=${limit}`);
                const result = await response.json();

                const conseils = result.data || [];
                const tbody = document.getElementById('conseil-table-body');
                tbody.innerHTML = '';

                if (conseils.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-gray-400">Aucun conseil en base.</td></tr>';
                    renderPagination(0, 0);
                    return;
                }

                conseils.forEach(c => {
                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-gray-400">#${c.id}</td>
                            <td class="p-4">${c.titre}</td>
                            <td class="p-4">${c.description}</td>
                            <td class="p-4">${c.date}</td>
                            <td class="p-4">${c.categorie}</td>
                            <td class="p-4 flex justify-center gap-8">
                                <button onclick="openEditModal(${c.id}, '${c.titre.replace(/'/g, "\\'")}', '${c.description.replace(/'/g, "\\'")}', '${c.categorie.replace(/'/g, "\\'")}')" class="text-[#E1AB2B] bg-[#E1AB2B]/10 hover:bg-[#E1AB2B]/20 px-1 rounded-lg font-bold">Modifier</button>
                                <button onclick="openDeleteModal(${c.id})" class="text-[#FF0000] bg-[#FF0000]/10 hover:bg-[#FF0000]/20 px-1 rounded-lg font-bold">Supprimer</button>
                            </td>
                        </tr>
                    `;
                });

                renderPagination(result.totalPages, result.total);
            } catch (err) {
                showAlert("Erreur lors de la récupération des conseils.", false);
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
                    <span class="text-gray-500 font-semibold">Total : ${totalItems} conseils</span>
                    <div class="flex gap-2">
                        <button ${currentPage === 1 ? 'disabled' : ''} onclick="fetchConseils(${currentPage - 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Précédent</button>
            `;

            for (let i = 1; i <= totalPages; i++) {
                const activeClass = i === currentPage ? 'bg-[#1C5B8F] text-white' : 'text-[#1C5B8F] hover:bg-blue-50';
                html += `<button onclick="fetchConseils(${i})" class="px-3 py-1 border border-[#1C5B8F] rounded transition ${activeClass}">${i}</button>`;
            }

            html += `
                        <button ${currentPage === totalPages ? 'disabled' : ''} onclick="fetchConseils(${currentPage + 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Suivant</button>
                    </div>
                </div>
            `;
            paginationContainer.innerHTML = html;
        }

        document.getElementById('add-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                titre: document.getElementById('add-titre').value,
                description: document.getElementById('add-description').value,
                categorie: document.getElementById('add-categorie').value
            };
            try {
                const response = await fetch(`${API_BASE}/create`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                if (response.ok) {
                    toggleModal('add-modal');
                    e.target.reset();
                    showAlert("Conseil ajouté !", true);
                    fetchConseils(1);
                }
            } catch (err) {
                showAlert("Erreur lors de l'envoi", false);
            }
        });

        function openEditModal(id, titre, description, categorie) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-titre').value = titre;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-categorie').value = categorie;
            toggleModal('edit-modal');
        }

        document.getElementById('edit-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('edit-id').value;
            const data = {
                titre: document.getElementById('edit-titre').value,
                description: document.getElementById('edit-description').value,
                categorie: document.getElementById('edit-categorie').value
            };
            try {
                const res = await fetch(`${API_BASE}/update/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                if (res.ok) {
                    toggleModal('edit-modal');
                    showAlert("Modifications enregistrées", true);
                    fetchConseils(currentPage);
                }
            } catch (err) {
                showAlert("Erreur lors de la mise à jour", false);
            }
        });

        function openDeleteModal(id) {
            document.getElementById('delete-id').value = id;
            toggleModal('delete-modal');
        }

        document.getElementById('confirm-delete').addEventListener('click', async () => {
            const id = document.getElementById('delete-id').value;
            try {
                const res = await fetch(`${API_BASE}/delete/${id}`, {
                    method: 'DELETE'
                });
                if (res.ok) {
                    toggleModal('delete-modal');
                    showAlert("Conseil supprimé", true);
                    fetchConseils(currentPage);
                }
            } catch (err) {
                showAlert("Erreur de suppression", false);
            }
        });

        window.onload = () => fetchConseils(1);
    </script>
</body>

</html>