<?php
$is_logged_in = isset($_COOKIE['session_token']);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie</title>
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

        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            if (modal) {
                modal.classList.toggle('hidden');
                modal.classList.toggle('flex');
            }
        }
    </script>
</head>

<body class="font-sans antialiased bg-gray-50">
    <?php include("../includes/header.php") ?>
    <main class="p-8 bg-gray-50">
        <?php if ($is_logged_in): ?>

            <div id="chat-container" class="hidden">
                <div class="flex justify-between items-center mx-8 mb-8">
                    <a href="/front/communication/list_contact.php">
                        <button class="flex items-center rounded-full px-6 py-2 bg-[#1C5B8F] text-white hover:bg-[#154670] transition">
                            <img src="/front/icons/fleche_gauche.svg" alt="fleche" class="w-7 h-7 mr-2"> Revenir à la liste
                        </button>
                    </a>
                </div>
                <div class="mx-8 bg-white rounded-[2.5rem] shadow-xl shadow-[#1C5B8F]/20 overflow-hidden">
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
            </div>

        <?php else: ?>
            <div class="flex flex-col items-center justify-center py-20 rounded-[2.5rem] shadow-xl shadow-blue-900/10">
                <p class="text-center font-semibold text-[#1C5B8F] text-2xl mb-8">
                    Vous devez être connecté(e) pour discuter avec notre équipe Silver Happy.</p>
                <a class="rounded-full px-4 py-2 bg-[#1C5B8F] text-white hover:bg-[#154670] transition" href="/front/account/signin.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">
                    Je me connecte
                </a>
            </div>
        <?php endif; ?>
    </main>
    <?php include("../includes/footer.php") ?>

    <script>
        let id1 = null;
        const path = window.location.pathname;
        const segments = path.split('/');
        const contactType = segments.pop();

        const id2 = segments[segments.length - 1];
        const name = segments[segments.length - 2];
        const firstname = segments[segments.length - 3];

        const API_BASE = contactType === 'admin' ?
            `${window.API_BASE_URL}/message` :
            `${window.API_BASE_URL}/message/prestataire`;

        window.addEventListener('auth_ready', () => {
            const noSubContainer = document.getElementById('no-sub-container');
            const chatContainer = document.getElementById('chat-container');

            if (chatContainer) chatContainer.classList.remove('hidden');

            id1 = window.currentUserId;
            document.getElementById('chat-title').innerText = `Discussion avec ${firstname} ${name}`;
            message();
            setInterval(message, 2000);
        });

        async function message() {
            const response = await fetch(`${API_BASE}/get/${id1}/with/${id2}`);

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

                const alignClass = isMe ? 'items-end' : 'items-start';
                const bubbleBg = isMe ? 'bg-[#1C5B8F] text-white' : 'bg-gray-200 text-[#1C5B8F]';
                const roundedClass = isMe ? 'rounded-l-lg rounded-tr-lg' : 'rounded-r-lg rounded-tl-lg';

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
                            <span class="text-sm">${msg.contenu}</span>
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

            if (contenu === "") return;

            const response = await fetch(`${API_BASE}/add`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    Contenu: contenu,
                    ID_Expediteur: parseInt(id1),
                    ID_Destinataire: parseInt(id2),
                    Expediteur: false
                })
            });

            if (response.ok) {
                input.value = "";
                await message();
            }
        }

        async function delete_message(message_id) {
            const response = await fetch(`${API_BASE}/delete/${message_id}`, {
                method: "DELETE"
            });

            if (response.ok) {
                await message();
            }
        }
    </script>
</body>

</html>