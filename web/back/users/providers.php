<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Prestataires - Silver Happy</title>
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
                    <h1 class="text-3xl font-semibold text-[#1C5B8F]">Gestion des Prestataires</h1>
                    <button onclick="toggleModal('add-modal')" class="bg-[#1C5B8F] text-white py-2 px-6 rounded-full hover:bg-blue-800 transition" type="button">
                        + Ajouter un Prestataire
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
                                <th class="p-4 font-semibold">Prestation</th>
                                <th class="p-4 font-semibold">Statut Dossier</th>
                                <th class="p-4 font-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="prestataire-table-body" class="divide-y divide-gray-100">
                        </tbody>
                    </table>
                </div>

                <div id="voir-plus-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-2xl border border-[#1C5B8F] shadow-xl overflow-y-auto max-h-[90vh]">
                        <h3 class="text-2xl font-semibold text-[#1C5B8F] mb-6">Détails du Prestataire</h3>

                        <div class="grid grid-cols-2 gap-8 text-sm mb-6">
                            <div class="space-y-2">
                                <h4 class="font-bold text-gray-700 text-base mb-2 border-b pb-1">Informations</h4>
                                <p><span class="text-gray-500">Nom :</span> <strong id="vp-nom" class="uppercase"></strong> <strong id="vp-prenom"></strong></p>
                                <p><span class="text-gray-500">Email :</span> <strong id="vp-email"></strong></p>
                                <p><span class="text-gray-500">Téléphone :</span> <strong id="vp-tel"></strong></p>
                                <p><span class="text-gray-500">Naissance :</span> <strong id="vp-date"></strong></p>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-bold text-gray-700 text-base mb-2 border-b pb-1">Activité</h4>
                                <p><span class="text-gray-500">SIRET :</span> <strong id="vp-siret"></strong></p>
                                <p><span class="text-gray-500">Prestation :</span> <strong id="vp-type"></strong></p>
                                <p><span class="text-gray-500">Tarifs :</span> <strong id="vp-tarifs"></strong> €</p>
                                <p><span class="text-gray-500">Dossier :</span> <strong id="vp-validation"></strong></p>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-8">
                            <h4 class="font-bold text-gray-700 mb-2">Documents du prestataire</h4>
                            <div id="vp-documents-list" class="text-sm text-gray-500">
                                <i>Aucun document lié pour le moment.</i>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                            <button type="button" onclick="triggerDelete()" class="text-red-500 font-bold hover:underline">Supprimer ce compte</button>
                            <div class="flex gap-4">
                                <button type="button" onclick="toggleModal('voir-plus-modal')" class="text-gray-400">Fermer</button>
                                <button type="button" onclick="triggerEdit()" class="bg-[#E1AB2B] text-white px-6 py-2 rounded-full font-semibold">Modifier</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="add-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50 p-4">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-2xl border border-[#1C5B8F] shadow-xl overflow-y-auto max-h-[90vh]">
                        <h3 class="text-2xl font-semibold text-[#1C5B8F] mb-6">Ajouter un Prestataire</h3>
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
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">N° SIRET</label>
                                    <input type="text" id="add-siret" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Type de prestation</label>
                                    <input type="text" id="add-type" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Tarifs (€)</label>
                                    <input type="number" min="1" step="0.01" id="add-tarifs" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Date de naissance *</label>
                                    <input type="date" id="add-date" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Validation Dossier</label>
                                    <select id="add-valide" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none">
                                        <option value="0">En attente</option>
                                        <option value="1">Validé</option>
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
                        <h3 class="text-2xl font-semibold text-[#E1AB2B] mb-6">Modifier le Prestataire</h3>
                        <form id="edit-form" class="space-y-4">
                            <input type="hidden" id="edit-id">

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
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">N° SIRET</label>
                                    <input type="text" id="edit-siret" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Type de prestation</label>
                                    <input type="text" id="edit-type" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none">
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Tarifs (€)</label>
                                    <input type="number" step="0.01" id="edit-tarifs" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500">Date de naissance *</label>
                                    <input type="date" id="edit-date" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-500">Validation Dossier</label>
                                    <select id="edit-valide" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none">
                                        <option value="0">En attente</option>
                                        <option value="1">Validé</option>
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

                <div id="delete-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-lg text-center border border-red-500 shadow-xl">
                        <div class="text-red-500 text-6xl mb-4 font-bold">!</div>
                        <h3 class="text-2xl font-semibold mb-2">Supprimer le prestataire ?</h3>
                        <p class="text-gray-400 mb-8 font-light">Cette action est irréversible.</p>
                        <input type="hidden" id="delete-id">
                        <div class="flex justify-center gap-6 border-t border-gray-100 pt-4">
                            <button type="button" onclick="toggleModal('delete-modal')" class="text-gray-400">Annuler</button>
                            <button type="button" id="confirm-delete" class="bg-red-500 text-white px-8 py-2 rounded-full font-semibold">Oui, supprimer</button>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        const API_BASE = "http://localhost:8082/prestataires";
        const messageBox = document.getElementById('api-message');

        let allPrestataires = [];
        let currentPrestataireId = null;

        function showAlert(msg, isSuccess) {
            messageBox.textContent = msg;
            messageBox.className = `max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold ${isSuccess ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'}`;
            messageBox.classList.remove('hidden');
            setTimeout(() => messageBox.classList.add('hidden'), 3500);
        }

        async function fetchPrestataires() {
            try {
                const response = await fetch(`${API_BASE}/read`);
                allPrestataires = await response.json();

                const tbody = document.getElementById('prestataire-table-body');
                tbody.innerHTML = '';

                if (!allPrestataires || allPrestataires.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-gray-400">Aucun prestataire en base.</td></tr>';
                    return;
                }

                allPrestataires.forEach(p => {
                    let validText = p.est_valide === 1 ? '<span class="text-green-600 font-bold">Validé</span>' : '<span class="text-yellow-600 font-bold">En attente</span>';

                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                            <td class="p-4 text-gray-400">#${p.id}</td>
                            <td class="p-4">
                                <span class="font-bold uppercase">${p.nom}</span> ${p.prenom}<br>
                                <span class="text-sm text-gray-500">${p.email}</span>
                            </td>
                            <td class="p-4">
                                ${p.num_telephone || '-'}
                            </td>
                            <td class="p-4">
                                <span class="font-semibold text-[#1C5B8F]">${p.type_prestation || '-'}</span><br>
                                <span class="text-sm text-gray-500">Tarifs: ${p.tarifs || 0} €</span>
                            </td>
                            <td class="p-4">
                                ${validText}
                            </td>
                            <td class="p-4 text-center">
                                <button onclick="openVoirPlusModal(${p.id})" class="bg-[#1C5B8F] text-white px-4 py-2 rounded-full font-semibold text-sm hover:bg-blue-800 transition">
                                    Voir détails
                                </button>
                            </td>
                        </tr>
                    `;
                });
            } catch (err) {
                showAlert("Erreur lors de la récupération des prestataires.", false);
            }
        }

        async function openVoirPlusModal(id) {
            const p = allPrestataires.find(item => item.id === id);
            if (!p) return;

            currentPrestataireId = id;

            document.getElementById('vp-nom').textContent = p.nom;
            document.getElementById('vp-prenom').textContent = p.prenom;
            document.getElementById('vp-email').textContent = p.email;
            document.getElementById('vp-tel').textContent = p.num_telephone || '-';
            document.getElementById('vp-date').textContent = p.date_naissance || '-';

            document.getElementById('vp-siret').textContent = p.siret || '-';
            document.getElementById('vp-type').textContent = p.type_prestation || '-';
            document.getElementById('vp-tarifs').textContent = p.tarifs || '0';

            document.getElementById('vp-validation').innerHTML = p.est_valide === 1 ?
                '<span class="text-green-600 font-bold">Validé</span>' :
                '<span class="text-yellow-600 font-bold">En attente</span>';

            const docList = document.getElementById('vp-documents-list');
            docList.innerHTML = '<i>Chargement des documents...</i>';

            toggleModal('voir-plus-modal');

            try {
                const docRes = await fetch(`${API_BASE}/documents/${id}`);
                if (!docRes.ok) throw new Error();

                const documents = await docRes.json();

                if (!documents || documents.length === 0) {
                    docList.innerHTML = '<i>Aucun document lié pour le moment.</i>';
                } else {
                    docList.innerHTML = '<div class="flex gap-4 flex-wrap">';
                    documents.forEach(doc => {
                        docList.innerHTML += `
                            <a href="${doc.lien}" target="_blank" class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg shadow-sm border text-[#1C5B8F] font-semibold hover:bg-blue-50 transition">
                                Document : ${doc.type}
                            </a>
                        `;
                    });
                    docList.innerHTML += '</div>';
                }
            } catch (e) {
                docList.innerHTML = '<i class="text-red-500 font-bold">Impossible de charger les documents.</i>';
            }
        }

        function triggerEdit() {
            toggleModal('voir-plus-modal');
            const p = allPrestataires.find(item => item.id === currentPrestataireId);

            document.getElementById('edit-id').value = p.id;
            document.getElementById('edit-nom').value = p.nom;
            document.getElementById('edit-prenom').value = p.prenom;
            document.getElementById('edit-email').value = p.email;
            document.getElementById('edit-tel').value = p.num_telephone;
            document.getElementById('edit-date').value = p.date_naissance;
            document.getElementById('edit-siret').value = p.siret;
            document.getElementById('edit-type').value = p.type_prestation;
            document.getElementById('edit-tarifs').value = p.tarifs;
            document.getElementById('edit-valide').value = p.est_valide;

            toggleModal('edit-modal');
        }

        function triggerDelete() {
            toggleModal('voir-plus-modal');
            document.getElementById('delete-id').value = currentPrestataireId;
            toggleModal('delete-modal');
        }

        document.getElementById('add-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                nom: document.getElementById('add-nom').value,
                prenom: document.getElementById('add-prenom').value,
                email: document.getElementById('add-email').value,
                num_telephone: document.getElementById('add-tel').value,
                date_naissance: document.getElementById('add-date').value,
                siret: document.getElementById('add-siret').value,
                type_prestation: document.getElementById('add-type').value,
                tarifs: parseFloat(document.getElementById('add-tarifs').value) || 0,
                est_valide: parseInt(document.getElementById('add-valide').value) || 0
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
                    showAlert("Prestataire ajouté !", true);
                    fetchPrestataires();
                } else {
                    showAlert("Erreur lors de l'insertion", false);
                }
            } catch (err) {
                showAlert("Erreur réseau", false);
            }
        });

        document.getElementById('edit-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('edit-id').value;
            const data = {
                nom: document.getElementById('edit-nom').value,
                prenom: document.getElementById('edit-prenom').value,
                email: document.getElementById('edit-email').value,
                num_telephone: document.getElementById('edit-tel').value,
                date_naissance: document.getElementById('edit-date').value,
                siret: document.getElementById('edit-siret').value,
                type_prestation: document.getElementById('edit-type').value,
                tarifs: parseFloat(document.getElementById('edit-tarifs').value) || 0,
                est_valide: parseInt(document.getElementById('edit-valide').value) || 0
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
                    fetchPrestataires();
                } else {
                    showAlert("Erreur lors de la mise à jour", false);
                }
            } catch (err) {
                showAlert("Erreur réseau", false);
            }
        });

        document.getElementById('confirm-delete').addEventListener('click', async () => {
            const id = document.getElementById('delete-id').value;
            try {
                const res = await fetch(`${API_BASE}/delete/${id}`, {
                    method: 'DELETE'
                });
                if (res.ok) {
                    toggleModal('delete-modal');
                    showAlert("Prestataire supprimé", true);
                    fetchPrestataires();
                } else {
                    showAlert("Erreur lors de la suppression", false);
                }
            } catch (err) {
                showAlert("Erreur réseau", false);
            }
        });

        window.onload = fetchPrestataires;
    </script>
</body>

</html>