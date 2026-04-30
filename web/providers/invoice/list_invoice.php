<?php
session_start();
$is_logged_in = isset($_SESSION['provider_id']);
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
                        sans: ['Alata', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 text-gray-800">

    <div class="flex min-h-screen relative">

        <?php include("../includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto relative">
            <main class="p-8">

                <?php if ($is_logged_in): ?>
                    <div id="main-content" class="space-y-12 max-w-6xl mx-auto">

                        <div>
                            <h1 class="text-3xl font-semibold text-[#1C5B8F]">Mes Factures & Bénéfices</h1>
                            <p class="text-gray-500 mt-1">Consultez l'historique de vos paiements et le récapitulatif de vos revenus.</p>
                        </div>

                        <div id="alert-box" class="hidden p-4 rounded-xl font-semibold text-sm"></div>

                        <div class="space-y-4">
                            <h2 class="text-xl font-semibold text-gray-700">Mes Factures (Abonnements)</h2>
                            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="text-gray-400 text-sm border-b border-gray-100">
                                                <th class="pb-4 font-medium px-4">Date</th>
                                                <th class="pb-4 font-medium px-4">Description</th>
                                                <th class="pb-4 font-medium px-4">Montant</th>
                                                <th class="pb-4 font-medium px-4">Statut</th>
                                                <th class="pb-4 font-medium text-right px-4">Contrat</th>
                                            </tr>
                                        </thead>
                                        <tbody id="invoices-table-body">
                                            <tr>
                                                <td colspan="5" class="py-8 text-center text-gray-500 text-sm">
                                                    <span class="animate-pulse">Chargement de vos factures...</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h2 class="text-xl font-semibold text-gray-700">Mes Relevés de Bénéfices (Prestations)</h2>
                            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="text-gray-400 text-sm border-b border-gray-100">
                                                <th class="pb-4 font-medium px-4">Période (Mois)</th>
                                                <th class="pb-4 font-medium px-4">CA Brut Généré</th>
                                                <th class="pb-4 font-medium px-4">Frais Plateforme</th>
                                                <th class="pb-4 font-medium px-4">Montant Net</th>
                                                <th class="pb-4 font-medium px-4">Date d'émission</th>
                                                <th class="pb-4 font-medium px-4">Statut</th>
                                                <th class="pb-4 font-medium text-right px-4">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="benefits-table-body">
                                            <tr>
                                                <td colspan="7" class="py-8 text-center text-gray-500 text-sm">
                                                    <span class="animate-pulse">Chargement de vos bénéfices...</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center py-20 rounded-[2.5rem] shadow-xl shadow-blue-900/10 m-8 bg-white border border-gray-100">
                        <p class="text-center font-semibold text-[#1C5B8F] text-2xl mb-8">
                            Vous devez être connecté(e) pour accéder à vos factures.
                        </p>
                        <a class="rounded-full px-8 py-3 bg-[#1C5B8F] text-white font-bold hover:bg-[#154670] transition-colors shadow-md" href="/providers/account/signin.php">
                            Je me connecte
                        </a>
                    </div>
                <?php endif; ?>

            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const invoicesTbody = document.getElementById('invoices-table-body');
            const benefitsTbody = document.getElementById('benefits-table-body');
            const alertBox = document.getElementById('alert-box');

            function showAlert(msg, isSuccess = false) {
                alertBox.textContent = msg;
                alertBox.className = `p-4 mb-6 rounded-xl font-bold block ${isSuccess ? 'text-green-700 bg-green-100 border border-green-400' : 'text-red-700 bg-red-100 border border-red-400'}`;
                alertBox.classList.remove('hidden');
            }

            function formatCurrency(amount) {
                return parseFloat(amount).toLocaleString('fr-FR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' €';
            }

            try {
                const meRes = await fetch(`${window.API_BASE_URL}/auth/me-provider`, {
                    method: 'GET',
                    credentials: 'include'
                });

                if (!meRes.ok) {
                    window.location.href = "/providers/account/signin.php";
                    return;
                }

                const data = await meRes.json();
                const providerId = data.id_prestataire || data.id || data.ID;

                const [invRes, benRes] = await Promise.all([
                    fetch(`${window.API_BASE_URL}/prestataire/${providerId}/invoices`, {
                        method: 'GET',
                        credentials: 'include'
                    }),
                    fetch(`${window.API_BASE_URL}/prestataire/${providerId}/factures-mensuelles`, {
                        method: 'GET',
                        credentials: 'include'
                    })
                ]);

                if (invRes.ok) {
                    const invoices = await invRes.json();
                    invoicesTbody.innerHTML = '';

                    if (!invoices || invoices.length === 0) {
                        invoicesTbody.innerHTML = `<tr><td colspan="5" class="py-10 text-center text-gray-500 font-medium">Aucune facture disponible pour le moment.</td></tr>`;
                    } else {
                        invoices.forEach(inv => {
                            const date = new Date(inv.date_paiement).toLocaleDateString('fr-FR', {
                                day: '2-digit',
                                month: 'long',
                                year: 'numeric'
                            });
                            const isPaid = inv.statut.toLowerCase() === 'valide' || inv.statut.toLowerCase() === 'payé';

                            const statusBadge = isPaid ?
                                `<span class="bg-green-100 text-green-700 py-1 px-3 rounded-full text-xs font-bold">Payé</span>` :
                                `<span class="bg-red-100 text-red-700 py-1 px-3 rounded-full text-xs font-bold">Échoué</span>`;

                            const linkBtn = inv.url_contrat ?
                                `<a href="${inv.url_contrat}" target="_blank" class="text-[#1C5B8F] hover:text-blue-800 font-bold text-sm underline flex items-center justify-end gap-1">
                                    Télécharger
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                   </a>` :
                                `<span class="text-gray-400 text-sm flex justify-end">Non disponible</span>`;

                            const tr = document.createElement('tr');
                            tr.className = "border-b border-gray-50 hover:bg-gray-50 transition-colors";
                            tr.innerHTML = `
                                <td class="py-4 px-4 text-sm text-gray-600">${date}</td>
                                <td class="py-4 px-4 text-sm font-semibold text-[#1C5B8F]">${inv.description || 'Paiement Silver Happy'}</td>
                                <td class="py-4 px-4 text-sm font-bold text-gray-800">${formatCurrency(inv.prix)}</td>
                                <td class="py-4 px-4">${statusBadge}</td>
                                <td class="py-4 px-4 text-right">${linkBtn}</td>
                            `;
                            invoicesTbody.appendChild(tr);
                        });
                    }
                } else {
                    invoicesTbody.innerHTML = `<tr><td colspan="5" class="py-8 text-center text-red-500 font-medium">Erreur lors du chargement des factures.</td></tr>`;
                }

                if (benRes.ok) {
                    const benefits = await benRes.json();
                    benefitsTbody.innerHTML = '';

                    if (!benefits || benefits.length === 0) {
                        benefitsTbody.innerHTML = `<tr><td colspan="7" class="py-10 text-center text-gray-500 font-medium">Aucun relevé de bénéfice n'a encore été généré.</td></tr>`;
                    } else {
                        benefits.forEach(inv => {
                            const date = new Date(inv.date).toLocaleDateString('fr-FR', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric'
                            });

                            let statusBadge = '';
                            const statusLower = (inv.statut || '').toLowerCase();
                            if (statusLower === 'payé' || statusLower === 'paye') {
                                statusBadge = `<span class="bg-green-100 text-green-700 py-1 px-3 rounded-full text-xs font-bold">Payé</span>`;
                            } else if (statusLower === 'en_attente' || statusLower === 'attente_compte_stripe') {
                                statusBadge = `<span class="bg-orange-100 text-orange-700 py-1 px-3 rounded-full text-xs font-bold">En attente</span>`;
                            } else {
                                statusBadge = `<span class="bg-gray-100 text-gray-700 py-1 px-3 rounded-full text-xs font-bold capitalize">${inv.statut.replace('_', ' ')}</span>`;
                            }

                            const downloadBtn = `
                                <a href="${window.API_BASE_URL}/prestataire/facture/${inv.id_facture}/download" target="_blank" class="text-[#E1AB2B] hover:text-[#c99723] font-bold text-sm flex items-center justify-end gap-1 bg-yellow-50 px-3 py-1.5 rounded-lg transition-colors border border-yellow-200">
                                    Télécharger
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </a>
                            `;

                            const tr = document.createElement('tr');
                            tr.className = "border-b border-gray-50 hover:bg-gray-50 transition-colors";
                            tr.innerHTML = `
                                <td class="py-4 px-4 text-sm font-semibold text-[#1C5B8F]">${inv.mois_annee}</td>
                                <td class="py-4 px-4 text-sm text-gray-600">${formatCurrency(inv.montant_brut)}</td>
                                <td class="py-4 px-4 text-sm text-red-500 font-medium">- ${formatCurrency(inv.frais_plateforme)}</td>
                                <td class="py-4 px-4 text-sm font-bold text-green-600">${formatCurrency(inv.montant_net)}</td>
                                <td class="py-4 px-4 text-sm text-gray-500">${date}</td>
                                <td class="py-4 px-4">${statusBadge}</td>
                                <td class="py-4 px-4 text-right">${downloadBtn}</td>
                            `;
                            benefitsTbody.appendChild(tr);
                        });
                    }
                } else {
                    benefitsTbody.innerHTML = `<tr><td colspan="7" class="py-8 text-center text-red-500 font-medium">Erreur lors du chargement des bénéfices.</td></tr>`;
                }

            } catch (err) {
                console.error(err);
                showAlert("Impossible de se connecter au serveur.");
                invoicesTbody.innerHTML = `<tr><td colspan="5" class="py-8 text-center text-red-500 font-medium">Serveur injoignable.</td></tr>`;
                benefitsTbody.innerHTML = `<tr><td colspan="7" class="py-8 text-center text-red-500 font-medium">Serveur injoignable.</td></tr>`;
            }
        });
    </script>
</body>

</html>