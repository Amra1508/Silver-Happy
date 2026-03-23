<header>
    <style type="text/tailwindcss">
        <?php include("front.css") ?>
    </style>

    <div class="bg-[#E1AB2B]/60 py-2 px-6 flex justify-end gap-4">
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
        
        <button class="border border-[#AA1114] text-[#AA1114] px-4 py-1 rounded-full text-xl font-semibold hover:bg-[#AA1114] hover:text-white" data-i18n="nav_emergency">Urgence</button>

        <button onclick="toggleZoom()" class="header-button transition-all group" title="Modifier la taille du texte">
            <img src="/front/icons/zoom.svg" alt="zoom" class="w-7 h-7 object-contain transition-all group-hover:brightness-0 group-hover:invert">
        </button>

        <div class="relative group z-[100]">
            <button class="flex items-center gap-2 header-button transition-all">
                <img id="current-lang-flag" src="/front/icons/france.png" alt="Lang" class="h-6 w-6 object-contain">
                <img src="/front/icons/dropdown.svg" alt="dropdown" class="w-5 h-5 object-contain transition-all group-hover:brightness-0 group-hover:invert">
            </button>
            <div class="absolute right-0 mt-1 w-32 bg-white rounded-md shadow-lg hidden group-hover:block overflow-hidden border border-gray-100">
                <button onclick="changeLanguage('fr')" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-[#1C5B8F] hover:text-white transition-colors">
                    <img src="/front/icons/france.png" class="h-4 w-4"> Français
                </button>
                <button onclick="changeLanguage('en')" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-[#1C5B8F] hover:text-white transition-colors">
                    <img src="/front/icons/angleterre.png" class="h-4 w-4"> English
                </button>
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
            <input type="text"
                data-i18n-placeholder="nav_search"
                placeholder="Rechercher..."
                class="focus:outline-none w-full border border-[#1C5B8F] rounded-full px-6 py-2 
                      hover:placeholder:text-[#E1AB2B] hover:border-[#E1AB2B] focus:placeholder:text-[#E1AB2B] focus:border-[#E1AB2B] 
                      placeholder:text-[#1C5B8F] placeholder:text-2xl placeholder:font-medium lg:placeholder:text-3xl text-xl">
        </div>
    </div>

    <script>
        const zoomLevels = [1, 1.2, 1.4];
        let currentZoomIndex = 0;

        function toggleZoom() {
            currentZoomIndex++;
            if (currentZoomIndex >= zoomLevels.length) {
                currentZoomIndex = 0;
            }
            applyZoom(zoomLevels[currentZoomIndex]);
        }

        function applyZoom(level) {
            document.body.style.zoom = level;
            if (navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
                document.body.style.transform = `scale(${level})`;
                document.body.style.transformOrigin = 'top left';
                document.body.style.width = `${100 / level}%`;
            }
        }

        async function changeLanguage(lang) {
            localStorage.setItem('user_lang', lang);
            document.documentElement.lang = lang;

            const flagImg = document.getElementById('current-lang-flag');
            if (flagImg) {
                flagImg.src = lang === 'fr' ? '/front/icons/france.png' : '/front/icons/angleterre.png';
            }

            try {
                const response = await fetch(`/locales/${lang}.json`);
                
                if (!response.ok && lang === 'fr') {
                    location.reload();
                    return;
                }
                if (!response.ok) throw new Error(`Fichier ${lang}.json introuvable.`);
                
                const translations = await response.json();

                document.querySelectorAll('[data-i18n]').forEach(el => {
                    const key = el.getAttribute('data-i18n');
                    if (translations[key]) el.innerHTML = translations[key];
                });

                document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
                    const key = el.getAttribute('data-i18n-placeholder');
                    if (translations[key]) el.placeholder = translations[key];
                });

            } catch (error) {
                console.error("Erreur de traduction :", error);
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            
            const savedLang = localStorage.getItem('user_lang');
            if (savedLang && savedLang !== 'fr') {
                changeLanguage(savedLang);
            }

            const btnLogout = document.getElementById('btn_logout');

            if (btnLogout) {
                btnLogout.addEventListener('click', async (e) => {
                    e.preventDefault();
                    try {
                        const response = await fetch('http://localhost:8082/auth/logout', {
                            method: 'POST',
                            credentials: 'include'
                        });

                        if (response.ok) {
                            window.location.href = "/front/index.php";
                        } else {
                            alert("Erreur lors de la déconnexion.");
                        }
                    } catch (error) {
                        console.error("Erreur réseau lors de la déconnexion :", error);
                    }
                });
            }

            try {
                const response = await fetch('http://localhost:8082/auth/me', {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include'
                });

                if (!response.ok) {
                    throw new Error("Utilisateur non connecté ou session invalide");
                }

                const user = await response.json();

                if (user.statut === 'admin') {
                    window.location.href = "../../back/dashboard.php";
                    return;
                }

                window.currentUserId = user.id;
                window.isSubscribed = (user.id_abonnement !== null && user.id_abonnement !== undefined && user.id_abonnement !== 0);
                window.dispatchEvent(new Event('auth_ready'));

                const nameDisplay = document.getElementById('header-user-name');
                if (nameDisplay) {
                    nameDisplay.textContent = `${user.prenom} ${user.nom.toUpperCase()}`;
                }

            } catch (error) {
                console.error("Erreur d'authentification :", error);
            }
        });
    </script>
</header>