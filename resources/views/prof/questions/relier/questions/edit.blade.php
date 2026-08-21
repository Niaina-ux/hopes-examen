@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="py-3">
    <div class="flex items-center gap-3 ">
        <a href="" class=" flex justify-center items-center gap-2">
            <i class="fa-solid fa-angle-left"></i>
            Retour
        </a>
    </div>
    <div class="w-full">
        <h2 class="text-2xl font-semibold text-vert">Modifier l'exercice relier par flèche</h2>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mt-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('prof.relier.question.update', [$slug,  $relier->id, $question->id]) }}">
            @csrf
            @method('PUT')

            <div class="w-full pb-4 border-b-2 border-black/20">
                <div class="mb-2">
                    <label class="inline-block w-full">Question</label>
                    <textarea name="enonce" class="form-control w-full p-2 border border-black/10 rounded-md bg-black/3">{{ old('enonce', $question->enonce) }}</textarea>
                    @error('enonce') <small class="text-red-600">{{ $message }}</small> @enderror
                </div>
                <div class="w-[11cm] flex justify-between gap-5">
                    <div class="flex-1">
                        <label>Points</label>
                        <input type="number" name="points" value="{{ old('points', $question->points) }}" class="border border-black/10 rounded-md bg-black/3 p-2">
                        @error('points') <small class="text-red-600">{{ $message }}</small> @enderror
                    </div>
                    <div class="flex-1">
                        <label>Ordre Question</label>
                        <input type="number" name="ordre" value="{{ old('ordre', $question->ordre) }}" class="border border-black/10 rounded-md bg-black/3 p-2">
                        @error('ordre') <small class="text-red-600">{{ $message }}</small> @enderror
                    </div>
                </div>
            </div>

            <div class="w-[95%]">
                <div class="flex justify-between py-2">
                    <h4 class="font-semibold text-base flex-1 text-center">Colonne gauche</h4>
                    <h4 class="font-semibold text-base flex-1 text-center">Colonne droite</h4>
                </div>
                @error('element_gauche') <small class="text-red-600">{{ $message }}</small> @enderror
                @error('element_droit') <small class="text-red-600">{{ $message }}</small> @enderror

                <div id="tablePaires">
                    @foreach($question->paires as $paire)
                        <div class="paire flex justify-between gap-10 mb-2">
                            <div class="flex-1 flex gap-2">
                                <input class="form-control border w-full border-black/10 rounded-md p-2 bg-black/3" name="element_gauche[]" value="{{ $paire->element_gauche }}">
                                <input class="form-control border font-semibold border-black/3 rounded-md p-2 w-[2cm] text-center bg-black/10" type="number" name="order_left[]" value="{{ $paire->ordre_gauche }}">
                            </div>
                            <div class="flex-1 flex gap-2">
                                <input class="form-control border font-semibold border-black/10 rounded-md p-2 w-[2cm] text-center bg-black/10" type="number" name="order_right[]" value="{{ $paire->ordre_droite }}">
                                <input class="form-control border w-full border-black/10 rounded-md p-2 bg-black/3" name="element_droit[]" value="{{ $paire->element_droite }}">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-5">
                <button type="button" id="addRow" class="bg-black/50 rounded-md p-2 px-5 text-white">
                    <i class="fa-solid fa-plus"></i> Ajouter une paire
                </button>
                <button class="bg-rouge rounded-md p-2 px-5 text-white">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>

<script>
const table = document.getElementById('tablePaires');

document.getElementById('addRow').addEventListener('click', function () {
    table.insertAdjacentHTML('beforeend', `
        <div class="paire flex justify-between gap-10 mt-3 relative">
            <div class="flex-1 flex gap-2">
                <input class="border w-full border-black/10 rounded-md p-2 bg-black/3" name="element_gauche[]">
                <input class="border border-black/10 rounded-md p-2 w-[2cm] text-center bg-black/10" type="number" name="order_left[]" value="1">
            </div>
            <div class="flex-1 flex gap-2">
                <input class="border border-black/10 rounded-md p-2 w-[2cm] text-center bg-black/10" type="number" name="order_right[]" value="1">
                <input class="border w-full border-black/10 rounded-md p-2 bg-black/3" name="element_droit[]">
            </div>
            <button type="button" class="remove absolute top-0 -right-10 bg-red-500/70 text-white px-2 rounded">X</button>
        </div>
    `);
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove')) {
        e.target.closest('.paire').remove();
    }
});
</script>
@endsection