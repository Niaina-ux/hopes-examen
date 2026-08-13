@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="bg-white me-2 py-3 rounded-md">
    <div class="mb-1">
        <a href="">
            Retour /
        </a>
        <span class="font-semibold">création</span>
    </div>
    <h2 class="text-2xl text-vert border-b-2 border-black/10 pb-1 font-semibold mb-4">Créer un — {{ $examen->titre }}</h2>
    <form action="{{ route('prof.examen.pointiller.store',[$slug, $examen->id]) }}" method="POST" 
        class="p-4 rounded-md border border-black/3 bg-black/1">
        @csrf
        <div class="mb-4">
            <label class="block font-medium">Titre du QCM</label>
            <input type="text" 
                    name="titre" 
                    value="{{ old('titre') }}" 
                    class="border border-black/20 rounded w-full bg-white/90 formulaire p-2" 
                    placeholder="Ex: Grammaire ..">
                    @error('titre') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block font-medium">Description</label>
            <textarea name="description" 
                    rows="3" 
                    class="border border-black/20 bg-white/90 formulaire rounded w-full p-2"
                    placeholder="Description ...">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-4 mb-4">
            <div class="flex-1 hidden">
                <label class="block  font-medium">Durée (minutes)</label>
                <input type="text" 
                        name="duree_minutes" 
                        value="{{ old('duree_minutes') }}" 
                        class="border border-black/20 rounded w-[2cm] bg-white/90 formulaire p-2">
                        @error('duree_minutes') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="flex-1">
                <label class="block  font-medium">Note totale</label>
                <input type="text" 
                        name="note_totale" 
                        value="{{ old('note_totale') }}" 
                        class="border border-black/20 rounded w-[3cm] bg-white/90 formulaire p-2">
                        @error('note_totale') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit" class="bg-rouge text-white px-5  py-2 rounded-full">Créer le QCM</button>
    </form>
</div>
@endsection