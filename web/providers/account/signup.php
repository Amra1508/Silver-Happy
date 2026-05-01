<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/config.php');
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Prestataire</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>

    <script>
        window.API_BASE_URL = "<?php echo API_BASE_URL; ?>";

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

<body class="flex flex-col min-h-screen bg-gray-50">

    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center">
                    <a href="/index.php" class="text-3xl font-bold tracking-wider text-[#1C5B8F]">
                        Silver<span class="text-[#E1AB2B]">Happy</span>
                    </a>
                </div>
                <nav class="hidden md:flex space-x-8 items-center">
                    <a href="/front/index.php" class="text-gray-600 hover:text-[#1C5B8F] font-medium transition-colors">Espace Senior</a>
                    <a href="/providers/account/signin.php" class="px-5 py-2 bg-[#1C5B8F]/10 text-[#1C5B8F] rounded-full font-bold hover:bg-[#1C5B8F]/20 transition-colors">Connexion Pro</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <div class="px-4 sm:px-16 pt-10 pb-16">

            <h2 class="text-3xl text-center mb-10 font-semibold text-[#1C5B8F]">Devenir Prestataire :</h2>

            <div id="response_message" class="hidden max-w-4xl mx-auto mb-6 p-4 rounded-lg border"></div>

            <div class="border border-[#1C5B8F] bg-white rounded-[2.5rem] py-10 px-10 grid gap-x-6 gap-y-8 sm:grid-cols-6 max-w-4xl mx-auto shadow-md">

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-semibold">Prénom</label>
                    <div class="mt-2">
                        <input id="first_name" type="text" class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:border-[#1C5B8F]" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-semibold">Nom</label>
                    <div class="mt-2">
                        <input id="last_name" type="text" class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:border-[#1C5B8F]" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-semibold">Date de naissance</label>
                    <div class="mt-2">
                        <input id="birth_date" type="date" class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:border-[#1C5B8F]" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-semibold">Numéro de téléphone</label>
                    <div class="mt-2">
                        <input id="phone_number" type="tel" class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:border-[#1C5B8F]" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-semibold">Adresse mail pro</label>
                    <div class="mt-2">
                        <input id="signup_email" type="email" class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:border-[#1C5B8F]" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-semibold">Mot de passe</label>
                    <div class="mt-2">
                        <input id="signup_password" type="password" class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:border-[#1C5B8F]" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-semibold">Numéro SIRET (14 chiffres)</label>
                    <div class="mt-2">
                        <input id="siret" type="text" maxlength="14"
                            oninput="verifierSiret('siret', 'siret-status')"
                            class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:border-[#1C5B8F]" required />
                        <p id="siret-status" class="hidden"></p>
                    </div>
                </div>

                <div class="sm:col-span-3 relative">
                    <label class="text-sm text-gray-600 font-semibold">Votre métier / spécialité</label>
                    <div class="mt-2">
                        <input id="search_categorie" type="text" placeholder="Rechercher un métier (ex: Yoga, Infirmier...)"
                            class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:border-[#1C5B8F]" autocomplete="off" required />

                        <input type="hidden" id="id_categorie" name="id_categorie" required>

                        <div id="categorie_list" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-6 mt-4 border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-[#1C5B8F] mb-4">Documents justificatifs</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm text-gray-600 font-semibold">Pièce d'identité <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input id="doc_identite" type="file" accept=".pdf,.png,.jpg,.jpeg" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#1C5B8F]/10 file:text-[#1C5B8F] hover:file:bg-[#1C5B8F]/20 cursor-pointer" required />
                            </div>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-semibold">Extrait KBIS <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input id="doc_kbis" type="file" accept=".pdf,.png,.jpg,.jpeg" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#1C5B8F]/10 file:text-[#1C5B8F] hover:file:bg-[#1C5B8F]/20 cursor-pointer" required />
                            </div>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-semibold">Casier Judiciaire <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input id="casier_judiciaire" type="file" accept=".pdf,.png,.jpg,.jpeg" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#1C5B8F]/10 file:text-[#1C5B8F] hover:file:bg-[#1C5B8F]/20 cursor-pointer" required />
                            </div>
                        </div>
                    </div>

                    <div id="extra_docs_container" class="mt-6 space-y-4"></div>

                    <button type="button" id="btn_add_doc" class="mt-4 text-sm font-semibold text-[#E1AB2B] hover:text-yellow-600 transition-colors flex items-center">
                        Ajouter un autre document (Attestation, Diplôme...)
                    </button>
                </div>

                <div class="sm:col-span-6 flex justify-center mt-4">
                    <div class="cf-turnstile" data-sitekey="0x4AAAAAACpHSvX9fEtYZZTy" data-theme="light"></div>
                </div>

                <button id="btn_register" class="sm:col-span-6 mx-auto px-14 py-3 bg-[#1C5B8F] text-white font-semibold rounded-md hover:bg-blue-800 transition-colors shadow-md">Envoyer ma demande</button>
            </div>

            <div class="mt-8 text-center">
                <span class="text-gray-600">Vous avez déjà un compte validé ?</span>
                <a href="signin.php" class="text-[#E1AB2B] font-semibold hover:underline ml-1 transition-all">
                    Je me connecte
                </a>
            </div>

        </div>

    </main>

    <script>
        async function verifierSiret(inputId, statusId) {
            const siretInput = document.getElementById(inputId);
            const status_siret = document.getElementById(statusId);

            if (!siretInput || !status_siret) return false;

            const siret = siretInput.value.trim();

            status_siret.className = 'hidden';
            if (siret.length !== 14 || isNaN(siret)) return false;

            status_siret.textContent = 'Vérification...';
            status_siret.className = 'text-sm text-gray-500 mt-1';

            try {
                const res = await fetch(`https://recherche-entreprises.api.gouv.fr/search?q=${siret}&page=1&per_page=1`);
                const {
                    results
                } = await res.json();

                if (results && results.length && results[0].etat_administratif === 'A') {
                    status_siret.textContent = `${results[0].nom_complet}`;
                    status_siret.className = 'text-sm text-green-600 font-semibold mt-1';
                    return true;
                }

                status_siret.textContent = (results && results.length) ? 'Entreprise fermée (cessé)' : 'SIRET introuvable.';
                status_siret.className = 'text-sm text-red-600 font-semibold mt-1';
                return false;

            } catch {
                status_siret.textContent = 'Vérification impossible. Veuillez réssayeer';
                status_siret.className = 'text-sm text-orange-500 mt-1';
                return false;
            }
        }

        let allCategories = [];

        document.addEventListener('DOMContentLoaded', async () => {
            const searchInput = document.getElementById('search_categorie');
            const categorieList = document.getElementById('categorie_list');
            const hiddenInput = document.getElementById('id_categorie');

            try {
                const res = await fetch(`${window.API_BASE_URL}/categorie/read`);
                if (res.ok) {
                    const jsonResponse = await res.json();
                    allCategories = Array.isArray(jsonResponse) ? jsonResponse : (jsonResponse.data || []);
                }
            } catch (err) {
                console.error("Erreur chargement métiers:", err);
            }

            const filterCategories = (query) => {
                const filtered = allCategories.filter(cat =>
                    (cat.nom || cat.Nom).toLowerCase().includes(query.toLowerCase())
                );

                if (filtered.length > 0 && query.length > 0) {
                    categorieList.innerHTML = '';
                    filtered.forEach(cat => {
                        const div = document.createElement('div');
                        div.className = "p-2 hover:bg-[#1C5B8F] hover:text-white cursor-pointer transition-colors";
                        div.textContent = cat.nom || cat.Nom;
                        div.onclick = () => {
                            searchInput.value = cat.nom || cat.Nom;
                            hiddenInput.value = cat.id_categorie || cat.id || cat.ID;
                            categorieList.classList.add('hidden');
                        };
                        categorieList.appendChild(div);
                    });
                    categorieList.classList.remove('hidden');
                } else {
                    categorieList.classList.add('hidden');
                }
            };

            searchInput.addEventListener('input', (e) => filterCategories(e.target.value));

            searchInput.addEventListener('focus', () => {
                if (searchInput.value === "") filterCategories("");
            });

            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !categorieList.contains(e.target)) {
                    categorieList.classList.add('hidden');
                }
            });

            const limitDate = new Date();
            limitDate.setFullYear(limitDate.getFullYear() - 18);
            const strMax = limitDate.toISOString().split('T')[0];
            document.getElementById('birth_date').max = strMax;

            const btnAddDoc = document.getElementById('btn_add_doc');
            const extraDocsContainer = document.getElementById('extra_docs_container');

            btnAddDoc.addEventListener('click', () => {
                const div = document.createElement('div');
                div.className = 'grid grid-cols-1 sm:grid-cols-12 gap-4 items-end bg-gray-50 p-4 rounded-lg border border-gray-200';

                div.innerHTML = `
            <div class="sm:col-span-5">
                <label class="text-sm text-gray-600 font-semibold">Nom du document</label>
                <input type="text" class="extra-doc-name w-full border border-gray-300 rounded-md p-2 mt-2 focus:outline-none focus:border-[#1C5B8F]" placeholder="Ex: Attestation RC Pro" required />
            </div>
            <div class="sm:col-span-6">
                <label class="text-sm text-gray-600 font-semibold">Fichier</label>
                <input type="file" accept=".pdf,.png,.jpg,.jpeg" class="extra-doc-file w-full mt-2 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 cursor-pointer" required />
            </div>
            <div class="sm:col-span-1 flex justify-end pb-1">
                <button type="button" class="btn-remove-doc text-red-500 hover:text-red-700 p-2" title="Supprimer">
                </button>
            </div>
        `;
                extraDocsContainer.appendChild(div);

                div.querySelector('.btn-remove-doc').addEventListener('click', () => {
                    div.remove();
                });
            });

            const btnSubmit = document.getElementById('btn_register');

            btnSubmit.addEventListener('click', async (e) => {
                e.preventDefault();

                const messageBox = document.getElementById('response_message');
                const showError = (msg) => {
                    messageBox.textContent = msg;
                    messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold bg-red-100 border-red-400 text-red-700";
                    messageBox.classList.remove('hidden');
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                };

                const inputBirth = document.getElementById('birth_date').value;
                if (inputBirth > strMax) {
                    return showError("Désolé, vous devez avoir au moins 18 ans pour devenir prestataire.");
                }

                const data = {
                    prenom: document.getElementById('first_name').value,
                    nom: document.getElementById('last_name').value,
                    date_naissance: inputBirth,
                    num_telephone: document.getElementById('phone_number').value,
                    email: document.getElementById('signup_email').value,
                    mdp: document.getElementById('signup_password').value,
                    siret: document.getElementById('siret').value,
                    id_categorie: parseInt(document.getElementById('id_categorie').value),
                    "cf-turnstile-response": document.querySelector('[name="cf-turnstile-response"]')?.value
                };

                if (!data.prenom || !data.nom || !data.date_naissance || !data.email || !data.mdp || !data.siret || !data.id_categorie) {
                    return showError("Veuillez remplir tous les champs obligatoires du formulaire.");
                }
                if (data.siret.length !== 14 || isNaN(data.siret)) {
                    return showError("Le numéro SIRET doit contenir exactement 14 chiffres.");
                }
                if (!await verifierSiret('siret', 'siret-status')) {
                    return showError("Le SIRET saisi n'est pas valide ou correspond à une entreprise fermée.");
                }
                if (!data["cf-turnstile-response"]) {
                    return showError("Veuillez valider la vérification de sécurité (Captcha).");
                }

                const fileIdentite = document.getElementById('doc_identite').files[0];
                const fileKbis = document.getElementById('doc_kbis').files[0];
                const fileCasierJudiciaire = document.getElementById('casier_judiciaire').files[0];


                if (!fileIdentite || !fileKbis || !fileCasierJudiciaire) {
                    return showError("Vous devez obligatoirement fournir tous les documents attendus.");
                }

                const extraNames = document.querySelectorAll('.extra-doc-name');
                const extraFiles = document.querySelectorAll('.extra-doc-file');

                for (let i = 0; i < extraNames.length; i++) {
                    if (!extraNames[i].value.trim() || !extraFiles[i].files[0]) {
                        return showError("Veuillez renseigner un nom et sélectionner un fichier pour tous vos documents supplémentaires.");
                    }
                }

                try {
                    const response = await fetch(`${window.API_BASE_URL}/auth/register-provider`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        return showError("Erreur : " + errorText);
                    }

                    const result = await response.json();
                    const newProviderId = result.ID || result.id || result.id_prestataire;

                    const uploadPromises = [];

                    const uploadDoc = async (file, typeDoc) => {
                        const formData = new FormData();
                        formData.append('fichier_document', file);
                        formData.append('type_document', typeDoc);

                        return fetch(`${window.API_BASE_URL}/prestataires/upload/${newProviderId}`, {
                            method: 'POST',
                            body: formData
                        });
                    };

                    uploadPromises.push(uploadDoc(fileIdentite, "Pièce d'identité"));
                    uploadPromises.push(uploadDoc(fileKbis, "KBIS"));
                    uploadPromises.push(uploadDoc(fileCasierJudiciaire, "Casier Judiciaire"));

                    for (let i = 0; i < extraNames.length; i++) {
                        uploadPromises.push(uploadDoc(extraFiles[i].files[0], extraNames[i].value.trim()));
                    }

                    await Promise.all(uploadPromises);

                    messageBox.textContent = "Demande envoyée avec succès ! Vos documents ont bien été joints. Votre compte est en attente de validation.";
                    messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold bg-green-100 border-green-400 text-green-700";
                    messageBox.classList.remove('hidden');
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });

                    setTimeout(() => {
                        window.location.href = "signin.php";
                    }, 3000);

                } catch (error) {
                    showError("Impossible de joindre le serveur. Vérifiez votre connexion.");
                }
            });
        });
    </script>

</body>

</html>