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

<body class="bg-gray-50 flex flex-col min-h-screen">

    <?php include("../includes/header.php") ?>

    <main class="flex-1 relative">
        <div class="w-full px-6 md:px-16 mt-8 mb-8 text-center">
            <h2 class="text-4xl font-bold mb-4 text-[#1C5B8F]">Avis de la communauté</h2>
            <p class="text-lg max-w-4xl mx-auto text-gray-600 mb-8">
                Découvrez les retours d'expérience de nos membres ou partagez le vôtre pour aider la communauté.
            </p>

            <?php if ($is_logged_in): ?>
                <button onclick="toggleModal(true)" class="rounded-full px-8 py-3 bg-[#E1AB2B] font-bold text-white hover:bg-[#1C5B8F] transition-all shadow-lg transform hover:scale-105">
                    Laisser mon avis
                </button>
            <?php endif; ?>
        </div>

        <?php if ($is_logged_in): ?>
            <div id="list-review" class="flex flex-wrap gap-10 px-6 md:px-16 py-10 justify-center">
            </div>

            <div id="pagination-controls" class="flex justify-center items-center gap-4 pb-16"></div>

            <div id="modal-review" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-[2rem] p-8 max-w-lg w-full relative shadow-2xl">
                    <button onclick="toggleModal(false)" class="absolute top-4 right-6 text-2xl text-gray-400 hover:text-red-500">&times;</button>
                    <h2 class="text-2xl font-bold text-[#1C5B8F] mb-6">Votre avis nous intéresse</h2>
                    <form id="form-review" onsubmit="submitAvis(event)" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Catégorie</label>
                            <select id="review-categorie" onchange="handleCategoryChange()" required class="w-full border-2 border-gray-100 rounded-xl px-4 py-2 outline-none focus:border-[#1C5B8F]">
                                <option value="Service">Service</option>
                                <option value="Evènement">Evènement</option>
                                <option value="Prestataire">Prestataire</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <div id="prestataire-selection" class="hidden mt-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nom du Prestataire</label>
                            <select id="review-id-prestataire" class="w-full border-2 border-gray-100 rounded-xl px-4 py-2 focus:border-[#1C5B8F] outline-none">
                                <option value="">Sélectionner un prestataire</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Titre</label>
                            <input type="text" id="review-titre" required placeholder="ex. Sortie au Louvre" class="w-full border-2 border-gray-100 rounded-xl px-4 py-2 focus:border-[#1C5B8F] outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Note (0 à 5)</label>
                            <input type="number" id="review-note" min="0" max="5" value="5" required class="w-full border-2 border-gray-100 rounded-xl px-4 py-2 focus:border-[#1C5B8F] outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                            <textarea id="review-desc" rows="4" required class="w-full border-2 border-gray-100 rounded-xl px-4 py-2 focus:border-[#1C5B8F] outline-none"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-[#1C5B8F] text-white font-semibold py-3 rounded-xl hover:bg-[#E1AB2B] transition-all">Publier l'avis</button>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <div class="flex flex-col items-center justify-center py-20 mx-16 bg-white rounded-[2.5rem] shadow-xl shadow-blue-900/10">
                <p class="text-center font-semibold text-[#1C5B8F] text-2xl mb-8">
                    Vous devez être connecté(e) pour consulter les avis.
                </p>
                <a class="rounded-full px-8 py-3 bg-[#1C5B8F] text-white font-bold hover:bg-[#E1AB2B] transition-all" href="/front/account/signin.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">
                    Je me connecte
                </a>
            </div>
        <?php endif; ?>
    </main>

    <?php include("../includes/footer.php") ?>

    <script>
        const API_BASE = "http://localhost:8082";
        let currentPage = 1;
        const limit = 6;

        function toggleModal(show) {
            const modal = document.getElementById('modal-review');
            if (show) modal.classList.remove('hidden');
            else {
                modal.classList.add('hidden');
                document.getElementById('form-review').reset();
            }
        }

        async function loadPrestataires() {
            try {
                const response = await fetch(`${API_BASE}/prestataires/read?limit=500&status=valide`);
                const result = await response.json();
                const select = document.getElementById('review-id-prestataire');
                select.innerHTML = '<option value="">Sélectionner un prestataire</option>';
                (result.data || []).forEach(p => {
                    const id = p.id_prestataire || p.ID || p.id;
                    const nom = p.nom || p.Nom || '';
                    const prenom = p.prenom || p.Prenom || '';
                    if (id) {
                        const option = document.createElement('option');
                        option.value = id;
                        option.textContent = `${prenom} ${nom}`.trim();
                        select.appendChild(option);
                    }
                });
            } catch (err) {
                console.error(err);
            }
        }

        function handleCategoryChange() {
            const categorie = document.getElementById('review-categorie').value;
            const div = document.getElementById('prestataire-selection');
            if (categorie === "Prestataire") {
                div.classList.remove('hidden');
                loadPrestataires();
            } else {
                div.classList.add('hidden');
            }
        }

        async function submitAvis(event) {
            event.preventDefault();
            const idPresta = document.getElementById('review-id-prestataire').value;
            const categorie = document.getElementById('review-categorie').value;
            const data = {
                titre: document.getElementById('review-titre').value,
                description: document.getElementById('review-desc').value,
                note: parseInt(document.getElementById('review-note').value),
                categorie: categorie,
                id_prestataire: (categorie === "Prestataire" && idPresta) ? parseInt(idPresta) : null
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
                }
            } catch (err) {
                alert("Erreur serveur.");
            }
        }

        function formatDisplayDate(dateStr) {
            if (!dateStr) return "Date inconnue";
            const d = new Date(dateStr.replace(' ', 'T'));
            return isNaN(d) ? "Date invalide" : d.toLocaleDateString('fr-FR', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }

        async function fetchAvis(page = 1) {
            try {
                currentPage = page;
                const response = await fetch(`${API_BASE}/avis/read?page=${currentPage}&limit=${limit}`);
                const result = await response.json();
                const avis = result.data || [];
                const container = document.getElementById('list-review');
                container.innerHTML = '';

                avis.forEach(c => {
                    const id = c.id_avis || c.id;
                    const titre = c.titre || 'Sans titre';
                    const note = c.note || 0;
                    const stars = "★".repeat(note) + "☆".repeat(5 - note);
                    const datePub = formatDisplayDate(c.date);

                    const fullNom = `${c.prenom_prestataire || ''} ${c.nom_prestataire || ''}`.trim();

                    const labelPrestataire = (c.categorie === "Prestataire" && fullNom !== "") ?
                        `<div class="flex items-center gap-2 mb-2">
                            <span class="text-[11px] font-bold text-[#1C5B8F] bg-blue-50 px-2 py-0.5 rounded">${fullNom}</span>
                        </div>` :
                        "";

                    container.innerHTML += `
                        <div class="w-[380px] bg-white border border-gray-100 flex flex-col p-8 rounded-[2.5rem] shadow-xl hover:-translate-y-2 transition-all relative">
                            <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-1/3 h-1.5 bg-[#1C5B8F] rounded-b-md"></div>
                            
                            <div class="flex justify-between items-start mb-4">
                                <span class="bg-[#E1AB2B]/10 text-[#E1AB2B] border border-[#E1AB2B]/30 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">
                                    ${c.categorie || 'Général'}
                                </span>
                                <span class="text-[11px] text-gray-400 font-bold">${datePub}</span>
                            </div>

                            ${labelPrestataire}

                            <h3 class="text-2xl text-[#1C5B8F] font-bold mb-2 leading-tight">${titre}</h3>
                            <div class="text-[#E1AB2B] text-lg mb-4">${stars}</div>
                            
                            <p class="text-gray-500 mb-8 flex-grow leading-relaxed line-clamp-1 italic">
                                "${c.description || ''}"
                            </p>
                            
                            <div class="mt-auto pt-4 border-t border-gray-50">
                                <a href="detail_review.php?id=${id}" class="inline-flex items-center text-[#1C5B8F] font-bold hover:text-[#E1AB2B] transition-colors group">
                                    Lire le témoignage <span class="ml-2 transition-transform group-hover:translate-x-1">→</span>
                                </a>
                            </div>
                        </div>
                    `;
                });
                renderPagination(result.totalPages);
            } catch (err) {
                console.error(err);
            }
        }

        function renderPagination(totalPages) {
            const container = document.getElementById('pagination-controls');
            container.innerHTML = '';
            if (totalPages <= 1) return;

            const btnClass = "px-6 py-2 border-2 border-[#1C5B8F] text-[#1C5B8F] rounded-full font-bold transition-all hover:bg-[#1C5B8F] hover:text-white";

            if (currentPage > 1) {
                container.innerHTML += `<button onclick="fetchAvis(${currentPage - 1})" class="${btnClass}">← Précédent</button>`;
            }
            container.innerHTML += `<span class="text-gray-500 font-bold px-4">Page ${currentPage} / ${totalPages}</span>`;
            if (currentPage < totalPages) {
                container.innerHTML += `<button onclick="fetchAvis(${currentPage + 1})" class="${btnClass}">Suivant →</button>`;
            }
        }

        window.onload = () => {
            fetchAvis(1);
        };
    </script>
</body>

</html>