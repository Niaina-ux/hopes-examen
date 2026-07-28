@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="bg-white py-3 rounded-md">
    <div class="">
        <a href="">Retour /</a>
        <span class="font-semibold">Creation qcm</span>
    </div>
    <h2 class="text-xl font-semibold my-2 text-vert">Créer un QCM — {{ $examen->titre }}</h2>
    <form action="{{ route('prof.examen.qcm.store', [$slug, $examen->id]) }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium">Titre du QCM</label>
            <input type="text" name="titre" value="{{ old('titre') }}" class="border border-black/20 rounded w-full p-2" placeholder="Ex: QCM Introduction HTML">
            @error('titre') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Description</label>
            <textarea name="description" rows="3" class="border border-black/20 rounded w-full p-2">{{ old('description') }}</textarea>
            @error('description') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-4 mb-4 w-[10cm]">
            <div class="flex-1">
                <label class="block text-sm font-medium">Durée (minutes)</label>
                <input type="number" name="duree_minutes" value="{{ old('duree_minutes') }}" class="border border-black/20 rounded w-full p-2">
                @error('duree_minutes') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium">Note totale</label>
                <input type="number" name="note_totale" value="{{ old('note_totale') }}" class="border border-black/20 rounded w-full p-2">
                @error('note_totale') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit" class="bg-rouge text-white px-4 py-2 rounded">Créer l'exercice</button>
    </form>
</div>
@endsection