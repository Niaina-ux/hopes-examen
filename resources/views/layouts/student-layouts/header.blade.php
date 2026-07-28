<div class="">
    <div class="relative container flex justify-between items-center border-b-2 border-black/8 py-2 md:py-3 lg:py-4 gap-3">
        <div class="font-bold  text-5xl text-vert flex-1">
            Hopes
        </div>
        
        <nav id="navLinks" class="hidden absolute lg:sticky top-[110%] right-0 rounded-md p-4 bg-gray-100 lg:bg-transparent border border-black/8 lg:border-0 lg:block">
            <ul class="flex justify-between items-center flex-col lg:flex-row gap-2">
                <li>
                    <a href="" class="p-1 px-3 uppercase font-semibold hover:text-[rgb(250,131,51)] transition">Accueil</a>
                </li>

                <li id="examToggle" class="relative uppercase font-semibold lg:header-link-examen">
                    <span class="hover:text-[rgb(250,131,51)] transition inline-block px-3 cursor-pointer">
                        Examen <i class="fa-solid fa-angle-down -me-2"></i>
                    </span>
                    <ul id="examLinks" class="hidden absolute top-full left-0 bg-white p-4 shadow rounded border border-black/3 lg:links-examen">
                        <li>
                            <a href="" class="p-1 uppercase font-semibold inline-block border-b-2 border-black/5 hover:text-[rgb(250,131,51)] transition">Français</a>
                        </li>
                        <li>
                            <a href="" class="p-1 uppercase font-semibold inline-block border-b-2 border-black/5 hover:text-[rgb(250,131,51)] transition">Anglais</a>
                        </li>
                        <li>
                            <a href="" class="p-1 uppercase font-semibold inline-block border-b-2 border-black/5 hover:text-[rgb(250,131,51)] transition">Dev</a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="" class="p-1 px-3 uppercase font-semibold hover:text-[rgb(250,131,51)] transition">A propos</a>
                </li>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="hover:underline bg-rouge px-4 font-semibold uppercase rounded-md">
                        Deconnexion
                    </button>
                </form>
            </ul>
        </nav>
        <div class="ms-[5%] flex justify-end gap-3 items-center cursor-pointer">
            <span class=" uppercase font-semibold hover:text-[rgb(250,131,51)] transition">Pofil</span>
            <img src="" alt="" class="w-7 h-7 md:w-10 md:h-10 rounded-full bg-black/5">
        </div>
        <button id="navToggle" class="border border-black/5 rounded-md px-1 md:text-xl lg:hidden focus:outline-none" >
            <i class="fa-solid fa-chart-bar"></i>
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const navToggle = document.getElementById('navToggle');
        const navLinks = document.getElementById('navLinks');

        if (navToggle && navLinks) {
            navToggle.addEventListener('click', function () {
                navLinks.classList.toggle('hidden');
            });
        }

        const examToggle = document.getElementById('examToggle');
        const examLinks = document.getElementById('examLinks');

        if (examToggle && examLinks) {
            examToggle.addEventListener('click', function (e) {
                e.stopPropagation(); 
                examLinks.classList.toggle('hidden');
            });
        }
    });
</script>