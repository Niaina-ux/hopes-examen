@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="py-3">
    <div class="mb-1">
        <a href="">
            Retour /
        </a>
        <span class="font-semibold">création</span>
    </div>
    <h2 class="text-2xl text-vert border-b-2 border-black/10 pb-1 font-semibold mb-4
        dark:border-white/20">Création d'exercice</h2>
    <form action="{{route('prof.question.pointiller.store', $slug)}}" method="POST" 
        class="p-4 rounded-md border border-black/3 bg-black/1
            dark:border-white/3 dark:bg-white/1">
        @csrf
        <div class="mb-4">
            <label class="block font-medium">Titre du QCM</label>
            <input type="text" 
                    name="titre" 
                    value="{{ old('titre') }}" 
                    class="border border-black/20 rounded w-full bg-white/90 formulaire p-2
                        dark:border-white/10 dark:bg-white/3" 
                    placeholder="Ex: Grammaire ..">
                    @error('titre') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block font-medium">Description</label>
            <textarea name="description" 
                    rows="3" 
                    class="border border-black/20 bg-white/90 formulaire rounded w-full p-2
                        dark:border-white/10 dark:bg-white/3"
                    placeholder="Description ...">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="bg-rouge text-white px-5  py-2 rounded-md">Créer le QCM</button>
    </form>
</div>
@endsection