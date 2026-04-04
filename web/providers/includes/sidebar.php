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
        
        <a href="/front/providers/dashboard.php" class="flex items-center gap-3 px-4 py-3 bg-white/10 text-[#E1AB2B] rounded-xl font-semibold transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            Tableau de bord
        </a>

        <a href="/front/providers/prestations.php" class="flex items-center gap-3 px-4 py-3 text-gray-200 hover:bg-white/5 hover:text-white rounded-xl transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            Mes services
        </a>

        <a href="/front/providers/planning.php" class="flex items-center gap-3 px-4 py-3 text-gray-200 hover:bg-white/5 hover:text-white rounded-xl transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Planning & Résas
            <span class="ml-auto bg-[#E1AB2B] text-[#1C5B8F] text-xs font-bold px-2 py-0.5 rounded-full">3</span>
        </a>

        <a href="/front/providers/messages.php" class="flex items-center gap-3 px-4 py-3 text-gray-200 hover:bg-white/5 hover:text-white rounded-xl transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            Messagerie
        </a>

        <a href="/front/providers/avis.php" class="flex items-center gap-3 px-4 py-3 text-gray-200 hover:bg-white/5 hover:text-white rounded-xl transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
            Mes avis
        </a>

        <a href="/front/providers/factures.php" class="flex items-center gap-3 px-4 py-3 text-gray-200 hover:bg-white/5 hover:text-white rounded-xl transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Facturation
        </a>

    </nav>

    <div class="p-4 border-t border-white/10 mt-auto">
        <a href="/front/providers/profil.php" class="flex items-center gap-3 px-4 py-3 text-gray-200 hover:bg-white/5 hover:text-white rounded-xl transition-all mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            Mon Profil
        </a>
        <button id="btn-logout-provider" class="w-full flex items-center gap-3 px-4 py-3 text-red-300 hover:bg-red-500/20 hover:text-red-200 rounded-xl transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
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