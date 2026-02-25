<aside class="w-80 min-h-screen bg-[#0F3452] flex flex-col text-white">
    <div class="p-4">
        <img src="/front/images/SilverHappy_logo.png" alt="logo" class="w-full object-contain">
    </div>

    <div class="my-2">
        <a href="dashboard.php" class="border-t-2 sidebar-links">
            <img src="/back/icons/tableau_bord.png" alt="dashboard" class="w-7 h-7 mx-4">
            <span>Tableau de bord</span>
        </a>

        <a href="#" class="sidebar-links">
            <img src="/back/icons/prestataires.png" alt="prestataire" class="w-7 h-7 mx-4">
            <span>Prestataires</span>
        </a>

        <a href="#" class="sidebar-links">
            <img src="/back/icons/seniors.png" alt="seniors" class="w-7 h-7 mx-4">
            <span>Seniors</span>
        </a>

        <a href="#" class="sidebar-links">
            <img src="/back/icons/catalogue.png" alt="catalogue" class="w-7 h-7 mx-4">
            <span>Catalogue des services</span>
        </a>

        <a href="#" class="sidebar-links">
            <img src="/back/icons/evenements.png" alt="evenements" class="w-7 h-7 mx-4">
            <span>Évènements & Planning</span>
        </a>

        <a href="#" class="sidebar-links">
            <img src="/back/icons/articles.png" alt="articles" class="w-7 h-7 mx-4">
            <span>Produits de la boutique</span>
        </a>

        <a href="#" class="sidebar-links">
            <img src="/back/icons/messages.png" alt="messages" class="w-7 h-7 mx-4">
            <span>Messagerie</span>
        </a>

        <a href="#" class="sidebar-links">
            <img src="/back/icons/newsletter.png" alt="newsletter" class="w-7 h-7 mx-4">
            <span>Newsletter</span>
        </a>

        <a href="captcha.php" class="border-b-2 sidebar-links">
            <img src="/back/icons/captcha.png" alt="dashboard" class="w-7 h-7 mx-4">
            <span>Captcha</span>
        </a>

    </div>

    <div class="">
        <a id="btn_logout" class="flex items-center px-6 py-4 hover:bg-[#1C5B8F]">
            <span class="text-lg">Déconnexion</span>
        </a>
    </div>

    <script>
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
                        window.location.replace("/front/index.php");
                    } else {
                        alert("Erreur lors de la déconnexion.");
                    }
                } catch (error) {
                    console.error("Erreur réseau :", error);
                }
            });
        }
    </script>

</aside>