@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="w-full py-3">
    <a href="{{ route('prof.examen.code.question.show', [$slug, $examen->id, $code->id]) }}">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="bg-white p-4 rounded-md">
        @if(session('success'))
            <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md mt-4 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('success-alert').remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        <h2 class="text-xl font-semibold mb-4">Modifier l'exercice de code — {{ $code->titre }}</h2>
        <form action="{{ route('prof.examen.code.question.update', [$slug, $examen->id, $code->id, $question->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-base font-medium">Instruction</label>
                <textarea name="instruction" rows="3" class="border rounded w-full p-2" placeholder="Ex: Écrivez une fonction qui calcule la somme de deux nombres.">{{ old('instruction', $question->instruction) }}</textarea>
                @error('instruction') <p class="text-red-500 text-base mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-base font-medium">Langage</label>
                <select name="langage" class="border rounded w-full p-2">
                    <option value="ensemble" {{ old('langage', $question->langage) == 'ensemble' ? 'selected' : '' }}>Ensemble</option>
                    <option value="php" {{ old('langage', $question->langage) == 'php' ? 'selected' : '' }}>PHP</option>
                    <option value="javascript" {{ old('langage', $question->langage) == 'javascript' ? 'selected' : '' }}>JavaScript</option>
                    <option value="html" {{ old('langage', $question->langage) == 'html' ? 'selected' : '' }}>HTML</option>
                    <option value="css" {{ old('langage', $question->langage) == 'css' ? 'selected' : '' }}>CSS</option>
                    <option value="laravel" {{ old('langage', $question->langage) == 'laravel' ? 'selected' : '' }}>Laravel</option>
                </select>
                @error('langage') <p class="text-red-500 text-base mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-base font-medium">Code de départ (optionnel)</label>
                <textarea name="code_starter" rows="6" class="border rounded w-full p-2 font-mono text-base" placeholder="function somme($a, $b) {&#10;    // votre code ici&#10;}">{{ old('code_starter', $question->code_starter) }}</textarea>
                @error('code_starter') <p class="text-red-500 text-base mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-base font-medium">Points</label>
                <input type="number" name="points" value="{{ old('points', $question->points) }}" min="0.1" step="0.1" class="border rounded w-32 p-2">
                @error('points') <p class="text-red-500 text-base mt-1">{{ $message }}</p> @enderror
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