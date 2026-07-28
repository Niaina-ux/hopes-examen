@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="bg-white py-3 rounded-md me-2">
    <div class="flex gap-3 items-center my-2">
        <a href="" class="">
            Examen /
        </a>
        <span class="font-semibold">Creation</span>
    </div>
    <h2 class="text-xl font-semibold my-2 text-vert">Créer un texte — {{ $examen->titre }}</h2>
    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('prof.examen.text.store', [$slug, $examen->id]) }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium">Titre</label>
            <input 
                type="text" 
                name="titre" 
                value="{{ old('titre') }}" 
                class="border border-black/20 rounded w-full p-2" 
                placeholder="Ex: Texte sur l'environnement">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Texte à lire</label>
            <textarea 
                name="texte" 
                rows="10" 
                class="border border-black/20 rounded w-full p-2"
                placeholder="Text-...."
                >{{ old('texte') }}</textarea>
        </div>

        <div class="flex gap-4 mb-4">
            <div class="w-[5cm]">
                <label class="block text-sm font-medium">Note totale</label>
                <input 
                    type="number" 
                    name="note_totale" 
                    value="{{ old('note_totale') }}" 
                    class="border border-black/20 rounded w-full p-2">
            </div>
        </div>

        <button type="submit" class="bg-rouge text-white px-4 py-2 rounded">Créer le texte</button>
    </form>
</div>
@endsection