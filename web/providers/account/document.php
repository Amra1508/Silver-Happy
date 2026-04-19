<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Documents</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Alata', 'sans-serif'] } } } }
    </script>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="flex min-h-screen">
        <?php include("../includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto">
            <main class="p-8">
                <div class="max-w-5xl mx-auto space-y-8">
                    
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-3xl font-semibold text-[#1C5B8F]">Mes Documents</h1>
                            <p class="text-gray-500 mt-1">Importez et gérez vos justificatifs professionnels.</p>
                        </div>
                        <a href="profile.php" class="text-sm font-bold text-gray-500 hover:text-[#1C5B8F] transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Retour au profil
                        </a>
                    </div>

                    <div id="alert-box" class="hidden p-4 rounded-xl font-semibold text-sm mb-4"></div>

                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                        <h2 class="text-xl font-bold text-gray-700 mb-4">Ajouter un document</h2>
                        <form id="upload-form" class="flex flex-col md:flex-row gap-4 items-end">
                            <div class="flex-1 w-full">
                                <label class="text-xs text-gray-500 font-bold uppercase mb-2 block">Type de document</label>
                                <select id="doc-type" class="w-full p-3 border border-gray-300 rounded-xl bg-gray-50 focus:border-[#1C5B8F] outline-none">
                                    <option value="RIB">Relevé d'Identité Bancaire (RIB)</option>
                                    <option value="Kbis">Extrait Kbis (ou SIRENE)</option>
                                    <option value="RC Pro">Assurance RC Pro</option>
                                    <option value="Pièce d'identité">Pièce d'Identité / Passeport</option>
                                    <option value="Autre">Autre document</option>
                                </select>
                            </div>
                            <div class="flex-1 w-full">
                                <label class="text-xs text-gray-500 font-bold uppercase mb-2 block">Fichier (PDF, JPG, PNG)</label>
                                <input type="file" id="doc-file" accept=".pdf,.jpg,.jpeg,.png" class="w-full p-2.5 border border-gray-300 rounded-xl bg-gray-50 cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#1C5B8F]/10 file:text-[#1C5B8F] hover:file:bg-[#1C5B8F]/20">
                            </div>
                            <button type="submit" id="btn-upload" class="bg-[#1C5B8F] hover:bg-[#154670] text-white font-bold py-3 px-8 rounded-xl transition-all h-[52px]">
                                Uploader
                            </button>
                        </form>
                    </div>

                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                    <th class="p-4 font-bold">Nom du Fichier</th>
                                    <th class="p-4 font-bold w-1/3">Type de document</th>
                                    <th class="p-4 font-bold text-right w-32">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="docs-list">
                                <tr><td colspan="3" class="p-8 text-center text-gray-400 animate-pulse">Chargement...</td></tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const docsList = document.getElementById('docs-list');
            const uploadForm = document.getElementById('upload-form');
            const alertBox = document.getElementById('alert-box');

            function showAlert(msg, isError = false) {
                alertBox.textContent = msg;
                alertBox.className = `p-4 rounded-xl font-bold mb-4 block ${isError ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'}`;
                setTimeout(() => alertBox.classList.add('hidden'), 4000);
            }

            setTimeout(fetchDocuments, 500);

            async function fetchDocuments() {
                if (!window.currentUserId) return;

                try {
                    const apiBase = window.API_BASE_URL;
                    const res = await fetch(`${apiBase}/prestataire/documents/${window.currentUserId}/get`);
                    const docs = await res.json();
                    
                    docsList.innerHTML = '';
                    
                    if (docs.length === 0) {
                        docsList.innerHTML = `<tr><td colspan="3" class="p-8 text-center text-gray-500 font-medium">Aucun document ajouté pour le moment.</td></tr>`;
                        return;
                    }

                    docs.forEach(doc => {
                        const fileLink = `${apiBase}/${doc.url}`;

                        docsList.innerHTML += `
                            <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                <td class="p-4 font-semibold text-gray-700 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span class="truncate max-w-[300px] block" title="${doc.nom}">${doc.nom}</span>
                                </td>
                                <td class="p-4 text-sm"><span class="bg-blue-50 text-blue-700 px-3 py-1.5 rounded-md font-bold text-xs uppercase tracking-wide border border-blue-100">${doc.type}</span></td>
                                <td class="p-4 text-right flex justify-end gap-2">
                                    <a href="${fileLink}" target="_blank" class="text-gray-600 hover:text-[#1C5B8F] hover:bg-gray-100 px-3 py-1.5 rounded-lg text-sm font-bold border border-gray-200 transition">Voir</a>
                                    <button onclick="deleteDoc(${doc.id})" class="text-red-500 hover:text-white hover:bg-red-500 px-3 py-1.5 rounded-lg text-sm font-bold border border-red-200 transition">Supprimer</button>
                                </td>
                            </tr>
                        `;
                    });
                } catch (e) {
                    console.error(e);
                    docsList.innerHTML = `<tr><td colspan="3" class="p-8 text-center text-red-500">Erreur lors de la récupération des documents.</td></tr>`;
                }
            }

            uploadForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const fileInput = document.getElementById('doc-file');
                if (!fileInput.files.length) return showAlert("Sélectionnez un fichier.", true);

                const formData = new FormData();
                formData.append("document", fileInput.files[0]);
                formData.append("type_document", document.getElementById('doc-type').value);

                const btn = document.getElementById('btn-upload');
                btn.textContent = "Envoi...";
                btn.disabled = true;

                try {
                    const apiBase = window.API_BASE_URL;
                    const res = await fetch(`${apiBase}/prestataire/documents/${window.currentUserId}/create`, {
                        method: 'POST',
                        body: formData
                    });

                    if (res.ok) {
                        showAlert("Document ajouté avec succès !");
                        uploadForm.reset();
                        fetchDocuments();
                    } else {
                        showAlert("Erreur lors de l'upload.", true);
                    }
                } catch (err) {
                    showAlert("Erreur de connexion.", true);
                } finally {
                    btn.textContent = "Uploader";
                    btn.disabled = false;
                }
            });

            window.deleteDoc = async function(id) {
                if (!confirm("Voulez-vous vraiment supprimer ce document définitivement ?")) return;
                try {
                    const apiBase = window.API_BASE_URL;
                    await fetch(`${apiBase}/documents/${id}/delete`, { 
                        method: 'DELETE' });
                    fetchDocuments();
                } catch (e) {
                    showAlert("Erreur lors de la suppression.", true);
                }
            };
        });
    </script>
</body>
</html>