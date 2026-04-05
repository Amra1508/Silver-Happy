<?php include("../includes/login.php"); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codes</title>
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
                    <h1 class="title-text">Liste des codes</h1>
                    <a href="/back/services/products.php">
                        <button class="add-button" type="button">
                            Retour aux produits
                        </button>
                    </a>

                </div>

                <div id="api-message" class="hidden max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold"></div>

                <div class="table-container">
                    <table class="w-full text-left">
                        <thead class="bg-[#1C5B8F] text-white">
                            <tr>
                                <th class="p-4 font-semibold">Code</th>
                                <th class="p-4 font-semibold">Valeur</th>
                                <th class="p-4 font-semibold">Type</th>
                                <th class="p-4 font-semibold">Date d'expiration</th>
                                <th class="p-4 font-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="code-table-body" class="divide-y divide-gray-100">
                        </tbody>
                    </table>
                </div>
        </div>

        <div id="edit-code-modal" class="hidden modal">
            <div class="edit-modal">
                <h3 class="text-2xl font-semibold text-[#E1AB2B] mb-6">Modifier un Code Promo</h3>
                <form id="edit-code-form" class="space-y-6">
                    <input type="hidden" id="edit-id">
                    <div>
                        <label class="text-sm text-gray-500">Code</label>
                        <input type="text" id="edit-code" class="edit-input" required>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Valeur</label>
                        <input type="number" min="5" step="5" id="edit-valeur" class="edit-input" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-gray-500">Type</label>
                            <select id="edit-type" class="edit-input">
                                <option value="fixe">Fixe</option>
                                <option value="pourcentage">Pourcentage</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Date d'expiration</label>
                            <input type="datetime-local" id="edit-date" class="edit-input" required>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 mt-8 pt-4">
                        <button type="button" onclick="toggleModal('edit-code-modal')" class="text-gray-400">Annuler</button>
                        <button type="submit" class="edit-button">Modifier</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="delete-modal" class="hidden modal">
            <div class="delete-modal">
                <div class="text-red-500 text-6xl mb-4 font-bold">!</div>
                <h3 class="text-2xl font-semibold mb-2">Supprimer le code ?</h3>
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
        const API_BASE = window.API_BASE_URL;
        let currentPage = 1;
        const limit = 10;
        const messageBox = document.getElementById('api-message');

        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        const minDT = now.toISOString().slice(0, 16);
        document.getElementById('edit-date').min = minDT;

        function showAlert(msg, isSuccess) {
            messageBox.textContent = msg;
            messageBox.className = `max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold ${isSuccess ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'}`;
            messageBox.classList.remove('hidden');
            setTimeout(() => messageBox.classList.add('hidden'), 3500);
        }

        async function fetchCodes(page = 1) {
            try {
                currentPage = page;
                const response = await fetch(`${API_BASE}/code/read?page=${page}&limit=10`);
                const result = await response.json();

                const codes = result.data || [];
                const tbody = document.getElementById('code-table-body');
                tbody.innerHTML = '';

                if (codes.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-gray-400">Aucun code en base.</td></tr>';
                    renderPagination(0, 0);
                    return;
                }

                codes.forEach(c => {
                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-medium">${c.code}</td>
                            <td class="p-4 text-gray-600 text-sm">${c.valeur}</td>
                            <td class="p-4 font-bold text-[#1C5B8F]">${c.type}</td>
                            <td class="p-4">${c.date_expiration}</td>
                            <td class="p-4 flex justify-center gap-4">
                                <button onclick="openEditModal(${c.id_reduction}, '${c.code.replace(/'/g, "\\'")}', '${c.valeur}', '${c.type.replace(/'/g, "\\'")}', '${c.date_expiration}')" class="text-[#E1AB2B] bg-[#E1AB2B]/10 hover:bg-[#E1AB2B]/20 px-1 rounded-lg font-bold">Modifier</button>
                                <button onclick="openDeleteModal(${c.id_reduction})" class="text-[#FF0000] bg-[#FF0000]/10 hover:bg-[#FF0000]/20 px-1 rounded-lg font-bold">Supprimer</button>
                            </td>
                        </tr>
                    `;
                });

                renderPagination(result.totalPages, result.total);
            } catch (err) {
                showAlert("Erreur lors de la récupération des codes.", false);
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
                    <span class="text-gray-500 font-semibold">Total : ${totalItems} codes</span>
                    <div class="flex gap-2">
                        <button ${currentPage === 1 ? 'disabled' : ''} onclick="fetchCodes(${currentPage - 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Précédent</button>
            `;

            for (let i = 1; i <= totalPages; i++) {
                const activeClass = i === currentPage ? 'bg-[#1C5B8F] text-white' : 'text-[#1C5B8F] hover:bg-blue-50';
                html += `<button onclick="fetchCodes(${i})" class="px-3 py-1 border border-[#1C5B8F] rounded transition ${activeClass}">${i}</button>`;
            }

            html += `
                        <button ${currentPage === totalPages ? 'disabled' : ''} onclick="fetchCodes(${currentPage + 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Suivant</button>
                    </div>
                </div>
            `;
            paginationContainer.innerHTML = html;
        }

        function formatDateForInput(dateStr) {
            if (!dateStr) return "";
            const d = new Date(dateStr);
            if (isNaN(d)) return "";

            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            const hours = String(d.getHours()).padStart(2, '0');
            const minutes = String(d.getMinutes()).padStart(2, '0');

            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }

        function openEditModal(id, code, valeur, type, date_expiration) {
            const minDT = new Date();
            minDT.setMinutes(minDT.getMinutes() - minDT.getTimezoneOffset());
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-code').value = code;
            document.getElementById('edit-valeur').value = valeur;
            document.getElementById('edit-type').value = type;
            const inputEdit = document.getElementById('edit-date');
            inputEdit.min = minDT.toISOString().slice(0, 16);
            inputEdit.value = formatDateForInput(date_expiration);
            toggleModal('edit-code-modal');
        }

        document.getElementById('edit-code-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('edit-id').value;

            const formData = new FormData();
            formData.append('code', document.getElementById('edit-code').value);
            formData.append('valeur', document.getElementById('edit-valeur').value);
            formData.append('type', document.getElementById('edit-type').value);
            formData.append('date_expiration', document.getElementById('edit-date').value);

            try {
                const res = await fetch(`${API_BASE}/code/update/${id}`, {
                    method: 'PUT',
                    body: formData
                });

                if (res.ok) {
                    toggleModal('edit-code-modal');
                    showAlert("Code promo mis à jour !", true);
                    fetchCodes(currentPage);
                } else {
                    showAlert("Erreur lors de la mise à jour", false);
                }
            } catch (err) {
                console.error(err);
                showAlert("Erreur de connexion", false);
            }
        });

        function openDeleteModal(id) {
            document.getElementById('delete-id').value = id;
            toggleModal('delete-modal');
        }

        document.getElementById('confirm-delete').addEventListener('click', async () => {
            const id = document.getElementById('delete-id').value;
            try {
                const res = await fetch(`${API_BASE}/code/delete/${id}`, {
                    method: 'DELETE'
                });
                if (res.ok) {
                    toggleModal('delete-modal');
                    showAlert("Code supprimé", true);
                    fetchCodes(currentPage);
                }
            } catch (err) {
                showAlert("Erreur de suppression", false);
            }
        });

        window.onload = () => fetchCodes(1);
    </script>
</body>

</html>