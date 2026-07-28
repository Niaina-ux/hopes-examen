@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="w-full py-3">
    <div class="">
        <a href="">
            Retour / 
        </a>
        <span class="font-semibold">Modication</span>
    </div>
    <div class="bg-white rounded-md me-2">
        <h2 class="text-xl font-semibold my-2 text-vert">Modifier l'exercice de rédaction</h2>
        <form action="{{ route('prof.examen.redaction.update', [$slug, $examen->id, $redaction->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium">Titre (optionnel)</label>
                <input 
                    type="text" 
                    name="titre" 
                    value="{{ old('titre', $redaction->titre) }}" 
                    class="border border-black/20 rounded w-full p-2">
                    @error('titre') 
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                    @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Sujet</label>
                <textarea 
                    name="sujet" 
                    rows="3" 
                    class="border border-black/20 rounded w-full p-2"
                    >{{ old('sujet', $redaction->sujet) }}</textarea>
                    @error('sujet') 
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                    @enderror
            </div>
            <div class="mb-4">
                <label 
                    class="block text-sm font-medium">
                    Instruction (optionnel)
                </label>
                <textarea 
                    name="instruction" 
                    rows="3" 
                    class="border border-black/20 rounded w-full p-2"
                    >{{ old('instruction', $redaction->instruction) }}</textarea>
                    @error('instruction') 
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                    @enderror
            </div>
            <div class="flex gap-4 mb-4 w-[10cm]">
                <div class="flex-1 hidden">
                    <label class="block text-sm font-medium">
                        Mots minimum
                    </label>
                    <input 
                        type="number" 
                        name="nombre_mots_min" 
                        value="{{ old('nombre_mots_min', $redaction->nombre_mots_min) }}" 
                        min="1" 
                        class="border border-black/20 rounded w-full p-2">
                        @error('nombre_mots_min') 
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                        @enderror
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium">
                        Mots maximum
                    </label>
                    <input 
                        type="number" 
                        name="nombre_mots_max" 
                        value="{{ old('nombre_mots_max', $redaction->nombre_mots_max) }}" 
                        min="1" 
                        class="border border-black/20 rounded w-full p-2">
                        @error('nombre_mots_max') 
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                        @enderror
                </div>
                <div class="flex-1 gap-4 mb-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium">
                            Note totale
                        </label>
                        <input 
                            type="number" 
                            name="note_totale" 
                            value="{{ old('note_totale', $redaction->note_totale) }}" 
                            min="0.1" 
                            step="0.1" 
                            class="border border-black/20 rounded w-full p-2">
                        @error('note_totale') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
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