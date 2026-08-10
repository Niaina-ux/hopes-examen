@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class=" py-3 me-2">
    <div class="">
        <a href="{{ route('prof.examen.image', [$slug, $examen->id]) }}"
            class="hover:underline">
            Retour/
        </a>
        <span class="font-semibold">Création</span>
    </div>
    <div class="bg-white  rounded-md my-2">
        <h2 class="text-xl font-semibold mb-4 text-vert border-b-2 border-black/20 pb-1">Ajouter une image — {{ $image->titre }}</h2>
        @if($errors->any())
            <div class="mb-4 p-3 rounded-md bg-red-50 border border-rouge text-rouge text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('prof.examen.image.question.store', [$slug, $examen->id, $image->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-base font-medium">Instruction</label>
                <textarea name="instruction" rows="2" class="border border-black/20 rounded w-full p-2" placeholder="Ex: Détourez cette image">{{ old('instruction') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-base font-medium">Image</label>
                <input type="file" name="image" accept="image/*" class="border border-black/20 rounded w-full p-2">
            </div>

            <div class="mb-4">
                <label class="block text-base font-medium">Points</label>
                <input type="text" name="points" value="{{ old('points', 1) }}" min="0.1" step="0.1" class="border border-black/20 rounded w-32 p-2">
            </div>

            <button type="submit" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer</button>
        </form>
    </div>
</div>
@endsection