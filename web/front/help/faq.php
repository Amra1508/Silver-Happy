<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Silver Happy</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alata&display=swap');
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Alata', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

    <?php include("../includes/header.php") ?>

    <main class="flex-grow max-w-4xl mx-auto w-full px-6 py-16">
        
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-[#1C5B8F] mb-4">Foire Aux Questions</h1>
            <p class="text-lg text-gray-600">Vous avez des questions sur Silver Happy ? Nous avons les réponses.</p>
        </div>

        <div class="space-y-4" id="faq-container">
            
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm transition-all">
                <button class="faq-button w-full px-6 py-5 text-left flex justify-between items-center focus:outline-none">
                    <span class="font-bold text-[#1C5B8F] text-lg">Qu'est-ce que Silver Happy ?</span>
                    <svg class="w-6 h-6 text-[#E1AB2B] transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div class="faq-answer hidden px-6 pb-6 text-gray-600 leading-relaxed">
                    Silver Happy est une plateforme dédiée à l'épanouissement et au bien-être des seniors. Nous proposons des conseils pratiques, l'accès à des événements exclusifs (ateliers, rencontres, sorties) ainsi qu'une sélection de produits adaptés à vos besoins.
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm transition-all">
                <button class="faq-button w-full px-6 py-5 text-left flex justify-between items-center focus:outline-none">
                    <span class="font-bold text-[#1C5B8F] text-lg">Dois-je obligatoirement être abonné(e) pour utiliser le site ?</span>
                    <svg class="w-6 h-6 text-[#E1AB2B] transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div class="faq-answer hidden px-6 pb-6 text-gray-600 leading-relaxed">
                    Non, la consultation d'une grande partie de nos conseils et de notre boutique est libre. Cependant, souscrire à un abonnement Silver Happy vous donne accès à des tarifs préférentiels, à la réservation d'événements privés et à des contenus exclusifs.
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm transition-all">
                <button class="faq-button w-full px-6 py-5 text-left flex justify-between items-center focus:outline-none">
                    <span class="font-bold text-[#1C5B8F] text-lg">Comment retrouver mes factures (abonnements, commandes, événements) ?</span>
                    <svg class="w-6 h-6 text-[#E1AB2B] transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div class="faq-answer hidden px-6 pb-6 text-gray-600 leading-relaxed">
                    Toutes vos factures sont regroupées au même endroit. Connectez-vous à votre compte, rendez-vous sur votre profil, puis cliquez sur <strong>"Mes Factures"</strong>. Vous pourrez y consulter et télécharger les reçus de vos abonnements, de vos achats en boutique et de vos inscriptions aux événements.
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm transition-all">
                <button class="faq-button w-full px-6 py-5 text-left flex justify-between items-center focus:outline-none">
                    <span class="font-bold text-[#1C5B8F] text-lg">Les paiements sur la plateforme sont-ils sécurisés ?</span>
                    <svg class="w-6 h-6 text-[#E1AB2B] transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div class="faq-answer hidden px-6 pb-6 text-gray-600 leading-relaxed">
                    Absolument. Tous les paiements réalisés sur Silver Happy sont traités par <strong>Stripe</strong>, l'un des leaders mondiaux du paiement en ligne sécurisé. Vos données bancaires sont chiffrées de bout en bout et ne transitent jamais directement par nos serveurs.
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm transition-all">
                <button class="faq-button w-full px-6 py-5 text-left flex justify-between items-center focus:outline-none">
                    <span class="font-bold text-[#1C5B8F] text-lg">Comment puis-je contacter le service client ?</span>
                    <svg class="w-6 h-6 text-[#E1AB2B] transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div class="faq-answer hidden px-6 pb-6 text-gray-600 leading-relaxed">
                    Si vous ne trouvez pas la réponse à votre question, vous pouvez nous contacter directement via notre formulaire de contact, ou par e-mail à l'adresse <strong>contact@silverhappy.fr</strong>. Notre équipe se fera un plaisir de vous répondre dans les plus brefs délais.
                </div>
            </div>

        </div>

    </main>

    <?php include("../includes/footer.php") ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const faqButtons = document.querySelectorAll('.faq-button');

            faqButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const answer = button.nextElementSibling;
                    const icon = button.querySelector('svg');

                    const isOpen = !answer.classList.contains('hidden');

                    document.querySelectorAll('.faq-answer').forEach(el => el.classList.add('hidden'));
                    document.querySelectorAll('.faq-button svg').forEach(el => el.classList.remove('rotate-180'));

                    if (!isOpen) {
                        answer.classList.remove('hidden');
                        icon.classList.add('rotate-180');
                    }
                });
            });
        });
    </script>

</body>
</html>