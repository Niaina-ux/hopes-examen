@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="w-full py-3">
    <div class="">
        <a href="">
            Retour / 
        </a>
        <span class="font-semibold">Creation</span>
    </div>
    <div class="bg-white rounded-md me-2">
        @if(session('success'))
            <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md mt-4 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('success-alert').remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        <h2 class="text-2xl font-semibold mt-1 text-vert border-b-2 border-black/10 pb-1">Ajouter un exercice de rédaction</h2>
        <form action="{{ route('prof.examen.redaction.store', [$slug, $examen->id]) }}" method="POST"
            class="p-4 rounded-md border border-black/3 bg-black/1 mt-4">
            @csrf
            <div class="mb-4">
                <label class="block text-base font-medium">Titre (optionnel)</label>
                <input type="text" 
                        name="titre" 
                        value="{{ old('titre') }}" 
                        class="border border-black/20 bg-white/90 formulaire rounded w-full p-2" 
                        placeholder="Ex: Rédaction sur le développement ...">
                        @error('titre') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-base font-medium">Sujet</label>
                <textarea name="sujet" 
                        rows="3" 
                        class="border border-black/20 bg-white/90 formulaire rounded w-full p-2" 
                        placeholder="Ex: Développez ce sujet et répondez .">{{ old('sujet') }}</textarea>
                        @error('sujet') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-base font-medium">Instruction (optionnel)</label>
                <textarea name="instruction" 
                        rows="3" 
                        class="border bg-white/90 formulaire border-black/20 rounded w-full p-2" 
                        placeholder="Consignes supplémentaires...">{{ old('instruction') }}</textarea>
                        @error('instruction') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-4 mb-4 w-[10cm]">
                <div class="flex-1 hidden">
                    <label class="block text-base font-medium">Mots minimum</label>
                    <input type="text" 
                            name="nombre_mots_min" 
                            value="{{ old('nombre_mots_min') }}" 
                            min="1" 
                            class="border border-black/20 bg-white/90 formulaire rounded w-full p-2 " 
                            placeholder="Ex: 100">
                            @error('nombre_mots_min') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex-1">
                    <label class="block text-base font-medium">Mots maximum</label>
                    <input type="text" 
                        name="nombre_mots_max" 
                        value="{{ old('nombre_mots_max') }}" 
                        min="1" 
                        class="border border-black/20 bg-white/90 formulaire rounded w-full p-2" 
                        placeholder="Ex: 250">
                        @error('nombre_mots_max') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex-1">
                    <label class="block text-base font-medium">Note totale</label>
                    <input type="text" 
                            name="note_totale" 
                            value="{{ old('note_totale') }}" 
                            min="0.1" 
                            step="0.1" 
                            class="border border-black/20 bg-white/90 formulaire rounded w-full p-2">
                            @error('note_totale') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <button type="submit" id="submit-btn" class="bg-rouge text-white px-5 py-2 rounded-full mt-2">Enregistrer</button>
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