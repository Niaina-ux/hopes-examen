@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="bg-white py-3 me-2 rounded-md">
    <div class="">
        <a href="">Retour /</a>
        <span class="font-semibold">Creation qcm</span>
    </div>
    <h2 class="text-xl font-semibold mb-4 mt-2 pb-2 border-b-2 border-black/20 text-vert">Créer un QCM — {{ $examen->titre }}</h2>
    <form action="{{ route('prof.examen.qcm.store', [$slug, $examen->id]) }}" method="POST" 
        class="bg-black/1 rounded-md p-4 border border-black/3">
        @csrf
        <div class="mb-4">
            <label class="block font-medium">Titre du QCM</label>
            <input 
                type="text" 
                name="titre"
                value="{{ old('titre') }}" 
                class="formulaire bg-white border border-black/20 rounded w-full p-2" 
                placeholder="Ex: QCM Introduction HTML">
            @error('titre') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block  font-medium">Description</label>
            <textarea 
                name="description" 
                rows="3" 
                class="formulaire border border-black/20 bg-white rounded w-full p-2"
            placeholder="Decrivez votre examen ..">{{ old('description') }}</textarea>
            @error('description') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-4 mb-4 w-[10cm]">
            <div class="flex-1">
                <label class="block  font-medium">Durée (minutes)</label>
                <input type="text" 
                    name="duree_minutes" 
                    value="{{ old('duree_minutes') }}" 
                    class="formulaire bg-white border border-black/20 rounded w-full p-2">
                @error('duree_minutes') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
            <div class="flex-1">
                <label class="block  font-medium">Note totale</label>
                <input 
                    type="text" 
                    name="note_totale" 
                    value="{{ old('note_totale') }}" 
                    class="formulaire bg-white border border-black/20 rounded w-full p-2">
                @error('note_totale') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit" class="bg-rouge text-white px-5 py-2 rounded-full">Créer l'exercice</button>
    </form>
</div>
@endsection