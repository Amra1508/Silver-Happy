<?php
$is_logged_in = isset($_COOKIE['session_token']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Contacts - Prestataire</title>
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
    </script>
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="flex min-h-screen relative">
        <?php include("../includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto relative">
            <main class="p-8">
                <div class="max-w-7xl mx-auto space-y-8">

                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <h1 class="text-3xl font-semibold text-[#1C5B8F]">Ma Messagerie</h1>

                        <div class="bg-gray-200 p-1 rounded-full flex shadow-inner">
                            <button id="tab-senior" onclick="switchTab('senior')"
                                class="px-6 py-2 rounded-full font-semibold transition-all duration-300 bg-[#1C5B8F] text-white shadow-md">
                                Mes Adhérents
                            </button>
                            <button id="tab-admin" onclick="switchTab('admin')"
                                class="px-6 py-2 rounded-full font-semibold transition-all duration-300 text-gray-500 hover:text-[#1C5B8F]">
                                Équipe Silver Happy
                            </button>
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
                                    <th class="p-4 font-semibold text-center">Messages</th>
                                    <th class="p-4 font-semibold">Action</th>
                                </tr>
                            </thead>
                            <tbody id="list-user-body" class="divide-y divide-gray-100">
                            </tbody>
                        </table>
                    </div>

                    <div id="pagination-controls"></div>
                </div>
            </main>
        </div>
    </div>

    <script>
        const API_BASE = `${window.API_BASE_URL}`;
        let currentTab = 'senior';
        let currentPage = 1;
        const limit = 10;
        let currentUserId = null;

        window.addEventListener('auth_ready', () => {
            currentUserId = window.currentUserId;
            fetchContacts(1);
        });

        async function switchTab(tab) {
            currentTab = tab;
            currentPage = 1;

            const btnSenior = document.getElementById('tab-senior');
            const btnAdmin = document.getElementById('tab-admin');

            if (tab === 'senior') {
                btnSenior.className = "px-6 py-2 rounded-full font-semibold transition-all bg-[#1C5B8F] text-white shadow-md";
                btnAdmin.className = "px-6 py-2 rounded-full font-semibold transition-all text-gray-500 hover:text-[#1C5B8F]";
            } else {
                btnAdmin.className = "px-6 py-2 rounded-full font-semibold transition-all bg-[#1C5B8F] text-white shadow-md";
                btnSenior.className = "px-6 py-2 rounded-full font-semibold transition-all text-gray-500 hover:text-[#1C5B8F]";
            }
            fetchContacts(1);
        }

        async function fetchContacts(page = 1) {
            try {
                currentPage = page;
                const tbody = document.getElementById('list-user-body');
                tbody.innerHTML = '<tr><td colspan="5" class="p-8 text-center text-gray-400">Chargement...</td></tr>';

                const endpoint = currentTab === 'admin' ? '/admin/read' : '/seniors/read-presta';
                const url = `${API_BASE}${endpoint}?user_id=${currentUserId}&page=${currentPage}&limit=${limit}`;

                const response = await fetch(url);
                const result = await response.json();
                const data = result.data || [];

                tbody.innerHTML = '';

                if (data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" class="p-8 text-center text-gray-400">Aucun ${currentTab === 'admin' ? 'administrateur' : 'adhérent'} trouvé.</td></tr>`;
                    renderPagination(0, 0);
                    return;
                }

                data.forEach(u => {
                    const id = u.id_utilisateur || u.ID || u.id;
                    const unreadCount = u.est_lu || 0;
                    const badgeHtml = unreadCount > 0 ?
                        `<span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full animate-pulse">${unreadCount}</span>` :
                        `<span class="text-gray-400 text-sm">0</span>`;

                    const typePath = currentTab === 'admin' ? 'admin' : 'senior';

                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 border-b transition-colors">
                            <td class="p-4 font-medium text-gray-700">${u.prenom}</td>
                            <td class="p-4 uppercase font-medium text-gray-700">${u.nom}</td>
                            <td class="p-4 text-gray-500 text-sm">${u.email}</td>
                            <td class="p-4 text-center">${badgeHtml}</td>
                            <td class="p-4">
                                <a href="/providers/communication/messaging.php/${u.prenom}/${u.nom}/${id}/${typePath}">
                                    <button class="bg-gray-100 hover:bg-[#1C5B8F] hover:text-white text-[#1C5B8F] px-4 py-2 rounded-full transition-all font-semibold text-sm">
                                        Discuter
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
            let container = document.getElementById('pagination-controls');
            if (totalItems === 0 || totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = `<div class="flex justify-between items-center mt-6 text-sm">
                <span class="text-gray-500">Total : ${totalItems}</span>
                <div class="flex gap-2">`;

            for (let i = 1; i <= totalPages; i++) {
                const active = i === currentPage ? 'bg-[#1C5B8F] text-white' : 'text-[#1C5B8F] hover:bg-blue-50';
                html += `<button onclick="fetchContacts(${i})" class="px-3 py-1 border border-[#1C5B8F] rounded ${active}">${i}</button>`;
            }
            html += `</div></div>`;
            container.innerHTML = html;
        }
    </script>
</body>

</html>