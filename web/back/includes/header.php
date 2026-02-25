<header>
    <style type="text/tailwindcss">
        <?php include("back.css") ?>
    </style>

    <div class="bg-[#D9D9D9]/40 py-4 px-4 md:px-8 flex items-center justify-between gap-2 md:gap-8">
        <div class="flex-1 max-w-md">
            <input type="text"
                placeholder="Rechercher..."
                class="focus:outline-none w-full bg-[#D9D9D9]/10 border border-[#1C5B8F] rounded-full px-4 md:px-6 py-2 
                   hover:placeholder:text-[#E1AB2B] hover:border-[#E1AB2B] focus:placeholder:text-[#E1AB2B] focus:border-[#E1AB2B] 
                   placeholder:text-[#1C5B8F] placeholder:text-lg md:placeholder:text-xl placeholder:font-semibold text-lg md:text-xl">
        </div>

        <div class="flex items-center gap-3 md:gap-8 shrink-0">
            <img src="/back/icons/notifications.png" alt="notifications" class="w-6 h-6 md:w-7 md:h-7 object-contain">
            <div class="flex items-center gap-2 text-[#1C5B8F] font-semibold text-sm md:text-xl">
                <img src="/back/icons/utilisateur.png" alt="utilisateur" class="w-6 h-6 md:w-7 md:h-7 object-contain">
                <span class="hidden sm:inline whitespace-nowrap">Prénom NOM</span>
            </div>
        </div>
    </div>
</header>