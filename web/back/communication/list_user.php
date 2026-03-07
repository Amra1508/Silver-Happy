<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Seniors - Silver Happy</title>
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

        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            if (modal) {
                modal.classList.toggle('hidden');
                modal.classList.toggle('flex');
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

                <div id="api-message" class="hidden max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold"></div>

                <div class="border border-[#1C5B8F] rounded-[2.5rem] overflow-hidden bg-white shadow-sm">
                    <table class="w-full text-left">
                        <thead class="bg-[#1C5B8F] text-white">
                            <tr>
                                <th class="p-4 font-semibold">ID</th>
                                <th class="p-4 font-semibold">Prénom</th>
                                <th class="p-4 font-semibold">Nom</th>
                                <th class="p-4 font-semibold">Adresse mail</th>
                                <th class="p-4 font-semibold">Contacter</th>
                            </tr>
                        </thead>
                        <tbody id="list-user-body" class="divide-y divide-gray-100"></tbody>
                    </table>
                </div>

            </main>
        </div>
    </div>

    <script>
        const API_BASE = "http://localhost:8082/seniors";
        const messageBox = document.getElementById('api-message');

        function showAlert(msg, isSuccess) {
            messageBox.textContent = msg;
            messageBox.className = `max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold ${isSuccess ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'}`;
            messageBox.classList.remove('hidden');
            setTimeout(() => messageBox.classList.add('hidden'), 3500);
        }

        async function fetchSeniors() {
            try {
                const response = await fetch(`${API_BASE}/read`);
                const seniors = await response.json();
                const tbody = document.getElementById('list-user-body');
                tbody.innerHTML = '';

                seniors.forEach(s => {

                    const s_nom = s.nom ? s.nom.replace(/'/g, "\\'") : '';
                    const s_prenom = s.prenom ? s.prenom.replace(/'/g, "\\'") : '';

                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 border-b">
                            <td class="p-4 text-gray-400">#${s.id}</td>
                            <td class="p-4">
                                ${s.prenom}<br>
                            </td>
                            <td class="p-4">
                                ${s.nom}<br>
                            </td>
                            <td class="p-4">
                                ${s.email}<br>
                            </td>
                            <td class="p-4">
                                <a href="/back/communication/messaging.php/${s.id}">
                                        <button class="bg-gray-100 hover:bg-gray-200 text-[#1C5B8F] px-4 py-2 rounded-full transition font-semibold text-sm">Voir la discussion</button>
                                </a>
                            </td>
                        </tr>
                    `;
                });
            } catch (err) {
                showAlert("Erreur réseau", false);
            }
        }

        window.onload = fetchSeniors;
    </script>
</body>

</html>