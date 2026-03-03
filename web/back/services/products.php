<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits</title>
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
                    <h1 class="text-3xl font-semibold text-[#1C5B8F]">Gestion des Produits</h1>
                    <button onclick="toggleModal('add-modal')" class="bg-[#1C5B8F] text-white py-2 px-6 rounded-full hover:bg-blue-800 transition" type="button">
                        + Ajouter un Produit
                    </button>
                </div>

                <div id="api-message" class="hidden max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold"></div>

                <div class="border border-[#1C5B8F] rounded-[2.5rem] overflow-hidden bg-white">
                    <table class="w-full text-left">
                        <thead class="bg-[#1C5B8F] text-white">
                            <tr>
                                <th class="p-4 font-semibold">Image</th>
                                <th class="p-4 font-semibold">Nom</th>
                                <th class="p-4 font-semibold">Description</th>
                                <th class="p-4 font-semibold">Prix</th>
                                <th class="p-4 font-semibold">Stock</th>
                                <th class="p-4 font-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="produit-table-body" class="divide-y divide-gray-100">
                        </tbody>
                    </table>
                </div>

                <div id="add-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-md border border-[#1C5B8F] shadow-xl">
                        <h3 class="text-2xl font-semibold text-[#1C5B8F] mb-6">Ajouter un Produit</h3>
                        <form id="add-form" class="space-y-6">
                            <div>
                                <label class="text-sm text-gray-500">Nom</label>
                                <input type="text" id="add-nom" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Description</label>
                                <textarea id="add-description" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Prix (€)</label>
                                    <input type="number" id="add-prix" min="1" step="0.01" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Stock</label>
                                    <input type="number" id="add-stock" min="1" step="1" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Image du produit</label>
                                <input type="file" id="add-image" accept="image/*" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none">
                            </div>
                            <div class="flex justify-end gap-4 mt-8 pt-4">
                                <button type="button" onclick="toggleModal('add-modal')" class="text-gray-400">Annuler</button>
                                <button type="submit" class="bg-[#1C5B8F] text-white px-8 py-2 rounded-full font-semibold">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="edit-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-md border border-[#E1AB2B] shadow-xl">
                        <h3 class="text-2xl font-semibold text-[#E1AB2B] mb-6">Modifier le Produit</h3>
                        <form id="edit-form" class="space-y-6">
                            <input type="hidden" id="edit-id">
                            <div>
                                <label class="text-sm text-gray-500">Nom</label>
                                <input type="text" id="edit-nom" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Description</label>
                                <textarea id="edit-description" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Prix (€)</label>
                                    <input type="number" id="edit-prix" min="1" step="0.01" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Stock</label>
                                    <input type="number" id="edit-stock" min="1" step="1" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Nouvelle image (laisser vide pour conserver l'actuelle)</label>
                                <input type="file" id="edit-image" accept="image/*" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none">
                            </div>
                            <div class="flex justify-end gap-4 mt-8 pt-4">
                                <button type="button" onclick="toggleModal('edit-modal')" class="text-gray-400">Annuler</button>
                                <button type="submit" class="bg-[#E1AB2B] text-white px-8 py-2 rounded-full font-semibold">Sauvegarder</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="delete-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-lg text-center border border-red-500 shadow-xl">
                        <div class="text-red-500 text-6xl mb-4 font-bold">!</div>
                        <h3 class="text-2xl font-semibold mb-2">Supprimer le produit ?</h3>
                        <p class="text-gray-400 mb-8 font-light">Cette action est irréversible.</p>
                        <input type="hidden" id="delete-id">
                        <div class="flex justify-center gap-6">
                            <button type="button" onclick="toggleModal('delete-modal')" class="px-8 py-2 border border-gray-200 rounded-full">Annuler</button>
                            <button type="button" id="confirm-delete" class="bg-red-500 text-white px-8 py-2 rounded-full font-semibold">Oui, supprimer</button>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        const API_BASE = "http://localhost:8082";
        const messageBox = document.getElementById('api-message');

        function showAlert(msg, isSuccess) {
            messageBox.textContent = msg;
            messageBox.className = `max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold ${isSuccess ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'}`;
            messageBox.classList.remove('hidden');
            setTimeout(() => messageBox.classList.add('hidden'), 3500);
        }

        async function fetchProduits() {
            try {
                const response = await fetch(`${API_BASE}/produit/read`);
                const produits = await response.json();
                const tbody = document.getElementById('produit-table-body');
                tbody.innerHTML = '';

                if (!produits || produits.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-gray-400">Aucun produit en base.</td></tr>';
                    return;
                }

                produits.forEach(p => {
                    const imgSrc = p.image ? `${API_BASE}/${p.image}` : 'https://via.placeholder.com/150';
                    
                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <img src="${imgSrc}" class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                            </td>
                            <td class="p-4 font-medium">${p.nom}</td>
                            <td class="p-4 text-gray-600 text-sm">${p.description}</td>
                            <td class="p-4 font-bold text-[#1C5B8F]">${p.prix} €</td>
                            <td class="p-4">${p.stock}</td>
                            <td class="p-4 flex justify-center gap-4">
                                <button onclick="openEditModal(${p.id}, '${p.nom.replace(/'/g, "\\'")}', '${p.description.replace(/'/g, "\\'")}', '${p.prix}', '${p.stock}')" class="text-[#E1AB2B] font-bold hover:underline">Modifier</button>
                                <button onclick="openDeleteModal(${p.id})" class="text-red-500 font-bold hover:underline">Supprimer</button>
                            </td>
                        </tr>
                    `;
                });
            } catch (err) {
                showAlert("Erreur lors de la récupération des produits.", false);
            }
        }

        document.getElementById('add-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('nom', document.getElementById('add-nom').value);
            formData.append('description', document.getElementById('add-description').value);
            formData.append('prix', document.getElementById('add-prix').value);
            formData.append('stock', document.getElementById('add-stock').value);
            
            const fileInput = document.getElementById('add-image');
            if (fileInput.files[0]) {
                formData.append('image', fileInput.files[0]);
            }

            try {
                const response = await fetch(`${API_BASE}/produit/create`, {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    toggleModal('add-modal');
                    e.target.reset();
                    showAlert("Produit ajouté avec succès !", true);
                    fetchProduits();
                } else {
                    showAlert("Erreur serveur lors de l'ajout.", false);
                }
            } catch (err) {
                showAlert("Erreur de connexion à l'API.", false);
            }
        });

        function openEditModal(id, nom, description, prix, stock) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nom').value = nom;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-prix').value = prix;
            document.getElementById('edit-stock').value = stock;
            document.getElementById('edit-image').value = '';
            toggleModal('edit-modal');
        }

        document.getElementById('edit-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('edit-id').value;
            
            const formData = new FormData();
            formData.append('nom', document.getElementById('edit-nom').value);
            formData.append('description', document.getElementById('edit-description').value);
            formData.append('prix', document.getElementById('edit-prix').value);
            formData.append('stock', document.getElementById('edit-stock').value);
            
            const fileInput = document.getElementById('edit-image');
            if (fileInput.files[0]) {
                formData.append('image', fileInput.files[0]);
            }

            try {
                const res = await fetch(`${API_BASE}/produit/update/${id}`, {
                    method: 'PUT',
                    body: formData
                });
                if (res.ok) {
                    toggleModal('edit-modal');
                    showAlert("Modifications enregistrées", true);
                    fetchProduits();
                } else {
                    showAlert("Erreur lors de la mise à jour", false);
                }
            } catch (err) {
                showAlert("Erreur de connexion à l'API", false);
            }
        });

        function openDeleteModal(id) {
            document.getElementById('delete-id').value = id;
            toggleModal('delete-modal');
        }

        document.getElementById('confirm-delete').addEventListener('click', async () => {
            const id = document.getElementById('delete-id').value;
            try {
                const res = await fetch(`${API_BASE}/produit/delete/${id}`, {
                    method: 'DELETE'
                });
                if (res.ok) {
                    toggleModal('delete-modal');
                    showAlert("Produit supprimé", true);
                    fetchProduits();
                }
            } catch (err) {
                showAlert("Erreur de suppression", false);
            }
        });

        window.onload = fetchProduits;
    </script>
</body>

</html>