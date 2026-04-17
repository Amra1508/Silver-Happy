<?php include("../includes/login.php"); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Alata', 'sans-serif'], }
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
                <h1 class="title-text">Gestion des Services</h1>
                <div class="flex items-center gap-4">
                    <label for="filter-category" class="font-semibold text-gray-700">Filtrer par :</label>
                    <select id="filter-category" class="filter-select w-48" onchange="fetchServices(1)">
                        <option value="">Toutes les catégories</option>
                    </select>
                    <button onclick="toggleModal('add-modal')" class="add-button" type="button">
                        + Ajouter un Service
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
                            <th class="p-4 font-semibold">Catégorie</th>
                            <th class="p-4 font-semibold">Description</th>
                            <th class="p-4 font-semibold">Prix</th>
                            <th class="p-4 font-semibold">Prestataire</th>
                            <th class="p-4 font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="service-table-body" class="divide-y divide-gray-100"></tbody>
                </table>
            </div>

            <div id="add-modal" class="hidden modal">
                <div class="add-modal w-full max-w-lg">
                    <h3 class="text-2xl font-semibold text-[#1C5B8F] mb-6">Ajouter un Service</h3>
                    <form id="add-form" class="space-y-4">
                        <div>
                            <label class="text-sm text-gray-500">Nom</label>
                            <input type="text" id="add-nom" class="add-input w-full p-2 border rounded" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm text-gray-500">Prix (€)</label>
                                <input type="number" id="add-prix" step="0.01" min="0" class="add-input w-full p-2 border rounded" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">ID Prestataire</label>
                                <input type="number" id="add-prestataire" min="1" class="add-input w-full p-2 border rounded" required>
                            </div>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Catégorie</label>
                            <select id="add-categorie" class="add-input w-full p-2 border rounded">
                                <option value="">-- Sans catégorie --</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Description</label>
                            <textarea id="add-description" class="add-input w-full p-2 border rounded" rows="3" required></textarea>
                        </div>
                        <div class="flex justify-end gap-4 mt-8 pt-4">
                            <button type="button" onclick="toggleModal('add-modal')" class="text-gray-400">Annuler</button>
                            <button type="submit" class="add-button bg-[#1C5B8F] text-white px-4 py-2 rounded">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="edit-modal" class="hidden modal">
                <div class="edit-modal w-full max-w-lg">
                    <h3 class="text-2xl font-semibold text-[#E1AB2B] mb-6">Modifier le Service</h3>
                    <form id="edit-form" class="space-y-4">
                        <input type="hidden" id="edit-id">
                        <div>
                            <label class="text-sm text-gray-500">Nom</label>
                            <input type="text" id="edit-nom" class="edit-input w-full p-2 border rounded" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm text-gray-500">Prix (€)</label>
                                <input type="number" id="edit-prix" step="0.01" min="0" class="edit-input w-full p-2 border rounded" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">ID Prestataire</label>
                                <input type="number" id="edit-prestataire" min="1" class="edit-input w-full p-2 border rounded" required>
                            </div>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Catégorie</label>
                            <select id="edit-categorie" class="edit-input w-full p-2 border rounded">
                                <option value="">-- Sans catégorie --</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Description</label>
                            <textarea id="edit-description" class="edit-input w-full p-2 border rounded" rows="3" required></textarea>
                        </div>
                        <div class="flex justify-end gap-4 mt-8 pt-4">
                            <button type="button" onclick="toggleModal('edit-modal')" class="text-gray-400">Annuler</button>
                            <button type="submit" class="edit-button bg-[#E1AB2B] text-white px-4 py-2 rounded">Sauvegarder</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="delete-modal" class="hidden modal">
                <div class="delete-modal text-center bg-white p-8 rounded-lg shadow-xl">
                    <div class="text-red-500 text-6xl mb-4 font-bold">!</div>
                    <h3 class="text-2xl font-semibold mb-2">Supprimer le service ?</h3>
                    <p class="text-gray-400 mb-8 font-light">Cette action est irréversible.</p>
                    <input type="hidden" id="delete-id">
                    <div class="flex justify-center gap-6">
                        <button type="button" onclick="toggleModal('delete-modal')" class="text-gray-400">Annuler</button>
                        <button type="button" id="confirm-delete" class="delete-button bg-red-500 text-white px-4 py-2 rounded">Oui, supprimer</button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    const API_BASE_SERVICE = `${window.API_BASE_URL}/service`;
    const API_BASE_CATEGORIE = `${window.API_BASE_URL}/categorie`;
    
    let currentPage = 1;
    const limit = 10;
    const messageBox = document.getElementById('api-message');
    let categoriesData = []; 

    function showAlert(msg, isSuccess) {
        messageBox.textContent = msg;
        messageBox.className = `max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold ${isSuccess ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'}`;
        messageBox.classList.remove('hidden');
        setTimeout(() => messageBox.classList.add('hidden'), 3500);
    }

    async function fetchCategories() {
        try {
            const response = await fetch(`${API_BASE_CATEGORIE}/read`); 
            if(!response.ok) return;
            const result = await response.json();
            categoriesData = result.data || result || []; 
            populateCategorySelects();
        } catch (err) {
            console.error(err);
        }
    }

    function populateCategorySelects() {
        const filterSelect = document.getElementById('filter-category');
        const addSelect = document.getElementById('add-categorie');
        const editSelect = document.getElementById('edit-categorie');

        let optionsHTML = '';
        categoriesData.forEach(cat => {
            const id = cat.id_categorie || cat.id || cat.ID;
            const nom = cat.nom || cat.Nom || `Catégorie ${id}`;
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

    async function fetchServices(page = 1) {
        try {
            currentPage = page;
            const categoryId = document.getElementById('filter-category').value;
            
            let url = `${API_BASE_SERVICE}/read?page=${currentPage}&limit=${limit}`;

            const response = await fetch(url);
            if (!response.ok) throw new Error("Erreur serveur");
            const result = await response.json();

            let services = Array.isArray(result) ? result : (result.data || []); 

            if (categoryId) {
                services = services.filter(service => {
                    const idCat = service.id_categorie || service.IDCategorie;
                    return idCat == categoryId; 
                });
            }

            const tbody = document.getElementById('service-table-body');
            tbody.innerHTML = '';

            if (services.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-gray-400">Aucun service trouvé.</td></tr>';
                renderPagination(0, 0);
                return;
            }

            services.forEach(c => {
                const id = c.id_service || c.id || c.ID;
                const nom = c.nom || c.Nom || '';
                const description = c.description || c.Description || '';
                const idCat = c.id_categorie || c.IDCategorie || '';
                const nomCat = c.categorie_nom || getCategoryName(idCat);
                const prix = c.prix !== undefined ? parseFloat(c.prix).toFixed(2) + ' €' : '0.00 €';
                const idPrestataire = c.id_prestataire || c.IDPrestataire || '';
                const prestataireAffiche = idPrestataire ? `Prestataire #${idPrestataire}` : `<span class="text-red-400 italic">Non assigné</span>`;

                tbody.innerHTML += `
                    <tr class="hover:bg-gray-50 transition border-b">
                        <td class="p-4 text-gray-400">#${id}</td>
                        <td class="p-4 font-medium">${nom}</td>
                        <td class="p-4 text-sm text-gray-600">
                            <span class="bg-gray-100 px-2 py-1 rounded border">${nomCat}</span>
                        </td>
                        <td class="p-4 text-sm text-gray-600 max-w-xs truncate" title="${description}">${description}</td>
                        <td class="p-4 font-bold text-[#E1AB2B]">${prix}</td>
                        <td class="p-4 text-sm font-semibold text-[#1C5B8F]">${prestataireAffiche}</td>
                        <td class="p-4 flex justify-center gap-2">
                            <button onclick="openEditModal(${id}, '${nom.replace(/'/g, "\\'")}', '${description.replace(/'/g, "\\'")}', '${idCat}', ${c.prix || 0}, '${idPrestataire}')" class="text-[#E1AB2B] bg-[#E1AB2B]/10 hover:bg-[#E1AB2B]/20 px-3 py-1 rounded-lg font-bold text-sm">Modifier</button>
                            <button onclick="openDeleteModal(${id})" class="text-[#FF0000] bg-[#FF0000]/10 hover:bg-[#FF0000]/20 px-3 py-1 rounded-lg font-bold text-sm">Supprimer</button>
                        </td>
                    </tr>
                `;
            });

            if (categoryId) {
                 renderPagination(1, services.length);
            } else {
                if (Array.isArray(result) && !result.totalPages) {
                    renderPagination(0, 0); 
                } else {
                    renderPagination(result.totalPages, result.total);
                }
            }

        } catch (err) {
            showAlert("Erreur lors de la récupération des services.", false);
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
                <span class="text-gray-500 font-semibold">Total : ${totalItems} services</span>
                <div class="flex gap-2">
                    <button ${currentPage === 1 ? 'disabled' : ''} onclick="fetchServices(${currentPage - 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Précédent</button>
        `;

        for (let i = 1; i <= totalPages; i++) {
            const activeClass = i === currentPage ? 'bg-[#1C5B8F] text-white' : 'text-[#1C5B8F] hover:bg-blue-50';
            html += `<button onclick="fetchServices(${i})" class="px-3 py-1 border border-[#1C5B8F] rounded transition ${activeClass}">${i}</button>`;
        }

        html += `
                    <button ${currentPage === totalPages ? 'disabled' : ''} onclick="fetchServices(${currentPage + 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Suivant</button>
                </div>
            </div>
        `;
        paginationContainer.innerHTML = html;
    }

    document.getElementById('add-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        let catValue = document.getElementById('add-categorie').value;
        let idCategorie = catValue ? parseInt(catValue) : null; 
        
        const data = {
            nom: document.getElementById('add-nom').value,
            description: document.getElementById('add-description').value,
            id_categorie: idCategorie,
            prix: parseFloat(document.getElementById('add-prix').value),
            id_prestataire: parseInt(document.getElementById('add-prestataire').value)
        };

        try {
            const response = await fetch(`${API_BASE_SERVICE}/create`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            if (response.ok) {
                toggleModal('add-modal');
                e.target.reset();
                showAlert("Service ajouté !", true);
                fetchServices(1);
            } else {
                showAlert("Erreur lors de l'envoi", false);
            }
        } catch (err) { showAlert("Erreur réseau", false); }
    });

    function openEditModal(id, nom, description, idCat, prix, idPrestataire) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-nom').value = nom;
        document.getElementById('edit-description').value = description;
        document.getElementById('edit-categorie').value = idCat || ""; 
        document.getElementById('edit-prix').value = prix;
        document.getElementById('edit-prestataire').value = idPrestataire || "";
        toggleModal('edit-modal');
    }

    document.getElementById('edit-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('edit-id').value;
        let catValue = document.getElementById('edit-categorie').value;
        let idCategorie = catValue ? parseInt(catValue) : null;
        
        const data = {
            nom: document.getElementById('edit-nom').value,
            description: document.getElementById('edit-description').value,
            id_categorie: idCategorie,
            prix: parseFloat(document.getElementById('edit-prix').value),
            id_prestataire: parseInt(document.getElementById('edit-prestataire').value)
        };

        try {
            const res = await fetch(`${API_BASE_SERVICE}/update/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            if (res.ok) {
                toggleModal('edit-modal');
                showAlert("Modifications enregistrées", true);
                fetchServices(currentPage);
            } else {
                showAlert("Erreur lors de la mise à jour", false);
            }
        } catch (err) { showAlert("Erreur réseau", false); }
    });

    function openDeleteModal(id) {
        document.getElementById('delete-id').value = id;
        toggleModal('delete-modal');
    }

    document.getElementById('confirm-delete').addEventListener('click', async () => {
        const id = document.getElementById('delete-id').value;
        try {
            const res = await fetch(`${API_BASE_SERVICE}/delete/${id}`, { method: 'DELETE' });
            if (res.ok) {
                toggleModal('delete-modal');
                showAlert("Service supprimé", true);
                fetchServices(currentPage);
            } else {
                showAlert("Erreur de suppression", false);
            }
        } catch (err) { showAlert("Erreur réseau", false); }
    });

    window.onload = async () => {
        await fetchCategories();
        fetchServices(1);
    };
</script>
</body>
</html>