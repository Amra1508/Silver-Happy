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
                        <input id="siret" type="text" maxlength="14" class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:border-[#1C5B8F]" required />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-semibold">Catégorie de prestation</label>
                    <div class="mt-2">
                        <select id="id_categorie" class="w-full border border-gray-300 rounded-md p-2 bg-white focus:outline-none focus:border-[#1C5B8F]" required>
                            <option value="" disabled selected>Chargement des catégories...</option>
                        </select>
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label class="text-sm text-gray-600 font-semibold">Tarif horaire/forfait (€)</label>
                    <div class="mt-2">
                        <input id="tarif" type="number" step="0.01" min="0" placeholder="Ex: 25.50" class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:border-[#1C5B8F]" required />
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
                    </div>

                    <div id="extra_docs_container" class="mt-6 space-y-4"></div>

                    <button type="button" id="btn_add_doc" class="mt-4 text-sm font-semibold text-[#E1AB2B] hover:text-yellow-600 transition-colors flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
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

    <footer class="bg-[#1C5B8F] text-white py-10 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="text-center md:text-left">
                <span class="text-2xl font-bold tracking-wider">
                    Silver<span class="text-[#E1AB2B]">Happy</span>
                </span>
                <p class="text-sm text-blue-200 mt-2">Accompagner nos aînés au quotidien.</p>
                <p class="text-sm text-blue-300 mt-1">&copy; 2026 Silver Happy. Tous droits réservés.</p>
            </div>
            <div class="flex flex-wrap justify-center gap-x-8 gap-y-2 text-sm text-blue-200">
                <a href="#" class="hover:text-[#E1AB2B] transition-colors">Mentions légales</a>
                <a href="#" class="hover:text-[#E1AB2B] transition-colors">Politique de confidentialité</a>
                <a href="#" class="hover:text-[#E1AB2B] transition-colors">CGU Prestataires</a>
                <a href="#" class="hover:text-[#E1AB2B] transition-colors">Nous contacter</a>
            </div>
        </div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', async () => {
    const selectCategorie = document.getElementById('id_categorie');
    try {
        const res = await fetch('http://localhost:8082/categorie/read');
        if (res.ok) {
            const jsonResponse = await res.json();
            const categories = Array.isArray(jsonResponse) ? jsonResponse : (jsonResponse.data || []);
            selectCategorie.innerHTML = '<option value="" disabled selected>Sélectionnez une catégorie</option>';
            if (categories.length > 0) {
                categories.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id_categorie || cat.id || cat.ID; 
                    option.textContent = cat.nom || cat.Nom;
                    selectCategorie.appendChild(option);
                });
            } else {
                selectCategorie.innerHTML = '<option value="" disabled>Aucune catégorie disponible</option>';
            }
        }
    } catch (err) {
        selectCategorie.innerHTML = '<option value="" disabled>Serveur injoignable</option>';
    }

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
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
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
            window.scrollTo({ top: 0, behavior: 'smooth' });
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
            tarifs: parseFloat(document.getElementById('tarif').value),
            "cf-turnstile-response": document.querySelector('[name="cf-turnstile-response"]')?.value
        };

        if (!data.prenom || !data.nom || !data.date_naissance || !data.email || !data.mdp || !data.siret || !data.id_categorie || isNaN(data.tarifs)) {
            return showError("Veuillez remplir tous les champs obligatoires du formulaire.");
        }
        if (data.siret.length !== 14 || isNaN(data.siret)) {
            return showError("Le numéro SIRET doit contenir exactement 14 chiffres.");
        }
        if (!data["cf-turnstile-response"]) {
            return showError("Veuillez valider la vérification de sécurité (Captcha).");
        }

        const fileIdentite = document.getElementById('doc_identite').files[0];
        const fileKbis = document.getElementById('doc_kbis').files[0];

        if (!fileIdentite || !fileKbis) {
            return showError("Vous devez obligatoirement fournir votre pièce d'identité et votre extrait KBIS.");
        }

        const extraNames = document.querySelectorAll('.extra-doc-name');
        const extraFiles = document.querySelectorAll('.extra-doc-file');
        
        for (let i = 0; i < extraNames.length; i++) {
            if (!extraNames[i].value.trim() || !extraFiles[i].files[0]) {
                return showError("Veuillez renseigner un nom et sélectionner un fichier pour tous vos documents supplémentaires.");
            }
        }

        try {
            const response = await fetch('http://localhost:8082/auth/register-provider', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
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

                return fetch(`http://localhost:8082/prestataires/upload/${newProviderId}`, { 
                    method: 'POST',
                    body: formData 
                });
            };

            uploadPromises.push(uploadDoc(fileIdentite, "Pièce d'identité"));
            uploadPromises.push(uploadDoc(fileKbis, "KBIS"));

            for (let i = 0; i < extraNames.length; i++) {
                uploadPromises.push(uploadDoc(extraFiles[i].files[0], extraNames[i].value.trim()));
            }

            await Promise.all(uploadPromises);

            messageBox.textContent = "Demande envoyée avec succès ! Vos documents ont bien été joints. Votre compte est en attente de validation.";
            messageBox.className = "max-w-xl mx-auto mb-6 p-4 rounded-lg border text-center font-bold bg-green-100 border-green-400 text-green-700";
            messageBox.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });

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