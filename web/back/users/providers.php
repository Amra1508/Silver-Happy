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
                    <div class="flex gap-4">
                        <button onclick="startVerification()" class="bg-[#E1AB2B] text-white py-2 px-6 rounded-full hover:bg-yellow-600 transition shadow-sm font-semibold" type="button">
                            Vérifier les prestataires
                        </button>
                        <button onclick="toggleModal('add-modal')" class="bg-[#1C5B8F] text-white py-2 px-6 rounded-full hover:bg-blue-800 transition shadow-sm font-semibold" type="button">
                            + Ajouter un Prestataire
                        </button>
                    </div>
                </div>

                <div id="api-message" class="hidden max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold"></div>

                <div class="border border-[#1C5B8F] rounded-[2.5rem] overflow-hidden bg-white shadow-sm">
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

                <div id="voir-plus-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50 p-4">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-3xl border border-[#1C5B8F] shadow-xl overflow-y-auto max-h-[90vh]">

                        <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-6">
                            <h3 class="text-2xl font-semibold text-[#1C5B8F]">Détails du Prestataire</h3>
                            <div class="flex items-center gap-4">
                                <span id="validation-counter" class="hidden bg-gray-100 text-gray-600 px-4 py-1 rounded-full font-bold text-sm"></span>
                                <button onclick="toggleModal('voir-plus-modal')" class="text-gray-400 hover:text-gray-700 text-2xl font-bold">&times;</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-8 text-sm mb-6">
                            <div class="space-y-2">
                                <h4 class="font-bold text-gray-700 text-base mb-2 border-b pb-1">Informations</h4>
                                <p><span class="text-gray-500">Nom :</span> <strong id="vp-nom" class="uppercase"></strong> <strong id="vp-prenom"></strong></p>
                                <p><span class="text-gray-500">Email :</span> <strong id="vp-email"></strong></p>
                                <p><span class="text-gray-500">Téléphone :</span> <strong id="vp-tel"></strong></p>
                                <p><span class="text-gray-500">Naissance :</span> <strong id="vp-date"></strong></p>
                                <p><span class="text-gray-500">Inscrit le :</span> <strong id="vp-date-creation" class="text-[#1C5B8F]"></strong></p>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-bold text-gray-700 text-base mb-2 border-b pb-1">Activité</h4>
                                <p><span class="text-gray-500">SIRET :</span> <strong id="vp-siret"></strong></p>
                                <p><span class="text-gray-500">Prestation :</span> <strong id="vp-type"></strong></p>
                                <p><span class="text-gray-500">Tarifs :</span> <strong id="vp-tarifs"></strong> €</p>
                                <p><span class="text-gray-500">Statut actuel :</span> <span id="vp-validation"></span></p>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-8">
                            <h4 class="font-bold text-gray-700 mb-2">Documents du prestataire</h4>
                            <div id="vp-documents-list" class="text-sm text-gray-500">
                                <i>Chargement des documents...</i>
                            </div>
                        </div>

                        <div id="standard-actions" class="flex justify-between items-center pt-4 border-t border-gray-100">
                            <button type="button" onclick="prepareDelete()" class="text-red-500 font-bold hover:underline">Supprimer ce compte</button>
                            <div class="flex gap-4">
                                <button type="button" onclick="toggleModal('voir-plus-modal')" class="text-gray-400">Fermer</button>
                                <button type="button" onclick="prepareEdit()" class="bg-[#E1AB2B] text-white px-6 py-2 rounded-full font-semibold">Modifier</button>
                            </div>
                        </div>

                        <div id="validation-actions" class="hidden flex-col gap-4 pt-4 border-t border-gray-100">
                            <input type="text" id="motif-refus" placeholder="Motif du refus (ex: Casier judiciaire invalide)..." class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:border-red-400 text-sm">

                            <div class="flex justify-center gap-4">
                                <button type="button" onclick="rejectVerification()" class="bg-red-50 text-red-600 border border-red-200 px-6 py-2 rounded-xl font-bold hover:bg-red-100 transition">Refuser</button>
                                <button type="button" onclick="skipVerification()" class="bg-gray-100 text-gray-600 px-6 py-2 rounded-xl font-bold hover:bg-gray-200 transition">Garder en attente</button>
                                <button type="button" onclick="approveVerification()" class="bg-green-500 text-white px-8 py-2 rounded-xl font-bold hover:bg-green-600 transition">Valider</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="add-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50 p-4">
                    <div class="bg-white p-10 rounded-[2.5rem] w-full max-w-2xl border border-[#1C5B8F] shadow-xl overflow-y-auto max-h-[90vh]">
                        <h3 class="text-2xl font-semibold text-[#1C5B8F] mb-6">Ajouter un Prestataire</h3>
                        <form id="add-form" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="text-sm text-gray-500">Nom *</label><input type="text" id="add-nom" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required></div>
                                <div><label class="text-sm text-gray-500">Prénom *</label><input type="text" id="add-prenom" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="text-sm text-gray-500">Email *</label><input type="email" id="add-email" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required></div>
                                <div><label class="text-sm text-gray-500">Téléphone</label><input type="text" id="add-tel" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none"></div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div><label class="text-sm text-gray-500">N° SIRET</label><input type="text" id="add-siret" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none"></div>
                                <div><label class="text-sm text-gray-500">Type de prestation</label><input type="text" id="add-type" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none"></div>
                                <div><label class="text-sm text-gray-500">Tarifs (€)</label><input type="number" min="1" step="0.01" id="add-tarifs" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="text-sm text-gray-500">Date de naissance *</label><input type="date" id="add-date" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none" required></div>
                                <div>
                                    <label class="text-sm text-gray-500">Statut initial</label>
                                    <select id="add-status" class="w-full mt-2 p-3 border border-[#1C5B8F] rounded-xl focus:outline-none">
                                        <option value="en attente">En attente</option>
                                        <option value="validé">Validé</option>
                                        <option value="refusé">Refusé</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex justify-end gap-4 mt-8 pt-4 border-t border-gray-100">
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
                                <div><label class="text-sm text-gray-500">Nom *</label><input type="text" id="edit-nom" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none" required></div>
                                <div><label class="text-sm text-gray-500">Prénom *</label><input type="text" id="edit-prenom" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none" required></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="text-sm text-gray-500">Email *</label><input type="email" id="edit-email" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none" required></div>
                                <div><label class="text-sm text-gray-500">Téléphone</label><input type="text" id="edit-tel" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none"></div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div><label class="text-sm text-gray-500">N° SIRET</label><input type="text" id="edit-siret" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none"></div>
                                <div><label class="text-sm text-gray-500">Type de prestation</label><input type="text" id="edit-type" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none"></div>
                                <div><label class="text-sm text-gray-500">Tarifs (€)</label><input type="number" min="1" step="0.01" id="edit-tarifs" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="text-sm text-gray-500">Date de naissance *</label><input type="date" id="edit-date" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none" required></div>
                                <div>
                                    <label class="text-sm text-gray-500">Statut</label>
                                    <select id="edit-status" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none">
                                    </select>
                                </div>
                            </div>
                            <div class="pt-2">
                                <label class="text-sm text-gray-500">Motif si refusé</label>
                                <input type="text" id="edit-motif" class="w-full mt-2 p-3 border border-[#E1AB2B] rounded-xl focus:outline-none">
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

        let allProviders = [];
        let pendingProviders = [];
        let selectedProviderId = null;
        let currentVerificationIndex = 0;

        function showAlert(message, isSuccess) {
            messageBox.textContent = message;

            if (isSuccess) {
                messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold bg-green-100 border-green-400 text-green-700";
            } else {
                messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold bg-red-100 border-red-400 text-red-700";
            }

            messageBox.classList.remove('hidden');

            setTimeout(function() {
                messageBox.classList.add('hidden');
            }, 3500);
        }

        function getProviderById(searchId) {
            for (let i = 0; i < allProviders.length; i++) {
                if (allProviders[i].id === searchId) {
                    return allProviders[i];
                }
            }
            return null;
        }

        // ====================================================
        // 1. AFFICHER LE GRAND TABLEAU
        // ====================================================
        async function loadProviders() {
            const response = await fetch(API_BASE + "/read");

            if (response.ok) {
                allProviders = await response.json();

                if (allProviders === null) {
                    allProviders = [];
                }

                const tableBody = document.getElementById('prestataire-table-body');
                tableBody.innerHTML = "";

                if (allProviders.length === 0) {
                    tableBody.innerHTML = "<tr><td colspan='6' class='p-8 text-center text-gray-400'>Aucun prestataire en base.</td></tr>";
                    return;
                }

                for (let i = 0; i < allProviders.length; i++) {
                    let provider = allProviders[i];

                    let badge = "";
                    if (provider.status === "validé") {
                        badge = "<span class='text-green-700 font-bold bg-green-100 px-3 py-1 rounded-full text-xs border border-green-200'>Validé</span>";
                    } else if (provider.status === "refusé") {
                        badge = "<span class='text-red-700 font-bold bg-red-100 px-3 py-1 rounded-full text-xs border border-red-200'>Refusé</span>";
                    } else {
                        badge = "<span class='text-yellow-700 font-bold bg-yellow-100 px-3 py-1 rounded-full text-xs border border-yellow-200'>En attente</span>";
                    }

                    let phone = "-";
                    if (provider.num_telephone) {
                        phone = provider.num_telephone;
                    }

                    let price = 0;
                    if (provider.tarifs) {
                        price = provider.tarifs;
                    }

                    let detailsButton = "<button onclick='openDetailsModal(" + provider.id + ")' class='bg-gray-100 hover:bg-gray-200 text-[#1C5B8F] px-4 py-2 rounded-full transition font-semibold text-sm'>Voir détails</button>";

                    let htmlRow = `
                        <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                            <td class="p-4 text-gray-400">#${provider.id}</td>
                            <td class="p-4">
                                <span class="font-bold uppercase">${provider.nom}</span> ${provider.prenom}<br>
                                <span class="text-sm text-gray-500">${provider.email}</span>
                            </td>
                            <td class="p-4">${phone}</td>
                            <td class="p-4">
                                <span class="font-semibold text-[#1C5B8F]">${provider.type_prestation}</span><br>
                                <span class="text-sm text-gray-500">Tarifs: ${price} €</span>
                            </td>
                            <td class="p-4">${badge}</td>
                            <td class="p-4 text-center">${detailsButton}</td>
                        </tr>
                    `;

                    tableBody.innerHTML += htmlRow;
                }
            } else {
                showAlert("Erreur pour lire la base de données.", false);
            }
        }

        // ====================================================
        // 2. AFFICHER LA FENÊTRE DES DÉTAILS
        // ====================================================
        async function setupDetailsModal(id) {
            let provider = getProviderById(id);
            selectedProviderId = id;

            document.getElementById('vp-nom').textContent = provider.nom;
            document.getElementById('vp-prenom').textContent = provider.prenom;
            document.getElementById('vp-email').textContent = provider.email;

            if (provider.num_telephone) {
                document.getElementById('vp-tel').textContent = provider.num_telephone;
            } else {
                document.getElementById('vp-tel').textContent = "-";
            }

            if (provider.date_naissance) {
                document.getElementById('vp-date').textContent = provider.date_naissance.substring(0, 10);
            } else {
                document.getElementById('vp-date').textContent = "-";
            }

            if (provider.date_creation) {
                document.getElementById('vp-date-creation').textContent = provider.date_creation;
            } else {
                document.getElementById('vp-date-creation').textContent = "-";
            }

            document.getElementById('vp-siret').textContent = provider.siret;
            document.getElementById('vp-type').textContent = provider.type_prestation;
            document.getElementById('vp-tarifs').textContent = provider.tarifs;

            if (provider.status === 'validé') {
                document.getElementById('vp-validation').innerHTML = '<span class="text-green-600 font-bold">Validé</span>';
            } else if (provider.status === 'refusé') {
                document.getElementById('vp-validation').innerHTML = '<span class="text-red-600 font-bold">Refusé (' + provider.motif_refus + ')</span>';
            } else {
                document.getElementById('vp-validation').innerHTML = '<span class="text-yellow-600 font-bold">En attente</span>';
            }

            const documentsArea = document.getElementById('vp-documents-list');
            documentsArea.innerHTML = "<i>Chargement des documents...</i>";

            const responseDocs = await fetch(API_BASE + "/documents/" + id);

            if (responseDocs.ok) {
                const documentList = await responseDocs.json();

                if (documentList.length === 0) {
                    documentsArea.innerHTML = "<i>Aucun document lié pour le moment.</i>";
                } else {
                    documentsArea.innerHTML = "<div class='flex gap-4 flex-wrap'>";
                    for (let i = 0; i < documentList.length; i++) {
                        let documentItem = documentList[i];
                        documentsArea.innerHTML += `
                            <a href="${documentItem.lien}" target="_blank" class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg shadow-sm border text-[#1C5B8F] font-semibold hover:bg-blue-50 transition">
                                📄 ${documentItem.type}
                            </a>
                        `;
                    }
                    documentsArea.innerHTML += "</div>";
                }
            } else {
                documentsArea.innerHTML = "<i class='text-red-500 font-bold'>Erreur pendant le chargement des documents.</i>";
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

        // ====================================================
        // 3. LE MODE "VÉRIFIER LES PRESTATAIRES"
        // ====================================================
        function startVerification() {
            pendingProviders = [];
            for (let i = 0; i < allProviders.length; i++) {
                if (allProviders[i].status === "en attente") {
                    pendingProviders.push(allProviders[i]);
                }
            }

            if (pendingProviders.length === 0) {
                showAlert("Génial ! Il n'y a aucun dossier en attente à vérifier.", true);
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
                showAlert("Vous avez vu tous les dossiers en attente.", true);
                loadProviders();
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

        // ====================================================
        // 4. ACTIONS: MODIFIER ET SUPPRIMER
        // ====================================================
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

        // ====================================================
        // 5. ENVOI DES REQUETES AU SERVEUR
        // ====================================================

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
                toggleModal('add-modal');
                document.getElementById('add-form').reset();
                showAlert("Prestataire ajouté avec succès !", true);
                loadProviders();
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
                loadProviders();
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
                loadProviders();
            } else {
                showAlert("Impossible de supprimer.", false);
            }
        });

        window.onload = loadProviders;
    </script>
</body>

</html>