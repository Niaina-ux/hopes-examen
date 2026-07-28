@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
    <div class="w-full ">
    <div class="bg-white p-4 rounded-md">
        <a href="{{ route('prof.examen.fichier.qeustion.show', [$slug, $examen->id, $fichier->id, $question->id]) }}">
            <i class="fa-solid fa-arrow-left-long"></i>
        </a>
        <h2 class="text-xl font-semibold mb-4">Modifier le devoir</h2>

        <form action="{{ route('prof.examen.fichier.qeustion.update', [$slug, $examen->id, $fichier->id, $question->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium">Instruction</label>
                <textarea name="instruction" rows="4" class="border rounded w-full p-2">{{ old('instruction', $question->instruction) }}</textarea>
                @error('instruction') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Fichier actuel</label>
                @if($question->fichier_prof)
                    <a href="{{ asset('fichiers/prof/' . $question->fichier_prof) }}" target="_blank" class="text-vert underline text-sm block mb-2">
                        <i class="fa-solid fa-paperclip"></i> {{ $question->fichier_prof }}
                    </a>
                @else
                    <p class="text-black/50 text-sm mb-2">Aucun fichier fourni actuellement.</p>
                @endif
                <label class="block text-sm font-medium">Remplacer le fichier (optionnel)</label>
                <input type="file" name="fichier_prof" class="border rounded w-full p-2">
                @error('fichier_prof') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Points</label>
                <input type="number" name="points" value="{{ old('points', $question->points) }}" min="0.1" step="0.1" class="border rounded w-32 p-2">
                @error('points') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" id="submit-btn" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer les modifications</button>
        </form>
    </div>
</div>

<script>
document.querySelector('form').addEventListener('submit', function () {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerText = 'Enregistrement...';
});
</script>
@endsection