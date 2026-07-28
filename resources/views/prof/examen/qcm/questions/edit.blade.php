@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class=" py-3">
    <div class="w-full">
        <div class="">
            <a href="{{ route('prof.examen.qcm.question.show', [$slug, $examen->id, $qcm->id]) }}">
                Retour / 
            </a>
            <span class="font-semibold">Modification</span>
        </div>
        <div class="bg-white rounded-md me-2">
            <h2 class="text-xl font-semibold my-2 text-vert">Modifier la question - {{ $qcm->titre }}</h2>
            <form action="{{ route('prof.examen.qcm.question.update', [$slug, $examen->id, $qcm->id, $question->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="question-block rounded-md mb-4">
                    <div class="mb-2">
                        <label class="block text-sm font-medium">Énoncé</label>
                        <textarea name="enonce" rows="2" class="border border-black/20 rounded w-full p-2" required>{{ old('enonce', $question->enonce) }}</textarea>
                        @error('enonce') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-2">
                        <label class="block text-sm font-medium mb-1">Média actuel</label>
                        @if($question->image)
                            <img src="{{ asset('images/questions/' . $question->image) }}" class="w-24 h-24 object-cover rounded-md mb-2">
                        @endif
                        @if($question->video)
                            <video controls class="w-48 rounded-md mb-2">
                                <source src="{{ asset('videos/questions/' . $question->video) }}">
                            </video>
                        @endif

                        <div class="flex gap-4 mb-2">
                            <div class="flex-1">
                                <label class="block text-sm font-medium">Remplacer par une image</label>
                                <input type="file" name="image" accept="image/*" class="border border-black/20 rounded w-full p-2">
                                @error('image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium">Remplacer par une vidéo</label>
                                <input type="file" name="video" accept="video/*" class="border border-black/20 rounded w-full p-2">
                                @error('video') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                    <div class=" flex gap-5 my-4">
                        <div class="">
                            <label class="block text-sm font-medium">Points</label>
                            <input 
                                type="number" 
                                name="points" 
                                value="{{ old('points', $question->points) }}" 
                                min="0.1" step="0.1" 
                                class="border  border-black/20 rounded w-32 p-2">
                            @error('points') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="">
                            <label class="block text-sm font-medium">Durée (secondes)</label>
                            <input
                                type="number"
                                name="duree_seconde"
                                value="{{ old('duree_seconde', $question->duree_seconde) }}"
                                min="1"
                                class="border border-black/20 rounded w-32 p-2">
                            @error('duree_seconde')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="">
                            <label class="block text-sm font-medium">Type de réponse</label>
                            <select name="reponse_type" class="reponse-type border border-black/20 rounded w-full p-2" onchange="toggleReponseType(this)">
                                <option value="single" {{ old('reponse_type', $question->reponse_type) == 'single' ? 'selected' : '' }}>Réponse simple</option>
                                <option value="multiple" {{ old('reponse_type', $question->reponse_type) == 'multiple' ? 'selected' : '' }}>Réponse multiple</option>
                                <option value="true_false" {{ old('reponse_type', $question->reponse_type) == 'true_false' ? 'selected' : '' }}>Vrai / Faux</option>
                            </select>
                        </div>
                    </div>
                    <label class="block font-medium mb-1">Choix</label>
                    <div class="choices-block border border-black/20 bg-black/2 p-2 rounded-md {{ $question->reponse_type === 'true_false' ? 'hidden' : '' }}">
                        <div class="choices-container space-y-2 ">
                            @foreach($question->qcmChoices as $cIndex => $choice)
                                <div class="flex gap-2 items-center border border-black/20 rounded px-2">
                                    @if($question->reponse_type === 'single')
                                        <input type="radio" class="est-correcte-input" name="correct_choice" value="{{ $cIndex }}" {{ $choice->est_correcte ? 'checked' : '' }}>
                                    @else
                                        <input type="checkbox" class="est-correcte-input" name="choices[{{ $cIndex }}][est_correcte]" value="1" {{ $choice->est_correcte ? 'checked' : '' }}>
                                    @endif
                                    <input type="text" name="choices[{{ $cIndex }}][texte]" value="{{ $choice->texte }}" class="border-s-2 border-black/20 bg-white p-2 flex-1">
                                </div>
                            @endforeach
                        </div>
                        @error('correct_choice') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        @error('choices') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        <button type="button" class="add-choice text-vert underline text-sm mt-2 border border-black/10 rounded p-1 px-2 bg-black/5">+ Ajouter un choix</button>
                    </div>
                    <div class="vrai-faux-block mt-2 {{ $question->reponse_type === 'true_false' ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium mb-1">Bonne réponse</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 border border-black/20 rounded p-2">
                                <input type="radio" name="vrai_faux_correct" value="vrai" {{ $question->reponse_type === 'true_false' && $question->qcmChoices->firstWhere('texte', 'Vrai')?->est_correcte ? 'checked' : '' }}> Vrai
                            </label> 
                            <label class="flex items-center gap-2 border border-black/20 rounded p-2">
                                <input type="radio" name="vrai_faux_correct" value="faux" {{ $question->reponse_type === 'true_false' && $question->qcmChoices->firstWhere('texte', 'Faux')?->est_correcte ? 'checked' : '' }}> Faux
                            </label>
                        </div>
                        @error('vrai_faux_correct') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <button type="submit" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer les modifications</button>
            </form>
        </div>
    </div>
</div>

<script>
function toggleReponseType(select) {
    const block = select.closest('.question-block');
    const choicesBlock = block.querySelector('.choices-block');
    const vraiFauxBlock = block.querySelector('.vrai-faux-block');

    if (select.value === 'true_false') {
        choicesBlock.classList.add('hidden');
        vraiFauxBlock.classList.remove('hidden');
    } else {
        choicesBlock.classList.remove('hidden');
        vraiFauxBlock.classList.add('hidden');
    }
}

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('add-choice')) {
        const block = e.target.closest('.question-block');
        const container = block.querySelector('.choices-container');
        const choiceIndex = container.querySelectorAll('input[type="text"]').length;
        const reponseType = block.querySelector('.reponse-type').value;

        const inputType = reponseType === 'single' ? 'radio' : 'checkbox';
        const inputName = reponseType === 'single' ? 'correct_choice' : `choices[${choiceIndex}][est_correcte]`;
        const inputValue = reponseType === 'single' ? choiceIndex : '1';

        const div = document.createElement('div');
        div.className = 'flex gap-2 items-center';
        div.innerHTML = `
        <div class="choices-container space-y-2 border border-black/20 bg-black/2 p-2 rounded-md">
            <input type="${inputType}" class="est-correcte-input" name="${inputName}" value="${inputValue}">
            <input type="text" name="choices[${choiceIndex}][texte]" placeholder="Choix ${choiceIndex + 1}" class="border-s-2 border-black/20 bg-white p-2 flex-1">
        </div>
        `;
        container.appendChild(div);
    }
});
</script>
@endsection