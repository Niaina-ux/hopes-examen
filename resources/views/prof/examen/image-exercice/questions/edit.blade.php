@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="me-2 py-3">
    <div class="">
        <a href="{{ route('prof.examen.image', [$slug, $examen->id]) }}"
            class="hover:underline">
            Retour/
        </a>
        <span class="font-semibold">Création</span>
    </div>
    <div class="bg-white py-2 rounded-md">
        <h2 class="text-xl font-semibold mb-4 border-b-2 border-black/20 pb-1">Modifier la question — {{ $image->titre }}</h2>

        @if($errors->any())
            <div class="mb-4 p-3 rounded-md bg-red-50 border border-rouge text-rouge text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('prof.examen.image.question.update', [$slug, $examen->id, $image->id, $question->id]) }}" 
              method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-base font-medium">Instruction</label>
                <textarea name="instruction" rows="2" class="border border-black/20 rounded w-full p-2" 
                    placeholder="Ex: Détourez cette image">{{ old('instruction', $question->instruction) }}</textarea>
            </div>
            <div class="flex gap-5">
                <div class="flex-1">
                    <div class="mb-4 w-[80%]">
                        <label class="block text-base font-medium">Image</label>
                        <input type="file" name="image" accept="image/*" class="border border-black/20 rounded w-full p-2">
                        <p class="text-xs text-black/40 mt-1">Laissez vide pour garder l'image actuelle.</p>
                    </div>
        
                    <div class="mb-4">
                        <label class="block text-base font-medium">Points</label>
                        <input type="number" name="points" value="{{ old('points', $question->points) }}" 
                            min="0.1" step="0.1" class="border border-black/20 rounded w-32 p-2">
                    </div>
        
                    <button type="submit" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer</button>
                </div>
                <div class="border border-black/10 w-[30%] rounded-md  overflow-hidden">
                    @if($question->image)
                        <img src="{{ asset('images/image_exercice/' . $question->image) }}" 
                             class="w-fll h-full object-cover ">
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
@endsection