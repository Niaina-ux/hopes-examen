@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="py-3">
    <div class="flex items-center">
        <a href="" class="hover:underline">
            Retour/ 
        </a>
        <span class="font-semibold"> Création</span>
    </div>
    <div class="w-full">
        <h2 class="text-2xl font-semibold text-vert border-b-2 border-black/20 pb-2
            dark:border-white/10">Créer votre exercice relier par flèche</h2>
        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mt-4
                dark:bg-red-500/10">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('prof.relier.question.store', [$slug, $relier->id]) }}"
            class="mt-4 p-4 rounded-md border border-black/3 bg-black/1
                dark:border-white/3 dark:bg-white/1">
            @csrf
            <div class="w-full pb-4 border-b-2 border-black/20
                dark:border-white/10 ">
                <div class="mb-2">
                    <label class="inline-block w-full">Question</label>
                    <textarea name="enonce" class="form-control bg-white/90 formulaire w-full p-2 border border-black/20 rounded-md 
                    dark:border-white/10 dark:bg-white/3">{{ old('enonce') }}</textarea>
                    @error('enonce') <small class="text-red-600">{{ $message }}</small> @enderror
                </div>
                <div class="w-[11cm] flex justify-between gap-5">
                    <div class="flex-1">
                        <label>Points</label>
                        <input type="text" name="points" value="{{ old('points', 1) }}" class="border bg-white/90 formulaire border-black/20 rounded-md  p-2
                        dark:border-white/10 dark:bg-white/3">
                        @error('points') <small class="text-red-600">{{ $message }}</small> @enderror
                    </div>
                    <div class="flex-1">
                        <label>Ordre Question</label>
                        <input type="text" name="ordre" value="{{ old('ordre', 1) }}" class="border bg-white/90 formulaire border-black/20 rounded-md  p-2
                        dark:border-white/10 dark:bg-white/3">
                        @error('ordre') <small class="text-red-600">{{ $message }}</small> @enderror
                    </div>
                </div>
            </div>
            <div class="w-[95%]">
                <div class="flex justify-between mt-2 gap-10">
                    <h4 class="font-semibold text-base flex-1 ">Colonne gauche</h4>
                    <h4 class="font-semibold text-base flex-1 ">Colonne droite</h4>
                </div>
                @error('element_gauche') <small class="text-red-600">{{ $message }}</small> @enderror
                @error('element_droit') <small class="text-red-600">{{ $message }}</small> @enderror
                <div id="tablePaires">
                    {{-- ---- --}}
                </div>
            </div>
            <div class="mt-5">
                <button type="button" id="addRow" 
                    class="bg-black/50 rounded-md p-2 px-5 text-white dark:bg-white/20">
                    <i class="fa-solid fa-plus"></i> Ajouter une paire
                </button>
                <button class="bg-rouge rounded-md p-2 px-5 text-white">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
const table = document.getElementById('tablePaires');

const oldElementGauche = @json(old('element_gauche', []));
const oldElementDroit  = @json(old('element_droit', []));
const oldOrderLeft     = @json(old('order_left', []));
const oldOrderRight    = @json(old('order_right', []));

function creerLigne(valGauche = '', valDroit = '', ordreGauche = 1, ordreDroit = 1) {
    const div = document.createElement('div');
    div.className = 'paire flex justify-between gap-10 mt-3 relative';
    div.innerHTML = `
        <div class="flex-1 flex gap-2">
            <input class="border w-full bg-white/90 formulaire border-black/20 rounded-md p-2 
                dark:border-white/10 dark:bg-white/3" name="element_gauche[]" value="${valGauche}">
            <input class="border border-black/20 bg-white/90 formulaire rounded-md p-2 w-[1cm] text-center bg-black/10
            dark:border-white/10 dark:bg-white/3" type="text" name="order_left[]" value="${ordreGauche}">
        </div>
        <div class="flex-1 flex gap-2">
            <input class="border border-black/20 bg-white/90 formulaire rounded-md bg-white/90 formulaire p-2 w-[1cm] text-center bg-black/10
            dark:border-white/10 dark:bg-white/3" type="text" name="order_right[]" value="${ordreDroit}">
            <input class="border w-full border-black/20 rounded-md p-2 bg-white/90 formulaire
                dark:border-white/10 dark:bg-white/3" name="element_droit[]" value="${valDroit}">
        </div>
        <button type="button" class="remove absolute top-0 -right-10 bg-red-500/70 text-white px-2 rounded">X</button>
    `;
    table.appendChild(div);
}

document.getElementById('addRow').addEventListener('click', function () {
    creerLigne();
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove')) {
        e.target.closest('.paire').remove();
    }
});


if (oldElementGauche.length > 0) {
    oldElementGauche.forEach(function (valGauche, index) {
        creerLigne(
            valGauche || '',
            oldElementDroit[index] || '',
            oldOrderLeft[index] || 1,
            oldOrderRight[index] || 1
        );
    });
} else {
    creerLigne(); 
}
</script>
@endsection