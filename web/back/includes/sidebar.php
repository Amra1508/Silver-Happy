<?php
if (!isset($_COOKIE['session_token']) || empty($_COOKIE['session_token'])) {
    header("Location: ../login.php");
    exit();
}
?>

<aside class="w-80 h-screen sticky top-0 bg-[#0F3452] flex flex-col text-white">
    <div class="p-4">
        <img src="/back/icons/SilverHappy_logo.png" alt="logo" class="w-full object-contain">
    </div>

    <div class="my-2">
        <a href="/back/dashboard.php" class="border-t-2 sidebar-links">
            <img src="/back/icons/tableau_bord.svg" alt="dashboard" class="w-7 h-7 mx-4">
            <span>Tableau de bord</span>
        </a>

        <a href="/back/users/providers.php" class="sidebar-links">
            <img src="/back/icons/prestataires.svg" alt="prestataire" class="w-7 h-7 mx-4">
            <span>Prestataires</span>
        </a>

        <a href="/back/users/seniors.php" class="sidebar-links">
            <img src="/back/icons/seniors.svg" alt="seniors" class="w-7 h-7 mx-4">
            <span>Seniors</span>
        </a>

        <a href="/back/services/catalog.php" class="sidebar-links">
            <img src="/back/icons/catalogue.svg" alt="catalogue" class="w-7 h-7 mx-4">
            <span>Catalogue des services</span>
        </a>

        <a href="/back/services/events.php" class="sidebar-links">
            <img src="/back/icons/evenements.svg" alt="evenements" class="w-7 h-7 mx-4">
            <span>Évènements & Planning</span>
        </a>

        <a href="/back/services/products.php" class="sidebar-links">
            <img src="/back/icons/articles.svg" alt="articles" class="w-7 h-7 mx-4">
            <span>Produits de la boutique</span>
        </a>

        <a href="/back/communication/list_user.php" class="sidebar-links">
            <img src="/back/icons/messages.svg" alt="messages" class="w-7 h-7 mx-4">
            <span>Messagerie</span>
        </a>

        <a href="/back/communication/newsletter.php" class="sidebar-links">
            <img src="/back/icons/newsletter.svg" alt="newsletter" class="w-7 h-7 mx-4">
            <span>Newsletter</span>
        </a>

        <a href="/back/captcha.php" class="sidebar-links">
            <img src="/back/icons/captcha.svg" alt="captcha" class="w-7 h-7 mx-4">
            <span>Captcha</span>
        </a>

        <a href="/back/communication/advice.php" class="border-b-2 sidebar-links">
            <img src="/back/icons/conseils.svg" alt="conseils" class="w-7 h-7 mx-4">
            <span>Conseils</span>
        </a>

    </div>

    <div class="">
        <a id="btn_logout" class="flex items-center px-6 py-2 hover:bg-[#1C5B8F]">
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