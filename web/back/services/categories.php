<?php include("../includes/login.php"); ?>

<!DOCTYPE html>

<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories</title>
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
                <h1 class="title-text">Gestion des Catégories</h1>
                
                <div class="flex items-center gap-4">
                    <button onclick="toggleModal('add-modal')" class="add-button" type="button">
                        + Ajouter une Catégorie
                    </button>
                </div>
            </div>

            <div id="api-message" class="hidden max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold"></div>

            <div class="table-container bg-white shadow-md rounded-lg overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-[#1C5B8F] text-white">
                        <tr>
                            <th class="p-4 font-semibold">ID</th>
                            <th class="p-4 font-semibold">Nom</th>
                            <th class="p-4 font-semibold">Description</th>
                            <th class="p-4 font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="category-table-body" class="divide-y divide-gray-100">
                    </tbody>
                </table>
            </div>

            <div id="add-modal" class="hidden modal">
                <div class="add-modal">
                    <h3 class="text-2xl font-semibold text-[#1C5B8F] mb-6">Ajouter une Catégorie</h3>
                    <form id="add-form" class="space-y-4">
                        <div>
                            <label class="text-sm text-gray-500">Nom</label>
                            <input type="text" id="add-nom" class="add-input" required>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Description</label>
                            <textarea id="add-description" class="add-input" required></textarea>
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
                    <h3 class="text-2xl font-semibold text-[#E1AB2B] mb-6">Modifier la Catégorie</h3>
                    <form id="edit-form" class="space-y-4">
                        <input type="hidden" id="edit-id">
                        <div>
                            <label class="text-sm text-gray-500">Nom</label>
                            <input type="text" id="edit-nom" class="edit-input" required>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Description</label>
                            <textarea id="edit-description" class="edit-input" required></textarea>
                        </div>
                        <div class="flex justify-end gap-4 mt-8 pt-4">
                            <button type="button" onclick="toggleModal('edit-modal')" class="text-gray-400">Annuler</button>
                            <button type="submit" class="edit-button">Sauvegarder</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="delete-modal" class="hidden modal">
                <div class="delete-modal text-center">
                    <div class="text-red-500 text-6xl mb-4 font-bold">!</div>
                    <h3 class="text-2xl font-semibold mb-2">Supprimer la catégorie ?</h3>
                    <p class="text-gray-400 mb-8 font-light">Les services liés n'auront plus de catégorie associée.</p>
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
    const API_BASE_CATEGORIE = "http://localhost:8082/categorie";
    
    let currentPage = 1;
    const limit = 10;
    const messageBox = document.getElementById('api-message');

    function showAlert(msg, isSuccess) {
        messageBox.textContent = msg;
        messageBox.className = `max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold ${isSuccess ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'}`;
        messageBox.classList.remove('hidden');
        setTimeout(() => messageBox.classList.add('hidden'), 3500);
    }

    async function fetchCategories(page = 1) {
        try {
            currentPage = page;
            const url = `${API_BASE_CATEGORIE}/read?page=${currentPage}&limit=${limit}`;

            const response = await fetch(url);
            const result = await response.json();

            const categories = Array.isArray(result) ? result : (result.data || []); 
            const tbody = document.getElementById('category-table-body');
            tbody.innerHTML = '';

            if (categories.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="p-8 text-center text-gray-400">Aucune catégorie trouvée.</td></tr>';
                renderPagination(result.totalPages || 0, result.total || 0);
                return;
            }

            categories.forEach(c => {
                const id = c.id_categorie || c.id || c.ID;
                const nom = c.nom || c.Nom || '';
                const description = c.description || c.Description || '';

                tbody.innerHTML += `
                    <tr class="hover:bg-gray-50 transition border-b">
                        <td class="p-4 text-gray-400">#${id}</td>
                        <td class="p-4 font-medium">${nom}</td>
                        <td class="p-4 text-sm text-gray-600">${description}</td>
                        <td class="p-4 flex justify-center gap-4">
                            <button onclick="openEditModal(${id}, '${nom.replace(/'/g, "\\'")}', '${description.replace(/'/g, "\\'")}')" class="text-[#E1AB2B] bg-[#E1AB2B]/10 hover:bg-[#E1AB2B]/20 px-3 py-1 rounded-lg font-bold text-sm">Modifier</button>
                            <button onclick="openDeleteModal(${id})" class="text-[#FF0000] bg-[#FF0000]/10 hover:bg-[#FF0000]/20 px-3 py-1 rounded-lg font-bold text-sm">Supprimer</button>
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
            console.error(err);
            showAlert("Erreur lors de la récupération des catégories.", false);
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
                <span class="text-gray-500 font-semibold">Total : ${totalItems} catégories</span>
                <div class="flex gap-2">
                    <button ${currentPage === 1 ? 'disabled' : ''} onclick="fetchCategories(${currentPage - 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Précédent</button>
        `;

        for (let i = 1; i <= totalPages; i++) {
            const activeClass = i === currentPage ? 'bg-[#1C5B8F] text-white' : 'text-[#1C5B8F] hover:bg-blue-50';
            html += `<button onclick="fetchCategories(${i})" class="px-3 py-1 border border-[#1C5B8F] rounded transition ${activeClass}">${i}</button>`;
        }

        html += `
                    <button ${currentPage === totalPages ? 'disabled' : ''} onclick="fetchCategories(${currentPage + 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Suivant</button>
                </div>
            </div>
        `;
        paginationContainer.innerHTML = html;
    }

    document.getElementById('add-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const data = {
            nom: document.getElementById('add-nom').value,
            description: document.getElementById('add-description').value
        };

        try {
            const response = await fetch(`${API_BASE_CATEGORIE}/create`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            if (response.ok) {
                toggleModal('add-modal');
                e.target.reset();
                showAlert("Catégorie ajoutée !", true);
                fetchCategories(1);
            } else {
                showAlert("Erreur lors de l'ajout", false);
            }
        } catch (err) {
            showAlert("Erreur réseau", false);
        }
    });

    function openEditModal(id, nom, description) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-nom').value = nom;
        document.getElementById('edit-description').value = description;
        toggleModal('edit-modal');
    }

    document.getElementById('edit-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('edit-id').value;

        const data = {
            nom: document.getElementById('edit-nom').value,
            description: document.getElementById('edit-description').value
        };

        try {
            const res = await fetch(`${API_BASE_CATEGORIE}/update/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            if (res.ok) {
                toggleModal('edit-modal');
                showAlert("Modifications enregistrées", true);
                fetchCategories(currentPage);
            } else {
                showAlert("Erreur lors de la mise à jour", false);
            }
        } catch (err) {
            showAlert("Erreur réseau", false);
        }
    });

    function openDeleteModal(id) {
        document.getElementById('delete-id').value = id;
        toggleModal('delete-modal');
    }

    document.getElementById('confirm-delete').addEventListener('click', async () => {
        const id = document.getElementById('delete-id').value;
        try {
            const res = await fetch(`${API_BASE_CATEGORIE}/delete/${id}`, {
                method: 'DELETE'
            });
            if (res.ok) {
                toggleModal('delete-modal');
                showAlert("Catégorie supprimée", true);
                fetchCategories(currentPage);
            } else {
                showAlert("Erreur de suppression", false);
            }
        } catch (err) {
            showAlert("Erreur réseau", false);
        }
    });

    window.onload = async () => {
        fetchCategories(1);
    };
</script>
</body>

</html>