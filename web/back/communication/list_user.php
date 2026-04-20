<?php include("../includes/login.php"); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Utilisateurs</title>
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
    </script>
</head>

<body class="bg-gray-50">
    <div class="flex min-h-screen">

        <?php include("../includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col">
            <?php include("../includes/header.php"); ?>

            <main class="p-8">

                <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                    <h1 class="text-3xl font-bold text-[#1C5B8F]">Messagerie Administrative</h1>

                    <div class="bg-gray-200 p-1 rounded-full flex shadow-inner">
                        <button id="tab-seniors" onclick="switchTab('seniors')"
                            class="px-6 py-2 rounded-full font-semibold transition-all duration-300 bg-[#1C5B8F] text-white shadow-md">
                            Séniors
                        </button>
                        <button id="tab-prestataires" onclick="switchTab('prestataires')"
                            class="px-6 py-2 rounded-full font-semibold transition-all duration-300 text-gray-500 hover:text-[#1C5B8F]">
                            Prestataires
                        </button>
                    </div>
                </div>

                <div id="api-message" class="hidden max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold"></div>

                <div class="table-container bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
                    <table class="w-full text-left">
                        <thead class="bg-[#1C5B8F] text-white">
                            <tr>
                                <th class="p-4 font-semibold">ID</th>
                                <th class="p-4 font-semibold">Prénom</th>
                                <th class="p-4 font-semibold">Nom</th>
                                <th class="p-4 font-semibold">Adresse mail</th>
                                <th class="p-4 font-semibold text-center">Messages non lus</th>
                                <th class="p-4 font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody id="list-user-body" class="divide-y divide-gray-100"></tbody>
                    </table>
                </div>

                <div id="pagination-controls"></div>

            </main>
        </div>
    </div>

    <script>
        const API_URL = `${window.API_BASE_URL}`;
        const messageBox = document.getElementById('api-message');

        let currentPage = 1;
        const limit = 10;
        let currentTab = 'seniors';

        function switchTab(tab) {
            currentTab = tab;
            currentPage = 1;

            const btnSeniors = document.getElementById('tab-seniors');
            const btnPrestas = document.getElementById('tab-prestataires');

            if (tab === 'seniors') {
                btnSeniors.className = "px-6 py-2 rounded-full font-semibold transition-all bg-[#1C5B8F] text-white shadow-md";
                btnPrestas.className = "px-6 py-2 rounded-full font-semibold transition-all text-gray-500 hover:text-[#1C5B8F]";
            } else {
                btnPrestas.className = "px-6 py-2 rounded-full font-semibold transition-all bg-[#1C5B8F] text-white shadow-md";
                btnSeniors.className = "px-6 py-2 rounded-full font-semibold transition-all text-gray-500 hover:text-[#1C5B8F]";
            }

            fetchUsers(1);
        }

        async function fetchUsers(page = 1) {
            try {
                currentPage = page;
                const tbody = document.getElementById('list-user-body');
                tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-gray-400">Chargement...</td></tr>';

                const currentUserId = window.currentUserId;

                const endpoint = currentTab === 'seniors' ? '/seniors/read' : '/prestataires/read';
                const url = `${API_URL}${endpoint}?page=${currentPage}&limit=${limit}&user_id=${currentUserId}`;

                const response = await fetch(url);
                const result = await response.json();

                const users = result.data || [];
                tbody.innerHTML = '';

                if (users.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" class="p-8 text-center text-gray-400">Aucun ${currentTab} trouvé.</td></tr>`;
                    renderPagination(0, 0);
                    return;
                }

                users.forEach(u => {
                    const id = u.id_utilisateur || u.ID || u.id;
                    const unreadCount = u.est_lu || 0;

                    const badgeHtml = unreadCount > 0 ?
                        `<span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full animate-pulse">${unreadCount}</span>` :
                        `<span class="text-gray-400 text-sm">0</span>`;

                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 border-b transition-colors">
                            <td class="p-4 text-gray-400 font-mono text-sm">#${id}</td>
                            <td class="p-4 font-medium text-gray-700">${u.prenom}</td>
                            <td class="p-4 uppercase font-medium text-gray-700">${u.nom}</td>
                            <td class="p-4 text-gray-500 text-sm">${u.email}</td>
                            <td class="p-4 text-center">${badgeHtml}</td>
                            <td class="p-4">
                                <a href="/back/communication/messaging.php/${u.prenom}/${u.nom}/${id}">
                                    <button class="bg-gray-100 hover:bg-[#1C5B8F] hover:text-white text-[#1C5B8F] px-4 py-2 rounded-full transition-all font-semibold text-sm shadow-sm">
                                        Répondre
                                    </button>
                                </a>
                            </td>
                        </tr>
                    `;
                });

                renderPagination(result.totalPages, result.total);

            } catch (err) {
                console.error(err);
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

            if (totalItems === 0 || totalPages <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }

            let html = `<div class="flex justify-between items-center mt-6 px-4 text-sm">
                            <span class="text-gray-500">Total : ${totalItems}</span>
                            <div class="flex gap-2">`;

            for (let i = 1; i <= totalPages; i++) {
                const activeClass = i === currentPage ? 'bg-[#1C5B8F] text-white shadow-md' : 'text-[#1C5B8F] hover:bg-blue-50';
                html += `<button onclick="fetchUsers(${i})" class="px-3 py-1 border border-[#1C5B8F] rounded transition-all font-medium ${activeClass}">${i}</button>`;
            }

            html += `</div></div>`;
            paginationContainer.innerHTML = html;
        }

        window.addEventListener('auth_ready', () => {
            fetchUsers(1);
        });
    </script>
</body>

</html>