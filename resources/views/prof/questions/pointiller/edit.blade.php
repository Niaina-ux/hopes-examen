@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="py-3">
    <h2 class="text-xl font-semibold mb-4">Modification d'exericice _ {{$pointiller->titre}} </h2>

    <form action="{{ route('prof.question.pointiller.update', [$slug, $pointiller->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium">Titre du QCM</label>
            <input type="text" name="titre" value="{{ old('titre', $pointiller->titre) }}" class="border rounded w-full p-2">
            @error('titre') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Description</label>
            <textarea name="description" rows="3" class="border rounded w-full p-2">{{ old('description', $pointiller->description) }}</textarea>
            @error('description') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>


        <button type="submit" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer les modifications</button>
    </form>
</div>
@endsection