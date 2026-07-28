@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="bg-white py-2 rounded-md">
    <a href="{{route('prof.examen.text.question.show', [$slug, $examen->id, $text->id])}}">
        <i class="fa-solid fa-arrow-left-long"></i>
    </a>
    <h2 class="text-xl font-semibold mb-4">Modifier le texte</h2>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('prof.examen.text.update', [$slug, $examen->id, $text->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium">Titre</label>
            <input type="text" name="titre" value="{{ old('titre', $text->titre) }}" class="border rounded w-full p-2">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Texte à lire</label>
            <textarea name="texte" rows="10" class="border rounded w-full p-2">{{ old('texte', $text->texte) }}</textarea>
        </div>

        <div class="flex gap-4 mb-4">
            {{-- <div class="flex-1">
                <label class="block text-sm font-medium">Durée (minutes)</label>
                <input type="number" name="duree_minutes" value="{{ old('duree_minutes', $text->duree_minutes) }}" class="border rounded w-full p-2">
            </div> --}}
            <div class="flex-1">
                <label class="block text-sm font-medium">Note totale</label>
                <input type="number" name="note_totale" value="{{ old('note_totale', $text->note_totale) }}" class="border rounded w-full p-2">
            </div>
        </div>

        <button type="submit" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer les modifications</button>
    </form>
</div>
@endsection