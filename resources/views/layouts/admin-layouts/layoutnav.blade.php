<div class="h-full p-2 px-3">
    <div class="text-vert font-bold text-3xl px-2">
        Hopes
    </div>
    <div class="mt-3 w-full">
        <span class="uppercase text-sm border-b text-black/50 border-black/10 pb-1 inline-block px-2 py-1">
            <i class="fa-solid fa-table-cells-large text-xl me-2"></i>
            Menu
        </span>
        <ul class="my-2">
            <li>
                <a href="{{ route('admin.dashboard') }}"
                class="inline-block px-2 py-1 navlink {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <div class="tirelink"></div>
                    <div>
                        <i class="fa-solid fa-user-tie me-2"></i>
                        Dashboard
                    </div>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.prof.index') }}"
                class="inline-block px-2 py-1 navlink {{ request()->routeIs('admin.prof.*') ? 'active' : '' }}">
                    <div class="tirelink"></div>
                    <div>
                        <i class="fa-solid fa-user-tie me-2"></i>
                        Profs
                    </div>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.student.index') }}"
                class="inline-block px-2 py-1 navlink {{ request()->routeIs('admin.student.*') ? 'active' : '' }}">
                    <div class="tirelink"></div>
                    <div>
                        <i class="fa-solid fa-user-tie me-2"></i>
                        Etudiants
                    </div>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.categorie.index') }}"
                class="inline-block px-2 py-1 navlink {{ request()->routeIs('admin.categorie.*') ? 'active' : '' }}">
                    <div class="tirelink"></div>
                    <div>
                        <i class="fa-solid fa-user-tie me-2"></i>
                        Categorie
                    </div>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.typeExercice.index') }}"
                class="inline-block px-2 py-1 navlink {{ request()->routeIs('admin.typeExercice.*') ? 'active' : '' }}">
                    <div class="tirelink"></div>
                    <div>
                        <i class="fa-solid fa-user-tie me-2"></i>
                        Type d'exercice
                    </div>
                </a>
            </li>
        </ul>
    </div>
    <div class="mt-5">
        <span class="uppercase text-sm border-b text-black/50 border-black/10 pb-1 inline-block px-2 py-1">
            <i class="fa-solid fa-book-open text-xl me-2"></i>
            Examen
        </span>
        <ul class="my-2">
            @foreach($navCategories as $categorie)
                <li>
                    <a href="{{ route('admin.examen.show', $categorie->slug) }}"
                        class="inline-block px-2 py-1 navlink {{ request()->route('slug') === $categorie->slug ? 'active' : '' }}">
                        <div class="tirelink"></div>
                        <div class="">
                            <i class="{{ $categorie->icone ?? 'fa-solid fa-folder' }} me-2"></i>
                            {{ \Illuminate\Support\Str::afterLast($categorie->nom, ' ') }}
                        </div>
                    </a>
                </li>
            @endforeach 
        </ul>
    </div>
    <div class="absolute bottom-10 left-4">
        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class=" w-[5cm] flex gap-3 items-center px-2 py-1  rounded-md transition-all hover:text-red-500 hover:bg-red-500/5 border-2 border-white shadow bg-black/3">
                <i class="fa-solid fa-arrow-up-from-bracket"></i>
                Deconnexion
            </button>
        </form>
    </div>
    </div>