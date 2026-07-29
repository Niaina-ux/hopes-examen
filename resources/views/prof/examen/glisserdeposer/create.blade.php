@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="bg-white py-3 rounded-md me-2">
    <div class="">
        <a href="">Retour / </a>
        <span class="font-semibold">Creation</span>
    </div>
    <h2 class="text-2xl font-semibold my-2 text-vert">Créer un — {{ $examen->titre }}</h2>
    <form action="{{ route('prof.examen.glisserdeposer.store',[$slug, $examen->id]) }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium">Titre du QCM</label>
            <input 
                type="text" 
                name="titre" 
                value="{{ old('titre') }}" 
                class="border border-black/20 rounded w-full p-2" 
                placeholder="Ex: QCM Introduction HTML">
                @error('titre') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Description</label>
            <textarea 
                name="description" 
                rows="3" 
                class="border border-black/20 rounded w-full p-2"
                >{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>
        <div class="flex gap-4 mb-4">
            <div class="flex-1">
                <label class="block text-sm font-medium">Note totale</label>
                <input 
                    type="number" 
                    name="note_totale" 
                    value="{{ old('note_totale') }}" 
                    class="border border-black/20 rounded w-full p-2">
                    @error('note_totale') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>
        <button type="submit" class="bg-rouge text-white px-4 mt-4 py-2 rounded ">Créer  Gliser deposer</button>
    </form>
</div>
@endsection