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

<body>
    <div class="flex min-h-screen">

        <?php include("../includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col">
            <?php include("../includes/header.php"); ?>

            <main class="p-8">

                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-semibold text-[#1C5B8F]">Gestion des Seniors</h1>
                    <button onclick="toggleModal('add-modal')" class="bg-[#1C5B8F] text-white py-2 px-6 rounded-full hover:bg-blue-800 transition" type="button">
                        + Ajouter un Senior
                    </button>
                </div>

                <div id="api-message" class="hidden max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold"></div>

                <div class="border border-[#1C5B8F] rounded-[2.5rem] overflow-hidden bg-white">
                    <table class="w-full text-left">
                        <thead class="bg-[#1C5B8F] text-white">
                            <tr>
                                <th class="p-4 font-semibold">ID</th>
                                <th class="p-4 font-semibold">Identité</th>
                                <th class="p-4 font-semibold">Contact</th>
                                <th class="p-4 font-semibold">Infos du compte</th>
                                <th class="p-4 font-semibold">Bannissement</th>
                                <th class="p-4 font-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="senior-table-body" class="divide-y divide-gray-100">
                        </tbody>
                    </table>
                </div>

                <div id="add-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50 p-4">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-2xl border border-[#1C5B8F] shadow-xl overflow-y-auto max-h-[90vh]">
                        <h3 class="text-2xl font-semibold text-[#1C5B8F] mb-6">Ajouter un Senior</h3>
                        <form id="add-form" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Nom *</label>
                                    <input type="text" id="add-nom" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Prénom *</label>
                                    <input type="text" id="add-prenom" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Email *</label>
                                    <input type="email" id="add-email" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Téléphone</label>
                                    <input type="text" id="add-tel" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Date de naissance</label>
                                    <input type="date" id="add-date" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Statut</label>
                                    <select id="add-statut" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none">
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                        <option value="banni">Banni</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex justify-end gap-4 mt-8 pt-4">
                                <button type="button" onclick="toggleModal('add-modal')" class="text-gray-400">Annuler</button>
                                <button type="submit" class="bg-[#1C5B8F] text-white px-8 py-2 rounded-full font-semibold">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="edit-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50 p-4">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-2xl border border-[#E1AB2B] shadow-xl overflow-y-auto max-h-[90vh]">
                        <h3 class="text-2xl font-semibold text-[#E1AB2B] mb-6">Modifier le Senior</h3>
                        <form id="edit-form" class="space-y-4">
                            <input type="hidden" id="edit-id">
                            <input type="hidden" id="edit-motif">
                            <input type="hidden" id="edit-duree">

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Nom *</label>
                                    <input type="text" id="edit-nom" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Prénom *</label>
                                    <input type="text" id="edit-prenom" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none" required>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Email *</label>
                                    <input type="email" id="edit-email" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Téléphone</label>
                                    <input type="text" id="edit-tel" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Date de naissance</label>
                                    <input type="date" id="edit-date" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Statut</label>
                                    <select id="edit-statut" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none">
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex justify-end gap-4 mt-8 pt-4">
                                <button type="button" onclick="toggleModal('edit-modal')" class="text-gray-400">Annuler</button>
                                <button type="submit" class="bg-[#E1AB2B] text-white px-8 py-2 rounded-full font-semibold">Sauvegarder</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="ban-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50 p-4">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-lg border border-gray-700 shadow-xl overflow-y-auto max-h-[90vh]">
                        <h3 class="text-2xl font-semibold text-gray-700 mb-6">Bannir le Senior</h3>
                        <form id="ban-form" class="space-y-4">
                            <input type="hidden" id="ban-id">
                            <input type="hidden" id="ban-nom">
                            <input type="hidden" id="ban-prenom">
                            <input type="hidden" id="ban-email">
                            <input type="hidden" id="ban-tel">
                            <input type="hidden" id="ban-date">

                            <div>
                                <label class="text-sm text-gray-500">Motif du bannissement *</label>
                                <input type="text" id="ban-motif" class="w-full mt-2 p-3 border border-gray-700 rounded-xl focus:outline-none" required>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Durée du ban (jours) *</label>
                                <input type="number" id="ban-duree" min="1" class="w-full mt-2 p-3 border border-gray-700 rounded-xl focus:outline-none" required>
                            </div>

                            <div class="flex justify-between items-center mt-8 pt-4">
                                <button type="button" id="btn-unban" class="text-red-500 font-bold hover:underline hidden">Supprimer le ban</button>
                                <div class="flex gap-4 ml-auto">
                                    <button type="button" onclick="toggleModal('ban-modal')" class="text-gray-400">Annuler</button>
                                    <button type="submit" class="bg-gray-700 text-white px-8 py-2 rounded-full font-semibold">Bannir</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="delete-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-lg text-center border border-red-500 shadow-xl">
                        <div class="text-red-500 text-6xl mb-4 font-bold">!</div>
                        <h3 class="text-2xl font-semibold mb-2">Supprimer le senior ?</h3>
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
        const API_BASE = "http://localhost:8082/seniors";
        const messageBox = document.getElementById('api-message');

        function showAlert(msg, isSuccess) {
            messageBox.textContent = msg;
            messageBox.className = `max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold ${isSuccess ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'}`;
            messageBox.classList.remove('hidden');
            setTimeout(() => messageBox.classList.add('hidden'), 3500);
        }

        async function fetchSeniors() {
            try {
                const response = await fetch(`${API_BASE}/read`);
                const seniors = await response.json();
                const tbody = document.getElementById('senior-table-body');
                tbody.innerHTML = '';

                if (!seniors || seniors.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-gray-400">Aucun senior en base.</td></tr>';
                    return;
                }

                seniors.forEach(s => {
                    let banCol = '<span class="text-gray-400 italic">-</span>';
                    if (s.statut === 'banni') {
                        banCol = `<span class="text-red-500 font-bold">${s.motif_bannisement || ''}</span><br><span class="text-sm text-gray-500">${s.duree_bannissement || 0} j</span>`;
                    }

                    // ICI : on coupe l'heure de la date avec substring pour ne garder que YYYY-MM-DD
                    let dNaissance = s.date_naissance ? s.date_naissance.substring(0, 10) : '';
                    let dCreation = s.date_creation ? s.date_creation.replace('T', ' ').substring(0, 16) : '';

                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                            <td class="p-4 text-gray-400">#${s.id}</td>
                            <td class="p-4">
                                <span class="font-bold uppercase">${s.nom}</span> ${s.prenom}<br>
                                <span class="text-sm text-gray-500">${dNaissance}</span>
                            </td>
                            <td class="p-4">
                                ${s.email}<br>
                                <span class="text-sm text-gray-500">${s.num_telephone}</span>
                            </td>
                            <td class="p-4">
                                Statut: <strong>${s.statut}</strong><br>
                                <span class="text-sm text-gray-500">Créé le: ${dCreation}</span>
                            </td>
                            <td class="p-4">
                                ${banCol}
                            </td>
                            <td class="p-4 flex justify-center gap-6 items-center h-full mt-2">
                                <button onclick="openBanModal(${s.id}, '${s.nom}', '${s.prenom}', '${s.email}', '${s.num_telephone}', '${dNaissance}', '${s.statut}', '${s.motif_bannisement}', ${s.duree_bannissement})" class="text-gray-700 font-bold hover:underline">Bannir</button>
                                <button onclick="openEditModal(${s.id}, '${s.nom}', '${s.prenom}', '${s.email}', '${s.num_telephone}', '${dNaissance}', '${s.statut}', '${s.motif_bannisement}', ${s.duree_bannissement})" class="text-[#E1AB2B] font-bold hover:underline">Modifier</button>
                                <button onclick="openDeleteModal(${s.id})" class="text-red-500 font-bold hover:underline">Supprimer</button>
                            </td>
                        </tr>
                    `;
                });
            } catch (err) {
                showAlert("Erreur lors de la récupération.", false);
            }
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
                motif_bannisement: "",
                duree_bannissement: 0
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
                    showAlert("Senior ajouté !", true);
                    fetchSeniors();
                }
            } catch (err) {
                showAlert("Erreur lors de l'envoi", false);
            }
        });

        function openEditModal(id, nom, prenom, email, tel, date, statut, motif, duree) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nom').value = nom;
            document.getElementById('edit-prenom').value = prenom;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-tel').value = tel;
            
            // La date est propre maintenant, le formulaire l'accepte !
            document.getElementById('edit-date').value = date;
            
            document.getElementById('edit-statut').value = statut;
            document.getElementById('edit-motif').value = motif;
            document.getElementById('edit-duree').value = duree;
            
            toggleModal('edit-modal');
        }

        document.getElementById('edit-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('edit-id').value;
            const statutChoisi = document.getElementById('edit-statut').value;
            
            const data = {
                nom: document.getElementById('edit-nom').value,
                prenom: document.getElementById('edit-prenom').value,
                email: document.getElementById('edit-email').value,
                num_telephone: document.getElementById('edit-tel').value,
                date_naissance: document.getElementById('edit-date').value,
                statut: statutChoisi,
                motif_bannisement: document.getElementById('edit-motif').value,
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
                    showAlert("Modifications enregistrées", true);
                    fetchSeniors();
                }
            } catch (err) {
                showAlert("Erreur lors de la mise à jour", false);
            }
        });

        function openBanModal(id, nom, prenom, email, tel, date, statut, motif, duree) {
            document.getElementById('ban-id').value = id;
            document.getElementById('ban-nom').value = nom;
            document.getElementById('ban-prenom').value = prenom;
            document.getElementById('ban-email').value = email;
            document.getElementById('ban-tel').value = tel;
            document.getElementById('ban-date').value = date; 
            
            document.getElementById('ban-motif').value = motif;
            document.getElementById('ban-duree').value = duree > 0 ? duree : '';
            
            if(statut === 'banni') {
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
                nom: document.getElementById('ban-nom').value,
                prenom: document.getElementById('ban-prenom').value,
                email: document.getElementById('ban-email').value,
                num_telephone: document.getElementById('ban-tel').value,
                date_naissance: document.getElementById('ban-date').value,
                statut: 'banni', 
                motif_bannisement: document.getElementById('ban-motif').value,
                duree_bannissement: parseInt(document.getElementById('ban-duree').value) || 0
            };
            try {
                const res = await fetch(`${API_BASE}/update/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                if (res.ok) {
                    toggleModal('ban-modal');
                    showAlert("Utilisateur banni avec succès", true);
                    fetchSeniors();
                }
            } catch (err) {
                showAlert("Erreur serveur", false);
            }
        });

        document.getElementById('btn-unban').addEventListener('click', async () => {
            const id = document.getElementById('ban-id').value;
            const data = {
                nom: document.getElementById('ban-nom').value,
                prenom: document.getElementById('ban-prenom').value,
                email: document.getElementById('ban-email').value,
                num_telephone: document.getElementById('ban-tel').value,
                date_naissance: document.getElementById('ban-date').value,
                statut: 'user', 
                motif_bannisement: '', 
                duree_bannissement: 0  
            };
            try {
                const res = await fetch(`${API_BASE}/update/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                if (res.ok) {
                    toggleModal('ban-modal');
                    showAlert("Le bannissement a été retiré !", true);
                    fetchSeniors();
                }
            } catch (err) {
                showAlert("Erreur", false);
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
                    showAlert("Senior supprimé", true);
                    fetchSeniors();
                }
            } catch (err) {
                showAlert("Erreur de suppression", false);
            }
        });

        window.onload = fetchSeniors;
    </script>
</body>

</html>