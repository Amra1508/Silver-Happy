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
                <h1 class="big-text mb-8 text-center">Discuter avec notre équipe</h1>

                <div id="no-sub-container" class="hidden flex flex-col items-center justify-center py-20 rounded-[2.5rem] shadow-xl shadow-blue-900/10">
                    <p class="text-center font-semibold text-[#1C5B8F] text-2xl mb-8">
                        Vous devez posséder un abonnement Silver Happy pour accéder à la messagerie.
                    </p>
                    <a class="rounded-full px-8 py-3 button-blue text-lg" href="/front/services/subscription.php">
                        Découvrir nos abonnements
                    </a>
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
        const API_BASE = "http://localhost:8082/admin";
        let currentPage = 1;
        const limit = 10;
        let currentUserId = null;

        window.addEventListener('auth_ready', () => {
            const noSubContainer = document.getElementById('no-sub-container');
            const contactsContainer = document.getElementById('contacts-container');
            
            currentUserId = window.currentUserId;

            const user = window.userData;
            const hasSubscription = user && user.id_abonnement && user.id_abonnement > 0;

            if (!hasSubscription) {
                if (noSubContainer) noSubContainer.classList.remove('hidden');
                if (contactsContainer) contactsContainer.classList.add('hidden'); 
                return;
            }

            if (noSubContainer) noSubContainer.classList.add('hidden');
            if (contactsContainer) {
                contactsContainer.classList.remove('hidden');
                fetchAdmins(1);
            }
        });

        async function fetchAdmins(page = 1) {
            try {
                currentPage = page;
                const url = `${API_BASE}/read?page=${currentPage}&limit=${limit}&user_id=${currentUserId || ''}`;
                const response = await fetch(url);
                const result = await response.json();

                const admins = result.data || [];
                const tbody = document.getElementById('list-user-body');
                tbody.innerHTML = '';

                if (admins.length === 0) {
                    tbody.innerHTML = '<div class="p-8 text-center text-gray-400">Aucun admin disponible.</div>';
                    renderPagination(0, 0);
                    return;
                }

                admins.forEach(s => {
                    const id = s.id_utilisateur || s.ID || s.id;
                    const unreadCount = s.est_lu || 0;
                    
                    const badgeHtml = unreadCount > 0 
                        ? `<span class="ml-3 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full animate-pulse shadow-sm">${unreadCount} message(s) non lu(s)</span>` 
                        : '';

                    const cardHtml = `
                        <div class="flex flex-col md:flex-row items-center justify-between index-components bg-white border border-gray-100 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-5 flex-1">
                                <div>
                                    <h2 class="text-xl font-bold text-[#1C5B8F] flex items-center">
                                        ${s.prenom} <span class="uppercase ml-1">${s.nom}</span>
                                        ${badgeHtml}
                                    </h2>
                                    <p class="text-gray-500 flex items-center gap-2 mt-1">
                                        ${s.email}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <a href="/front/communication/messaging.php/${s.prenom}/${s.nom}/${id}" class="inline-block">
                                    <button class="rounded-full px-6 py-2 bg-[#1C5B8F] text-white font-bold hover:bg-[#154670] transition-colors shadow-sm">
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
                console.error("Erreur lors de la connexion à l'API", err);
                const tbody = document.getElementById('list-user-body');
                if (tbody) tbody.innerHTML = '<div class="p-8 text-center text-red-500 font-bold">Erreur de chargement. Vérifiez que l\'API est lancée.</div>';
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
                    <span class="text-gray-500 font-semibold">Total : ${totalItems} admin(s)</span>
                    <div class="flex gap-2">
                        <button ${currentPage === 1 ? 'disabled' : ''} onclick="fetchAdmins(${currentPage - 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50 transition-colors">Précédent</button>
            `;

            for (let i = 1; i <= totalPages; i++) {
                const activeClass = i === currentPage ? 'bg-[#1C5B8F] text-white' : 'text-[#1C5B8F] hover:bg-blue-50';
                html += `<button onclick="fetchAdmins(${i})" class="px-3 py-1 border border-[#1C5B8F] rounded transition-colors ${activeClass}">${i}</button>`;
            }

            html += `
                        <button ${currentPage === totalPages ? 'disabled' : ''} onclick="fetchAdmins(${currentPage + 1})" class="px-3 py-1 border border-[#1C5B8F] text-[#1C5B8F] rounded disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-50 transition-colors">Suivant</button>
                    </div>
                </div>
            `;
            paginationContainer.innerHTML = html;
        }
    </script>
</body>

</html>