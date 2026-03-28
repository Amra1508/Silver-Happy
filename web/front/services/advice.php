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

        <div class="w-full px-6 md:px-16 mt-8 mb-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-[#1C5B8F] leading-tight mb-4">
                Nos conseils pratiques
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-12">
                Découvrez nos astuces, recommandations et guides pour profiter pleinement de chaque instant en toute sérénité.
            </p>

            <div class="flex justify-center">
                <h2 class="text-3xl font-bold text-[#1C5B8F] border-b-4 border-[#E1AB2B] inline-block pb-2">
                    Dernières publications
                </h2>
            </div>
        </div>

        <div id="advice-container" class="flex flex-wrap gap-10 px-6 md:px-16 py-10 justify-center">
            <div class="w-full text-center py-10">
                <p class="text-xl text-gray-500 animate-pulse">Chargement de nos conseils...</p>
            </div>
        </div>

        <div id="pagination-controls" class="flex justify-center items-center gap-4 pb-16"></div>

    </main>

    <?php include("../includes/footer.php") ?>

    <script>
        const API_BASE = "http://localhost:8082";
        let currentPage = 1;
        const limit = 6;

        window.addEventListener('DOMContentLoaded', () => {
            fetchConseils();
        });

        function formatDisplayDate(dateStr) {
            if (!dateStr) return "Date inconnue";
            const safeDateStr = String(dateStr).replace(' ', 'T');
            const d = new Date(safeDateStr);
            if (isNaN(d)) return "Date invalide";
            return d.toLocaleDateString('fr-FR', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }

        async function fetchConseils(page = 1) {
            try {
                currentPage = page;
                const userId = window.currentUserId || 1;

                const response = await fetch(`${API_BASE}/conseil/read?page=${currentPage}&limit=${limit}&user_id=${userId}`);

                if (!response.ok) throw new Error("Erreur de récupération des données");

                const result = await response.json();
                let conseils = result.data || [];
                const container = document.getElementById('advice-container');
                container.innerHTML = '';

                if (conseils.length === 0) {
                    container.innerHTML = '<p class="text-xl text-gray-500 py-10 italic col-span-full text-center">Aucun conseil disponible pour le moment.</p>';
                    return;
                }

                conseils.sort((a, b) => (b.likes || 0) - (a.likes || 0));

                conseils.forEach(c => {
                    const id = c.id_conseil || c.ID || c.id;
                    const titre = c.titre || c.Titre || 'Sans titre';
                    const description = c.description || c.Description || '';
                    const categorie = c.categorie || c.Categorie || 'Général';
                    const likes = c.likes || 0;
                    const rawDate = c.date_publication || c.Date || c.date;
                    const datePub = formatDisplayDate(rawDate);

                    container.innerHTML += `
                        <div class="w-[380px] bg-white border border-gray-100 flex flex-col p-8 rounded-[2.5rem] shadow-xl hover:-translate-y-2 transition-all relative">
                            <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-1/3 h-1.5 bg-[#1C5B8F] rounded-b-md"></div>
                            
                            <div class="flex justify-between items-start mb-4">
                                <span class="bg-[#E1AB2B]/10 text-[#E1AB2B] border border-[#E1AB2B]/30 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">
                                    ${categorie}
                                </span>
                                <div class="flex items-center gap-1 text-gray-400 font-bold text-[11px]">
                                     <span>❤️ ${likes}</span>
                                </div>
                            </div>

                            <h3 class="text-2xl text-[#1C5B8F] font-bold mb-2 leading-tight">${titre}</h3>
                            <p class="text-[11px] text-gray-400 font-bold mb-4 uppercase">${datePub}</p>
                            
                            <p class="text-gray-500 mb-8 flex-grow leading-relaxed line-clamp-3">
                                ${description || "Aucune description disponible."}
                            </p>
                            
                            <div class="mt-auto pt-4 border-t border-gray-50">
                                <a href="detail_advice.php?id=${id}" class="inline-flex items-center text-[#1C5B8F] font-bold hover:text-[#E1AB2B] transition-colors group">
                                    Lire la suite <span class="ml-2 transition-transform group-hover:translate-x-1">→</span>
                                </a>
                            </div>
                        </div>
                    `;
                });

                renderPagination(result.totalPages);
            } catch (err) {
                console.error(err);
                document.getElementById('advice-container').innerHTML = `
                    <div class="w-full text-center py-10">
                        <p class="text-xl text-red-500 font-bold">Impossible de charger les conseils.</p>
                        <p class="text-gray-500 mt-2">Vérifiez que votre API est bien lancée.</p>
                    </div>`;
            }
        }

        function renderPagination(totalPages) {
            const container = document.getElementById('pagination-controls');
            if (!container) return;
            container.innerHTML = '';
            if (totalPages <= 1) return;

            const btnClass = "px-6 py-2 border-2 border-[#1C5B8F] text-[#1C5B8F] rounded-full font-bold transition-all hover:bg-[#1C5B8F] hover:text-white disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-[#1C5B8F]";

            const prevDisabled = currentPage === 1 ? 'disabled' : '';
            container.innerHTML += `<button onclick="fetchConseils(${currentPage - 1})" class="${btnClass}" ${prevDisabled}>← Précédent</button>`;

            container.innerHTML += `<span class="text-gray-500 font-bold px-4">Page ${currentPage} / ${totalPages}</span>`;

            const nextDisabled = currentPage === totalPages ? 'disabled' : '';
            container.innerHTML += `<button onclick="fetchConseils(${currentPage + 1})" class="${btnClass}" ${nextDisabled}>Suivant →</button>`;
        }
    </script>
</body>

</html>