<?php 
if (!isset($_COOKIE['session_token'])) {
    header("Location: /front/account/signin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Factures</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>

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

    <main class="flex-grow max-w-5xl mx-auto w-full px-6 py-12">
        
        <div class="p-3 flex justify-between items-center mb-6">
            <a href="/front/account/profile.php">
                <button class="flex items-center rounded-full px-6 py-2 bg-[#1C5B8F] text-white font-bold hover:bg-[#154670] transition">
                    <img src="/front/icons/fleche_gauche.svg" alt="fleche" class="w-7 h-7 mr-2"> Revenir à mon profil
                </button>
            </a>
        </div>

        <h1 class="text-4xl font-bold text-[#1C5B8F] mb-8">Mes Factures</h1>

        <div id="no-sub-container" class="hidden flex flex-col items-center justify-center py-20 rounded-[2.5rem] shadow-xl shadow-blue-900/10">
            <p class="text-center font-semibold text-[#1C5B8F] text-2xl mb-8">
                Vous devez être abonné(e) pour consulter vos factures.</p>
            <a class="rounded-full px-4 py-2 bg-[#1C5B8F] text-white hover:bg-[#154670] transition" href="/front/services/subscription.php">
                Je m'abonne
            </a>
        </div>

        <div id="invoices-container" class="hidden bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-[#1C5B8F] text-white">
                    <tr>
                        <th class="p-4 font-semibold">Date</th>
                        <th class="p-4 font-semibold">Description</th>
                        <th class="p-4 font-semibold text-center">Montant</th>
                        <th class="p-4 font-semibold text-center">Facture</th>
                    </tr>
                </thead>
                <tbody id="invoices-tbody" class="divide-y divide-gray-100">
                    <tr><td colspan="4" class="p-8 text-center text-gray-400 animate-pulse">Chargement de vos factures...</td></tr>
                </tbody>
            </table>
        </div>

    </main>

    <?php include("../includes/footer.php") ?>

    <script>
        window.addEventListener('auth_ready', async () => {
            const noSubContainer = document.getElementById('no-sub-container');
            const invoicesContainer = document.getElementById('invoices-container');
            const tbody = document.getElementById('invoices-tbody');

            const user = window.userData;
            const hasSubscription = user && user.id_abonnement && user.id_abonnement > 0;

            if (!hasSubscription) {
                noSubContainer.classList.remove('hidden');
                invoicesContainer.classList.add('hidden'); 
                return;
            }

            noSubContainer.classList.add('hidden');
            invoicesContainer.classList.remove('hidden');

            try {
                const res = await fetch(`${window.API_BASE_URL}/factures/user/${window.currentUserId}`, { 
                    credentials: 'include' 
                });
                
                if (!res.ok) throw new Error("Erreur de récupération");
                
                const factures = await res.json();

                tbody.innerHTML = '';

                if (!factures || factures.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="p-8 text-center text-gray-500 italic">Vous n\'avez aucune facture pour le moment.</td></tr>';
                    return;
                }

                factures.forEach(f => {
                    const dateObj = new Date(f.date);
                    const displayDate = isNaN(dateObj) ? "-" : dateObj.toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' });
                    
                    const urlBtn = f.url 
                        ? `<a href="${f.url}" target="_blank" class="bg-[#E1AB2B] text-white px-5 py-2 rounded-full text-sm font-bold hover:bg-yellow-600 transition shadow-sm inline-block">Consulter</a>`
                        : `<span class="text-gray-400 text-sm italic">Non disponible</span>`;

                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="p-4 text-gray-600 font-medium">${displayDate}</td>
                            <td class="p-4 font-bold text-[#1C5B8F]">${f.description || 'Abonnement'}</td>
                            <td class="p-4 text-center font-bold text-gray-700">${f.montant} €</td>
                            <td class="p-4 text-center">${urlBtn}</td>
                        </tr>
                    `;
                });

            } catch (err) {
                console.error(err);
                tbody.innerHTML = '<tr><td colspan="4" class="p-8 text-center text-red-500 font-semibold">Erreur lors de la récupération de vos factures. Veuillez réessayer plus tard.</td></tr>';
            }
        });
    </script>
</body>
</html>