<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie</title>
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

        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            if (modal) {
                modal.classList.toggle('hidden');
            }
        }
    </script>
</head>

<body class="bg-gray-50 text-gray-800">

    <div class="flex min-h-screen relative">

        <?php include("../includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto relative">

            <main class="p-8">

                <div class="flex justify-between items-center mb-8">
                    <a href="/providers/communication/list_user.php">
                        <button class="flex items-center bg-gray-100 hover:bg-gray-200 text-[#1C5B8F] px-4 py-2 rounded-full transition font-semibold text-xl">
                            <img src="/back/icons/fleche.svg" alt="fleche" class="w-7 h-7 mr-2"> Revenir aux utilisateurs
                        </button>
                    </a>
                </div>
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-[#1C5B8F]/20 overflow-hidden">
                    <div class="bg-[#1C5B8F] px-6 py-4 text-center">
                        <h4 id="chat-title" class="text-2xl font-semibold text-white"></h4>
                    </div>

                    <div id="message_user" class="p-6 overflow-y-auto bg-gray-100" style="max-height: 350px;">
                    </div>

                    <div class="bg-white px-6 py-4 flex items-center gap-2">
                        <input type="text" id="add"
                            class="flex-1 block w-full px-4 py-2 text-gray-700 bg-white rounded-md border border-[#1C5B8F]
                            focus:outline-none focus:border-none focus:outline-1 focus:-outline-offset-1 focus:outline-[#E1AB2B]/60"
                            placeholder="Envoyer un message...">
                        <button type="submit" onclick="add_message()"
                            class="px-6 py-2 font-medium text-white bg-[#1C5B8F] rounded-md hover:bg-[#E1AB2B]/60">
                            Envoyer
                        </button>
                    </div>
                </div>

            </main>
        </div>

    </div>

    <script>
        const API_BASE = `${window.API_BASE_URL}/message`;

        let id1 = null;
        const path = window.location.pathname;
        const segments = path.split('/');
        const id2 = segments.pop();

        const name = segments[segments.length - 1];
        const firstname = segments[segments.length - 2];

        window.addEventListener('auth_ready', () => {
            id1 = window.currentUserId;
            document.getElementById('chat-title').innerText = `Discussion avec ${firstname} ${name}`;
            message();
            setInterval(message, 2000);
        });

        async function modifierEtatOffre(messageId, nouvelEtat) {
            const action = nouvelEtat === 'accepte' ? 'accept' : 'reject';

            const response = await fetch(`${window.API_BASE_URL}/message/prestataire/${action}/${messageId}`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json"
                }
            });

            if (response.ok) {
                message();
            } else {
                alert("Erreur lors de la modification");
            }
        }

        async function message() {
            const response = await fetch(`${API_BASE}/prestataire/get/${id1}/with/${id2}`);

            if (!response.ok) return;
            let list = await response.json();

            if (!list) {
                list = [];
            }

            if (Array.isArray(list)) {
                list.sort((a, b) => a.id - b.id);
            }

            let page = "";
            list.forEach(msg => {
                const isMe = msg.id_expediteur == id1;
                const isOffre = msg.prix_propose && msg.prix_propose > 0;

                const alignClass = isMe ? 'items-end' : 'items-start';
                const bubbleBg = isMe ? 'bg-[#1C5B8F] text-white' : 'bg-gray-200 text-[#1C5B8F]';
                const roundedClass = isMe ? 'rounded-l-lg rounded-tr-lg' : 'rounded-r-lg rounded-tl-lg';

                let contenuAffiche = `<span class="text-sm break-all">${msg.contenu}</span>`

                if (isOffre) {
                    let boutonsOffre = "";
                    let borderCol = "border-[#E1AB2B]";
                    let textCol = "text-[#E1AB2B]";
                    let statutLabel = "Offre de négociation reçue";

                    if (msg.etat_offre === 'en_attente' && !isMe) {
                        boutonsOffre = `
                            <div class="flex gap-2 mt-3">
                                <button onclick="modifierEtatOffre(${msg.id}, 'accepte')" class="bg-green-500 text-white px-3 py-1 rounded-md text-xs font-bold hover:bg-green-600 transition shadow-sm">Accepter</button>
                                <button onclick="modifierEtatOffre(${msg.id}, 'refuse')" class="bg-red-500 text-white px-3 py-1 rounded-md text-xs font-bold hover:bg-red-600 transition shadow-sm">Refuser</button>
                            </div>`;
                    } else if (msg.etat_offre === 'accepte') {
                        borderCol = "border-green-500";
                        textCol = "text-green-600";
                        statutLabel = isMe ? "Vous avez accepté cette offre" : "Offre acceptée";
                    }

                    contenuAffiche = `
                        <div class="flex flex-col border-l-4 ${borderCol} pl-3 py-1 text-left">
                            <span class="text-[10px] font-bold uppercase ${textCol}">${statutLabel}</span>
                            <span class="text-sm font-semibold">${msg.contenu}</span>
                            ${boutonsOffre}
                        </div>`;
                }

                const deleteButton = isMe ? `
                        <button type='button' 
                                class='p-1 transition-colors duration-200 rounded-full hover:bg-black/20' 
                                onclick='delete_message(${msg.id})'>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>` : '';

                page += `
                    <div class='w-full flex flex-col ${alignClass} mb-4'>
                        <div class='relative max-w-[80%] md:max-w-md px-4 py-2 shadow-sm ${bubbleBg} ${roundedClass} flex items-center gap-3'>
                            ${contenuAffiche}
                            ${deleteButton}
                        </div>
                    </div>`;
            });

            document.getElementById("message_user").innerHTML = page;

            const container = document.getElementById("message_user");
            container.scrollTop = container.scrollHeight;
        }

        async function add_message() {
            const input = document.getElementById("add");
            const contenu = input.value.trim();

            if (contenu === "") {
                console.error("Message vide");
                return;
            }

            const isProvider = window.location.pathname.includes('/providers/');

            const response = await fetch(`${API_BASE}/prestataire/add`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    Contenu: contenu,
                    ID_Expediteur: parseInt(id1),
                    ID_Destinataire: parseInt(id2),
                    Expediteur: isProvider
                })
            });

            if (response.ok) {
                input.value = "";
                await message();
            }
        }

        async function delete_message(message_id) {
            const response = await fetch(`${API_BASE}/prestataire/delete/${message_id}`, {
                method: "DELETE"
            });

            if (response.ok) {
                await message();
            }
        }
    </script>
</body>

</html>