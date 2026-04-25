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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                            <h1 class="text-3xl font-semibold text-[#1C5B8F] mb-4">Évolution de la Trésorerie (30 derniers jours)</h1>
                            <div>
                                <p class="text-gray-500 mt-1">Total sur 30 jours</p>
                                <p id="total-revenus" class="text-2xl font-bold text-[#1C5B8F]">...</p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-8">
                        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
                            <h2 class="text-xl font-semibold text-[#1C5B8F] mb-4">Revenus globaux</h2>
                            <div class="h-80">
                                <canvas id="revenusChart"></canvas>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
                            <h2 class="text-xl font-semibold text-[#1C5B8F] mb-4">Détail par catégorie</h2>
                            <div class="h-80">
                                <canvas id="detailsChart"></canvas>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script>
        const API_BASE = `${window.API_BASE_URL}/dashboard`;

        async function fetchRevenusData() {
            try {
                const res = await fetch(`${API_BASE}/revenus`);
                const data = await res.json();

                if (!data || data.length === 0) return;

                const labels = data.map(item => {
                    const dateObj = new Date(item.date);
                    return dateObj.toLocaleDateString('fr-FR', {
                        day: '2-digit',
                        month: 'short'
                    });
                });

                const totals = data.map(item => item.total);

                const sommeTotale = totals.reduce((acc, montant) => acc + montant, 0);
                const totalElement = document.getElementById('total-revenus');
                totalElement.textContent = sommeTotale.toFixed(2) + ' €';
                if (sommeTotale < 0) {
                    totalElement.classList.replace('text-[#1C5B8F]', 'text-red-500');
                }

                const ctx = document.getElementById('revenusChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Trésorerie (€)',
                            data: totals,
                            borderColor: '#1C5B8F',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: {
                                target: 'origin',
                                above: 'rgba(28, 91, 143, 0.1)',
                                below: 'rgba(239, 68, 68, 0.2)'
                            }
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let valeur = context.parsed.y;
                                        let valeurAffichage = valeur.toFixed(2);
                                        let type = valeur < 0 ? 'Dépense' : 'Revenu';
                                        return `${type} : ${valeurAffichage} €`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    maxRotation: 0,
                                    callback: function(value, index) {
                                        const dateActuelle = new Date(data[index].date);
                                        const moisActuel = dateActuelle.toLocaleDateString('fr-FR', {
                                            month: 'long'
                                        });

                                        if (index === 0) {
                                            return moisActuel.charAt(0).toUpperCase() + moisActuel.slice(1);
                                        }

                                        const datePrecedente = new Date(data[index - 1].date);
                                        const moisPrecedent = datePrecedente.toLocaleDateString('fr-FR', {
                                            month: 'long'
                                        });

                                        if (moisActuel !== moisPrecedent) {
                                            return moisActuel.charAt(0).toUpperCase() + moisActuel.slice(1);
                                        }

                                        return null;
                                    }
                                }
                            },
                            y: {
                                ticks: {
                                    callback: function(value) {
                                        return value + ' €';
                                    }
                                }
                            }
                        }
                    }
                });

            } catch (err) {
                console.error("Erreur lors de la récupération des données de revenus", err);
            }
        }

        async function fetchDetailsData() {
            try {
                const res = await fetch(`${API_BASE}/revenus/details`);
                const data = await res.json();

                if (!data || data.length === 0) {
                    console.log("Aucune donnée pour le détail");
                    return;
                }

                const labels = data.map(item => {
                    const d = new Date(item.date);
                    return d.toLocaleDateString('fr-FR', {
                        day: '2-digit',
                        month: 'short'
                    });
                });

                const ctx = document.getElementById('detailsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Commandes',
                                data: data.map(item => Number(item.commandes).toFixed(2)),
                                backgroundColor: '#1C5B8F',
                            },
                            {
                                label: 'Abonnements',
                                data: data.map(item => Number(item.abonnements).toFixed(2)),
                                backgroundColor: '#E1AB2B',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                stacked: true
                            },
                            y: {
                                stacked: true,
                                ticks: {
                                    callback: value => value + ' €'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            } catch (err) {
                console.error("Erreur détails:", err);
            }
        }

        window.onload = () => {
            fetchRevenusData();
            fetchDetailsData();
        };
    </script>
</body>

</html>