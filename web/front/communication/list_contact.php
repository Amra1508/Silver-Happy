<?php
$is_logged_in = isset($_COOKIE['session_token']);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacts</title>
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

<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php include("../includes/header.php") ?>

    <main class="pt-8 mb-10 bg-white flex-grow">
        <div class="max-w-4xl mx-auto">
            <?php if ($is_logged_in): ?>
                <h1 class="text-3xl font-bold text-[#1C5B8F] mb-8 text-center">Mes Conversations</h1>

                <div class="flex justify-center mb-10">
                    <div class="bg-gray-100 p-1 rounded-full flex shadow-inner">
                        <button id="tab-admin" onclick="switchTab('admin')"
                            class="px-6 py-2 rounded-full font-semibold transition-all duration-300 bg-[#1C5B8F] text-white shadow-md">
                            Équipe Silver Happy
                        </button>
                        <button id="tab-presta" onclick="switchTab('presta')"
                            class="px-6 py-2 rounded-full font-semibold transition-all duration-300 text-gray-500 hover:text-[#1C5B8F]">
                            Nos Prestataires
                        </button>
                    </div>
                </div>

                <div id="contacts-container" class="hidden">
                    <div id="list-user-body" class="grid gap-6">
                    </div>

                    <div id="pagination-controls"></div>
                </div>

            <?php else: ?>
                <div class="flex flex-col items-center justify-center py-20 rounded-[2.5rem] shadow-xl shadow-blue-900/10">
                    <p class="text-center font-semibold text-[#1C5B8F] text-2xl mb-8">
                        Vous devez être connecté(e) pour discuter avec notre équipe Silver Happy.</p>
                    <a class="rounded-full px-4 py-2 button-blue" href="/front/account/signin.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">
                        Je me connecte </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include("../includes/footer.php") ?>

    <script>
        const API_BASE = `${window.API_BASE_URL}`;
        let currentPage = 1;
        const limit = 5;
        let currentUserId = null;

        window.addEventListener('auth_ready', () => {
            const noSubContainer = document.getElementById('no-sub-container');
            const contactsContainer = document.getElementById('contacts-container');

            currentUserId = window.currentUserId;

            if (contactsContainer) {
                contactsContainer.classList.remove('hidden');
                fetchContacts(1);
            }
        });

        let currentTab = 'admin';

        async function switchTab(tab) {
            currentTab = tab;
            currentPage = 1;

            const btnAdmin = document.getElementById('tab-admin');
            const btnPresta = document.getElementById('tab-presta');

            if (tab === 'admin') {
                btnAdmin.className = "px-6 py-2 rounded-full font-semibold transition-all bg-[#1C5B8F] text-white shadow-md";
                btnPresta.className = "px-6 py-2 rounded-full font-semibold transition-all text-gray-500 hover:text-[#1C5B8F]";
            } else {
                btnPresta.className = "px-6 py-2 rounded-full font-semibold transition-all bg-[#1C5B8F] text-white shadow-md";
                btnAdmin.className = "px-6 py-2 rounded-full font-semibold transition-all text-gray-500 hover:text-[#1C5B8F]";
            }

            fetchContacts(1);
        }

        async function fetchContacts(page = 1) {
            try {
                currentPage = page;

                const tbody = document.getElementById('list-user-body');
                tbody.innerHTML = '<div class="text-center p-8 text-gray-400">Chargement...</div>';

                const endpoint = currentTab === 'admin' ? '/admin/read' : '/prestataire/read';
                const url = `${API_BASE}${endpoint}?page=${currentPage}&limit=${limit}&user_id=${currentUserId}`;

                const response = await fetch(url);
                const result = await response.json();

                const data = result.data || [];
                tbody.innerHTML = '';

                if (data.length === 0) {
                    tbody.innerHTML = '<div class="p-8 text-center text-gray-400">Aucun admin disponible.</div>';
                    renderPagination(0, 0);
                    return;
                }

                data.forEach(s => {
                    const id = s.id_utilisateur || s.ID || s.id;
                    const unreadCount = s.est_lu || 0;

                    const badgeHtml = unreadCount > 0 ?
                        `<span class="ml-3 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full animate-pulse shadow-sm">${unreadCount} message(s) non lu(s)</span>` :
                        '';

                    const contactType = currentTab === 'admin' ? 'admin' : 'presta';

                    const cardHtml = `
                        <div class="flex flex-col md:flex-row items-center justify-between index-components">
                            <div class="flex items-center gap-5 flex-1">
                                <div>
                                    <h2 class="small-text flex items-center">
                                        ${s.prenom} <span class="uppercase ml-1">${s.nom}</span>
                                        ${badgeHtml}
                                    </h2>
                                    <p class="text-black flex items-center gap-2">
                                        ${s.email}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <a href="/front/communication/messaging.php/${s.prenom}/${s.nom}/${id}/${contactType}" class="inline-block">
                                    <button class="rounded-full px-6 button-blue">
                                        Voir la discussion
                                    </button>
                                </a>
                            </div>
                        </div>
                    `;
                    tbody.insertAdjacentHTML('beforeend', cardHtml);
                });

                renderPagination(result.totalPages, result.total);

            } catch (err) {
                console.error("Erreur lors de la connexion à l'API");
            }
        }

        function renderPagination(totalPages, totalItems) {
            let paginationContainer = document.getElementById('pagination-controls');

            if (!paginationContainer) {
                const listContainer = document.getElementById('list-user-body');
                paginationContainer = document.createElement('div');
                paginationContainer.id = 'pagination-controls';
                listContainer.parentNode.insertBefore(paginationContainer, listContainer.nextSibling);
            }

            if (totalItems === 0 || totalPages <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }

            let html = `
                <div class="flex justify-between items-center mt-6 px-4 text-sm">
                    <span class="text-gray-500 font-semibold">Total : ${totalItems} contact(s)</span>
                    <div class="flex gap-2">
                        <button ${currentPage === 1 ? 'disabled' : ''} onclick="fetchContacts(${currentPage - 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Précédent</button>
            `;

            for (let i = 1; i <= totalPages; i++) {
                const activeClass = i === currentPage ? 'bg-[#1C5B8F] text-white' : 'text-[#1C5B8F] hover:bg-blue-50';
                html += `<button onclick="fetchContacts(${i})" class="px-3 py-1 border border-[#1C5B8F] rounded transition ${activeClass}">${i}</button>`;
            }

            html += `
                        <button ${currentPage === totalPages ? 'disabled' : ''} onclick="fetchContacts(${currentPage + 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50">Suivant</button>
                    </div>
                </div>
            `;
            paginationContainer.innerHTML = html;
        }

        setTimeout(() => {
            const isLogged = "<?php echo $is_logged_in ? '1' : '0'; ?>";
            if (isLogged === '1' && !window.currentUserId) {}
        }, 1500);
    </script>
</body>

</html>