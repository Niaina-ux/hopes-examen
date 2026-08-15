
<header class="absolute top-0 left-0 w-full pb-2">
    <div class="container flex justify-between">
        <div class="flex items-center gap-15">
            <div class="bg-vert p-2 lg:p-3 w-[3cm] lg:w-[5cm] text-white px-5 rounded-b-xl">
                <h2 class="font-bold text-2xl lg:text-4xl ">Hopes</h2>
            </div>
            <nav class="hidden md:flex items-center gap-5">
                <a href=" {{route('home')}}" class="nav-hover">Accueil</a>
                <a href="{{ $mySlug ? route('student.examen.show', $mySlug) :  route('login') }}" class="nav-hover">
                    Examen
                </a>
                <a href="" class="nav-hover">A propos</a>
            </nav>
        </div>
        <div class="flex justify-between">
            <nav 
                class="flex items-center gap-2 
                lg:gap-3">
                @auth
                    <a href="{{ route('student.dashboard') }}" 
                        class="shadow bg-white rounded-full px-2 py-1 hover-white
                        lg:px-5 border border-black/5  lg:block">
                        Dashboard
                    </a>
                    <div id="profil-dropdown-toggle"
                        class="shadow relative rounded-full hover-rouge bg-rouge border border-black/5 p-1 inline-flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-bars-progress ms-2 text-white"></i>
                        <div class="w-6 h-6 rounded-full overflow-hidden bg-white">
                            <img src="{{ Auth::user()->image ? asset('images/' . Auth::user()->image) : asset('images/default-avatar.png') }}"
                                alt="" class="w-full h-full object-cover">
                        </div>
                        <div id="profil-dropdown-menu" 
                            class="hidden absolute right-0 top-[120%] bg-white p-2 rounded shadow border border-black/3 z-60">
                            <a href="" class="flex items-center gap-2 px-2 p-1 border-y border-black/5">
                                <i class="fa-solid fa-user"></i> Profil
                            </a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 px-2 p-1 border-y border-black/5 w-full text-left">
                                    <i class="fa-solid fa-arrow-up-from-bracket"></i> Déconnecter
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('student.dashboard') }}" 
                        class="shadow bg-white rounded-full px-2 py-1 hidden md:block 
                        lg:px-5 border border-black/5 hover:bg-black/2 hover-white lg:block">
                        Dashboard
                    </a>
                    <a href="{{ route('login') }}" 
                        class="transition-3 shadow rounded-full bg-rouge text-white  hover-rouge
                        px-2 p-1
                        lg:px-5 border border-black/5  inline-block">
                        Se connecter
                    </a>
                @endauth
                <div id="nav-dropdown-toggle"  
                    class="ms-2 relative 
                    md:hidden" >
                    <i class="fa-solid fa-bars text-xl"></i>
                    <nav id="nav-dropdown-menu" 
                        class="absolute hidden right-0 top-[140%] bg-white rounded-xl shadow p-4">
                        <a href=" {{route('home')}} "
                            class="text-nowrap nav-hover  p-2 inline-block border-y border-black/10 w-full">Accueil</a>
                        <a href="{{ $mySlug ? route('student.examen.show', $mySlug) :  route('login') }}" 
                        class="text-nowrap nav-hover p-2 inline-block border-y border-black/10 w-full">
                            Examen
                        </a>
                        <a href="" class="text-nowrap nav-hover p-2 inline-block border-y border-black/10 w-full">A propos</a>
                    </nav>
                </div>
            </nav>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('profil-dropdown-toggle');
    const menu = document.getElementById('profil-dropdown-menu');
    

    toggle.addEventListener('click', function (e) {
        e.stopPropagation(); 
        menu.classList.toggle('hidden');
    });

    
    document.addEventListener('click', function (e) {
        if (!menu.contains(e.target) && !toggle.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });

});

document.addEventListener('DOMContentLoaded', function () {
    const navtoggle = document.getElementById('nav-dropdown-toggle');
    const navmenu = document.getElementById('nav-dropdown-menu');

    navtoggle.addEventListener('click', function (e) {
        e.stopPropagation(); 
        navmenu.classList.toggle('hidden');
    });

    
    document.addEventListener('click', function (e) {
        if (!navmenu.contains(e.target) && !navtoggle.contains(e.target)) {
            navmenu.classList.add('hidden');
        }
    });
});
</script>