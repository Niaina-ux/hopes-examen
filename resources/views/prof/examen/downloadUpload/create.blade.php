@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="bg-white p-4 rounded-md">
    <h2 class="text-xl font-semibold mb-4">Créer un — {{ $examen->titre }}</h2>

    <form action="{{ route('prof.examen.fichier.store',[$slug, $examen->id]) }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium">Titre du QCM</label>
            <input type="text" name="titre" value="{{ old('titre') }}" class="border rounded w-full p-2" placeholder="Ex: QCM Introduction HTML">
            @error('titre') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Description</label>
            <textarea name="description" rows="3" class="border rounded w-full p-2">{{ old('description') }}</textarea>
            @error('description') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-4 mb-4">
            <div class="flex-1">
                <label class="block text-sm font-medium">Note totale</label>
                <input type="number" name="note_totale" value="{{ old('note_totale') }}" class="border rounded w-full p-2">
                @error('note_totale') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit" class="bg-rouge text-white px-4 py-2 rounded">Créer le QCM</button>
    </form>
</div>
@endsection