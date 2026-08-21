@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="py-3">
    <a href="" class="flex items-center gap-2 uppercase text-sm">
        <i class="fa-solid fa-angle-left"></i> Questions
    </a>
    <div class="">
        <h2 class="text-2xl font-semibold mb-4">Modifier la question — Compléter le pointillé</h2>
        @if(session('success'))
            <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md mt-4 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('success-alert').remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        
        <form action="{{ route('prof.pointiller.question.update', [$slug, $pointiller->id, $question->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium">Énoncé</label>
                <p class="text-xs  mb-1">Utilisez [1], [2], [3]... pour indiquer les mots à compléter. Ex: Le [1] web est l'ensemble de [2] parfait.</p>
                <textarea id="enonce" name="enonce" rows="3" class="border rounded w-full p-2" placeholder="Le [1] web est l'ensemble de [2] parfait.">{{ old('enonce', $question->enonce) }}</textarea>
                @error('enonce') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-4 mb-4 hidden">
                <div class="flex-1">
                    <label class="block text-sm font-medium">Image (optionnel)</label>
                    @if($question->image)
                        <img src="{{ asset('images/questions/' . $question->image) }}" class="w-32 rounded-md mb-2 border border-black/10" alt="">
                    @endif
                    <input type="file" name="image" accept="image/*" class="border rounded w-full p-2">
                    @error('image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium">Vidéo (optionnel)</label>
                    @if($question->video)
                        <video src="{{ asset('videos/questions/' . $question->video) }}" controls class="w-32 rounded-md mb-2 border border-black/10"></video>
                    @endif
                    <input type="file" name="video" accept="video/*" class="border rounded w-full p-2">
                    @error('video') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Points</label>
                <input type="text" name="points" value="{{ old('points', $question->points) }}" min="0.1" step="0.1" class="border rounded w-32 p-2">
                @error('points') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            @error('trous')
                <p class="text-red-500 text-sm mb-2">{{ $message }}</p>
            @enderror

            <button type="button" id="detect-trous" class="bg-black/5 border rounded-md px-4 py-2 mb-4">
                Détecter les trous depuis l'énoncé
            </button>

            <div id="trous-container" class="space-y-4">
                @php
                    // Priorité : anciennes données (old(), si erreur de validation), sinon les réponses déjà enregistrées
                    $trousData = old('trous', $question->reponses->map(function ($reponse) {
                        return [
                            'reponse_correcte' => $reponse->reponse_correcte,
                            'choices' => $reponse->choices->pluck('texte')->toArray(),
                        ];
                    })->toArray());
                @endphp

                @foreach($trousData as $index => $trou)
                    <div class="border rounded-md p-3 bg-black/2">
                        <h4 class="font-semibold mb-2">Trou {{ $index + 1 }}</h4>

                        <div class="mb-2">
                            <label class="block text-sm font-medium">Réponse correcte</label>
                            <input type="text" name="trous[{{ $index }}][reponse_correcte]" value="{{ $trou['reponse_correcte'] ?? '' }}" class="border rounded w-full p-2">
                            @error("trous.$index.reponse_correcte") <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="choices-container">
                            <label class="block text-sm font-medium mb-1">Banque de mots (choix proposés)</label>
                            <div class="space-y-1">
                                @foreach(($trou['choices'] ?? [null, null]) as $choice)
                                    <input type="text" name="trous[{{ $index }}][choices][]" value="{{ $choice }}" class="border rounded w-full p-2">
                                @endforeach
                            </div>
                            @error("trous.$index.choices") <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            <button type="button" class="add-choice text-vert underline text-sm mt-1">+ Ajouter un choix</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                <button type="submit" id="submit-btn" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer les modifications</button>
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
                <label class="block text-sm font-medium">Réponse correcte</label>
                <input type="text" name="trous[${index}][reponse_correcte]" class="border rounded w-full p-2" placeholder="Ex: developement">
            </div>
            <div class="choices-container">
                <label class="block text-sm font-medium mb-1">Banque de mots (choix proposés)</label>
                <div class="space-y-1">
                    <input type="text" name="trous[${index}][choices][]" class="border rounded w-full p-2" placeholder="Choix 1">
                    <input type="text" name="trous[${index}][choices][]" class="border rounded w-full p-2" placeholder="Choix 2">
                </div>
                <button type="button" class="add-choice text-vert underline text-sm mt-1">+ Ajouter un choix</button>
            </div>
        `;
        container.appendChild(div);
    });
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('add-choice')) {
        const block = e.target.closest('.choices-container');
        const container = block.querySelector('.space-y-1');
        const count = container.querySelectorAll('input').length;
        const trouDiv = e.target.closest('[class*="border"]');
        const trouIndex = trouDiv.querySelector('input[name*="reponse_correcte"]').name.match(/trous\[(\d+)\]/)[1];

        const input = document.createElement('input');
        input.type = 'text';
        input.name = `trous[${trouIndex}][choices][]`;
        input.placeholder = `Choix ${count + 1}`;
        input.className = 'border rounded w-full p-2';
        container.appendChild(input);
    }
});

document.querySelector('form').addEventListener('submit', function () {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerText = 'Enregistrement...';
});
</script>
@endsection