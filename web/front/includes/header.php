<header>
    <div class="bg-[#E1AB2B]/60 py-2 px-6 flex justify-end gap-4">
        <a href="../signin.php">
            <button class="border border-[#1C5B8F] text-[#1C5B8F] px-4 py-1 rounded-full text-xl font-semibold hover:bg-[#1C5B8F] hover:text-white">Se connecter</button>
        </a>
        <a href="../signup.php">
            <button class="border border-[#1C5B8F] text-[#1C5B8F] px-4 py-1 rounded-full text-xl font-semibold hover:bg-[#1C5B8F] hover:text-white">S'inscrire</button>
        </a>
        <button class="border border-[#AA1114] text-[#AA1114] px-4 py-1 rounded-full text-xl font-semibold hover:bg-[#AA1114] hover:text-white">Urgence</button>
        <button class="border border-[#1C5B8F] text-[#1C5B8F] px-4 py-1 rounded-full text-xl font-semibold hover:bg-[#1C5B8F] hover:text-white transition-all group">
            <img src="/icones/zoom.png" alt="zoom" class="w-7 h-7 object-contain transition-all group-hover:brightness-0 group-hover:invert">
        </button>
        <button class="flex items-center gap-2 border border-[#1C5B8F] text-[#1C5B8F] px-3 py-1 rounded-full hover:bg-[#1C5B8F] hover:text-white transition-all group">
            <img src="/icones/france.png" alt="french" class="h-6 w-6 object-contain">
            <img src="/icones/dropdown.png" alt="dropdown" class="w-5 h-5 object-contain transition-all group-hover:brightness-0 group-hover:invert">
        </button>
    </div>

    <div class="flex flex-col md:flex-row items-center justify-between gap-4 md:gap-8 px-4 py-4 md:px-6">

        <img class="w-30 h-12 md:w-35 md:h-12 object-contain" src="/images/SilverHappy_logo.png" alt="logo">

        <nav class="md:flex items-center gap-6 lg:gap-8 text-[#1C5B8F] font-medium text-2xl lg:text-3xl">
            <a href="../index.php" class="hover:text-[#E1AB2B] transition-colors">Accueil</a>
            <a href="#" class="hover:text-[#E1AB2B] transition-colors">Activités</a>
            <a href="#" class="hover:text-[#E1AB2B] transition-colors">Boutique</a>
            <a href="#" class="hover:text-[#E1AB2B] transition-colors">Messagerie</a>
        </nav>

        <div class="w-full md:w-auto flex-1 max-w-md">
            <input type="text"
                placeholder="Rechercher..."
                class="w-full border border-[#1C5B8F] rounded-full px-6 py-2 
                      placeholder:text-[#1C5B8F] placeholder:text-2xl lg:placeholder:text-3xl">
        </div>

    </div>
</header>