<?php
session_start();
$is_logged_in = isset($_SESSION['provider_id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer mes Services</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: { fontFamily: { sans: ['Alata', 'sans-serif'] } }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="flex min-h-screen relative">
        <?php include("../includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto relative">
            <main class="p-8">
                <?php if ($is_logged_in): ?>
                    <div id="main-content" class="space-y-8 max-w-6xl mx-auto">
                        
                        <div class="flex justify-between items-center">
                            <div>
                                <h1 class="text-3xl font-semibold text-[#1C5B8F]">Mes Services</h1>
                                <p class="text-gray-500 mt-1">Gérez les prestations que vous proposez à vos clients.</p>
                            </div>
                            <button onclick="toggleModal('serviceModal')" class="rounded-full px-6 py-2.5 bg-[#1C5B8F] text-white font-bold hover:bg-[#154670] transition-colors shadow-md flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Ajouter un service
                            </button>
                        </div>

                        <div id="alert-box" class="hidden p-4 rounded-xl font-semibold text-sm transition-all"></div>

                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="text-gray-400 text-sm border-b border-gray-100">
                                            <th class="pb-4 font-medium px-4">Nom du Service</th>
                                            <th class="pb-4 font-medium px-4">Description</th>
                                            <th class="pb-4 font-medium px-4">Prix</th>
                                            <th class="pb-4 font-medium text-right px-4">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="services-table-body">
                                        <tr>
                                            <td colspan="4" class="py-8 text-center text-gray-500 text-sm">
                                                <span class="animate-pulse">Chargement de vos services...</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center py-20 rounded-[2.5rem] shadow-xl shadow-blue-900/10 m-8 bg-white border border-gray-100">
                        <p class="text-center font-semibold text-[#1C5B8F] text-2xl mb-8">Vous devez être connecté(e).</p>
                        <a class="rounded-full px-8 py-3 bg-[#1C5B8F] text-white font-bold hover:bg-[#154670] transition-colors shadow-md" href="/providers/account/signin.php">Je me connecte</a>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <div id="serviceModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 transition-opacity">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-lg p-8 m-4">
            <h2 class="text-2xl font-bold text-[#1C5B8F] mb-6">Ajouter un nouveau service</h2>
            
            <form id="add-service-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du service</label>
                    <input type="text" id="nom_service" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#1C5B8F] focus:border-transparent outline-none transition-all">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description courte</label>
                    <textarea id="desc_service" rows="3" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#1C5B8F] focus:border-transparent outline-none transition-all"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix unitaire (€)</label>
                    <input type="number" id="prix_service" step="0.01" min="0" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#1C5B8F] focus:border-transparent outline-none transition-all">
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" onclick="toggleModal('serviceModal')" class="px-6 py-2.5 rounded-full font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">Annuler</button>
                    <button type="submit" class="px-6 py-2.5 rounded-full font-bold text-white bg-[#1C5B8F] hover:bg-[#154670] transition-colors shadow-md">Créer le service</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editServiceModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 transition-opacity">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-lg p-8 m-4">
            <h2 class="text-2xl font-bold text-[#E1AB2B] mb-6">Modifier le service</h2>
            
            <form id="edit-service-form" class="space-y-4">
                <input type="hidden" id="edit_id_service">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du service</label>
                    <input type="text" id="edit_nom_service" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#E1AB2B] focus:border-transparent outline-none transition-all">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description courte</label>
                    <textarea id="edit_desc_service" rows="3" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#E1AB2B] focus:border-transparent outline-none transition-all"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix unitaire (€)</label>
                    <input type="number" id="edit_prix_service" step="0.01" min="0" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#E1AB2B] focus:border-transparent outline-none transition-all">
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" onclick="toggleModal('editServiceModal')" class="px-6 py-2.5 rounded-full font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">Annuler</button>
                    <button type="submit" class="px-6 py-2.5 rounded-full font-bold text-white bg-[#E1AB2B] hover:bg-[#c99723] transition-colors shadow-md">Sauvegarder</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentProviderId = null;

        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            modal.classList.toggle('hidden');
        }

        function showAlert(msg, isSuccess = false) {
            const alertBox = document.getElementById('alert-box');
            alertBox.textContent = msg;
            alertBox.className = `p-4 mb-6 rounded-xl font-bold block ${isSuccess ? 'text-green-700 bg-green-100 border border-green-400' : 'text-red-700 bg-red-100 border border-red-400'}`;
            alertBox.classList.remove('hidden');
            setTimeout(() => alertBox.classList.add('hidden'), 5000); 
        }

        async function loadServices() {
            const tbody = document.getElementById('services-table-body');
            try {
                const res = await fetch(`${window.API_BASE_URL}/prestataire/services/${currentProviderId}/get`, { credentials: 'include' });
                if (!res.ok) throw new Error('Erreur réseau');
                
                const services = await res.json();
                tbody.innerHTML = '';

                if (!services || services.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="4" class="py-10 text-center text-gray-500 font-medium">Vous n'avez pas encore de services.</td></tr>`;
                    return;
                }

                services.forEach(s => {
                    const tr = document.createElement('tr');
                    tr.className = "border-b border-gray-50 hover:bg-gray-50 transition-colors";
                    tr.innerHTML = `
                        <td class="py-4 px-4 text-sm font-semibold text-[#1C5B8F]">${s.nom}</td>
                        <td class="py-4 px-4 text-sm text-gray-600 max-w-xs truncate" title="${s.description}">${s.description}</td>
                        <td class="py-4 px-4 text-sm font-bold text-[#E1AB2B]">${parseFloat(s.prix).toFixed(2)} €</td>
                        <td class="py-4 px-4 text-right flex justify-end gap-2">
                            <button onclick="openEditModal(${s.id_service}, '${s.nom.replace(/'/g, "\\'")}', '${s.description.replace(/'/g, "\\'")}', ${s.prix})" class="text-[#E1AB2B] hover:text-[#c99723] font-bold text-sm bg-yellow-50 px-3 py-1 rounded-lg transition-colors">
                                Modifier
                            </button>
                            <button onclick="deleteService(${s.id_service})" class="text-red-500 hover:text-red-700 font-bold text-sm bg-red-50 px-3 py-1 rounded-lg transition-colors">
                                Supprimer
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } catch (err) {
                tbody.innerHTML = `<tr><td colspan="4" class="py-8 text-center text-red-500 font-medium">Erreur lors du chargement.</td></tr>`;
            }
        }

        document.getElementById('add-service-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const nom = document.getElementById('nom_service').value;
            const desc = document.getElementById('desc_service').value;
            const prix = parseFloat(document.getElementById('prix_service').value);

            try {
                const res = await fetch(`${window.API_BASE_URL}/prestataire/services/${currentProviderId}/create`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include',
                    body: JSON.stringify({ nom: nom, description: desc, prix: prix, id_categorie: null })
                });

                if (res.ok) {
                    toggleModal('serviceModal');
                    e.target.reset(); 
                    showAlert("Service ajouté avec succès !", true);
                    loadServices(); 
                } else {
                    showAlert("Erreur lors de l'ajout du service.");
                }
            } catch (err) {
                showAlert("Erreur serveur.");
            }
        });

        function openEditModal(id, nom, desc, prix) {
            document.getElementById('edit_id_service').value = id;
            document.getElementById('edit_nom_service').value = nom;
            document.getElementById('edit_desc_service').value = desc;
            document.getElementById('edit_prix_service').value = prix;
            toggleModal('editServiceModal');
        }

        document.getElementById('edit-service-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const idService = document.getElementById('edit_id_service').value;
            const nom = document.getElementById('edit_nom_service').value;
            const desc = document.getElementById('edit_desc_service').value;
            const prix = parseFloat(document.getElementById('edit_prix_service').value);

            try {
                const res = await fetch(`${window.API_BASE_URL}/prestataire/services/${currentProviderId}/${idService}/update`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include',
                    body: JSON.stringify({ nom: nom, description: desc, prix: prix })
                });

                if (res.ok) {
                    toggleModal('editServiceModal');
                    showAlert("Service modifié avec succès !", true);
                    loadServices();
                } else {
                    showAlert("Erreur lors de la modification.");
                }
            } catch (err) {
                showAlert("Erreur serveur.");
            }
        });

        async function deleteService(idService) {
            if (!confirm("Voulez-vous vraiment supprimer ce service ?")) return;
            try {
                const res = await fetch(`${window.API_BASE_URL}/prestataire/services/${currentProviderId}/${idService}/delete`, {
                    method: 'DELETE',
                    credentials: 'include'
                });
                if (res.ok) {
                    showAlert("Service supprimé avec succès.", true);
                    loadServices();
                } else {
                    showAlert("Erreur lors de la suppression.");
                }
            } catch (err) {
                showAlert("Erreur serveur.");
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const meRes = await fetch(`${window.API_BASE_URL}/auth/me-provider`, { method: 'GET', credentials: 'include' });
                if (meRes.ok) {
                    const data = await meRes.json();
                    currentProviderId = data.id_prestataire || data.id || data.ID;
                    loadServices();
                } else {
                    window.location.href = "/providers/account/signin.php";
                }
            } catch (err) {
                console.error("Non connecté");
            }
        });
    </script>
</body>
</html>