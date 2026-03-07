<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captcha</title>
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

        <?php include("./includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col">
            <?php include("./includes/header.php"); ?>

            <main class="p-8">

                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-semibold text-[#1C5B8F]">Gestion des Captchas</h1>
                    <button onclick="toggleModal('add-modal')" class="bg-[#1C5B8F] text-white py-2 px-6 rounded-full hover:bg-blue-800 transition" type="button">
                        + Ajouter un Captcha
                    </button>
                </div>

                <div id="api-message" class="hidden max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold"></div>

                <div class="border border-[#1C5B8F] rounded-[2.5rem] overflow-hidden bg-white">
                    <table class="w-full text-left">
                        <thead class="bg-[#1C5B8F] text-white">
                            <tr>
                                <th class="p-4 font-semibold">ID</th>
                                <th class="p-4 font-semibold">Question / Consigne</th>
                                <th class="p-4 font-semibold">Réponse attendue</th>
                                <th class="p-4 font-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="captcha-table-body" class="divide-y divide-gray-100">
                        </tbody>
                    </table>
                </div>

                <div id="add-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-md border border-[#1C5B8F] shadow-xl">
                        <h3 class="text-2xl font-semibold text-[#1C5B8F] mb-6">Ajouter un Captcha</h3>
                        <form id="add-form" class="space-y-6">
                            <div>
                                <label class="text-sm text-gray-500">Question ou consigne</label>
                                <input type="text" id="add-question" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Réponse exacte attendue</label>
                                <input type="text" id="add-reponse" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
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
                        <h3 class="text-2xl font-semibold text-[#E1AB2B] mb-6">Modifier le Captcha</h3>
                        <form id="edit-form" class="space-y-6">
                            <input type="hidden" id="edit-id">
                            <div>
                                <label class="text-sm text-gray-500">Question</label>
                                <input type="text" id="edit-question" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Réponse</label>
                                <input type="text" id="edit-reponse" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none" required>
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
                        <h3 class="text-2xl font-semibold mb-2">Supprimer le captcha ?</h3>
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
        const API_BASE = "http://localhost:8082/captcha";
        let currentPage = 1;
        const limit = 10;
        const messageBox = document.getElementById('api-message');

        function showAlert(msg, isSuccess) {
            messageBox.textContent = msg;
            messageBox.className = `max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold ${isSuccess ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'}`;
            messageBox.classList.remove('hidden');
            setTimeout(() => messageBox.classList.add('hidden'), 3500);
        }

        async function fetchCaptchas(page = 1) {
            try {
                currentPage = page;
                const response = await fetch(`${API_BASE}/read?page=${currentPage}&limit=${limit}`);
                const result = await response.json();
                
                const captchas = result.data || [];
                const tbody = document.getElementById('captcha-table-body');
                tbody.innerHTML = '';

                if (captchas.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="p-8 text-center text-gray-400">Aucun captcha en base.</td></tr>';
                    renderPagination(0, 0);
                    return;
                }

                captchas.forEach(c => {
                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-gray-400">#${c.id}</td>
                            <td class="p-4">${c.question}</td>
                            <td class="p-4">${c.reponse}</td>
                            <td class="p-4 flex justify-center gap-8">
                                <button onclick="openEditModal(${c.id}, '${c.question.replace(/'/g, "\\'")}', '${c.reponse.replace(/'/g, "\\'")}')" class="text-[#E1AB2B] font-bold">Modifier</button>
                                <button onclick="openDeleteModal(${c.id})" class="text-red-500 font-bold">Supprimer</button>
                            </td>
                        </tr>
                    `;
                });

                renderPagination(result.totalPages, result.total);
            } catch (err) {
                showAlert("Erreur lors de la récupération des captchas.", false);
            }
        }

        function renderPagination(totalPages, totalItems) {
            let paginationContainer = document.getElementById('pagination-controls');
            
            if (!paginationContainer) {
                const tableContainer = document.querySelector('.overflow-hidden.bg-white');
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
                    <span class="text-gray-500 font-semibold">Total : ${totalItems} captchas</span>
                    <div class="flex gap-2">
                        <button ${currentPage === 1 ? 'disabled' : ''} onclick="fetchCaptchas(${currentPage - 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Précédent</button>
            `;

            for (let i = 1; i <= totalPages; i++) {
                const activeClass = i === currentPage ? 'bg-[#1C5B8F] text-white' : 'text-[#1C5B8F] hover:bg-blue-50';
                html += `<button onclick="fetchCaptchas(${i})" class="px-3 py-1 border border-[#1C5B8F] rounded transition ${activeClass}">${i}</button>`;
            }

            html += `
                        <button ${currentPage === totalPages ? 'disabled' : ''} onclick="fetchCaptchas(${currentPage + 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Suivant</button>
                    </div>
                </div>
            `;
            paginationContainer.innerHTML = html;
        }

        document.getElementById('add-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                question: document.getElementById('add-question').value,
                reponse: document.getElementById('add-reponse').value
            };
            try {
                const response = await fetch(`${API_BASE}/create`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                if (response.ok) {
                    toggleModal('add-modal');
                    e.target.reset();
                    showAlert("Captcha ajouté !", true);
                    fetchCaptchas(1); 
                }
            } catch (err) {
                showAlert("Erreur lors de l'envoi", false);
            }
        });

        function openEditModal(id, question, reponse) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-question').value = question;
            document.getElementById('edit-reponse').value = reponse;
            toggleModal('edit-modal');
        }

        document.getElementById('edit-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('edit-id').value;
            const data = {
                question: document.getElementById('edit-question').value,
                reponse: document.getElementById('edit-reponse').value
            };
            try {
                const res = await fetch(`${API_BASE}/update/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                if (res.ok) {
                    toggleModal('edit-modal');
                    showAlert("Modifications enregistrées", true);
                    fetchCaptchas(currentPage);
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
                const res = await fetch(`${API_BASE}/delete/${id}`, { method: 'DELETE' });
                if (res.ok) {
                    toggleModal('delete-modal');
                    showAlert("Captcha supprimé", true);
                    fetchCaptchas(currentPage); 
                }
            } catch (err) {
                showAlert("Erreur de suppression", false);
            }
        });

        window.onload = () => fetchCaptchas(1);
    </script>
</body>

</html>