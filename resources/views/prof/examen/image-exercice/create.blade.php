@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="w-full py-3">
    <a href="{{ route('prof.examen.image', [$slug, $examen->id]) }}">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="bg-white p-4 rounded-md">
        <h2 class="text-xl font-semibold mb-4">Créer un exercice image</h2>

        <form action="{{ route('prof.examen.image.store', [$slug, $examen->id]) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-base font-medium">Titre</label>
                <input type="text" name="titre" value="{{ old('titre') }}" class="border rounded w-full p-2" placeholder="Ex: Détourage d'image">
                @error('titre') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-base font-medium">Description (optionnel)</label>
                <textarea name="description" rows="2" class="border rounded w-full p-2">{{ old('description') }}</textarea>
            </div>

            <div class="flex gap-4 mb-4">
                <div class="flex-1 hidden">
                    <label class="block text-base font-medium">Durée (minutes)</label>
                    <input type="number" name="duree_minutes" value="{{ old('duree_minutes') }}" min="1" class="border rounded w-full p-2">
                </div>
                <div class="flex-1">
                    <label class="block text-base font-medium">Note totale</label>
                    <input type="number" name="note_totale" value="{{ old('note_totale', 10) }}" min="0.1" step="0.1" class="border rounded w-full p-2">
                </div>
            </div>

            <button type="submit" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer</button>
        </form>
    </div>
</div>
@endsection