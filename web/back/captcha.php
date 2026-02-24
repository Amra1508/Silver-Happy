<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Captchas - Silver Happy</title>
    <style>@import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');</style>
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

        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            if(modal) {
                modal.classList.toggle('hidden');
            }
        }
    </script>
</head>
<body>
    <div class="flex min-h-screen">

        <?php include("./includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col">
            <?php include("./includes/header.php"); ?>

            <main class="p-8">
                
                <div class="flex justify-between items-center mb-8">
                    <h1 class="big-text">Gestion des Captchas</h1>
                    <button onclick="toggleModal('add-modal')" class="button-blue px-6 rounded-full" type="button">
                        + Ajouter un Captcha
                    </button>
                </div>

                <table class="simple-table">
                    <thead class="table-header">
                        <tr>
                            <th class="table-cell font-semibold">ID</th>
                            <th class="table-cell font-semibold">Question / Consigne</th>
                            <th class="table-cell font-semibold">Réponse attendue</th>
                            <th class="table-cell font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-row">
                            <td class="table-cell">1</td>
                            <td class="table-cell">Combien font 3 + 4 ?</td>
                            <td class="table-cell">7</td>
                            <td class="table-cell flex justify-center gap-6">
                                <button onclick="toggleModal('edit-modal')" class="text-[#E1AB2B] hover:text-[#1C5B8F] font-bold">Modifier</button>
                                <button onclick="toggleModal('delete-modal')" class="text-red-500 hover:text-red-700 font-bold">Supprimer</button>
                            </td>
                        </tr>
                        <tr class="table-row">
                            <td class="table-cell">2</td>
                            <td class="table-cell">Recopiez le mot "Sécurité"</td>
                            <td class="table-cell">Sécurité</td>
                            <td class="table-cell flex justify-center gap-6">
                                <button onclick="toggleModal('edit-modal')" class="text-[#E1AB2B] hover:text-[#1C5B8F] font-bold">Modifier</button>
                                <button onclick="toggleModal('delete-modal')" class="text-red-500 hover:text-red-700 font-bold">Supprimer</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div id="add-modal" class="hidden modal-bg">
                    <div class="modal-box">
                        <h3 class="small-text text-2xl mb-6">Ajouter un Captcha</h3>
                        <form class="space-y-4">
                            <input type="text" class="form-input" placeholder="Question ou consigne">
                            <input type="text" class="form-input" placeholder="Réponse exacte attendue">
                            
                            <div class="flex justify-end gap-4 mt-8 pt-4 border-t border-gray-100">
                                <button type="button" onclick="toggleModal('add-modal')" class="header-button">Annuler</button>
                                <button type="button" class="button-blue px-6 rounded-full">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="edit-modal" class="hidden modal-bg">
                    <div class="modal-box">
                        <h3 class="small-text text-2xl mb-6">Modifier le Captcha</h3>
                        <form class="space-y-4">
                            <input type="text" class="form-input" value="Combien font 3 + 4 ?">
                            <input type="text" class="form-input" value="7">
                            
                            <div class="flex justify-end gap-4 mt-8 pt-4 border-t border-gray-100">
                                <button type="button" onclick="toggleModal('edit-modal')" class="header-button">Annuler</button>
                                <button type="button" class="button-blue px-6 rounded-full">Sauvegarder</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="delete-modal" class="hidden modal-bg">
                    <div class="modal-box max-w-lg text-center">
                        <div class="text-red-500 text-5xl mb-4 font-bold">!</div>
                        <h3 class="small-text text-2xl mb-2">Supprimer le captcha ?</h3>
                        <p class="text-gray-500 mb-8">Cette action est irréversible.</p>
                        
                        <div class="flex justify-center gap-4">
                            <button type="button" onclick="toggleModal('delete-modal')" class="header-button">Annuler</button>
                            <button type="button" class="btn-red">Oui, supprimer</button>
                        </div>
                    </div>
                </div>           
            </main>
        </div>
    </div>

   
</body>
</html>