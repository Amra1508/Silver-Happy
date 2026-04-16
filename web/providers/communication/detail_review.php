<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail de l'avis</title>
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
            <main class="flex-1 container mx-auto px-6 py-12 max-w-4xl">
                <div class="p-3 flex justify-between items-center mx-8">
                    <a href="/providers/communication/reviews.php">
                        <button class="flex items-center rounded-full px-6 bg-[#1C5B8F] text-white py-2 font-semibold text-xl hover:bg-[#E1AB2B]/60 transition shadow-md">
                            <img src="/front/icons/fleche_gauche.svg" alt="fleche" class="w-7 h-7 mr-2"> Revenir aux avis
                        </button>
                    </a>
                </div>
                <div id="detail-container">

                </div>
            </main>
        </div>

    </div>

    <script>
        const API_BASE = window.API_BASE_URL;

        const urlParams = new URLSearchParams(window.location.search);
        const avisId = urlParams.get('id');

        window.addEventListener('auth_ready', () => {
            userId = window.currentUserId;
            fetchOneAvis();
        });

        async function fetchOneAvis() {

            try {

                const response = await fetch(`${API_BASE}/prestataire/${avisId}/read-one?user_id=${userId}`);
                const result = await response.json();

                const a = result.data || result;

                if (!a || Object.keys(a).length === 0) {
                    throw new Error("Données vides");
                }

                renderDetail(a);
            } catch (err) {
                console.error("Erreur détaillée :", err);
                document.getElementById('detail-container').innerHTML = `
                    <p class="text-red-500 font-bold">Erreur : Impossible de charger les détails (ID: ${avisId}).</p>
                `;
            }
        }

        function renderDetail(a) {
            const container = document.getElementById('detail-container');
            if (!container) return;

            const rawDate = a.date_publication || a.Date || a.date;
            let dateStr = "Date inconnue";

            if (rawDate) {
                const safeDateStr = String(rawDate).replace(' ', 'T');
                const d = new Date(safeDateStr);
                if (!isNaN(d)) {
                    dateStr = d.toLocaleDateString('fr-FR', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                }
            }

            const titre = a.titre || "Titre absent";
            const description = a.description || "";
            const note = a.note || 0;

            const fullNom = `${a.prenom_u || ''} ${a.nom_u || ''}`.trim();

            const labelUser = (fullNom !== "") ?
                `<div class="flex items-center gap-2 mb-2">
                            <span class="text-[11px] font-bold text-[#1C5B8F] bg-blue-50 px-2 py-0.5 rounded">${fullNom}</span>
                </div>` :
                "";

            container.innerHTML = `
                <article class="bg-white rounded-2xl shadow-xl overflow-hidden relative">
                    <div class="p-8 md:p-12 relative">

                        <div class="flex flex-wrap items-center gap-4 mb-6 mt-6 md:mt-0">
                            <span class="text-[#E1AB2B] border border-[#E1AB2B] text-sm px-3 py-1 rounded-full font-bold uppercase tracking-wider">
                                ${"★".repeat(note)}${"☆".repeat(5 - note)}
                            </span>
                            <span class="text-gray-400 font-medium">Publié le ${dateStr}</span>
                        </div>

                        <h1 class="text-4xl md:text-5xl font-bold text-[#1C5B8F] mb-8 leading-tight">
                            ${titre}
                        </h1>

                        ${labelUser}

                        <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                            <p class="leading-relaxed flex-grow mb-4 break-words text-xl font-semibold text-gray-800">
                                ${description || "Aucune description détaillée disponible."}
                            </p>
                        </div>
                    </div>
                </article>
            `;
        }

        window.onload = () => {
            if (document.getElementById('detail-container')) {
                fetchOneAvis();
            }
        };
    </script>
</body>

</html>