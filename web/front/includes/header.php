<header>
    <style type="text/tailwindcss">
        <?php include("front.css") ?>
    </style>

    <div class="bg-[#E1AB2B]/60 py-2 px-6 flex justify-end gap-4">
        <?php if (isset($_COOKIE['session_token'])): ?>
            <button id="btn_logout" class="header-button">Déconnexion</button>
        <?php else: ?>
            <a href="/front/account/signin.php">
                <button class="header-button">Se connecter</button>
            </a>
            <a href="/front/account/signup.php">
                <button class="header-button">S'inscrire</button>
            </a>
        <?php endif; ?>
        <button class="border border-[#AA1114] text-[#AA1114] px-4 py-1 rounded-full text-xl font-semibold hover:bg-[#AA1114] hover:text-white">Urgence</button>
        <button class="header-button transition-all group">
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
            <a href="#" class="menu-text">Activités</a>
            <a href="#" class="menu-text">Boutique</a>
            <a href="#" class="menu-text">Messagerie</a>
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