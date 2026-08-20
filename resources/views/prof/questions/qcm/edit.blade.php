@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class=" p-4 rounded-md">
    <h2 class="text-xl font-semibold mb-4">Modifier le QCM — </h2>

    <form action="{{ route('prof.question.qcm.update', [$slug, $qcm->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium">Titre du QCM</label>
            <input type="text" name="titre" value="{{ old('titre', $qcm->titre) }}" class="border rounded w-full p-2">
            @error('titre') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Description</label>
            <textarea name="description" rows="3" class="border rounded w-full p-2">{{ old('description', $qcm->description) }}</textarea>
            @error('description') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer les modifications</button>
    </form>
</div>
@endsection