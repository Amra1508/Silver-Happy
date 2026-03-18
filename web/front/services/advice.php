<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conseils</title>

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

    <main class="flex-1 relative">

        <div class="w-full px-6 md:px-16 mt-16 mb-8">
            <h1 class="text-4xl md:text-5xl font-bold text-[#1C5B8F] leading-tight mb-4">
                Nos conseils pratiques
            </h1>
            <p class="text-xl md:text-2xl text-gray-700 max-w-2xl mb-12">
                Découvrez nos astuces, recommandations et guides pour profiter pleinement de chaque instant en toute sérénité.
            </p>
            
            <h2 class="text-3xl font-bold text-[#1C5B8F] border-b-4 border-[#E1AB2B] inline-block pb-2">
                Dernières publications
            </h2>
        </div>

        <div id="advice-container" class="flex flex-wrap gap-8 px-6 md:px-16 py-4 justify-center md:justify-start">
            <div class="w-full text-center py-10">
                <p class="text-xl text-gray-500 animate-pulse">Chargement de nos conseils...</p>
            </div>
        </div>

        <div id="pagination-controls" class="flex justify-center items-center gap-4 py-12"></div>

    </main>

    <?php include("../includes/footer.php") ?>

    <script>
        const API_BASE = "http://localhost:8082";
        let currentPage = 1;
        const limit = 6; 

        function formatDisplayDate(dateStr) {
            if (!dateStr) return "Date inconnue";
            
            const safeDateStr = String(dateStr).replace(' ', 'T');
            const d = new Date(safeDateStr);
            
            if (isNaN(d)) return "Date invalide";
            
            return d.toLocaleDateString('fr-FR', {
                day: 'numeric', month: 'long', year: 'numeric'
            });
        }

        async function fetchConseils(page = 1) {
            try {
                currentPage = page;
                const response = await fetch(`${API_BASE}/conseil/read?page=${currentPage}&limit=${limit}`);
                
                if (!response.ok) throw new Error("Erreur de récupération des données");
                
                const result = await response.json();
                const conseils = result.data || [];
                const container = document.getElementById('advice-container');
                container.innerHTML = '';

                if (conseils.length === 0) {
                    container.innerHTML = '<p class="text-xl text-gray-500 py-10 italic">Aucun conseil disponible pour le moment.</p>';
                    return;
                }

                conseils.forEach(c => {
                    const id = c.id_conseil || c.ID || c.id; 
                        
                    const titre = c.titre || c.Titre || 'Sans titre';
                    const description = c.description || c.Description || '';
                    const categorie = c.categorie || c.Categorie || 'Général';
                    
                    const rawDate = c.date_publication || c.Date || c.date;
                    const datePub = formatDisplayDate(rawDate);

                    container.innerHTML += `
                        <div class="md:max-w-[450px] w-full bg-white border-l-8 border-[#1C5B8F] rounded-xl shadow-md p-6 flex flex-col hover:shadow-lg transition-shadow">
                            <h3 class="text-2xl text-[#1C5B8F] font-bold mb-4 leading-snug">${titre}</h3>
                            <p class="text-gray-600 leading-relaxed flex-grow text-lg mb-4">${description}</p>
                            
                            <a href="detail_advice.php?id=${id}" class="self-start text-[#1C5B8F] font-bold hover:text-[#E1AB2B] transition-colors flex items-center gap-2">
                                Lire la suite <span class="text-xl">→</span>
                            </a>
                        </div>
                    `;
                });

                renderPagination(result.totalPages);
            } catch (err) {
                console.error(err);
                document.getElementById('advice-container').innerHTML = `
                    <div class="w-full text-center py-10">
                        <p class="text-xl text-red-500 font-bold">Impossible de charger les conseils.</p>
                        <p class="text-gray-500 mt-2">Veuillez vérifier votre connexion au serveur.</p>
                    </div>`;
            }
        }

        function renderPagination(totalPages) {
            const paginationContainer = document.getElementById('pagination-controls');
            paginationContainer.innerHTML = '';
            
            if (totalPages <= 1) return;

            const prevDisabled = currentPage === 1 ? 'disabled opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 text-[#1C5B8F]';
            paginationContainer.innerHTML += `<button onclick="fetchConseils(${currentPage - 1})" class="px-4 py-2 border-2 border-[#1C5B8F] text-[#1C5B8F] rounded-full font-bold transition-colors ${prevDisabled}" ${currentPage === 1 ? 'disabled' : ''}>← Précédent</button>`;
            
            paginationContainer.innerHTML += `<span class="text-gray-500 font-medium px-4">Page <strong class="text-[#1C5B8F]">${currentPage}</strong> sur ${totalPages}</span>`;
            
            const nextDisabled = currentPage === totalPages ? 'disabled opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 text-[#1C5B8F]';
            paginationContainer.innerHTML += `<button onclick="fetchConseils(${currentPage + 1})" class="px-4 py-2 border-2 border-[#1C5B8F] text-[#1C5B8F] rounded-full font-bold transition-colors ${nextDisabled}" ${currentPage === totalPages ? 'disabled' : ''}>Suivant →</button>`;
        }

        window.onload = () => {
            fetchConseils(1);
        };
    </script>
</body>

</html>