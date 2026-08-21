@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="py-3">
    <div>
        <a href=" ">
            Retour / 
        </a>
        <span class="font-semibold">Création</span>
    </div>
    
    <div class="">
        <h2 class="text-2xl text-vert border-b-2 border-black/10 pb-1 mb-4 font-semibold
        dark:border-white/10">Ajouter une question — Compléter le pointillé</h2>
        @if(session('success'))
            <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 mb-2 rounded-md mt-2 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('success-alert').remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        
        <form action="{{route('prof.pointiller.question.store', [$slug, $pointiller->id])}}" 
            method="POST" 
            enctype="multipart/form-data"
            class="border  border-black/3 bg-black/1 p-4 rounded-md
            dark:border-white/3 dark:bg-white/1">
            @csrf

            <div class="">
                <label class="block font-medium">Énoncé</label>
                <p class="text-xs mb-1">Utilisez [1], [2], [3]... pour indiquer les mots à compléter. Ex: Le [1] web est l'ensemble de [2] parfait.</p>
                <textarea id="enonce" 
                        name="enonce" 
                        rows="3" 
                        class="border border-black/20 rounded bg-white/90 formulaire w-full p-2
                            dark:border-white/10 dark:bg-white/2" 
                        placeholder="Le [1] web est l'ensemble de [2] parfait.">{{ old('enonce') }}</textarea>
                        @error('enonce') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class=" gap-4 mb-4 hidden">
                <div class="flex-1">
                    <label class="block font-medium">Image (optionnel)</label>
                    <input type="file" 
                            name="image" 
                            accept="image/*" 
                            class="border border-black/20 rounded bg-white/90 formulaire w-full p-2
                                dark:border-white/10 dark:bg-white/2">
                            @error('image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex-1">
                    <label class="block  font-medium">Vidéo (optionnel)</label>
                    <input type="file" 
                            name="video" 
                            accept="video/*" 
                            class="border border-black/20 rounded bg-white/90 formulaire w-full p-2
                                dark:border-white/10 dark:bg-white/2">
                            @error('video') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Points</label>
                <input type="text" 
                        name="points" 
                        value="{{ old('points', 1) }}" 
                        min="0.1" 
                        step="0.1" 
                        class="border border-black/20 bg-white/90 formulaire rounded w-32 p-2
                            dark:border-white/10 dark:bg-white/2">
                        @error('points') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            @error('trous')
                <p class="text-red-500 text-sm mb-2">{{ $message }}</p>
            @enderror

            <button type="button" id="detect-trous" class="bg-black/5 border rounded-md px-4 py-2 mb-4
                 dark:bg-white/5">
                Détecter les trous depuis l'énoncé
            </button>

            <div id="trous-container" class="space-y-4">
                @if($errors->any() && old('trous'))
                    @foreach(old('trous') as $index => $trou)
                        <div class="border rounded-md p-3 bg-black/2">
                            <h4 class="font-semibold mb-2">Trou {{ $index + 1 }}</h4>

                            <div class="mb-2">
                                <label class="block font-medium">Réponse correcte</label>
                                <input type="text" 
                                        name="trous[{{ $index }}][reponse_correcte]" 
                                        value="{{ $trou['reponse_correcte'] ?? '' }}" 
                                        class="border border-black/20 rounded w-full bg-white/90 formulaire p-2
                                            dark:border-white/10 dark:bg-white/2">
                                        @error("trous.$index.reponse_correcte") <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="choices-container">
                                <label class="block font-medium mb-1">Banque de mots (choix proposés)</label>
                                <div class="space-y-1 choices-list">
                                    @foreach(($trou['choices'] ?? [null, null]) as $choice)
                                        <div class="flex gap-2 choice-row">
                                            <input type="text" 
                                                    name="trous[{{ $index }}][choices][]" 
                                                    value="{{ $choice }}" 
                                                    class="border border-black/20 bg-white/90 formulaire rounded w-full p-2
                                                        dark:border-white/10 dark:bg-white/2">
                                            <button type="button" class="remove-choice text-rouge px-2 border rounded" title="Supprimer ce choix">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                @error("trous.$index.choices") <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                <button type="button" class="add-choice text-vert underline text-sm mt-1">+ Ajouter un choix</button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="mt-4">
                <button type="submit" id="submit-btn" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('detect-trous').addEventListener('click', function () {
    const enonce = document.getElementById('enonce').value;
    const matches = [...enonce.matchAll(/\[(\d+)\]/g)];
    const container = document.getElementById('trous-container');
    container.innerHTML = '';

    if (matches.length === 0) {
        alert('Aucun trou détecté. Utilisez le format [1], [2], [3]... dans l\'énoncé.');
        return;
    }

    matches.forEach((match, index) => {
        const trouNum = match[1];
        const div = document.createElement('div');
        div.className = 'border rounded-md p-3 bg-black/2';
        div.innerHTML = `
            <h4 class="font-semibold mb-2">Trou [${trouNum}]</h4>
            <div class="mb-2">
                <label class="block  font-medium">Réponse correcte</label>
                <input type="text" 
                        name="trous[${index}][reponse_correcte]" 
                        class="border border-black/20 bg-white/90 formulaire rounded w-full p-2
                            dark:border-white/10 dark:bg-white/2" 
                        placeholder="Ex: developement">
            </div>
            <div class="choices-container">
                <label class="block  font-medium mb-1">Banque de mots (choix proposés)</label>
                <div class="space-y-1 choices-list">
                    <div class="flex gap-2 choice-row">
                        <input type="text" 
                                name="trous[${index}][choices][]" 
                                class="border border-black/20 rounded bg-white/90 formulaire w-full p-2
                                    dark:border-white/10 dark:bg-white/2" 
                                placeholder="Choix 1">
                        <button type="button" class="remove-choice text-rouge px-2 border rounded" title="Supprimer ce choix">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="flex gap-2 choice-row">
                        <input type="text" 
                                name="trous[${index}][choices][]" 
                                class="border border-black/20 rounded bg-white/90 formulaire w-full p-2
                                    dark:border-white/10 dark:bg-white/2" 
                                placeholder="Choix 2">
                        <button type="button" class="remove-choice text-rouge px-2 border rounded" title="Supprimer ce choix">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="add-choice text-vert underline text-sm mt-1">+ Ajouter un choix</button>
            </div>
        `;
        container.appendChild(div);
    });
});

// ✅ Un seul listener global gère ajout ET suppression
document.addEventListener('click', function (e) {
    // Ajouter un choix
    if (e.target.closest('.add-choice')) {
        const btn = e.target.closest('.add-choice');
        const block = btn.closest('.choices-container');
        const list = block.querySelector('.choices-list');
        const count = list.querySelectorAll('input').length;
        const trouDiv = btn.closest('[class*="border"]');
        const trouIndex = trouDiv.querySelector('input[name*="reponse_correcte"]').name.match(/trous\[(\d+)\]/)[1];

        const row = document.createElement('div');
        row.className = 'flex gap-2 choice-row';
        row.innerHTML = `
            <input type="text" 
                    name="trous[${trouIndex}][choices][]" 
                    placeholder="Choix ${count + 1}" 
                    class="border border-black/20 rounded bg-white/90 formulaire w-full p-2">
            <button type="button" class="remove-choice text-rouge px-2 border rounded" title="Supprimer ce choix">
                <i class="fa-solid fa-xmark"></i>
            </button>
        `;
        list.appendChild(row);
        return;
    }

    // ✅ Supprimer un choix
    if (e.target.closest('.remove-choice')) {
        const row = e.target.closest('.choice-row');
        const list = row.closest('.choices-list');

        if (list.querySelectorAll('.choice-row').length <= 2) {
            alert('Il faut garder au moins 2 choix.');
            return;
        }

        row.remove();
    }
});

document.querySelector('form').addEventListener('submit', function () {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerText = 'Enregistrement...';
});
</script>
@endsection