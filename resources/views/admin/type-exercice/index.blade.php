
@extends('layouts.admin-layouts.layouthead')
@section('contenue-admin')
<div class=" py-3 rounded-md">
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-semibold text-vert">Types d'exercice</h2>
            <p>Gérez les types d'exercice disponibles pour les examens.</p>
        </div>
        <a href="{{ route('admin.typeExercice.create') }}" class="bg-rouge text-white hover-rouge px-4 py-1 rounded-full">
            + Ajouter nouveau
        </a>
    </div>

    @if(session('success'))
        <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md mt-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="document.getElementById('success-alert').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- ✅ Tabs de filtrage par catégorie --}}
    <div class="flex gap-2 mt-3 border-b-2 border-black/10 pb-2">
        <a href="{{ route('admin.typeExercice.index') }}"
           class="px-3 py-1 rounded-full border border-black/10 {{ !$categorieSlug ? 'bg-vert text-white ' : 'bg-black/5' }}
           dark:border-white/10 bg-white/5">
            Tous
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('admin.typeExercice.index', ['categorie' => $cat->slug]) }}"
               class="px-3 py-1 rounded-full border border-black/10 {{ $categorieSlug === $cat->slug ? 'bg-vert text-white' : 'bg-black/5 ' }}
               dark:border-white/10 bg-white/5">
                {{ $cat->nom }}
            </a>
        @endforeach
    </div>

    <div 
        class="mt-2 border border-black/3 rounded-md bg-black/2 p-2
        dark:bg-white/2 dark:border-white/3">
        @forelse($typesExercice as $type)
            <div 
                class="flex justify-between items-center gap-5 p-2 border border-black/3 rounded bg-white/70
                dark:border-white/3 dark:bg-white/2">
                <div class="w-10 h-10 rounded-md bg-black/5 flex justify-center items-center">
                    <i class="{{ $type->icone ?? 'fa-solid fa-chart-simple' }} text-vert"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base">{{ $type->nom }}</h3>
                    <div class="text-sm">
                        Slug: <span class="inline-block border border-black/10 rounded-full px-3 text-rouge">{{ $type->slug }}</span>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.typeExercice.edit', $type->id) }}" class="text-vert">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                    <form action="{{ route('admin.typeExercice.destroy', $type->id) }}" method="POST" onsubmit="return confirm('Supprimer {{ $type->nom }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div 
                class="p-10 rounded-md bg-black/5 text-center
                dark:bg-white/2">
                <i class="fa-solid fa-box-open text-2xl"></i>
                <p>
                    @if($categorie)
                        Aucun type d'exercice assigné à {{ $categorie->nom }}.
                    @else
                        Aucun type d'exercice n'a encore été créé.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $typesExercice->appends(request()->query())->links() }}
    </div>
    @if($categorie)
        <div class="flex justify-end mt-2">
            <a href="{{ route('admin.categorie.editTypesExercice', $categorie->id) }}"
               class="text-vert underline hover:underline">
                <i class="fa-solid fa-plus"></i> Ajouter/retirer un type pour {{ $categorie->nom }}
            </a>
        </div>
    @endif
</div>
@endsection