<div class="h-full p-2 px-4 bg-black/3 dark:bg-white/3">
    <div class="text-vert font-bold text-3xl px-2">
        Hopes
    </div>
    <div class="mt-3 w-full">
        <ul class="my-2">
            <li>
                <a href="{{route('prof.dashboard')}}"
                class="inline-block px-2 py-1 navlink {{ request()->routeIs('prof.dashboard') ? 'active' : '' }}">
                    <div class="tirelink"></div>
                    <div>
                        <i class="fa-solid fa-user-tie me-2"></i>
                        Dashboard
                    </div>
                </a>
            </li>
            @if($profCategorie ?? false)
                <li>
                    <a href="{{ route('prof.student.show', $profCategorie->slug) }}"
                    class="inline-block px-2 py-1 navlink
                    {{ request()->routeIs('prof.student.*') ? 'active' : '' }}">
                        <div class="tirelink"></div>
                        <div>
                            <i class="fa-solid fa-user-graduate me-2"></i>
                            Etudiants
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('prof.examen.show', $profCategorie->slug) }}"
                    class="inline-block px-2 py-1 navlink
                    {{ request()->routeIs('prof.examen.*') ? 'active' : '' }}">
                        <div class="tirelink"></div>
                        <div>
                            <i class="fa-solid fa-folder me-2"></i>
                            Examen
                        </div>
                    </a>
                </li>
            @endif
        </ul>
    </div>
    <div class="absolute bottom-10 left-4">
        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" 
                class=" w-[5cm] flex gap-3 items-center px-2 py-1  rounded-md transition-all hover:text-red-500 hover:bg-red-500/5 border-2 border-white shadow bg-black/3
                dark:border-white/25 dark:bg-white/10">
                <i class="fa-solid fa-arrow-up-from-bracket"></i>
                Deconnexion
            </button>
        </form>
    </div>
    </div>