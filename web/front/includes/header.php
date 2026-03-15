<header>
    <style type="text/tailwindcss">
        <?php include("front.css") ?>
    </style>

    <div class="bg-[#E1AB2B]/60 py-2 px-6 flex justify-end gap-4">
        <?php if (isset($_COOKIE['session_token'])): ?>
            <button id="btn_logout" class="header-button">Déconnexion</button>
            <a href="/front/account/profile.php">
                <button class="header-button">Mon compte</button>
            </a>
        <?php else: ?>
            <a href="/front/account/signin.php">
                <button class="header-button">Se connecter</button>
            </a>
            <a href="/front/account/signup.php">
                <button class="header-button">S'inscrire</button>
            </a>
        <?php endif; ?>
        <button class="border border-[#AA1114] text-[#AA1114] px-4 py-1 rounded-full text-xl font-semibold hover:bg-[#AA1114] hover:text-white">Urgence</button>

        <button onclick="toggleZoom()" class="header-button transition-all group" title="Modifier la taille du texte">
            <img src="/front/icons/zoom.svg" alt="zoom" class="w-7 h-7 object-contain transition-all group-hover:brightness-0 group-hover:invert">
        </button>

        <button class="flex items-center gap-2 header-button transition-all group">
            <img src="/front/icons/france.png" alt="french" class="h-6 w-6 object-contain">
            <img src="/front/icons/dropdown.svg" alt="dropdown" class="w-5 h-5 object-contain transition-all group-hover:brightness-0 group-hover:invert">
        </button>
    </div>

    <div class="border border-[#D4D4D4] flex flex-col md:flex-row items-center justify-between gap-4 md:gap-8 px-4 py-4 md:px-6">
        <img class="w-30 h-12 md:w-35 md:h-12 object-contain" src="/front/images/SilverHappy_logo.png" alt="logo">

        <nav class="md:flex items-center gap-6 lg:gap-8">
            <a href="/front/index.php" class="menu-text">Accueil</a>
            <a href="/front/services/menu_activity.php" class="menu-text">Activités</a>
            <a href="/front/services/products.php" class="menu-text">Boutique</a>
            <a href="/front/communication/list_contact.php" class="menu-text">Messagerie</a>
            <a href="/front/services/subscription.php" class="menu-text">S'abonner</a>
        </nav>

        <div class="w-full md:w-auto flex-1 max-w-md">
            <input type="text"
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

        document.addEventListener('DOMContentLoaded', async () => {
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
                    headers: {
                        'Content-Type': 'application/json'
                    },
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
                window.dispatchEvent(new Event('auth_ready'));

                const nameDisplay = document.getElementById('header-user-name');
                if (nameDisplay) {
                    nameDisplay.textContent = `${user.prenom} ${user.nom.toUpperCase()}`;
                }

                if (user.premiere_connexion === 1) {
                    const tourOverlay = document.getElementById('tour-overlay');
                    const tourDialog = document.getElementById('tour-dialog');
                    const tCounter = document.getElementById('tour-step-counter');
                    const tTitle = document.getElementById('tour-title');
                    const tText = document.getElementById('tour-text');
                    const btnNext = document.getElementById('tour-next');
                    const btnPrev = document.getElementById('tour-prev');
                    const btnClose = document.getElementById('tour-close');

                    const steps = [{
                            selector: 'a[href="/front/account/profile.php"] button',
                            title: 'Votre compte personnel',
                            text: 'Ce bouton vous donne accès à vos informations personnelles, la gestion de votre abonnement et l\'historique de vos activités.'
                        },
                        {
                            selector: 'button[class*="text-[#AA1114]"]',
                            title: 'Bouton d\'urgence',
                            text: 'En cas de nécessité, ce bouton d\'urgence vous permet de contacter immédiatement une assistance. Il reste visible sur toutes les pages.'
                        },
                        {
                            selector: 'nav',
                            title: 'Menu de navigation',
                            text: 'Utilisez ces liens pour consulter les différentes rubriques du site : catalogue des activités, boutique et messagerie sécurisée.'
                        },
                        {
                            selector: 'input[placeholder="Rechercher..."]',
                            title: 'Barre de recherche',
                            text: 'Saisissez un terme dans ce champ pour trouver rapidement un prestataire, un service ou une activité spécifique.'
                        },
                        {
                            selector: 'button[title="Modifier la taille du texte"]',
                            title: 'Ajustement de l\'affichage',
                            text: 'Pour un meilleur confort de lecture, cliquez sur cette icône en forme de loupe afin d\'agrandir les textes de l\'interface.'
                        },
                        {
                            selector: 'main a.button-blue',
                            title: 'Découverte des services',
                            text: 'Vous pouvez cliquer sur ce bouton pour parcourir l\'ensemble de nos offres.'
                        },
                        {
                            selector: 'footer',
                            title: 'Informations complémentaires',
                            text: 'Le pied de page contient les mentions légales, les conditions d\'utilisation, ainsi que nos coordonnées de contact.'
                        }
                    ];

                    let currentStep = 0;
                    let highlightedElement = null;

                    function highlightElement(selector) {
                        if (highlightedElement) {
                            highlightedElement.classList.remove('relative', 'z-[90]', 'ring-4', 'ring-[#E1AB2B]', 'bg-white');
                        }

                        const el = document.querySelector(selector);
                        if (el) {
                            el.classList.add('relative', 'z-[90]', 'ring-4', 'ring-[#E1AB2B]', 'bg-white', 'transition-all', 'duration-300');
                            el.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                            highlightedElement = el;
                        }
                    }

                    function updateTour() {
                        const step = steps[currentStep];

                        tCounter.textContent = `Étape ${currentStep + 1} sur ${steps.length}`;
                        tTitle.textContent = step.title;
                        tText.textContent = step.text;

                        highlightElement(step.selector);

                        btnPrev.classList.toggle('invisible', currentStep === 0);

                        if (currentStep === steps.length - 1) {
                            btnNext.classList.add('hidden');
                            btnClose.classList.remove('hidden');
                        } else {
                            btnNext.classList.remove('hidden');
                            btnClose.classList.add('hidden');
                        }
                    }

                    if (tourOverlay && tourDialog) {
                        setTimeout(() => {
                            tourOverlay.classList.remove('hidden');
                            tourDialog.classList.remove('hidden');
                            updateTour();
                        }, 500);

                        btnNext.addEventListener('click', (e) => {
                            e.preventDefault();
                            if (currentStep < steps.length - 1) {
                                currentStep++;
                                updateTour();
                            }
                        });

                        btnPrev.addEventListener('click', (e) => {
                            e.preventDefault();
                            if (currentStep > 0) {
                                currentStep--;
                                updateTour();
                            }
                        });

                        const endTour = async (e) => {
                            if (e) e.preventDefault();
                            tourOverlay.classList.add('hidden');
                            tourDialog.classList.add('hidden');

                            if (highlightedElement) {
                                highlightedElement.classList.remove('relative', 'z-[90]', 'ring-4', 'ring-[#E1AB2B]', 'bg-white');
                            }

                            try {
                                await fetch('http://localhost:8082/auth/tutorial-seen', {
                                    method: 'POST',
                                    credentials: 'include'
                                });
                            } catch (err) {
                                console.error("Erreur de validation du tutoriel :", err);
                            }
                        };

                        btnClose.addEventListener('click', endTour);
                    }
                }

            } catch (error) {
                console.error("Erreur d'authentification :", error);
            }
        });
    </script>
</header>