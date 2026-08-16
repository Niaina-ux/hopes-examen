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
                <a href=" {{route('admin.dashboard')}} "
                    class="inline-block px-2 py-1">
                    <i class="fa-solid fa-user-tie me-2"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a  href="{{ route('admin.prof.index') }}"
                    class="inline-block px-2 py-1">
                    <i class="fa-solid fa-user-tie me-2"></i>
                    Profs
                </a>
            </li>
            <li>
                <a href="{{ route('admin.student.index') }}"
                    class="inline-block px-2 py-1">
                    <i class="fa-solid fa-user-graduate me-2"></i>
                    Etudiants
                </a>
            </li>
            <li>
                <a  href="{{ route('admin.categorie.index') }}"
                    class="inline-block px-2 py-1">
                    <i class="fa-solid fa-user-tie me-2"></i>
                    Categorie
                </a>
            </li>
            <li>
                <a  href="{{ route('admin.typeExercice.index') }}"
                    class="inline-block px-2 py-1">
                    <i class="fa-solid fa-user-tie me-2"></i>
                    Type d'exercice
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
                        class="inline-block px-2 py-1 {{ request()->route('slug') === $categorie->slug ? 'text-vert font-semibold' : '' }}">
                        <i class="{{ $categorie->icone ?? 'fa-solid fa-folder' }} me-2"></i>
                        {{ \Illuminate\Support\Str::afterLast($categorie->nom, ' ') }}
                    </a>
                </li>
            @endforeach 
        </ul>
    </div>
    <div class="absolute bottom-5 left-3">
        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="hover:underline bg-rouge px-4 font-semibold uppercase rounded-md">
                Deconnexion
            </button>
        </form>
    </div>
    </div>