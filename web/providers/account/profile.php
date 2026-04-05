<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: { fontFamily: { sans: ['Alata', 'sans-serif'] } }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="flex min-h-screen">
        <?php include("../includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto">
            <main class="p-8">
                <div class="max-w-4xl mx-auto space-y-8">
                    
                    <div>
                        <h1 class="text-3xl font-semibold text-[#1C5B8F]">Mon Profil</h1>
                        <p class="text-gray-500 mt-1">Consultez et modifiez toutes vos informations.</p>
                    </div>

                    <div id="alert-box" class="hidden p-4 rounded-xl font-semibold text-sm"></div>

                    <form id="form-profil" class="space-y-6">
                        
                        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                            <h2 class="text-xl font-bold text-[#1C5B8F] mb-6 flex items-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Informations Personnelles
                            </h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-sm text-gray-600 font-semibold block mb-2">Prénom</label>
                                    <input type="text" id="prenom" class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600 font-semibold block mb-2">Nom</label>
                                    <input type="text" id="nom" class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600 font-semibold block mb-2">Adresse E-mail</label>
                                    <input type="email" id="email" class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600 font-semibold block mb-2">Téléphone</label>
                                    <input type="tel" id="telephone" class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-sm text-gray-600 font-semibold block mb-2">Nouveau mot de passe</label>
                                    <input type="password" id="mdp" placeholder="Laissez vide pour conserver votre mot de passe actuel" class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]">
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                            <h2 class="text-xl font-bold text-[#1C5B8F] mb-6 flex items-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                Activité Professionnelle
                            </h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-sm text-gray-600 font-semibold block mb-2">Numéro SIRET</label>
                                    <input type="text" id="siret" maxlength="14" class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600 font-semibold block mb-2">Catégorie</label>
                                    <select id="id_categorie" class="w-full border border-gray-300 rounded-xl p-3 bg-white focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required>
                                        <option value="" disabled selected>Chargement...</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-sm text-gray-600 font-semibold block mb-2">Tarif de base (€/h)</label>
                                    <div class="relative w-full">
                                        <input type="number" id="tarifs" step="0.01" min="0" class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:border-[#1C5B8F] focus:ring-1 focus:ring-[#1C5B8F]" required>
                                        <span class="absolute right-4 top-3 text-gray-400 font-bold">€</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" id="btn-save" class="bg-[#E1AB2B] hover:bg-yellow-500 text-[#1C5B8F] font-bold py-3 px-8 rounded-xl shadow-md transition-all flex items-center gap-2">
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>

                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            let providerData = null;
            const selectCategorie = document.getElementById('id_categorie');

            try {
                const catRes = await fetch(`${window.API_BASE_URL}/categorie/read`);
                if (catRes.ok) {
                    const jsonResponse = await catRes.json();
                    const categories = Array.isArray(jsonResponse) ? jsonResponse : (jsonResponse.data || []);
                    selectCategorie.innerHTML = '<option value="" disabled>Sélectionnez une catégorie</option>';
                    categories.forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.id_categorie || cat.id || cat.ID; 
                        option.textContent = cat.nom || cat.Nom;
                        selectCategorie.appendChild(option);
                    });
                }
            } catch (err) {
                console.error("Erreur chargement catégories:", err);
            }

            try {
                const res = await fetch(`${window.API_BASE_URL}/auth/me-provider`, {
                    method: 'GET',
                    credentials: 'include'
                });

                if (res.ok) {
                    providerData = await res.json();

                    document.getElementById('prenom').value = providerData.prenom || '';
                    document.getElementById('nom').value = providerData.nom || '';
                    document.getElementById('email').value = providerData.email || '';
                    document.getElementById('telephone').value = providerData.num_telephone || '';
                    document.getElementById('siret').value = providerData.siret || '';
                    document.getElementById('tarifs').value = providerData.tarifs || 0;
                    
                    if(providerData.id_categorie || providerData.IdCategorie) {
                        selectCategorie.value = providerData.id_categorie || providerData.IdCategorie;
                    }

                } else {
                    window.location.href = "/front/providers/account/signin.php";
                }
            } catch (err) {
                console.error("Erreur :", err);
            }

            const form = document.getElementById('form-profil');
            const alertBox = document.getElementById('alert-box');
            const btnSave = document.getElementById('btn-save');

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                btnSave.disabled = true;
                btnSave.innerHTML = "Sauvegarde...";

                const pwd = document.getElementById('mdp').value.trim();

                const updatedData = {
                    ...providerData,
                    prenom: document.getElementById('prenom').value.trim(),
                    nom: document.getElementById('nom').value.trim(),
                    email: document.getElementById('email').value.trim(),
                    num_telephone: document.getElementById('telephone').value.trim(),
                    siret: document.getElementById('siret').value.trim(),
                    tarifs: parseFloat(document.getElementById('tarifs').value),
                    id_categorie: parseInt(selectCategorie.value)
                };

                if (pwd !== "") {
                    updatedData.mdp = pwd;
                }

                try {
                    const updateRes = await fetch(`${window.API_BASE_URL}/auth/update-provider`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        credentials: 'include',
                        body: JSON.stringify(updatedData)
                    });

                    if (updateRes.ok) {
                        alertBox.textContent = "Profil mis à jour avec succès !";
                        alertBox.className = "p-4 rounded-xl font-bold text-green-700 bg-green-100 border border-green-400";
                        alertBox.classList.remove('hidden');
                        document.getElementById('mdp').value = "";
                        
                        const nameDisplay = document.getElementById('provider-name-display');
                        if(nameDisplay) {
                            const catText = selectCategorie.options[selectCategorie.selectedIndex].text;
                            nameDisplay.innerHTML = `<span class="font-bold text-[#E1AB2B]">${updatedData.prenom} ${updatedData.nom}</span><br><span class="text-xs text-gray-300">${catText}</span>`;
                        }
                    } else {
                        const errorMsg = await updateRes.text();
                        alertBox.textContent = "Erreur : " + errorMsg;
                        alertBox.className = "p-4 rounded-xl font-bold text-red-700 bg-red-100 border border-red-400";
                        alertBox.classList.remove('hidden');
                    }
                } catch (err) {
                    alertBox.textContent = "Impossible de joindre le serveur.";
                    alertBox.className = "p-4 rounded-xl font-bold text-red-700 bg-red-100 border border-red-400";
                    alertBox.classList.remove('hidden');
                } finally {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    btnSave.disabled = false;
                    btnSave.innerHTML = "Enregistrer les modifications";
                }
            });
        });
    </script>
</body>
</html>