@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="w-full py-3">
    <a href="{{ route('prof.examen.motscroises.mot.index', [$slug, $examen->id, $motsCroise->id]) }}">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="bg-white p-4 rounded-md">
        <h2 class="text-xl font-semibold mb-4">Modifier le mot — {{ $motsCroise->titre }}</h2>

        @error('reponse')
            <div class="mb-4 p-3 rounded-md bg-red-50 border border-rouge text-rouge text-sm">
                {{ $message }}
            </div>
        @enderror

        <form action="{{ route('prof.examen.motscroises.mot.update', [$slug, $examen->id, $motsCroise->id, $mot->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-base font-medium">Indice</label>
                <input type="text" name="indice" value="{{ old('indice', $mot->indice) }}" class="border rounded w-full p-2">
                @error('indice') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-base font-medium">Réponse</label>
                <input type="text" id="reponse-input" name="reponse" value="{{ old('reponse', $mot->reponse) }}" class="border rounded w-full p-2 uppercase" maxlength="30">
                @error('reponse') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-4 mb-4">
                <div class="flex-1">
                    <label class="block text-base font-medium">Direction</label>
                    <select name="direction" class="border rounded w-full p-2">
                        <option value="horizontal" {{ old('direction', $mot->direction) == 'horizontal' ? 'selected' : '' }}>Horizontal</option>
                        <option value="vertical" {{ old('direction', $mot->direction) == 'vertical' ? 'selected' : '' }}>Vertical</option>
                    </select>
                    @error('direction') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex-1">
                    <label class="block text-base font-medium">Position X (colonne)</label>
                    <input type="number" name="position_x" value="{{ old('position_x', $mot->position_x) }}" min="0" class="border rounded w-full p-2">
                    @error('position_x') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex-1">
                    <label class="block text-base font-medium">Position Y (ligne)</label>
                    <input type="number" name="position_y" value="{{ old('position_y', $mot->position_y) }}" min="0" class="border rounded w-full p-2">
                    @error('position_y') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-4 mb-4">
                <div class="flex-1">
                    <label class="block text-base font-medium">Numéro</label>
                    <input type="number" name="numero" value="{{ old('numero', $mot->numero) }}" min="1" class="border rounded w-full p-2">
                    @error('numero') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex-1">
                    <label class="block text-base font-medium">Points</label>
                    <input type="number" name="points" value="{{ old('points', $mot->points) }}" min="0.1" step="0.1" class="border rounded w-full p-2">
                    @error('points') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-base font-medium mb-1">Lettres à afficher comme indice (optionnel)</label>
                <div id="lettres-container" class="flex gap-2 flex-wrap"></div>
            </div>

            <button type="submit" id="submit-btn" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer les modifications</button>
        </form>
    </div>
</div>

<script>
const reponseInput = document.getElementById('reponse-input');
const lettresContainer = document.getElementById('lettres-container');
const oldPositionsVisibles = @json(old('positions_lettres_visibles', $mot->positions_lettres_visibles ?? []));

function genererCheckboxLettres() {
    const reponse = reponseInput.value.toUpperCase();
    lettresContainer.innerHTML = '';

    for (let i = 0; i < reponse.length; i++) {
        const label = document.createElement('label');
        label.className = 'flex flex-col items-center gap-1 border rounded p-2 cursor-pointer';

        const checked = oldPositionsVisibles.includes(i) ? 'checked' : '';

        label.innerHTML = `
            <span class="font-mono font-bold">${reponse[i]}</span>
            <input type="checkbox" name="positions_lettres_visibles[]" value="${i}" ${checked}>
        `;
        lettresContainer.appendChild(label);
    }
}

reponseInput.addEventListener('input', function () {
    this.value = this.value.toUpperCase();
    genererCheckboxLettres();
});

genererCheckboxLettres();

document.querySelector('form').addEventListener('submit', function () {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerText = 'Enregistrement...';
});
</script>
@endsection