@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class=" py-3 me-2 rounded-md">
    <div class="">
        <a href="">Retour /</a>
        <span class="font-semibold">Creation qcm</span>
    </div>
    <h2 class="text-xl font-semibold mb-4 mt-2 pb-2 border-b-2 border-black/20 text-vert
        dark:border-white/20">Créer un QCM </h2>
    <form action="{{ route('prof.question.qcm.store', $slug) }}" method="POST" 
        class="bg-black/1 rounded-md p-4 border border-black/3
        dark:bg-white/1 dark:border-white/3"
        enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="block font-medium">Titre du QCM</label>
            <input 
                type="text" 
                name="titre"
                value="{{ old('titre') }}" 
                class="formulaire bg-white border border-black/20 rounded w-full p-2
                dark:bg-white/2 dark:border-white/20" 
                placeholder="Ex: QCM Introduction HTML">
            @error('titre') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block  font-medium">Description</label>
            <textarea 
                name="description" 
                rows="3" 
                class="formulaire border border-black/20 bg-white rounded w-full p-2
                dark:bg-white/2 dark:border-white/20"
            placeholder="Decrivez votre examen ..">{{ old('description') }}</textarea>
            @error('description') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="bg-rouge text-white px-5 py-2 rounded-full hover-rouge">Créer l'exercice</button>
    </form>
</div>
@endsection