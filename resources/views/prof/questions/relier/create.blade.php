@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class=" py-3 ">
    <div class="">
        <a href=""
            class="hover:underline">Retour / </a>
        <span class="font-semibold">Creation</span>
    </div>
    <h2 class="text-2xl font-semibold text-vert my-2 border-b-2 border-black/20 pb-1">Créer l'exercice relier par flèche — </h2>
    <form action="{{route('prof.question.relier.store', $slug)}}" method="POST"
        class="mt-4 rounded-md border border-black/3 bg-black/1 p-4
        dark:border-white/3 dark:bg-white/2">
        @csrf
        <div class="mb-3">
            <label class="block font-medium">Titre d'exercice</label>
            <input 
                type="text" 
                name="titre" 
                value="{{ old('titre') }}" 
                class=" border border-black/20 bg-white/90 formulaire rounded w-full p-2
                dark:bg-white/2 dark:border-white/10" 
                placeholder="Ex: Grammaire">
                @error('titre') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-1">
            <label class="block font-medium">Description</label>
            <textarea 
                name="description" 
                rows="3" 
                class="border border-black/20 bg-white/90 formulaire rounded w-full p-2
                dark:bg-white/2 dark:border-white/10"
                placeholder="Ex: Pour réaliser le ...">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="bg-rouge text-white px-5 py-2 mt-5 rounded-full">Créer l'exercice</button>
    </form>
</div>
@endsection