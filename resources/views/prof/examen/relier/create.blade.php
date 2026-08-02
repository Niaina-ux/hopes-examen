@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="bg-white py-3 me-2">
    <div class="">
        <a href="{{route('prof.examen.relier', [$slug, $examen->id])}}"
            class="hover:underline">Retour / </a>
        <span class="font-semibold">Creation</span>
    </div>
    <h2 class="text-xl font-semibold text-vert my-2 border-b-2 border-black/20 pb-1">Créer l'exercice relier par fleèche — {{ $examen->titre }}</h2>
    <form action="{{ route('prof.examen.relier.store', [$slug, $examen->id]) }}" method="POST"
        class="mt-3">
        @csrf
        <div class="mb-3">
            <label class="block font-medium">Titre du QCM</label>
            <input 
                type="text" 
                name="titre" 
                value="{{ old('titre') }}" 
                class=" border border-black/20 rounded w-full p-2" 
                placeholder="Ex: Grammaire">
                @error('titre') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-1">
            <label class="block font-medium">Description</label>
            <textarea 
                name="description" 
                rows="3" 
                class="border border-black/20 rounded w-full p-2"
                placeholder="Ex: Pour réaliser le ...">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-4 w-[10cm]">
            <div class="flex-1 hidden">
                <label class="block font-medium">Durée (minutes)</label>
                <input 
                    type="number" 
                    name="duree_minutes" 
                    value="{{ old('duree_minutes') }}" 
                    class="border rounded border-black/20 w-full p-2">
                    @error('duree_minutes') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
            <div class="flex-1">
                <label class="block font-medium">Note totale</label>
                <input 
                    type="text" 
                    name="note_totale" 
                    value="{{ old('note_totale') }}" 
                    class="border rounded border-black/20 w-[3cm] p-2">
                    @error('note_totale') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit" class="bg-rouge text-white px-4 py-2 mt-5 rounded">Créer l'exercice</button>
    </form>
</div>
@endsection