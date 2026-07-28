@extends('layouts.admin-layouts.layouthead')
@section('contenue-admin')
    <div class="py-3">
        <div class="flex justify-between items-end">
            <div class="w-[60%]">
                <h3 class="text-2xl font-semibold text-vert">Categorie existent</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Labore dolore possimus fuga.</p>
            </div>
            <div class="">
                <a href="{{ route('admin.categorie.create') }}" class="p-1 px-5 rounded-md bg-rouge inline-block">
                    Créer nouveaux
                </a>
            </div>
        </div>

        @if(session('success'))
            <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md mt-4 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('success-alert').remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        <div class="py-5">
            @forelse($categories as $categorie)
                <div class="flex justify-between gap-7 border-b border-black/10 p-2">
                    <div class="w-10 h-10 rounded-md bg-black/5 flex justify-center items-center">
                        <i class="fa-solid fa-caret-right text-rouge"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-semibold">{{ $categorie->nom }}</h3>
                        <div class="text-sm">
                            <span>Slug:</span>
                            <span class="px-4 border border-black/5 rounded-full text-vert">{{ $categorie->slug }}</span>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <a href="{{ route('admin.categorie.edit', $categorie->id) }}" class="text-vert">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <form action="{{ route('admin.categorie.destroy', $categorie->id) }}" method="POST" onsubmit="return confirm('Supprimer {{ $categorie->nom }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-10 rounded-md bg-black/5 text-center">
                    <i class="fa-solid fa-box-open text-2xl"></i>
                    <p>Aucune catégorie n'a encore été créée.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    </div>
@endsection