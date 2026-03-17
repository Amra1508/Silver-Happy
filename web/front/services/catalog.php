<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>

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

    <main class="flex-1">

        <div class="w-full px-6 md:px-16 mt-12 mb-8 bg-gray-50 text-center">
            <h2 class="big-text mb-4 text-[#1C5B8F]">
                Notre catalogue
            </h2>
            <h2 class="small-text max-w-4xl mx-auto text-gray-600">
                Parcourez ci-dessous l'ensemble des services proposés par nos prestataires rigoureusement sélectionnés. 
                Certains services peuvent nécessiter une réservation à l'avance.
            </h2>
        </div>

        <div id="api-message" class="hidden max-w-2xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold"></div>

        <div id="services-container" class="flex flex-wrap gap-8 px-6 md:px-16 py-10 justify-center">
            <div class="w-full text-center py-10">
                <p class="text-xl text-gray-500 animate-pulse">Chargement des services en cours...</p>
            </div>
        </div>

        <div id="pagination-controls" class="flex justify-center items-center gap-4 pb-16">
            </div>

    </main>

    <?php include("../includes/footer.php") ?>

    <script>
        const API_BASE = "http://localhost:8082/service";
        let currentPage = 1;
        const limit = 6;
        const messageBox = document.getElementById('api-message');

        function showAlert(msg, isSuccess) {
            messageBox.textContent = msg;
            messageBox.className = `max-w-2xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold shadow-md ${isSuccess ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'}`;
            messageBox.classList.remove('hidden');
        }

        async function fetchServices(page = 1) {
            try {
                currentPage = page;
                const response = await fetch(`${API_BASE}/read?page=${currentPage}&limit=${limit}`);
                
                if (!response.ok) throw new Error("Erreur de récupération");
                
                const result = await response.json();
                const services = result.data || [];
                const container = document.getElementById('services-container');
                
                container.innerHTML = '';

                if (services.length === 0) {
                    container.innerHTML = '<p class="text-xl text-gray-500 py-10 italic">Aucun service n\'est disponible pour le moment. Revenez très vite !</p>';
                    renderPagination(0);
                    return;
                }

                services.forEach(s => {
                    const nom = s.nom || s.Nom || 'Service sans nom';
                    const description = s.description || s.Description || 'Aucune description disponible.';
                    const dispoVal = s.disponibilite !== undefined ? parseInt(s.disponibilite) : parseInt(s.Disponibilite);
                    
                    let badgeHTML = '';
                    let buttonHTML = '';

                    if (dispoVal === 1) {
                        badgeHTML = `<span class="bg-green-100 text-green-700 border border-green-300 text-xs px-3 py-1 rounded-full font-bold uppercase tracking-wider">Disponible</span>`;
                        buttonHTML = `<button class="w-full rounded-full py-3 px-6 button-blue font-bold text-lg mt-auto hover:bg-[#154670] transition-colors">Réserver ce service</button>`;
                    } else {
                        badgeHTML = `<span class="bg-red-100 text-red-700 border border-red-300 text-xs px-3 py-1 rounded-full font-bold uppercase tracking-wider">Indisponible</span>`;
                        buttonHTML = `<button class="w-full rounded-full py-3 px-6 bg-gray-300 text-gray-500 font-bold text-lg mt-auto cursor-not-allowed" disabled>Actuellement réservé</button>`;
                    }

                    const cardHTML = `
                        <div class="md:max-w-[400px] w-full bg-white index-components border border-gray-200 flex flex-col p-8 rounded-[2rem] shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 relative">
                            <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-1/3 h-1.5 bg-[#E1AB2B] rounded-b-md"></div>
                            
                            <div class="flex justify-between items-start mb-4 mt-2">
                                <h3 class="big-text text-2xl text-[#1C5B8F] font-bold pr-2">${nom}</h3>
                                <div class="flex-shrink-0 mt-1">${badgeHTML}</div>
                            </div>
                            
                            <p class="small-text text-gray-600 mb-8 flex-grow leading-relaxed">
                                ${description}
                            </p>
                            
                            ${buttonHTML}
                        </div>
                    `;
                    container.innerHTML += cardHTML;
                });

                renderPagination(result.totalPages);

            } catch (err) {
                console.error(err);
                showAlert("Impossible de charger les services. Veuillez vérifier votre connexion.", false);
                document.getElementById('services-container').innerHTML = '';
            }
        }

        function renderPagination(totalPages) {
            const paginationContainer = document.getElementById('pagination-controls');
            paginationContainer.innerHTML = '';

            if (totalPages <= 1) return;

            const prevDisabled = currentPage === 1 ? 'disabled opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 text-[#1C5B8F]';
            paginationContainer.innerHTML += `
                <button onclick="fetchServices(${currentPage - 1})" class="px-4 py-2 border-2 border-[#1C5B8F] rounded-full font-bold transition-colors ${prevDisabled}" ${currentPage === 1 ? 'disabled' : ''}>
                    ← Précédent
                </button>
            `;

            paginationContainer.innerHTML += `
                <span class="text-gray-500 font-medium px-4">
                    Page <strong class="text-[#1C5B8F]">${currentPage}</strong> sur ${totalPages}
                </span>
            `;

            const nextDisabled = currentPage === totalPages ? 'disabled opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 text-[#1C5B8F]';
            paginationContainer.innerHTML += `
                <button onclick="fetchServices(${currentPage + 1})" class="px-4 py-2 border-2 border-[#1C5B8F] rounded-full font-bold transition-colors ${nextDisabled}" ${currentPage === totalPages ? 'disabled' : ''}>
                    Suivant →
                </button>
            `;
        }

        window.onload = () => {
            fetchServices(1);
        };
    </script>
</body>

</html>