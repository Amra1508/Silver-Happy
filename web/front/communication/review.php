<?php
$is_logged_in = isset($_COOKIE['session_token']);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis</title>
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

<body>
    <?php include("../includes/header.php") ?>
    <main class="p-8 bg-white">
        <div class="max-w-6xl mx-auto">
            <?php if ($is_logged_in): ?>
                <div class="max-w-6xl mx-auto px-4">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-10 pb-6 border-b border-gray-200">
                        <h1 class="big-text text-center">Liste des avis</h1>
                        <button onclick="toggleModal(true)" class="rounded-full px-6 py-3 bg-[#E1AB2B] font-semibold text-xl text-white hover:bg-[#1C5B8F] transition-colors">
                            Laisser mon avis
                        </button>
                    </div>
                    <div id="list-review" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8 items-stretch">

                    </div>

                    <div id="pagination-controls"></div>
                </div>
                <div id="modal-review" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-8 relative">
                        <button onclick="toggleModal(false)" class="absolute top-4 right-6 text-2xl text-gray-400 hover:text-red-500">&times;</button>

                        <h2 class="text-2xl font-bold text-[#1C5B8F] mb-6">Votre avis nous intéresse</h2>

                        <form id="form-review" onsubmit="submitAvis(event)" class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Catégorie</label>
                                <select id="review-categorie" required class="w-full border-2 border-gray-100 rounded-xl px-4 py-2 focus:border-[#1C5B8F] outline-none">
                                    <option value="Service">Service</option>
                                    <option value="Evènement">Evènement</option>
                                    <option value="Prestataire">Prestataire</option>
                                    <option value="Autre">Autre</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Titre</label>
                                <input type="text" id="review-titre" required placeholder="En quelques mots..."
                                    class="w-full border-2 border-gray-100 rounded-xl px-4 py-2 focus:border-[#1C5B8F] outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Note (0 à 5)</label>
                                <input type="number" id="review-note" min="0" max="5" value="5" required
                                    class="w-full border-2 border-gray-100 rounded-xl px-4 py-2 focus:border-[#1C5B8F] outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Description</label>
                                <textarea id="review-desc" rows="4" required placeholder="Racontez votre expérience..."
                                    class="w-full border-2 border-gray-100 rounded-xl px-4 py-2 focus:border-[#1C5B8F] outline-none"></textarea>
                            </div>

                            <button type="submit" class="w-full bg-[#1C5B8F] text-white font-bold py-3 rounded-xl hover:bg-[#E1AB2B] transition-all">
                                Publier l'avis
                            </button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="flex flex-col items-center justify-center py-20 rounded-[2.5rem] shadow-xl shadow-blue-900/10">
                    <p class="text-center font-semibold text-[#1C5B8F] text-2xl mb-8">
                        Vous devez être connecté(e) pour consulter les avis des autres adhérents.</p>
                    <a class="rounded-full px-4 py-2 button-blue" href="/front/account/signin.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">
                        Je me connecte
                    </a>
                </div>
            <?php endif; ?>
        </div>

    </main>
    <?php include("../includes/footer.php") ?>

    <script>
        const API_BASE = "http://localhost:8082";
        let currentPage = 1;
        const limit = 6;

        function toggleModal(show) {
            const modal = document.getElementById('modal-review');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
                document.getElementById('form-review').reset();
            }
        }

        async function submitAvis(event) {
            event.preventDefault();

            const data = {
                titre: document.getElementById('review-titre').value,
                description: document.getElementById('review-desc').value,
                note: parseInt(document.getElementById('review-note').value),
                categorie: document.getElementById('review-categorie').value
            };

            try {
                const response = await fetch(`${API_BASE}/avis/create`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    alert("Avis publié avec succès !");
                    toggleModal(false);
                    fetchAvis(1);
                } else {
                    const error = await response.text();
                    alert("Erreur : " + error);
                }
            } catch (err) {
                console.error(err);
                alert("Impossible de contacter le serveur.");
            }
        }

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

        async function fetchAvis(page = 1) {
            try {
                currentPage = page;
                const response = await fetch(`${API_BASE}/avis/read?page=${currentPage}&limit=${limit}`);

                if (!response.ok) throw new Error("Erreur de récupération des données");

                const result = await response.json();
                const avis = result.data || [];
                const container = document.getElementById('list-review');
                container.innerHTML = '';

                if (avis.length === 0) {
                    container.innerHTML = '<p class="text-xl text-gray-500 py-10 italic">Aucun avis disponible pour le moment.</p>';
                    return;
                }

                avis.forEach(c => {
                    const id = c.id_avis || c.ID || c.id;
                    const titre = c.titre || c.Titre || 'Sans titre';
                    const description = c.description || c.Description || '';
                    const note = c.note || 0;
                    const categorie = c.categorie || c.Categorie || 'Général';

                    const rawDate = c.Date || c.date;
                    const datePub = formatDisplayDate(rawDate);

                    container.innerHTML += `
                        <div class="bg-white border-l-8 border-[#1C5B8F] rounded-xl shadow-md p-6 flex flex-col hover:shadow-xl transition-all h-full">
                            <div class="flex justify-between items-start mb-3">
                                <span class="text-[#E1AB2B] border border-[#E1AB2B] text-xs px-3 py-1 rounded-full font-bold uppercase tracking-wider">
                                    ${"★".repeat(note)}${"☆".repeat(5 - note)}
                                </span>
                                <span class="text-sm text-gray-400 font-semibold">${datePub}</span>
                            </div>
                            
                            <span class="text-[#1C5B8F] text-xs font-bold uppercase tracking-widest mb-1 italic">
                                ${categorie}
                            </span>

                            <h3 class="text-2xl text-[#1C5B8F] font-bold mb-4 leading-snug">${titre}</h3>
                            <p class="text-gray-600 leading-relaxed flex-grow text-lg mb-4 break-words line-clamp-1">${description}</p>
                            
                            <a href="detail_review.php?id=${id}" class="self-start text-[#1C5B8F] font-bold hover:text-[#E1AB2B] transition-colors flex items-center gap-2 mt-auto pt-4">
                                    Lire la suite <span class="text-xl">→</span>
                            </a>
                        </div>
                    `;
                });

                renderPagination(result.totalPages);
            } catch (err) {
                console.error(err);
                document.getElementById('list-review').innerHTML = `
                    <div class="w-full text-center py-10">
                        <p class="text-xl text-red-500 font-bold">Impossible de charger les avis.</p>
                        <p class="text-gray-500 mt-2">Veuillez vérifier votre connexion au serveur.</p>
                    </div>`;
            }
        }

        function renderPagination(totalPages) {
            const paginationContainer = document.getElementById('pagination-controls');
            paginationContainer.innerHTML = '';

            if (totalPages <= 1) return;

            const prevDisabled = currentPage === 1 ? 'disabled opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 text-[#1C5B8F]';
            paginationContainer.innerHTML += `<button onclick="fetchAvis(${currentPage - 1})" class="px-4 py-2 border-2 border-[#1C5B8F] text-[#1C5B8F] rounded-full font-bold transition-colors ${prevDisabled}" ${currentPage === 1 ? 'disabled' : ''}>← Précédent</button>`;

            paginationContainer.innerHTML += `<span class="text-gray-500 font-medium px-4">Page <strong class="text-[#1C5B8F]">${currentPage}</strong> sur ${totalPages}</span>`;

            const nextDisabled = currentPage === totalPages ? 'disabled opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 text-[#1C5B8F]';
            paginationContainer.innerHTML += `<button onclick="fetchAvis(${currentPage + 1})" class="px-4 py-2 border-2 border-[#1C5B8F] text-[#1C5B8F] rounded-full font-bold transition-colors ${nextDisabled}" ${currentPage === totalPages ? 'disabled' : ''}>Suivant →</button>`;
        }

        window.onload = () => {
            fetchAvis(1);
        };
    </script>
</body>

</html>