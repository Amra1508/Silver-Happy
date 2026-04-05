<?php
$is_logged_in = isset($_COOKIE['session_token']);
if (!$is_logged_in) {
    header("Location: /front/account/signin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Avis - Communauté</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
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

<body class="bg-gray-50 flex flex-col min-h-screen">

    <?php include("../includes/header.php") ?>

    <main class="flex-1">
        <div class="w-full px-6 md:px-16 mt-8 mb-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h2 class="text-4xl font-bold text-[#1C5B8F]">Mes avis publiés</h2>
                    <p class="text-gray-600 mt-2">Gérez vos retours d'expérience et aidez la communauté.</p>
                </div>
                <div class="p-3 flex justify-between items-center mb-6">
                    <a href="/front/communication/review.php">
                        <button class="flex items-center rounded-full px-6 py-2 bg-[#1C5B8F] text-white font-bold hover:bg-[#154670] transition">
                            <img src="/front/icons/fleche_gauche.svg" alt="fleche" class="w-7 h-7 mr-2"> Revenir aux avis
                        </button>
                    </a>
                </div>
            </div>
        </div>

        <div id="list-review" class="flex flex-wrap gap-10 px-6 md:px-16 py-10 justify-center">
            <div class="animate-pulse text-gray-400">Chargement de vos avis...</div>
        </div>

        <div id="modal-review" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-[2rem] p-8 max-w-lg w-full relative shadow-2xl">
                <button onclick="toggleModal(false)" class="absolute top-4 right-6 text-2xl text-gray-400 hover:text-red-500">&times;</button>
                <h2 class="text-2xl font-bold text-[#1C5B8F] mb-6">Modifier mon avis</h2>

                <form id="form-review" onsubmit="submitEdit(event)" class="space-y-4">
                    <input type="hidden" id="edit-id">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Titre</label>
                        <input type="text" id="edit-titre" required class="w-full border-2 border-gray-100 rounded-xl px-4 py-2 focus:border-[#1C5B8F] outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Note (0-5)</label>
                            <input type="number" id="edit-note" min="0" max="5" required class="w-full border-2 border-gray-100 rounded-xl px-4 py-2 focus:border-[#1C5B8F] outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Catégorie</label>
                            <select id="edit-categorie" required class="w-full border-2 border-gray-100 rounded-xl px-4 py-2 focus:border-[#1C5B8F] outline-none">
                                <option value="Service">Service</option>
                                <option value="Evènement">Evènement</option>
                                <option value="Prestataire">Prestataire</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                    </div>
                    <div id="container-edit-presta" class="hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Prestataire concerné</label>
                        <select id="edit-id-prestataire" class="w-full border-2 border-gray-100 rounded-xl px-4 py-2 focus:border-[#1C5B8F] outline-none">
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                        <textarea id="edit-desc" rows="4" required class="w-full border-2 border-gray-100 rounded-xl px-4 py-2 focus:border-[#1C5B8F] outline-none"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-[#1C5B8F] text-white font-semibold py-3 rounded-xl hover:bg-[#E1AB2B] transition-all">
                        Enregistrer les modifications
                    </button>
                </form>
            </div>
        </div>
    </main>

    <?php include("../includes/footer.php") ?>

    <script>
        const API_BASE = window.API_BASE_URL;

        function getUserId() {
            return window.currentUserId || null;
        }

        async function fetchMyAvis() {
            const userId = getUserId();
            if (!userId) {
                setTimeout(fetchMyAvis, 100);
                return;
            }

            try {
                const response = await fetch(`${API_BASE}/avis/read-mine/${userId}`);
                const reviews = await response.json();
                const container = document.getElementById('list-review');
                container.innerHTML = '';

                if (!reviews || reviews.length === 0) {
                    container.innerHTML = `<p class="text-gray-400 italic">Aucun avis publié.</p>`;
                    return;
                }

                reviews.forEach(a => {
                    const stars = "★".repeat(a.note) + "☆".repeat(5 - a.note);
                    container.innerHTML += `
                    <div class="w-[350px] bg-white border border-gray-100 p-6 rounded-[2rem] shadow-lg">
                        <div class="flex justify-between mb-4">
                            <span class="bg-blue-50 text-[#1C5B8F] px-3 py-1 rounded-full text-[10px] font-bold uppercase">${a.categorie}</span>
                            <span class="text-[10px] text-gray-400">${new Date(a.date).toLocaleDateString()}</span>
                        </div>
                        <h3 class="text-xl font-bold mb-1">${a.titre}</h3>
                        <div class="text-[#E1AB2B] mb-3">${stars}</div>
                        <p class="text-gray-500 text-sm mb-6 italic line-clamp-3">"${a.description}"</p>
                        <div class="flex gap-3 pt-4 border-t">
                            <button onclick="openEditModal(${a.id_avis})" class="flex-1 py-2 text-sm font-bold text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all">Modifier</button>
                            <button onclick="deleteAvis(${a.id_avis})" class="flex-1 py-2 text-sm font-bold text-red-500 border border-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all">Supprimer</button>
                        </div>
                    </div>`;
                });
            } catch (err) {
                console.error("Erreur chargement:", err);
            }
        }

        async function deleteAvis(id) {
            const userId = getUserId();
            if (!confirm("Supprimer cet avis ?")) return;

            try {
                const response = await fetch(`${API_BASE}/avis/delete/${id}?user_id=${userId}`, {
                    method: 'DELETE'
                });
                if (response.ok) fetchMyAvis();
            } catch (err) {
                console.error(err);
            }
        }

        async function fillPrestatairesList(selectedId) {
            try {
                const response = await fetch(`${API_BASE}/prestataires/read?limit=500&status=valide`);
                const result = await response.json();
                const select = document.getElementById('edit-id-prestataire');

                select.innerHTML = '<option value="">Sélectionner un prestataire</option>';

                (result.data || []).forEach(p => {
                    const id = p.id_prestataire || p.ID || p.id;
                    if (id) {
                        const option = document.createElement('option');
                        option.value = id;
                        option.textContent = `${p.prenom || ''} ${p.nom || ''}`.trim();
                        if (id == selectedId) option.selected = true;
                        select.appendChild(option);
                    }
                });
            } catch (err) {
                console.error("Erreur chargement prestataires:", err);
            }
        }

        document.getElementById('edit-categorie').addEventListener('change', function() {
            const container = document.getElementById('container-edit-presta');
            if (this.value === "Prestataire") {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        });

        async function openEditModal(id) {
            try {
                const response = await fetch(`${API_BASE}/avis/read-one/${id}`);
                const a = await response.json();

                document.getElementById('edit-id').value = a.id_avis;
                document.getElementById('edit-titre').value = a.titre;
                document.getElementById('edit-note').value = a.note;
                document.getElementById('edit-categorie').value = a.categorie;
                document.getElementById('edit-desc').value = a.description;

                const container = document.getElementById('container-edit-presta');
                if (a.categorie === "Prestataire") {
                    container.classList.remove('hidden');
                    await fillPrestatairesList(a.id_prestataire);
                } else {
                    container.classList.add('hidden');
                    await fillPrestatairesList(null);
                }

                toggleModal(true);
            } catch (err) {
                alert("Erreur de chargement");
            }
        }

        async function submitEdit(event) {
            event.preventDefault();
            const userId = getUserId();
            const idAvis = document.getElementById('edit-id').value;
            const categorie = document.getElementById('edit-categorie').value;

            const data = {
                titre: document.getElementById('edit-titre').value,
                description: document.getElementById('edit-desc').value,
                note: parseInt(document.getElementById('edit-note').value),
                categorie: categorie,
                id_utilisateur: parseInt(userId),
                id_prestataire: categorie === "Prestataire" ? parseInt(document.getElementById('edit-id-prestataire').value) : null
            };

            try {
                const response = await fetch(`${API_BASE}/avis/update/${idAvis}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                if (response.ok) {
                    toggleModal(false);
                    fetchMyAvis();
                }
            } catch (err) {
                console.error(err);
            }
        }

        function toggleModal(show) {
            document.getElementById('modal-review').classList.toggle('hidden', !show);
        }

        window.onload = fetchMyAvis;
    </script>
</body>

</html>