<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conseils</title>
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
    <div class="flex min-h-screen">

        <?php include("../includes/sidebar.php"); ?>

        <div class="flex-1 flex flex-col">
            <?php include("../includes/header.php"); ?>

            <main class="p-8">
                <div class="max-w-4xl mx-auto py-5">
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
                        <div class="bg-white px-6 py-4 border-b border-gray-200 text-center">
                            <h4 class="text-xl font-semibold text-gray-800">Messagerie</h4>
                        </div>

                        <div id="message_user" class="p-6 overflow-y-auto bg-gray-50" style="max-height: 400px;">
                        </div>

                        <div class="bg-white px-6 py-4 border-t border-gray-200 flex items-center gap-2">
                            <input type="text" id="add"
                                class="flex-1 block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500 focus:outline-none focus:ring focus:ring-opacity-40"
                                placeholder="Envoyer un message...">
                            <button type="submit" onclick="add_message()"
                                class="px-6 py-2 font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-blue-600 rounded-md hover:bg-blue-500 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-80">
                                Envoyer
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        const API_BASE = "http://localhost:8082/message";

        let id1 = null;
        const path = window.location.pathname;
        const segments = path.split('/');
        const id2 = segments.pop();

        window.addEventListener('auth_ready', () => {
            id1 = window.currentUserId;
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

            let page = "";
            list.forEach(msg => {
                const isMe = msg.id_expediteur == id1;

                const alignClass = isMe ? 'items-end' : 'items-start';
                const bubbleBg = isMe ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800';
                const roundedClass = isMe ? 'rounded-l-lg rounded-tr-lg' : 'rounded-r-lg rounded-tl-lg';

                page += `
                <div class='flex flex-col ${alignClass} mb-4'>
                    <div class='relative max-w-xs md:max-w-md px-4 py-2 shadow-sm ${bubbleBg} ${roundedClass} flex items-center gap-3'>
                        <span class="text-sm">${msg.contenu}</span>
                        <button type='button' 
                                class='p-1 transition-colors duration-200 rounded-full hover:bg-black/10' 
                                onclick='delete_message(${msg.id})'>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>`;
            });

            document.getElementById("message_user").innerHTML = page;

            // Auto-scroll vers le bas lors de la réception de messages
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
                    ID_Destinataire: parseInt(id2)
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