<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des utilisateurs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Alata', 'sans-serif']
                    }
                }
            }
        }

        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            if (modal) {
                modal.classList.toggle('hidden');
            }
        }
    </script>
</head>

<body class="bg-gray-50 text-gray-800">

    <div class="flex min-h-screen relative">

        <?php include("../includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto relative">

            <main class="p-8">

                <div id="main-content-valide" class="hidden space-y-8 max-w-7xl mx-auto">

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                        <div>
                            <h1 class="text-3xl font-semibold text-[#1C5B8F]">Contacter un adhérent.</h1>
                        </div>
                    </div>

                    <div id="api-message" class="hidden p-4 rounded-xl font-semibold text-sm text-center"></div>

                    <div class="table-container bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
                        <table class="w-full text-left">
                            <thead class="bg-[#1C5B8F] text-white">
                                <tr>
                                    <th class="p-4 font-semibold">Prénom</th>
                                    <th class="p-4 font-semibold">Nom</th>
                                    <th class="p-4 font-semibold">Adresse mail</th>
                                    <th class="p-4 font-semibold text-center">Messages en attente</th>
                                    <th class="p-4 font-semibold">Contacter</th>
                                </tr>
                            </thead>
                            <tbody id="list-user-body" class="divide-y divide-gray-100"></tbody>
                        </table>
                    </div>

                </div>

            </main>
        </div>

    </div>

    <script>
        const API_BASE = `${window.API_BASE_URL}/seniors`;
        const messageBox = document.getElementById('api-message');

        let currentPage = 1;
        const limit = 10;

        function showAlert(msg, isSuccess) {
            messageBox.textContent = msg;
            messageBox.className = `max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold ${isSuccess ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'}`;
            messageBox.classList.remove('hidden');
            setTimeout(() => messageBox.classList.add('hidden'), 3500);
        }

        async function fetchSeniors(page = 1) {
            try {
                currentPage = page;
                const response = await fetch(`${API_BASE}/read-presta?page=${currentPage}&limit=${limit}`);
                const result = await response.json();

                const seniors = result.data || [];
                const tbody = document.getElementById('list-user-body');
                tbody.innerHTML = '';

                if (seniors.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-gray-400">Aucun sénior trouvé.</td></tr>';
                    renderPagination(0, 0);
                    return;
                }

                seniors.forEach(s => {
                    const s_nom = s.nom ? s.nom.replace(/'/g, "\\'") : '';
                    const s_prenom = s.prenom ? s.prenom.replace(/'/g, "\\'") : '';
                    const id = s.id_utilisateur || s.ID || s.id;

                    const unreadCount = s.est_lu || 0;

                    const badgeHtml = unreadCount > 0 ?
                        `<span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full animate-pulse shadow-sm">${unreadCount}</span>` :
                        `<span class="text-gray-400 text-sm font-medium">0</span>`;

                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 border-b transition-colors">
                            <td class="p-4 font-medium text-gray-700">${s.prenom}</td>
                            <td class="p-4 uppercase font-medium text-gray-700">${s.nom}</td>
                            <td class="p-4 text-gray-500 text-sm">${s.email}</td>
                            <td class="p-4 text-center">${badgeHtml}</td>
                            <td class="p-4">
                                <a href="/providers/communication/messaging.php/${s.prenom}/${s.nom}/${id}">
                                    <button class="bg-gray-100 hover:bg-[#1C5B8F] hover:text-white text-[#1C5B8F] px-4 py-2 rounded-full transition-all font-semibold text-sm shadow-sm">
                                        Voir la discussion
                                    </button>
                                </a>
                            </td>
                        </tr>
                    `;
                });

                renderPagination(result.totalPages, result.total);

            } catch (err) {
                console.error(err);
                showAlert("Erreur lors de la connexion à l'API", false);
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
                    <span class="text-gray-500 font-semibold bg-gray-100 px-3 py-1 rounded-full">Total : ${totalItems} utilisateurs</span>
                    <div class="flex gap-2">
                        <button ${currentPage === 1 ? 'disabled' : ''} onclick="fetchSeniors(${currentPage - 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50 transition-colors">Précédent</button>
            `;

            for (let i = 1; i <= totalPages; i++) {
                const activeClass = i === currentPage ? 'bg-[#1C5B8F] text-white shadow-md' : 'text-[#1C5B8F] hover:bg-blue-50';
                html += `<button onclick="fetchSeniors(${i})" class="px-3 py-1 border border-[#1C5B8F] rounded transition-all font-medium ${activeClass}">${i}</button>`;
            }

            html += `
                        <button ${currentPage === totalPages ? 'disabled' : ''} onclick="fetchSeniors(${currentPage + 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50 transition-colors">Suivant</button>
                    </div>
                </div>
            `;
            paginationContainer.innerHTML = html;
        }

        window.onload = () => fetchSeniors(1);
    </script>
</body>

</html>