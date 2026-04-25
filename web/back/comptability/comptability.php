<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/config.php');
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Comptabilité - Silver Happy</title>
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
    </script>
    <script>
        window.API_BASE_URL = "<?php echo API_BASE_URL; ?>"
    </script>
</head>

<body class="bg-gray-50 text-gray-800">

    <div class="flex min-h-screen relative">

        <div class="w-64 bg-[#1C5B8F] text-white flex flex-col hidden md:flex shadow-xl z-20 shrink-0">
            <div class="p-6 text-center border-b border-blue-800">
                <h2 class="text-2xl font-bold text-white">SilverHappy</h2>
                <p class="text-xs text-blue-300 uppercase tracking-widest mt-1">Espace Comptable</p>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="/back/comptability/dashboard.php" class="block px-4 py-3 hover:bg-white/10 rounded-xl font-bold flex items-center gap-3">
                    <img src="/back/icons/chiffres.svg" alt="dashboard" class="w-5 h-5">
                    <span>Récapitulatif des chiffres</span>
                </a>

                <a href="/back/comptability/comptability.php" class="block px-4 py-3 hover:bg-white/10 rounded-xl font-bold flex items-center gap-3">
                    <img src="/back/icons/factures.svg" alt="dashboard" class="w-5 h-5">
                    <span>Gestion des Factures</span>
                </a>

                <button onclick="logoutAccountant()" class="w-full text-left block px-4 py-3 text-red-200 hover:text-white hover:bg-red-500/20 rounded-xl transition-colors font-bold flex items-center gap-3 mt-10">
                    <img src="/back/icons/deconnexion.svg" alt="dashboard" class="w-5 h-5">
                    <span>Déconnexion</span>
                </button>
            </nav>
        </div>

        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto relative">
            <main class="p-8">
                <div class="max-w-7xl mx-auto space-y-8">

                    <div class="flex justify-between items-end flex-wrap gap-4">
                        <div>
                            <h1 class="text-3xl font-semibold text-[#1C5B8F]">Suivi des Reversements</h1>
                            <p class="text-gray-500 mt-1">Gérez les factures et contactez les prestataires si besoin.</p>
                        </div>

                        <div class="flex gap-3">
                            <button id="btn-trigger-invoices" onclick="triggerInvoices()" class="flex items-center gap-2 bg-[#E1AB2B] hover:bg-yellow-500 text-white px-4 py-2 rounded-lg transition shadow-md font-bold text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Générer les virements du mois
                            </button>

                            <button onclick="fetchInvoices()" class="flex items-center gap-2 bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-50 transition shadow-sm font-semibold text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Rafraîchir
                            </button>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse whitespace-nowrap">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                        <th class="p-4 font-bold">Période</th>
                                        <th class="p-4 font-bold">Prestataire</th>
                                        <th class="p-4 font-bold">Montant Brut</th>
                                        <th class="p-4 font-bold">Net Versé</th>
                                        <th class="p-4 font-bold">Statut</th>
                                        <th class="p-4 font-bold text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="invoices-table-body">
                                    <tr>
                                        <td colspan="6" class="py-12 text-center text-gray-500 font-medium animate-pulse">Chargement des données...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script>
        const formatCurrency = (amount) => parseFloat(amount).toLocaleString('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        });

        async function triggerInvoices() {
            if (!confirm("Voulez-vous vraiment lancer la génération de factures et les virements Stripe pour le mois en cours ? Cette action est irréversible.")) {
                return;
            }

            const btn = document.getElementById('btn-trigger-invoices');
            const originalContent = btn.innerHTML;
            btn.innerHTML = `<svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Traitement...`;
            btn.disabled = true;
            btn.classList.add('opacity-70');

            try {
                const response = await fetch(`${window.API_BASE_URL}/admin/test-virements`, {
                    method: 'GET',
                    credentials: 'include'
                });

                if (!response.ok) throw new Error("Erreur côté serveur lors de la génération");

                const data = await response.json();
                alert(data.message || "Les virements ont été traités avec succès !");

                await fetchInvoices();
            } catch (error) {
                console.error(error);
                alert("Une erreur est survenue lors de la génération des virements. Consultez le terminal de votre serveur.");
            } finally {
                btn.innerHTML = originalContent;
                btn.disabled = false;
                btn.classList.remove('opacity-70');
            }
        }

        async function fetchInvoices() {
            const tbody = document.getElementById('invoices-table-body');
            tbody.innerHTML = `<tr><td colspan="6" class="py-12 text-center text-gray-500 font-medium animate-pulse">Actualisation des données...</td></tr>`;

            try {
                const response = await fetch(`${window.API_BASE_URL}/comptable/factures`, {
                    method: 'GET',
                    credentials: 'include'
                });

                if (!response.ok) throw new Error("Erreur réseau");

                const factures = await response.json();
                tbody.innerHTML = '';

                if (factures.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" class="py-12 text-center text-gray-500 font-medium">Aucune facture à afficher.</td></tr>`;
                    return;
                }

                factures.forEach(f => {
                    let statusHtml = `<span class="bg-gray-100 text-gray-700 py-1 px-2.5 rounded-md text-[10px] font-bold uppercase">${f.statut}</span>`;

                    if (f.statut.toLowerCase() === 'paye' || f.statut.toLowerCase() === 'payé') {
                        statusHtml = `<span class="bg-green-100 text-green-700 py-1 px-2.5 rounded-md text-[10px] font-bold uppercase">Payé</span>`;
                    } else if (f.statut.toLowerCase() === 'en_attente') {
                        statusHtml = `<span class="bg-orange-100 text-orange-700 py-1 px-2.5 rounded-md text-[10px] font-bold uppercase">En cours</span>`;
                    } else if (f.statut.toLowerCase() === 'attente_compte_stripe') {
                        statusHtml = `<span class="bg-red-50 text-red-600 border border-red-200 py-1 px-2.5 rounded-md text-[10px] font-bold uppercase">RIB Manquant</span>`;
                    }

                    const actionButtons = `
                        <div class="flex justify-end gap-2">
                            <a href="${window.API_BASE_URL}/prestataire/facture/${f.id_facture}/download" target="_blank" class="inline-flex items-center gap-1 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-[#1C5B8F] transition px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                PDF
                            </a>
                        </div>
                    `;

                    const row = document.createElement('tr');
                    row.className = "border-b border-gray-50 hover:bg-gray-50 transition";
                    row.innerHTML = `
                        <td class="p-4 text-sm font-bold text-[#1C5B8F]">${f.mois_annee}</td>
                        <td class="p-4">
                            <div class="text-sm font-bold text-gray-800">${f.prestataire}</div>
                            <div class="text-[10px] text-gray-400 font-mono">SIRET: ${f.siret}</div>
                        </td>
                        <td class="p-4 text-sm font-semibold text-gray-600">${formatCurrency(f.montant)}</td>
                        <td class="p-4 text-sm font-bold text-green-600">${formatCurrency(f.montant_net)}</td>
                        <td class="p-4">${statusHtml}</td>
                        <td class="p-4 text-right">${actionButtons}</td>
                    `;
                    tbody.appendChild(row);
                });

            } catch (error) {
                console.error(error);
                tbody.innerHTML = `<tr><td colspan="6" class="py-12 text-center text-red-500 font-medium">Erreur lors de la connexion.</td></tr>`;
            }
        }

        async function logoutAccountant() {
            try {
                await fetch(`${window.API_BASE_URL}/auth/logout`, {
                    method: 'GET',
                    credentials: 'include'
                });
                window.location.href = '/front/index.php';
            } catch (error) {
                console.error(error);
                window.location.href = '/front/index.php';
            }
        }

        document.addEventListener('DOMContentLoaded', fetchInvoices);
    </script>
</body>

</html>