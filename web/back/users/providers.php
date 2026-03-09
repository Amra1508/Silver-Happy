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

        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
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
                    <h1 class="text-3xl font-semibold text-[#1C5B8F]">Gestion des Prestataires</h1>
                    <div class="flex items-center gap-4">

                        <select id="status-filter" onchange="loadProviders(1)" class="bg-[#F5F5F5]/40 shadow-[#1C5B8F] text-[#1C5B8F] py-2 px-4 rounded-full font-semibold hover:bg-[#D9D9D9]/40 focus:outline-none shadow-sm cursor-pointer">
                            <option value="tous">Tous les statuts</option>
                            <option value="en attente">En attente</option>
                            <option value="validé">Validé</option>
                            <option value="refusé">Refusé</option>
                        </select>

                        <button onclick="startVerification()" class="edit-button" type="button">
                            Vérifier les prestataires
                        </button>
                        <button onclick="toggleModal('add-modal')" class="add-button" type="button">
                            + Ajouter un Prestataire
                        </button>
                    </div>
                </div>

                <div id="api-message" class="hidden max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold"></div>

                <div class="table-container">
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
                        <tbody id="prestataire-table-body" class="divide-y divide-[#D9D9D9]/40">
                        </tbody>
                    </table>
                </div>

                <div id="voir-plus-modal" class="hidden modal">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-2xl shadow-xl shadow-[#1C5B8F]/20 overflow-y-auto">

                        <div class="flex justify-between items-center pb-6">
                            <h3 class="text-2xl font-semibold text-[#1C5B8F]">Détails du Prestataire</h3>
                            <div class="flex items-center gap-4">
                                <span id="validation-counter" class="hidden bg-[#D9D9D9]/40 text-[#1C5B8F] px-4 py-1 rounded-full font-bold text-sm"></span>
                                <button onclick="toggleModal('voir-plus-modal')" class="text-[#1C5B8F] hover:text-gray-700 text-2xl font-bold">&times;</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-8 text-sm mb-6">
                            <div class="space-y-2">
                                <h4 class="font-bold text-[#1C5B8F] text-base mb-2 border-b pb-1">Informations</h4>
                                <p><span class="text-gray-500">Nom :</span> <strong id="vp-nom" class="uppercase"></strong> <strong id="vp-prenom"></strong></p>
                                <p><span class="text-gray-500">Email :</span> <strong id="vp-email"></strong></p>
                                <p><span class="text-gray-500">Téléphone :</span> <strong id="vp-tel"></strong></p>
                                <p><span class="text-gray-500">Naissance :</span> <strong id="vp-date"></strong></p>
                                <p><span class="text-gray-500">Inscrit le :</span> <strong id="vp-date-creation"></strong></p>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-bold text-[#1C5B8F] text-base mb-2 border-b pb-1">Activité</h4>
                                <p><span class="text-gray-500">SIRET :</span> <strong id="vp-siret"></strong></p>
                                <p><span class="text-gray-500">Prestation :</span> <strong id="vp-type"></strong></p>
                                <p><span class="text-gray-500">Tarifs :</span> <strong id="vp-tarifs"></strong> €</p>
                                <p><span class="text-gray-500">Statut actuel :</span> <span id="vp-validation"></span></p>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-bold text-[#1C5B8F] text-base mb-2 border-b pb-1">Documents du prestataire</h4>

                            <div id="vp-documents-list" class="text-sm text-gray-500 mb-8">
                            </div>

                            <div>
                                <h4 class="font-bold text-[#1C5B8F] text-base mb-2 border-b pb-1">Ajouter un nouveau document</h4>
                                <form id="upload-form" class="space-y-3">
                                    <input type="hidden" id="upload-provider-id">
                                    <div>
                                        <input type="text" id="upload-type" placeholder="Type de document (ex: Kbis, RIB, Identité...)" class="add-input text-sm placeholder:text-gray-500" required>
                                    </div>
                                    <div>
                                        <input type="file" id="upload-file" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#D9D9D9]/40 file:text-[#1C5B8F] hover:file:bg-[#D9D9D9]/80" required>
                                    </div>
                                    <button type="submit" class="w-full text-sm add-button">Envoyer le fichier</button>
                                </form>
                            </div>
                        </div>

                        <div id="standard-actions" class="flex justify-between items-center pt-4">
                            <button type="button" onclick="prepareDelete()" class="delete-button">Supprimer ce compte</button>
                            <div class="flex gap-4">
                                <button type="button" onclick="toggleModal('voir-plus-modal')" class="text-gray-400">Fermer</button>
                                <button type="button" onclick="prepareEdit()" class="edit-button">Modifier</button>
                            </div>
                        </div>

                        <div id="validation-actions" class="hidden flex-col gap-4 pt-4 border-t border-gray-100">
                            <input type="text" id="motif-refus" placeholder="Motif du refus (ex: Casier judiciaire invalide)..." class="text-sm placeholder:text-gray-500 add-input">

                            <div class="flex justify-center gap-4">
                                <button type="button" onclick="rejectVerification()" class="bg-[#FF0000]/10 text-[#FF0000] px-6 py-2 rounded-xl font-semibold hover:bg-[#FF0000]/20">Refuser</button>
                                <button type="button" onclick="skipVerification()" class="bg-[#E1AB2B]/10 text-[#E1AB2B] px-6 py-2 rounded-xl font-semibold hover:bg-[#E1AB2B]/20">Garder en attente</button>
                                <button type="button" onclick="approveVerification()" class="bg-[#26D443]/10 text-[#26D443] px-6 py-2 rounded-xl font-semibold hover:bg-[#26D443]/20">Valider</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="add-modal" class="hidden modal">
                    <div class="add-modal">
                        <h3 class="text-2xl font-semibold text-[#1C5B8F] mb-6">Ajouter un Prestataire</h3>
                        <form id="add-form" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="text-sm text-gray-500">Nom *</label><input type="text" id="add-nom" class="add-input" required></div>
                                <div><label class="text-sm text-gray-500">Prénom *</label><input type="text" id="add-prenom" class="add-input" required></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="text-sm text-gray-500">Email *</label><input type="email" id="add-email" class="add-input" required></div>
                                <div><label class="text-sm text-gray-500">Téléphone</label><input type="text" id="add-tel" class="add-input"></div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div><label class="text-sm text-gray-500">N° SIRET</label><input type="text" id="add-siret" class="add-input"></div>
                                <div><label class="text-sm text-gray-500">Type de prestation</label><input type="text" id="add-type" class="add-input"></div>
                                <div><label class="text-sm text-gray-500">Tarifs (€)</label><input type="number" min="1" step="0.01" id="add-tarifs" class="add-input"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="text-sm text-gray-500">Date de naissance *</label><input type="date" id="add-date" class="add-input" required></div>
                                <div>
                                    <label class="text-sm text-gray-500">Statut initial</label>
                                    <select id="add-status" class="add-input">
                                        <option value="en attente">En attente</option>
                                        <option value="validé">Validé</option>
                                        <option value="refusé">Refusé</option>
                                    </select>
                                </div>
                            </div>
                            <div class="border-t border-gray-100 pt-4 mt-4">
                                <h4 class="font-bold text-[#1C5B8F] text-base mb-2">Documents (Facultatif)</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <input type="text" id="add-doc-type" placeholder="Type (ex: Kbis)" class="w-full rounded-xl p-2 border border-[#1C5B8F] focus:outline-none focus:border-none focus:outline-1 focus:-outline-offset-1 focus:outline-[#E1AB2B]/60 text-sm">
                                    </div>
                                    <div>
                                        <input type="file" id="add-doc-file" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#D9D9D9]/40 file:text-[#1C5B8F] hover:file:bg-[#D9D9D9]/80">
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end gap-4 mt-8 pt-4 border-t border-gray-100">
                                <button type="button" onclick="toggleModal('add-modal')" class="text-gray-400">Annuler</button>
                                <button type="submit" class="add-button">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="edit-modal" class="hidden modal">
                    <div class="edit-modal">
                        <h3 class="text-2xl font-semibold text-[#E1AB2B] mb-6">Modifier le Prestataire</h3>
                        <form id="edit-form" class="space-y-4">
                            <input type="hidden" id="edit-id">

                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="text-sm text-gray-500">Nom *</label><input type="text" id="edit-nom" class="edit-input" required></div>
                                <div><label class="text-sm text-gray-500">Prénom *</label><input type="text" id="edit-prenom" class="edit-input" required></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="text-sm text-gray-500">Email *</label><input type="email" id="edit-email" class="edit-input" required></div>
                                <div><label class="text-sm text-gray-500">Téléphone</label><input type="text" id="edit-tel" class="edit-input"></div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div><label class="text-sm text-gray-500">N° SIRET</label><input type="text" id="edit-siret" class="edit-input"></div>
                                <div><label class="text-sm text-gray-500">Type de prestation</label><input type="text" id="edit-type" class="edit-input"></div>
                                <div><label class="text-sm text-gray-500">Tarifs (€)</label><input type="number" min="1" step="0.01" id="edit-tarifs" class="edit-input"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="text-sm text-gray-500">Date de naissance *</label><input type="date" id="edit-date" class="edit-input" required></div>
                                <div>
                                    <label class="text-sm text-gray-500">Statut</label>
                                    <select id="edit-status" class="edit-input">
                                    </select>
                                </div>
                            </div>
                            <div class="pt-2">
                                <label class="text-sm text-gray-500">Motif si refusé</label>
                                <input type="text" id="edit-motif" class="edit-input">
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
                        <div class="text-[#FF0000] text-6xl mb-4 font-bold">!</div>
                        <h3 class="text-2xl font-semibold mb-2">Supprimer le prestataire ?</h3>
                        <p class="text-gray-400 mb-8 font-light">Cette action est irréversible.</p>
                        <input type="hidden" id="delete-id">
                        <div class="flex justify-center gap-6">
                            <button type="button" onclick="toggleModal('delete-modal')" class="text-gray-400">Annuler</button>
                            <button type="button" id="confirm-delete" class="delete-button">Oui, supprimer</button>
                        </div>
                    </div>
                </div>

                <div id="delete-doc-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-[60]">
                    <div class="delete-modal">
                        <div class="text-[#FF0000] text-6xl mb-4 font-bold">!</div>
                        <h3 class="text-2xl font-semibold mb-2">Supprimer ce document ?</h3>
                        <p class="text-gray-400 mb-8 font-light">Le fichier sera définitivement effacé.</p>
                        <div class="flex justify-center gap-4">
                            <button type="button" onclick="toggleModal('delete-doc-modal')" class="text-gray-500">Annuler</button>
                            <button type="button" id="confirm-delete-doc" class="delete-button">Oui, supprimer</button>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        const API_BASE = "http://localhost:8082/prestataires";
        const messageBox = document.getElementById('api-message');

        let allProviders = [];
        let pendingProviders = [];
        let selectedProviderId = null;
        let currentVerificationIndex = 0;

        let currentPage = 1;
        const limit = 10;

        function showAlert(message, isSuccess) {
            messageBox.textContent = message;
            if (isSuccess) {
                messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold bg-green-100 border-green-400 text-green-700";
            } else {
                messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold bg-red-100 border-red-400 text-red-700";
            }
            messageBox.classList.remove('hidden');
            setTimeout(() => messageBox.classList.add('hidden'), 3500);
        }

        function getProviderById(searchId) {
            for (let i = 0; i < allProviders.length; i++) {
                if (allProviders[i].id === searchId) {
                    return allProviders[i];
                }
            }
            return null;
        }

        async function loadProviders(page = 1) {
            currentPage = page;

            const statusFilterElement = document.getElementById('status-filter');
            const statusFilter = statusFilterElement ? statusFilterElement.value : 'tous';

            const response = await fetch(`${API_BASE}/read?page=${currentPage}&limit=${limit}&status=${statusFilter}`);

            if (response.ok) {
                const result = await response.json();
                allProviders = result.data || [];

                const tableBody = document.getElementById('prestataire-table-body');
                tableBody.innerHTML = "";

                if (allProviders.length === 0) {
                    tableBody.innerHTML = "<tr><td colspan='6' class='p-8 text-center text-gray-400'>Aucun prestataire en base.</td></tr>";
                    renderPagination(0, 0);
                    return;
                }

                for (let i = 0; i < allProviders.length; i++) {
                    let provider = allProviders[i];

                    let badge = "";
                    if (provider.status === "validé") {
                        badge = "<span class='bg-[#26D443]/10 text-[#26D443] py-2 px-6 rounded-md font-semibold text-sm'>Validé</span>";
                    } else if (provider.status === "refusé") {
                        badge = "<span class='bg-[#FF0000]/10 text-[#FF0000] py-2 px-6 rounded-md font-semibold text-sm'>Refusé</span>";
                    } else {
                        badge = "<span class='bg-[#E1AB2B]/10 text-[#E1AB2B] py-2 px-6 rounded-md font-semibold text-sm'>En attente</span>";
                    }

                    let phone = provider.num_telephone ? provider.num_telephone : "-";
                    let price = provider.tarifs ? provider.tarifs : 0;

                    let detailsButton = "<button onclick='openDetailsModal(" + provider.id + ")' class='add-button text-sm'>Voir détails</button>";

                    tableBody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                            <td class="p-4 text-gray-400">#${provider.id}</td>
                            <td class="p-4">
                                <span class="font-bold uppercase">${provider.nom}</span> ${provider.prenom}<br>
                                <span class="text-sm text-gray-500">${provider.email}</span>
                            </td>
                            <td class="p-4">${phone}</td>
                            <td class="p-4">
                                <span class="font-semibold">${provider.type_prestation}</span><br>
                                <span class="text-sm text-gray-500">Tarifs: ${price} €</span>
                            </td>
                            <td class="p-4">${badge}</td>
                            <td class="p-4 text-center">${detailsButton}</td>
                        </tr>
                    `;
                }

                renderPagination(result.totalPages, result.total);

            } else {
                showAlert("Erreur pour lire la base de données.", false);
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
                    <span class="text-gray-500 font-semibold">Total : ${totalItems} prestataires</span>
                    <div class="flex gap-2">
                        <button ${currentPage === 1 ? 'disabled' : ''} onclick="loadProviders(${currentPage - 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Précédent</button>
            `;

            for (let i = 1; i <= totalPages; i++) {
                const activeClass = i === currentPage ? 'bg-[#1C5B8F] text-white' : 'text-[#1C5B8F] hover:bg-blue-50';
                html += `<button onclick="loadProviders(${i})" class="px-3 py-1 border border-[#1C5B8F] rounded transition ${activeClass}">${i}</button>`;
            }

            html += `
                        <button ${currentPage === totalPages ? 'disabled' : ''} onclick="loadProviders(${currentPage + 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Suivant</button>
                    </div>
                </div>
            `;
            paginationContainer.innerHTML = html;
        }

        async function setupDetailsModal(id) {
            let provider = getProviderById(id);
            if (!provider) return;
            selectedProviderId = id;

            document.getElementById('upload-provider-id').value = id;
            document.getElementById('vp-nom').textContent = provider.nom;
            document.getElementById('vp-prenom').textContent = provider.prenom;
            document.getElementById('vp-email').textContent = provider.email;
            document.getElementById('vp-tel').textContent = provider.num_telephone ? provider.num_telephone : "-";
            document.getElementById('vp-date').textContent = provider.date_naissance ? provider.date_naissance.substring(0, 10) : "-";
            document.getElementById('vp-date-creation').textContent = provider.date_creation ? provider.date_creation : "-";
            document.getElementById('vp-siret').textContent = provider.siret;
            document.getElementById('vp-type').textContent = provider.type_prestation;
            document.getElementById('vp-tarifs').textContent = provider.tarifs;

            if (provider.status === 'validé') {
                document.getElementById('vp-validation').innerHTML = '<span class="font-bold">Validé</span>';
            } else if (provider.status === 'refusé') {
                document.getElementById('vp-validation').innerHTML = '<span class="font-bold">Refusé (' + provider.motif_refus + ')</span>';
            } else {
                document.getElementById('vp-validation').innerHTML = '<span class="font-bold">En attente</span>';
            }

            const documentsArea = document.getElementById('vp-documents-list');
            documentsArea.innerHTML = "<i>Chargement des documents...</i>";

            const responseDocs = await fetch(API_BASE + "/documents/" + id);

            if (responseDocs.ok) {
                const documentList = await responseDocs.json();

                if (documentList.length === 0) {
                    documentsArea.innerHTML = "<i>Aucun document lié pour le moment.</i>";
                } else {
                    documentsArea.innerHTML = "<div class='flex flex-col gap-2'>";
                    for (let i = 0; i < documentList.length; i++) {
                        let documentItem = documentList[i];
                        documentsArea.innerHTML += `
                            <div class="flex items-center justify-between bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
                                <a href="http://localhost:8082/${documentItem.lien}" target="_blank" class="text-[#1C5B8F] font-semibold text-sm hover:underline truncate">
                                    📄 ${documentItem.type}
                                </a>
                                <button type="button" onclick="prepareDeleteDoc(${documentItem.id_document})" class="text-red-400 font-bold hover:text-red-600 px-2 text-xl transition">
                                    &times;
                                </button>
                            </div>
                        `;
                    }
                    documentsArea.innerHTML += "</div>";
                }
            } else {
                documentsArea.innerHTML = "<i class='text-[#FF0000] font-bold'>Erreur pendant le chargement des documents.</i>";
            }
        }

        async function openDetailsModal(id) {
            document.getElementById('validation-actions').classList.add('hidden');
            document.getElementById('validation-actions').classList.remove('flex');
            document.getElementById('validation-counter').classList.add('hidden');

            document.getElementById('standard-actions').classList.remove('hidden');

            await setupDetailsModal(id);
            toggleModal('voir-plus-modal');
        }

        function startVerification() {
            pendingProviders = [];
            for (let i = 0; i < allProviders.length; i++) {
                if (allProviders[i].status === "en attente") {
                    pendingProviders.push(allProviders[i]);
                }
            }

            if (pendingProviders.length === 0) {
                showAlert("Génial ! Il n'y a aucun dossier en attente à vérifier sur cette page.", true);
                return;
            }

            currentVerificationIndex = 0;
            document.getElementById('motif-refus').value = "";

            document.getElementById('standard-actions').classList.add('hidden');

            document.getElementById('validation-actions').classList.remove('hidden');
            document.getElementById('validation-actions').classList.add('flex');
            document.getElementById('validation-counter').classList.remove('hidden');

            showCurrentVerificationProfile();
            toggleModal('voir-plus-modal');
        }

        async function showCurrentVerificationProfile() {
            let currentProvider = pendingProviders[currentVerificationIndex];
            let fileNumber = currentVerificationIndex + 1;
            let totalFiles = pendingProviders.length;
            document.getElementById('validation-counter').textContent = "Dossier " + fileNumber + " sur " + totalFiles;

            document.getElementById('motif-refus').value = "";
            await setupDetailsModal(currentProvider.id);
        }

        function skipVerification() {
            currentVerificationIndex++;

            if (currentVerificationIndex >= pendingProviders.length) {
                toggleModal('voir-plus-modal');
                showAlert("Vous avez vu tous les dossiers en attente de cette page.", true);
                loadProviders(currentPage);
            } else {
                showCurrentVerificationProfile();
            }
        }

        async function saveVerificationStatus(newStatus) {
            let currentProvider = pendingProviders[currentVerificationIndex];

            let currentMotif = "";
            if (newStatus === "refusé") {
                currentMotif = document.getElementById('motif-refus').value;
            }

            let payload = {
                nom: currentProvider.nom,
                prenom: currentProvider.prenom,
                email: currentProvider.email,
                num_telephone: currentProvider.num_telephone,
                date_naissance: currentProvider.date_naissance,
                siret: currentProvider.siret,
                type_prestation: currentProvider.type_prestation,
                tarifs: currentProvider.tarifs,
                status: newStatus,
                motif_refus: currentMotif
            };

            const response = await fetch(API_BASE + "/update/" + currentProvider.id, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(payload)
            });

            if (response.ok) {
                skipVerification();
            } else {
                showAlert("Erreur pour enregistrer ce dossier.", false);
            }
        }

        function approveVerification() {
            saveVerificationStatus("validé");
        }

        function rejectVerification() {
            saveVerificationStatus("refusé");
        }

        function prepareEdit() {
            toggleModal('voir-plus-modal');

            let provider = getProviderById(selectedProviderId);

            document.getElementById('edit-id').value = provider.id;
            document.getElementById('edit-nom').value = provider.nom;
            document.getElementById('edit-prenom').value = provider.prenom;
            document.getElementById('edit-email').value = provider.email;
            document.getElementById('edit-tel').value = provider.num_telephone;
            document.getElementById('edit-siret').value = provider.siret;
            document.getElementById('edit-type').value = provider.type_prestation;
            document.getElementById('edit-tarifs').value = provider.tarifs;
            document.getElementById('edit-motif').value = provider.motif_refus;

            if (provider.date_naissance) {
                document.getElementById('edit-date').value = provider.date_naissance.substring(0, 10);
            } else {
                document.getElementById('edit-date').value = "";
            }

            const statusMenu = document.getElementById('edit-status');
            if (provider.status === "refusé") {
                statusMenu.innerHTML = `
                    <option value="refusé">Refusé</option>
                    <option value="en attente">Remettre en attente</option>
                `;
            } else {
                statusMenu.innerHTML = `
                    <option value="en attente">En attente</option>
                    <option value="validé">Validé</option>
                    <option value="refusé">Refusé</option>
                `;
            }

            statusMenu.value = provider.status;
            toggleModal('edit-modal');
        }

        function prepareDelete() {
            toggleModal('voir-plus-modal');
            document.getElementById('delete-id').value = selectedProviderId;
            toggleModal('delete-modal');
        }

        document.getElementById('add-form').addEventListener('submit', async function(event) {
            event.preventDefault();

            let payload = {
                nom: document.getElementById('add-nom').value,
                prenom: document.getElementById('add-prenom').value,
                email: document.getElementById('add-email').value,
                num_telephone: document.getElementById('add-tel').value,
                date_naissance: document.getElementById('add-date').value,
                siret: document.getElementById('add-siret').value,
                type_prestation: document.getElementById('add-type').value,
                tarifs: parseFloat(document.getElementById('add-tarifs').value) || 0,
                status: document.getElementById('add-status').value,
                motif_refus: ""
            };

            const response = await fetch(API_BASE + "/create", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(payload)
            });

            if (response.ok) {
                const nouveauPrestataire = await response.json();

                let fileInput = document.getElementById('add-doc-file');
                if (fileInput.files.length > 0 && nouveauPrestataire.id) {
                    let docType = document.getElementById('add-doc-type').value || "Document";
                    let formDataPayload = new FormData();
                    formDataPayload.append("type_document", docType);
                    formDataPayload.append("fichier_document", fileInput.files[0]);

                    await fetch(API_BASE + "/upload/" + nouveauPrestataire.id, {
                        method: "POST",
                        body: formDataPayload
                    });
                }

                toggleModal('add-modal');
                document.getElementById('add-form').reset();
                showAlert("Prestataire ajouté avec succès !", true);
                loadProviders(1);
            } else {
                showAlert("Erreur pour ajouter ce prestataire.", false);
            }
        });

        document.getElementById('edit-form').addEventListener('submit', async function(event) {
            event.preventDefault();

            let id = document.getElementById('edit-id').value;

            let payload = {
                nom: document.getElementById('edit-nom').value,
                prenom: document.getElementById('edit-prenom').value,
                email: document.getElementById('edit-email').value,
                num_telephone: document.getElementById('edit-tel').value,
                date_naissance: document.getElementById('edit-date').value,
                siret: document.getElementById('edit-siret').value,
                type_prestation: document.getElementById('edit-type').value,
                tarifs: parseFloat(document.getElementById('edit-tarifs').value) || 0,
                status: document.getElementById('edit-status').value,
                motif_refus: document.getElementById('edit-motif').value
            };

            const response = await fetch(API_BASE + "/update/" + id, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(payload)
            });

            if (response.ok) {
                toggleModal('edit-modal');
                showAlert("Les modifications sont enregistrées.", true);
                loadProviders(currentPage);
            } else {
                showAlert("Impossible de modifier.", false);
            }
        });

        document.getElementById('confirm-delete').addEventListener('click', async function() {
            let id = document.getElementById('delete-id').value;

            const response = await fetch(API_BASE + "/delete/" + id, {
                method: "DELETE"
            });

            if (response.ok) {
                toggleModal('delete-modal');
                showAlert("Le prestataire a été définitivement supprimé.", true);
                loadProviders(currentPage);
            } else {
                showAlert("Impossible de supprimer.", false);
            }
        });

        document.getElementById('upload-form').addEventListener('submit', async function(event) {
            event.preventDefault();

            let providerId = document.getElementById('upload-provider-id').value;
            let documentType = document.getElementById('upload-type').value;
            let fileInput = document.getElementById('upload-file');

            if (fileInput.files.length === 0) return;

            let formDataPayload = new FormData();
            formDataPayload.append("type_document", documentType);
            formDataPayload.append("fichier_document", fileInput.files[0]);

            const response = await fetch(API_BASE + "/upload/" + providerId, {
                method: "POST",
                body: formDataPayload
            });

            if (response.ok) {
                document.getElementById('upload-form').reset();
                showAlert("Le document a été sauvegardé avec succès !", true);
                setupDetailsModal(providerId);
            } else {
                showAlert("Erreur lors de l'envoi du fichier.", false);
            }
        });

        let selectedDocId = null;

        function prepareDeleteDoc(docId) {
            selectedDocId = docId;
            toggleModal('delete-doc-modal');
        }

        document.getElementById('confirm-delete-doc').addEventListener('click', async function() {
            const response = await fetch(API_BASE + "/document/delete/" + selectedDocId, {
                method: "DELETE"
            });

            if (response.ok) {
                toggleModal('delete-doc-modal');
                showAlert("Le document a été supprimé.", true);
                setupDetailsModal(selectedProviderId);
            } else {
                showAlert("Erreur lors de la suppression.", false);
            }
        });

        window.onload = () => loadProviders(1);
    </script>

</body>

</html>