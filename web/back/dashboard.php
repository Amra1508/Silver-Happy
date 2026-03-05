<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

<body>
    <div class="flex min-h-screen bg-gray-50">

        <?php include("includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col">
            <?php include("includes/header.php"); ?>

            <main class="p-8">

                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-semibold text-[#1C5B8F]">Tableau de bord</h1>
                </div>

                <div id="api-message" class="hidden max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold"></div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    
                    <div class="bg-white border border-[#1C5B8F] rounded-[2rem] p-6 shadow-sm flex flex-col items-center justify-center text-center">
                        <h3 class="text-gray-500 text-lg mb-2">Seniors inscrits (30 derniers jours)</h3>
                        <p id="count-seniors" class="text-4xl font-bold text-[#1C5B8F]">...</p>
                    </div>

                    <div class="bg-white border border-[#1C5B8F] rounded-[2rem] p-6 shadow-sm flex flex-col items-center justify-center text-center">
                        <h3 class="text-gray-500 text-lg mb-2">Prestataires inscrits (30 derniers jours)</h3>
                        <p id="count-prestataires" class="text-4xl font-bold text-[#1C5B8F]">...</p>
                    </div>

                    <div class="bg-white border border-[#E1AB2B] rounded-[2rem] p-6 shadow-sm flex flex-col items-center justify-center text-center">
                        <h3 class="text-gray-500 text-lg mb-2">Nouveaux abonnements (30 derniers jours)</h3>
                        <p id="count-abonnements" class="text-4xl font-bold text-[#E1AB2B]">...</p>
                    </div>

                </div>

                <div class="bg-white border border-[#1C5B8F] rounded-[2.5rem] p-8 shadow-sm">
                    <h2 class="text-2xl font-semibold text-[#1C5B8F] mb-6">Revenus & Dépenses</h2>
                    <div class="w-full h-96 flex items-center justify-center bg-gray-50 rounded-2xl border border-dashed border-gray-300">
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        const API_BASE = "http://localhost:8082/dashboard";
        const messageBox = document.getElementById('api-message');

        function showAlert(msg, isSuccess) {
            messageBox.textContent = msg;
            messageBox.className = `max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold ${isSuccess ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'}`;
            messageBox.classList.remove('hidden');
            setTimeout(() => messageBox.classList.add('hidden'), 3500);
        }

        async function fetchDashboardData() {
            try {
                const resSeniors = await fetch(`${API_BASE}/seniors`);
                const dataSeniors = await resSeniors.json();
                document.getElementById('count-seniors').textContent = dataSeniors.count || 0;

                const resPrestataires = await fetch(`${API_BASE}/prestataires`);
                const dataPrestataires = await resPrestataires.json();
                document.getElementById('count-prestataires').textContent = dataPrestataires.count || 0;

                const resAbonnements = await fetch(`${API_BASE}/abonnement`);
                const dataAbonnements = await resAbonnements.json();
                document.getElementById('count-abonnements').textContent = dataAbonnements.count || 0;

            } catch (err) {
                showAlert("Erreur lors de la récupération des statistiques.", false);
                console.error(err);
            }
        }

        window.onload = () => {
            fetchDashboardData();
        };
    </script>
</body>

</html>