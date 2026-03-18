<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail du Conseil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
        body { font-family: 'Alata', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

    <?php include("../includes/header.php") ?>

    <main class="flex-1 container mx-auto px-6 py-12 max-w-4xl">
        <a href="javascript:history.back()" class="text-[#1C5B8F] font-bold mb-6 inline-block hover:underline">← Retour aux conseils</a>
        
        <div id="detail-container">
            <div class="animate-pulse space-y-4">
                <div class="h-10 bg-gray-200 rounded w-3/4"></div>
                <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                <div class="h-64 bg-gray-200 rounded w-full"></div>
            </div>
        </div>
    </main>

    <?php include("../includes/footer.php") ?>

    <script>
        const API_BASE = "http://localhost:8082";
        
        const urlParams = new URLSearchParams(window.location.search);
        const conseilId = urlParams.get('id');

        async function fetchOneConseil() {
            try {
                const response = await fetch(`${API_BASE}/conseil/read-one/${conseilId}`);
                const result = await response.json();
                
                console.log("Données reçues de l'API :", result);

                const c = result.data || result; 

                if (!c || Object.keys(c).length === 0) {
                    throw new Error("Données vides");
                }

                renderDetail(c);
            } catch (err) {
                console.error("Erreur détaillée :", err);
                document.getElementById('detail-container').innerHTML = `
                    <p class="text-red-500">Erreur : Impossible de charger les détails (ID: ${conseilId}).</p>
                `;
            }
        }

        function renderDetail(c) {
            const container = document.getElementById('detail-container');
            
            const rawDate = c.date_publication || c.Date || c.date;
            let dateStr = "Date inconnue";
            
            if (rawDate) {
                const safeDateStr = String(rawDate).replace(' ', 'T');
                const d = new Date(safeDateStr);
                if (!isNaN(d)) {
                    dateStr = d.toLocaleDateString('fr-FR', {
                        day: 'numeric', month: 'long', year: 'numeric'
                    });
                }
            }

            const titre = c.titre || "Titre absent";
            const description = c.description || "";
            const categorie = c.categorie || "Général";

            container.innerHTML = `
                <article class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="h-64 bg-[#1C5B8F] flex items-center justify-center">
                        <span class="text-white text-6xl">💡</span>
                    </div>
                    
                    <div class="p-8 md:p-12">
                        <div class="flex items-center gap-4 mb-6">
                            <span class="bg-[#E1AB2B] text-white px-4 py-1 rounded-full text-sm font-bold uppercase">
                                ${categorie}
                            </span>
                            <span class="text-gray-400 font-medium">Publié le ${dateStr}</span>
                        </div>

                        <h1 class="text-4xl md:text-5xl font-bold text-[#1C5B8F] mb-8 leading-tight">
                            ${titre}
                        </h1>

                        <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                            <p class="text-xl mb-6 font-semibold text-gray-800">${description}</p>
                        </div>
                    </div>
                </article>
            `;
        }

        window.onload = fetchOneConseil;
    </script>
</body>
</html>