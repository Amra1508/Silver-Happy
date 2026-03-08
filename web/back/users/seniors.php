<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Seniors - Silver Happy</title>
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

<body class="bg-gray-50">
    <div class="flex min-h-screen">

        <?php include("../includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col">
            <?php include("../includes/header.php"); ?>

            <main class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <h1 class="title-text">Gestion des Seniors</h1>
                    <button onclick="toggleModal('add-modal')" class="add-button" type="button">
                        + Ajouter un Senior
                    </button>
                </div>

                <div id="api-message" class="hidden"></div>

                <div class="table-container">
                    <table class="w-full text-left">
                        <thead class="bg-[#1C5B8F] text-white">
                            <tr>
                                <th class="p-4 font-semibold">ID</th>
                                <th class="p-4 font-semibold">Identité</th>
                                <th class="p-4 font-semibold">Contact & Adresse</th>
                                <th class="p-4 font-semibold">Infos du compte</th>
                                <th class="p-4 font-semibold">Bannissement</th>
                                <th class="p-4 font-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="senior-table-body" class="divide-y divide-gray-100"></tbody>
                    </table>
                </div>

                <div id="add-modal" class="hidden modal">
                    <div class="add-modal">
                        <h3 class="text-2xl font-semibold text-[#1C5B8F] mb-6">Ajouter un Senior</h3>
                        <form id="add-form" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="text-sm text-gray-500">Nom *</label><input type="text" id="add-nom" class="add-input" required></div>
                                <div><label class="text-sm text-gray-500">Prénom *</label><input type="text" id="add-prenom" class="add-input" required></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="text-sm text-gray-500">Email *</label><input type="email" id="add-email" class="add-input" required></div>
                                <div><label class="text-sm text-gray-500">Téléphone</label><input type="text" id="add-tel" class="add-input"></div>
                            </div>
                            <div class="border-t border-gray-100 pt-4 mt-2">
                                <label class="text-sm text-[#1C5B8F] font-bold">Adresse</label>
                                <div class="grid grid-cols-1 mt-2 mb-4">
                                    <input type="text" id="add-adresse" placeholder="N° et Rue" class="add-input">
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <input type="text" id="add-cp" placeholder="Code Postal" class="add-input">
                                    <input type="text" id="add-ville" placeholder="Ville" class="add-input">
                                    <input type="text" id="add-pays" placeholder="Pays" class="add-input">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                                <div><label class="text-sm text-gray-500">Date de naissance</label><input type="date" id="add-date" class="add-input"></div>
                                <div><label class="text-sm text-gray-500">Statut</label>
                                    <select id="add-statut" class="add-input">
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
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
                        <h3 class="text-2xl font-semibold text-[#E1AB2B] mb-6">Modifier le Senior</h3>
                        <form id="edit-form" class="space-y-4">
                            <input type="hidden" id="edit-id"><input type="hidden" id="edit-motif"><input type="hidden" id="edit-duree">
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="text-sm text-gray-500">Nom *</label><input type="text" id="edit-nom" class="edit-input" required></div>
                                <div><label class="text-sm text-gray-500">Prénom *</label><input type="text" id="edit-prenom" class="edit-input" required></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="text-sm text-gray-500">Email *</label><input type="email" id="edit-email" class="edit-input" required></div>
                                <div><label class="text-sm text-gray-500">Téléphone</label><input type="text" id="edit-tel" class="edit-input"></div>
                            </div>
                            <div class="border-t border-gray-100 pt-4 mt-2">
                                <label class="text-sm text-[#E1AB2B] font-bold">Adresse</label>
                                <div class="grid grid-cols-1 mt-2 mb-4">
                                    <input type="text" id="edit-adresse" class="edit-input">
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <input type="text" id="edit-cp" class="edit-input">
                                    <input type="text" id="edit-ville" class="edit-input">
                                    <input type="text" id="edit-pays" class="edit-input">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                                <div><label class="text-sm text-gray-500">Date de naissance</label><input type="date" id="edit-date" class="edit-input"></div>
                                <div><label class="text-sm text-gray-500">Statut</label>
                                    <select id="edit-statut" class="edit-input">
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                        <option value="banni">Banni</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex justify-end gap-4 mt-8 pt-4">
                                <button type="button" onclick="toggleModal('edit-modal')" class="text-gray-400">Annuler</button>
                                <button type="submit" class="edit-button">Sauvegarder</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="ban-modal" class="hidden modal">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-lg shadow-xl shadow-black/20">
                        <h3 class="text-2xl font-semibold text-black mb-6">Bannissement</h3>
                        <form id="ban-form" class="space-y-4">
                            <input type="hidden" id="ban-id">
                            <div>
                                <label class="text-sm text-gray-500">Motif du bannissement *</label>
                                <input type="text" id="ban-motif" class="w-full mt-2 p-3 border border-black rounded-xl focus:outline-none focus:border-none focus:outline-1 focus:-outline-offset-1 focus:outline-[#8F9490]" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Durée du ban (en jours) *</label>
                                <input type="number" id="ban-duree" min="1" class="w-full mt-2 p-3 border border-black rounded-xl focus:outline-none focus:border-none focus:outline-1 focus:-outline-offset-1 focus:outline-[#8F9490]" required>
                            </div>
                            <div class="flex justify-between items-center mt-8 pt-4">
                                <button type="button" id="btn-unban" class="text-red-500 font-bold hover:underline hidden">Lever le ban</button>
                                <div class="flex gap-4 ml-auto">
                                    <button type="button" onclick="toggleModal('ban-modal')" class="text-gray-400">Annuler</button>
                                    <button type="submit" class="bg-black text-white px-8 py-2 rounded-full font-semibold">Bannir</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="delete-modal" class="hidden modal">
                    <div class="delete-modal">
                        <div class="text-red-500 text-6xl mb-4 font-bold">!</div>
                        <h3 class="text-2xl font-semibold mb-2">Supprimer le senior ?</h3>
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
        const API_BASE = "http://localhost:8082/seniors";
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

        async function fetchSeniors(page = 1) {
            try {
                currentPage = page;
                const response = await fetch(`${API_BASE}/read?page=${currentPage}&limit=${limit}`);

                if (!response.ok) {
                    await showErrorFromResponse(response, "Erreur lors de la récupération des seniors.");
                    return;
                }

                const result = await response.json();

                const seniors = result.data || [];
                const tbody = document.getElementById('senior-table-body');
                tbody.innerHTML = '';

                if (seniors.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-gray-400">Aucun senior en base.</td></tr>';
                    renderPagination(0, 0);
                    return;
                }

                seniors.forEach(s => {
                    let dNaissance = s.date_naissance ? s.date_naissance.substring(0, 10) : '';

                    let banCol = '<span class="text-gray-400 italic">-</span>';
                    if (s.statut === 'banni') {
                        banCol = `<span class="text-red-500 font-bold">${s.motif_bannisement || s.motif_bannissement || 'Non précisé'}</span><br><span class="text-sm text-gray-500">${s.duree_bannissement || 0} jours</span>`;
                    }

                    const s_nom = s.nom ? s.nom.replace(/'/g, "\\'") : '';
                    const s_prenom = s.prenom ? s.prenom.replace(/'/g, "\\'") : '';
                    const s_adresse = s.adresse ? s.adresse.replace(/'/g, "\\'") : '';
                    const s_ville = s.ville ? s.ville.replace(/'/g, "\\'") : '';
                    const s_cp = s.code_postal ? s.code_postal.replace(/'/g, "\\'") : '';
                    const s_pays = s.pays ? s.pays.replace(/'/g, "\\'") : '';
                    const s_motif = s.motif_bannisement || s.motif_bannissement ? (s.motif_bannisement || s.motif_bannissement).replace(/'/g, "\\'") : '';

                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 border-b">
                            <td class="p-4 text-gray-400">#${s.id}</td>
                            <td class="p-4">
                                <span class="font-bold uppercase">${s.nom}</span> ${s.prenom}<br>
                                <span class="text-sm text-gray-500">${dNaissance}</span>
                            </td>
                            <td class="p-4 text-sm">
                                ${s.email}<br>
                                ${s.num_telephone || ''}<br>
                                <span class="text-xs text-gray-400">${s.adresse || ''} ${s.code_postal || ''} ${s.ville || ''}</span>
                            </td>
                            <td class="p-4">
                                Statut: <strong>${s.statut}</strong>
                            </td>
                            <td class="p-4">${banCol}</td>
                            <td class="p-4 flex justify-center gap-3 mt-2">
                                <button onclick="openBanModal(${s.id}, '${s.statut}', '${s_motif}', ${s.duree_bannissement || 0})" class="text-gray-700 font-bold">Bannir</button>
                                <button onclick="openEditModal(${s.id}, '${s_nom}', '${s_prenom}', '${s.email}', '${s.num_telephone || ''}', '${dNaissance}', '${s.statut}', '${s_adresse}', '${s_ville}', '${s_cp}', '${s_pays}', '${s_motif}', ${s.duree_bannissement || 0})" class="text-[#E1AB2B] font-bold">Modifier</button>
                                <button onclick="openDeleteModal(${s.id})" class="text-red-500 font-bold">Supprimer</button>
                            </td>
                        </tr>
                    `;
                });

                renderPagination(result.totalPages, result.total);
            } catch (err) {
                showAlert(err.message || "Erreur réseau lors de la récupération des seniors.", false);
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
                    <span class="text-gray-500 font-semibold">Total : ${totalItems} seniors</span>
                    <div class="flex gap-2">
                        <button ${currentPage === 1 ? 'disabled' : ''} onclick="fetchSeniors(${currentPage - 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Précédent</button>
            `;

            for (let i = 1; i <= totalPages; i++) {
                const activeClass = i === currentPage ? 'bg-[#1C5B8F] text-white' : 'text-[#1C5B8F] hover:bg-blue-50';
                html += `<button onclick="fetchSeniors(${i})" class="px-3 py-1 border border-[#1C5B8F] rounded transition ${activeClass}">${i}</button>`;
            }

            html += `
                        <button ${currentPage === totalPages ? 'disabled' : ''} onclick="fetchSeniors(${currentPage + 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Suivant</button>
                    </div>
                </div>
            `;
            paginationContainer.innerHTML = html;
        }

        document.getElementById('add-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                nom: document.getElementById('add-nom').value,
                prenom: document.getElementById('add-prenom').value,
                email: document.getElementById('add-email').value,
                num_telephone: document.getElementById('add-tel').value,
                date_naissance: document.getElementById('add-date').value,
                statut: document.getElementById('add-statut').value,
                adresse: document.getElementById('add-adresse').value,
                ville: document.getElementById('add-ville').value,
                code_postal: document.getElementById('add-cp').value,
                pays: document.getElementById('add-pays').value,
                motif_bannissement: "",
                duree_bannissement: 0
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
                    fetchSeniors(1);
                    showAlert("Senior créé !", true);
                } else {
                    await showErrorFromResponse(response, "Erreur lors de la création");
                }
            } catch (err) {
                showAlert(err.message || "Erreur réseau", false);
            }
        });

        function openEditModal(id, nom, prenom, email, tel, date, statut, adresse, ville, cp, pays, motif, duree) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nom').value = nom;
            document.getElementById('edit-prenom').value = prenom;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-tel').value = tel;
            document.getElementById('edit-date').value = date;
            document.getElementById('edit-statut').value = statut;
            document.getElementById('edit-adresse').value = adresse;
            document.getElementById('edit-ville').value = ville;
            document.getElementById('edit-cp').value = cp;
            document.getElementById('edit-pays').value = pays;
            document.getElementById('edit-motif').value = motif;
            document.getElementById('edit-duree').value = duree;

            toggleModal('edit-modal');
        }

        document.getElementById('edit-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('edit-id').value;
            const data = {
                nom: document.getElementById('edit-nom').value,
                prenom: document.getElementById('edit-prenom').value,
                email: document.getElementById('edit-email').value,
                num_telephone: document.getElementById('edit-tel').value,
                date_naissance: document.getElementById('edit-date').value,
                statut: document.getElementById('edit-statut').value,
                adresse: document.getElementById('edit-adresse').value,
                ville: document.getElementById('edit-ville').value,
                code_postal: document.getElementById('edit-cp').value,
                pays: document.getElementById('edit-pays').value,
                motif_bannissement: document.getElementById('edit-motif').value,
                duree_bannissement: parseInt(document.getElementById('edit-duree').value) || 0
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
                    fetchSeniors(currentPage);
                    showAlert("Modifications enregistrées", true);
                } else {
                    await showErrorFromResponse(res, "Erreur lors de la mise à jour");
                }
            } catch (err) {
                showAlert(err.message || "Erreur réseau", false);
            }
        });

        function openBanModal(id, statut, motif, duree) {
            document.getElementById('ban-id').value = id;
            document.getElementById('ban-motif').value = motif;
            document.getElementById('ban-duree').value = duree > 0 ? duree : '';

            if (statut === 'banni') {
                document.getElementById('btn-unban').classList.remove('hidden');
            } else {
                document.getElementById('btn-unban').classList.add('hidden');
            }
            toggleModal('ban-modal');
        }

        document.getElementById('ban-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('ban-id').value;
            const data = {
                statut: 'banni',
                motif_bannissement: document.getElementById('ban-motif').value,
                duree_bannissement: parseInt(document.getElementById('ban-duree').value) || 0
            };

            try {
                const res = await fetch(`${API_BASE}/ban/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (res.ok) {
                    toggleModal('ban-modal');
                    fetchSeniors(currentPage);
                    showAlert("Utilisateur banni !", true);
                } else {
                    await showErrorFromResponse(res, "Erreur lors du bannissement");
                }
            } catch (err) {
                showAlert(err.message || "Erreur réseau", false);
            }
        });

        document.getElementById('btn-unban').addEventListener('click', async () => {
            const id = document.getElementById('ban-id').value;
            const data = {
                statut: 'user',
                motif_bannissement: '',
                duree_bannissement: 0
            };

            try {
                const res = await fetch(`${API_BASE}/ban/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (res.ok) {
                    toggleModal('ban-modal');
                    fetchSeniors(currentPage);
                    showAlert("Le bannissement a été retiré", true);
                } else {
                    await showErrorFromResponse(res, "Erreur lors de la levée du bannissement");
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
                const res = await fetch(`${API_BASE}/delete/${id}`, {
                    method: 'DELETE'
                });

                if (res.ok) {
                    toggleModal('delete-modal');
                    fetchSeniors(currentPage);
                    showAlert("Senior supprimé", true);
                } else {
                    await showErrorFromResponse(res, "Erreur lors de la suppression");
                }
            } catch (err) {
                showAlert(err.message || "Erreur réseau", false);
            }
        });

        window.onload = () => fetchSeniors(1);
    </script>
</body>

</html>