@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="bg-white py-2 rounded-md">
    <a href="{{route('prof.examen.text.question.show', [$slug, $examen->id, $text->id])}}">
        <i class="fa-solid fa-arrow-left-long"></i>
    </a>
    <h2 class="text-xl font-semibold mb-4">Modifier la question</h2>
    <div class="bg-black/5 p-3 rounded-md mb-4 text-sm text-black/60 whitespace-pre-line">
        {{ $text->texte}}
    </div>
    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('success'))
        <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md my-2 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="document.getElementById('success-alert').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif
    <form action="{{ route('prof.examen.text.question.update', [$slug, $examen->id, $text->id, $question->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium">Énoncé</label>
            <textarea name="enonce" rows="3" class="border rounded w-full p-2">{{ old('enonce', $question->enonce) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Points</label>
            <input type="number" name="points" value="{{ old('points', $question->points) }}" min="0.1" step="0.1" class="border rounded w-32 p-2">
        </div>

        <button type="submit" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer les modifications</button>
    </form>
</div>
@endsection