
<header class="absolute top-0 left-0 w-full pb-2">
    <div class="container flex justify-between">
        <div class="flex items-center gap-15">
            <div class="bg-vert p-3 w-[5cm] text-white px-5 rounded-b-xl">
                <h2 class="font-bold text-4xl">Hopes</h2>
            </div>
            <nav class="flex items-center gap-5">
                <a href=" {{route('home')}} ">Accueil</a>
                <a href="{{ $mySlug ? route('student.examen.show', $mySlug) :  route('login') }}" class="...">
                    examen
                </a>
                <a href="">Contact</a>
            </nav>
        </div>
        <div class="flex justify-between">
            <nav class="flex items-center gap-3">
                <a href="{{ route('student.dashboard') }}" class="shadow bg-white rounded-full px-5 border border-black/5 p-1 inline-block">
                    Dashboard
                </a>

                @auth
                    <div class="shadow relative rounded-full bg-white border border-black/5 p-1 inline-flex items-center gap-2 cursor-pointer" id="profil-dropdown-toggle">
                        <i class="fa-solid fa-bars-progress ms-2"></i>
                        <div class="w-6 h-6 rounded-full overflow-hidden">
                            <img src="{{ Auth::user()->image ? asset('images/' . Auth::user()->image) : asset('images/default-avatar.png') }}"
                                alt="" class="w-full h-full object-cover">
                        </div>
                        <div id="profil-dropdown-menu" class="hidden absolute right-0 top-[120%] bg-white p-2 rounded shadow border border-black/3 z-50">
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
                    <a href="{{ route('login') }}" class="shadow rounded-full bg-rouge text-white px-5 border border-black/5 p-1 inline-block">
                        Se connecter
                    </a>
                @endauth
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
</script>