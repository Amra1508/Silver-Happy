<aside class="w-64 bg-[#1C5B8F] text-white min-h-screen flex flex-col shadow-xl font-sans shrink-0 sticky top-0">
    
    <div class="p-6 flex flex-col items-center justify-center border-b border-white/10">
        <h2 class="text-2xl font-bold tracking-wider text-center mb-2">
            Silver<span class="text-[#E1AB2B]">Happy</span><br>
            <span class="text-sm font-normal text-gray-300">Espace Pro</span>
        </h2>
        
        <div id="provider-name-display" class="mt-4 px-4 py-2 bg-white/10 rounded-lg text-center w-full">
            <span class="text-sm text-gray-300">Chargement...</span>
        </div>

        <div id="provider-status-badge" class="mt-2 text-xs px-3 py-1 rounded-full hidden"></div>
    </div>

    <nav id="sidebar-nav" class="flex-1 px-4 py-6 space-y-2 overflow-y-auto hidden">
        
        <a href="/providers/index.php" class="flex items-center gap-3 px-4 py-3 bg-white/10 text-[#E1AB2B] rounded-xl font-semibold transition-all">
            Tableau de bord
        </a>

        <a href="/front/providers/prestations.php" class="flex items-center gap-3 px-4 py-3 text-gray-200 hover:bg-white/5 hover:text-white rounded-xl transition-all">
            Mes services
        </a>

        <a href="/front/providers/planning.php" class="flex items-center gap-3 px-4 py-3 text-gray-200 hover:bg-white/5 hover:text-white rounded-xl transition-all">
            Planning & Résas
            <span class="ml-auto bg-[#E1AB2B] text-[#1C5B8F] text-xs font-bold px-2 py-0.5 rounded-full">3</span>
        </a>

        <a href="/front/providers/messages.php" class="flex items-center gap-3 px-4 py-3 text-gray-200 hover:bg-white/5 hover:text-white rounded-xl transition-all">
            Messagerie
        </a>

        <a href="/front/providers/avis.php" class="flex items-center gap-3 px-4 py-3 text-gray-200 hover:bg-white/5 hover:text-white rounded-xl transition-all">
            Mes avis
        </a>

        <a href="/front/providers/factures.php" class="flex items-center gap-3 px-4 py-3 text-gray-200 hover:bg-white/5 hover:text-white rounded-xl transition-all">
            Facturation
        </a>

    </nav>

    <div class="p-4 border-t border-white/10 mt-auto">
        <a href="/providers/account/profile.php" class="flex items-center gap-3 px-4 py-3 text-gray-200 hover:bg-white/5 hover:text-white rounded-xl transition-all mb-2">
            Mon Profil
        </a>
        <button id="btn-logout-provider" class="w-full flex items-center gap-3 px-4 py-3 text-red-300 hover:bg-red-500/20 hover:text-red-200 rounded-xl transition-all">
            Déconnexion
        </button>
    </div>

    </aside>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const providerNameDisplay = document.getElementById('provider-name-display');
        const badgeStatus = document.getElementById('provider-status-badge');
        
        const navSidebar = document.getElementById('sidebar-nav');
        const contentValide = document.getElementById('main-content-valide');
        const contentAttente = document.getElementById('main-content-attente');
        const contentRefuse = document.getElementById('main-content-refuse');

        try {
            const response = await fetch('http://localhost:8082/auth/me-provider', {
                method: 'GET',
                credentials: 'include' 
            });

            if (response.ok) {
                const data = await response.json();
                
                providerNameDisplay.innerHTML = `<span class="font-bold text-[#E1AB2B]">${data.prenom} ${data.nom}</span><br><span class="text-xs text-gray-300">${data.categorie_nom || 'Pro'}</span>`;

                const status = data.status ? data.status.toLowerCase() : 'en attente';

                if (status === 'validé' || status === 'valide') {
                    navSidebar.classList.remove('hidden'); 
                    if(contentValide) contentValide.classList.remove('hidden');
                    
                    badgeStatus.textContent = "Compte Validé";
                    badgeStatus.className = "mt-2 text-[10px] px-2 py-0.5 rounded-full bg-green-500/20 text-green-300 border border-green-500/30 uppercase tracking-wide font-bold";
                    badgeStatus.classList.remove('hidden');

                } else if (status === 'en attente') {
                    if(contentAttente) contentAttente.classList.remove('hidden');
                    
                    badgeStatus.textContent = "En attente";
                    badgeStatus.className = "mt-2 text-[10px] px-2 py-0.5 rounded-full bg-yellow-500/20 text-yellow-300 border border-yellow-500/30 uppercase tracking-wide font-bold";
                    badgeStatus.classList.remove('hidden');

                } else if (status === 'refusé' || status === 'refuse') {
                    if(contentRefuse) {
                        contentRefuse.classList.remove('hidden');
                        document.getElementById('motif-refus-text').textContent = "Motif : " + (data.motif_refus || "Non précisé");
                    }
                    
                    badgeStatus.textContent = "Refusé";
                    badgeStatus.className = "mt-2 text-[10px] px-2 py-0.5 rounded-full bg-red-500/20 text-red-300 border border-red-500/30 uppercase tracking-wide font-bold";
                    badgeStatus.classList.remove('hidden');
                }

            } else {
                window.location.href = "/front/providers/account/signin.php"; 
            }
        } catch (error) {
            console.error("Serveur inaccessible :", error);
            window.location.href = "/front/providers/account/signin.php";
        }

        const btnLogout = document.getElementById('btn-logout-provider');
        if (btnLogout) {
            btnLogout.addEventListener('click', async () => {
                await fetch('http://localhost:8082/auth/logout-provider', {
                    method: 'POST',
                    credentials: 'include' 
                });
                window.location.href = "/providers/account/signin.php";
            });
        }
    });
</script>