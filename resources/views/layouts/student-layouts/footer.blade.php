<footer class="border-t border-black/5 mt-5 bg-black/10">
    <div class="h-30 w-full relative">
        <div class="w-full h-full absolute top-0 left-0 bg-white/30"></div>
        <img src="/images/hops.jpg" alt="" 
        class="w-full h-full object-cover">
    </div>
    <div class="container py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
            <div class="md:col-span-2">
                <div class="inline-block bg-vert text-white px-6 py-3 rounded-b-xl">
                    <h2 class="font-bold text-3xl">
                        Hopes
                    </h2>
                </div>
                <p class="mt-5 max-w-md  leading-6 text-black/55">
                    Une plateforme simple et intuitive pour vous accompagner
                    dans votre préparation, évaluer vos connaissances et
                    passer vos examens en ligne en toute confiance.
                </p>
                <div class="flex gap-2 mt-5">
                    <a href="#"
                       class="w-9 h-9 rounded-full bg-white border border-black/5
                              shadow-sm flex items-center justify-center
                              hover:bg-vert hover:text-white
                              transition-all duration-300">
                        <i class="fa-brands fa-facebook-f "></i>
                    </a>
                    <a href="#"
                       class="w-9 h-9 rounded-full bg-white border border-black/5
                              shadow-sm flex items-center justify-center
                              hover:bg-vert hover:text-white
                              transition-all duration-300">
                        <i class="fa-brands fa-instagram "></i>
                    </a>
                    <a href="#"
                       class="w-9 h-9 rounded-full bg-white border border-black/5
                              shadow-sm flex items-center justify-center
                              hover:bg-vert hover:text-white
                              transition-all duration-300">
                        <i class="fa-brands fa-whatsapp "></i>
                    </a>
                </div>
            </div>
            <div>
                <h3 class="font-bold text-lg mb-4">
                    Navigation
                </h3>
                <ul class="space-y-3  text-black/60">
                    <li>
                        <a href="{{ route('home') }}"
                           class="hover:text-vert transition-colors">
                            Accueil
                        </a>
                    </li>
                    <li>
                        <a href="{{ $mySlug ? route('student.examen.show', $mySlug) : route('login') }}"
                           class="hover:text-vert transition-colors">
                            Examen
                        </a>
                    </li>
                    <li>
                        <a href="#"
                           class="hover:text-vert transition-colors">
                            A propos
                        </a>
                    </li>
                    @auth
                        <li>
                            <a href="{{ route('student.dashboard') }}"
                               class="hover:text-vert transition-colors">
                                Dashboard
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
            <div>
                <h3 class="font-bold text-lg mb-4">
                    À propos
                </h3>
                <ul class="space-y-3  text-black/60">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-vert"></i>
                        Examens en ligne
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-vert"></i>
                        Évaluation des connaissances
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-vert"></i>
                        Suivi des résultats
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-vert"></i>
                        Préparation aux examens
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="border-t border-black/5">
        <div class="container py-5 flex flex-col md:flex-row
                    justify-between items-center gap-3">
            <p class="text-xs text-black/50 text-center md:text-left">
                © {{ date('Y') }} Hopes. Tous droits réservés.
            </p>
            <div class="flex items-center gap-5 text-xs text-black/50">
                <a href="#" class="hover:text-vert transition-colors">
                    Conditions d'utilisation
                </a>
                <a href="#" class="hover:text-vert transition-colors">
                    Politique de confidentialité
                </a>
            </div>
        </div>
    </div>
</footer>