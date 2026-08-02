@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="bg-white py-3 rounded-md">
    <div class="">
        <a href="">
            Examen-type/
        </a>
        <span>Assign-types</span>
    </div>
    <h2 class="text-2xl font-semibold my-1 text-vert">{{ $examen->titre }}</h2>
    <p class="text-black/60 mb-4">Sélectionnez les types d'exercice pour cet examen et définissez leur ordre</p>

    @if(session('success'))
        <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md my-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="document.getElementById('success-alert').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @error('type_exercice_id')
        <div class="bg-red-50 border border-rouge text-rouge px-4 py-2 rounded-md mb-4">{{ $message }}</div>
    @enderror

    @error('ordre')
        <div class="bg-red-50 border border-rouge text-rouge px-4 py-2 rounded-md mb-4">{{ $message }}</div>
    @enderror

    @error('ordre.*')
        <div class="bg-red-50 border border-rouge text-rouge px-4 py-2 rounded-md mb-4">{{ $message }}</div>
    @enderror

    <form action="{{ route('prof.examen.storeTypes',[$slug, $examen->id]) }}" method="POST">
        @csrf

        <div class="mb-10 mt-5 border border-black/10 rounded-md p-2 bg-black/3">
            @foreach($typesExercice as $type)
                @php
                    $isChecked = in_array($type->id, old('type_exercice_id', $examen->typesExercice->pluck('id')->toArray()));
                    $ordreActuel = old('ordre.' . $type->id, $examen->typesExercice->firstWhere('id', $type->id)?->pivot?->ordre ?? 0);
                @endphp
                <label class="flex items-center justify-between gap-3 border-b border-black/20 p-2 cursor-pointer {{ $loop->iteration == 2 ? ' bg-white/70' : '' }}">
                    <div class="gap-2 w-8 h-8 bg-black/5 rounded-md flex justify-center items-center">
                        <i class="{{ $type->icone ?? 'fa-solid fa-shapes' }} text-vert"></i>
                    </div>
                    <div class="flex-1">
                        {{ $type->nom }}
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-black/40">Ordre</span>
                        <input type="number" name="ordre[{{ $type->id }}]" value="{{ $ordreActuel }}" min="0"
                            class="border rounded w-16 p-1 text-center text-sm ordre-input">
                        <input type="checkbox" name="type_exercice_id[]" value="{{ $type->id }}" {{ $isChecked ? 'checked' : '' }}
                            class="custom-checkbox">
                    </div>
                </label>
            @endforeach
        </div>

        <button type="submit" id="submit-btn" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer</button>
    </form>
</div>

<script>
document.querySelector('form').addEventListener('submit', function (e) {
    const checkboxes = document.querySelectorAll('input[name="type_exercice_id[]"]:checked');

    if (checkboxes.length === 0) {
        return; // laisse le serveur gérer le message "Veuillez sélectionner au moins un type"
    }

    const ordres = [];

    checkboxes.forEach(function (checkbox) {
        const typeId = checkbox.value;
        const ordreInput = document.querySelector(`input[name="ordre[${typeId}]"]`);
        ordres.push(ordreInput.value);
    });

    const ordresUniques = new Set(ordres);

    if (ordresUniques.size !== ordres.length) {
        e.preventDefault();
        alert('Deux types d\'exercice sélectionnés ont le même ordre. Veuillez attribuer un ordre unique à chacun.');
        return;
    }

    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerText = 'Enregistrement...';
});
</script>
@endsection