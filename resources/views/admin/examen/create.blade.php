@extends('layouts.admin-layouts.layouthead')
@section('contenue-admin')
<div class="bg-white p-4 rounded-md">
    <h2 class="text-2xl font-semibold text-vert mb-4">Créer un examen — {{ $categorie->nom }}</h2>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.examen.store', $slug) }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium">Titre</label>
            <input type="text" name="titre" value="{{ old('titre') }}" class="border rounded w-full p-2" placeholder="Ex: Examen final HTML/CSS">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Description</label>
            <textarea name="description" rows="3" class="border rounded w-full p-2">{{ old('description') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Date d'examen (optionnel)</label>
            <input type="date" name="date_examen" value="{{ old('date_examen') }}" class="border rounded w-full p-2">
            @error('date_examen') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Durée (minutes)</label>
            <input type="number" name="duree_minutes" value="{{ old('duree_minutes') }}" class="border rounded w-full p-2">
        </div>

        <button type="submit" class="bg-rouge text-white px-4 py-2 rounded">Créer l'examen</button>
    </form>
</div>
@endsection