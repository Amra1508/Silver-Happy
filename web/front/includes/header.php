<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/config.php'); 
?>

<script>
    window.API_BASE_URL = "<?php echo API_BASE_URL; ?>"; 
</script>

<header>
    <style type="text/tailwindcss">
        <?php include("front.css") ?>
    </style>

    <div class="bg-[#E1AB2B]/60 py-2 px-6 flex justify-end gap-4 items-center">
        <?php if (isset($_COOKIE['session_token'])): ?>
            <button id="btn_logout" class="header-button" data-i18n="nav_logout">Déconnexion</button>
            <a href="/front/account/profile.php">
                <button class="header-button" data-i18n="nav_account">Mon compte</button>
            </a>
        <?php else: ?>
            <a href="/front/account/signin.php">
                <button class="header-button" data-i18n="nav_login">Se connecter</button>
            </a>
            <a href="/front/account/signup.php">
                <button class="header-button" data-i18n="nav_signup">S'inscrire</button>
            </a>
        <?php endif; ?>

        <button id="btn_urgence" class="bg-red-600 text-white px-4 py-1.5 rounded-full font-bold ml-2 hover:bg-red-700">
            Urgence
        </button>

        <button onclick="toggleZoom()" class="header-button transition-all group ml-2" title="Modifier la taille du texte">
            <img src="/front/icons/zoom.svg" alt="zoom" class="w-7 h-7 object-contain transition-all group-hover:brightness-0 group-hover:invert">
        </button>

        <button onclick="readALoud()" aria-label="Lire la page à voix haute" class="header-button transition-all group ml-2 focus:outline-none focus:ring-4 focus:ring-[#1C5B8F]" title="Lecture vocale">
            <img src="/front/icons/assistant-vocal.svg" alt="" class="w-7 h-7 object-contain transition-all group-hover:brightness-0 group-hover:invert">
        </button>

        <div class="relative group z-[100]">
            <button class="flex items-center gap-2 header-button transition-all">
                <img id="current-lang-flag" src="/front/icons/france.png" alt="Lang" class="h-6 w-6 object-contain">
                <img src="/front/icons/dropdown.svg" alt="dropdown" class="w-5 h-5 object-contain transition-all group-hover:brightness-0 group-hover:invert">
            </button>

            <div class="absolute right-0 top-full pt-2 w-32 hidden group-hover:block">
                <div class="bg-white rounded-md shadow-lg overflow-hidden border border-gray-100">
                    <button onclick="changeLanguage('fr')" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-[#1C5B8F] hover:text-white transition-colors">
                        <img src="/front/icons/france.png" class="h-4 w-4"> Français
                    </button>
                    <button onclick="changeLanguage('en')" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-[#1C5B8F] hover:text-white transition-colors">
                        <img src="/front/icons/angleterre.png" class="h-4 w-4"> English
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="border border-[#D4D4D4] flex flex-col md:flex-row items-center justify-between gap-4 md:gap-8 px-4 py-4 md:px-6">
        <img class="w-30 h-12 md:w-35 md:h-12 object-contain" src="/front/images/SilverHappy_logo.png" alt="logo">

        <nav class="md:flex items-center gap-6 lg:gap-8">
            <a href="/front/index.php" class="menu-text" data-i18n="nav_home">Accueil</a>
            <a href="/front/services/menu_activity.php" class="menu-text" data-i18n="nav_activities">Activités</a>
            <a href="/front/services/products.php" class="menu-text" data-i18n="nav_shop">Boutique</a>
            <a href="/front/communication/list_contact.php" class="menu-text" data-i18n="nav_messages">Messagerie</a>
            <a href="/front/services/subscription.php" class="menu-text" data-i18n="nav_subscribe">S'abonner</a>
        </nav>

        <div class="w-full md:w-auto flex-1 max-w-md">
            <form action="/front/search.php" method="GET" class="w-full m-0 p-0 relative">
                <input type="text"
                    name="q"
                    required
                    data-i18n-placeholder="nav_search"
                    placeholder="Faire une recherche..."
                    class="focus:outline-none w-full border border-[#1C5B8F] rounded-full px-6 py-2 hover:border-[#E1AB2B] focus:border-[#E1AB2B] placeholder:text-[#1C5B8F] text-xl">
                <button type="submit" class="hidden">Chercher</button>
            </form>
        </div>
    </div>

    <div id="modal_urgence" class="hidden fixed inset-0 bg-black bg-opacity-60 z-[200] flex items-center justify-center p-4">
        <div class="bg-white p-6 rounded-lg w-full max-w-md border-2 border-red-500 text-center">
            <h2 class="text-2xl font-bold text-red-600 mb-2">Besoin d'aide ?</h2>
            <p class="mb-6 text-gray-600">Appelez les secours immédiatement :</p>
            <div class="flex flex-col gap-3">
                <a href="tel:15" class="bg-blue-100 border border-blue-400 text-blue-800 text-xl font-bold py-3 rounded hover:bg-blue-200">🚑 15 - SAMU</a>
                <a href="tel:18" class="bg-red-100 border border-red-400 text-red-800 text-xl font-bold py-3 rounded hover:bg-red-200">🚒 18 - Pompiers</a>
                <a href="tel:112" class="bg-gray-100 border border-gray-400 text-gray-800 text-xl font-bold py-3 rounded hover:bg-gray-200">📞 112 - Urgences</a>
            </div>
            <button id="btn_fermer_urgence" class="mt-6 px-6 py-2 bg-gray-200 text-gray-800 rounded font-bold hover:bg-gray-300">Annuler</button>
        </div>
    </div>

    <script>
        const zoomLevels = [1, 1.2, 1.4];
        let currentZoomIndex = 0;
        function toggleZoom() {
            currentZoomIndex = (currentZoomIndex + 1) % zoomLevels.length;
            document.body.style.zoom = zoomLevels[currentZoomIndex];
        }

        async function changeLanguage(lang) {
            localStorage.setItem('user_lang', lang);
            document.documentElement.lang = lang;
            const flagImg = document.getElementById('current-lang-flag');
            if (flagImg) flagImg.src = lang === 'fr' ? '/front/icons/france.png' : '/front/icons/angleterre.png';

            try {
                const response = await fetch(`/locales/${lang}.json`);
                if (!response.ok && lang === 'fr') return location.reload();
                const translations = await response.json();

                document.querySelectorAll('[data-i18n]').forEach(el => {
                    if (translations[el.dataset.i18n]) el.innerHTML = translations[el.dataset.i18n];
                });
                document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
                    if (translations[el.dataset.i18nPlaceholder]) el.placeholder = translations[el.dataset.i18nPlaceholder];
                });
            } catch (err) { console.error(err); }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            const savedLang = localStorage.getItem('user_lang');
            if (savedLang && savedLang !== 'fr') changeLanguage(savedLang);

            const btnUrgence = document.getElementById('btn_urgence');
            const modalUrgence = document.getElementById('modal_urgence');
            const btnFermer = document.getElementById('btn_fermer_urgence');

            if(btnUrgence && modalUrgence && btnFermer) {
                btnUrgence.addEventListener('click', () => modalUrgence.classList.remove('hidden'));
                btnFermer.addEventListener('click', () => modalUrgence.classList.add('hidden'));
            }

            const btnLogout = document.getElementById('btn_logout');
            if (btnLogout) {
                btnLogout.addEventListener('click', async (e) => {
                    e.preventDefault();
                    try {
                        const res = await fetch(`${window.API_BASE_URL}/auth/logout`, { method: 'POST', credentials: 'include' });
                        if (res.ok) window.location.href = "/front/index.php";
                    } catch (err) { console.error(err); }
                });
            }

            try {
                const res = await fetch(`${window.API_BASE_URL}/auth/me`, { credentials: 'include' });
                if (!res.ok) return;
                
                const user = await res.json();
                if (user.statut === 'admin') return window.location.href = "../../back/dashboard.php";
                if (user.statut === 'banni') return window.location.href = "/front/ban.php";

                window.userData = user;
                window.currentUserId = user.id;
                window.dispatchEvent(new Event('auth_ready'));
            } catch (err) { console.error(err); }
        });

        function readALoud() {
            if ('speechSynthesis' in window) {
                
                if (window.speechSynthesis.speaking) {
                    window.speechSynthesis.cancel();
                    return;
                }


                let texteALire = document.body.innerText; 
                
                let message = new SpeechSynthesisUtterance(texteALire);
                message.lang = 'fr-FR'; 
                message.rate = 1;     
                message.pitch = 1;

                window.speechSynthesis.speak(message);
                
            } else {
                alert("Désolé, votre navigateur ne supporte pas la lecture vocale.");
            }
        }
    </script>
</header>