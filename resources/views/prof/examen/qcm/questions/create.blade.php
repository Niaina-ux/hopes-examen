@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="py-3 me-2">
    <div class="w-full ">
        <div class="">
            <a href="{{ route('prof.examen.qcm', [$slug, $examen->id]) }}">
                Retour / 
            </a>
            <span class="font-semibold">Creation</span>
        </div>
        <div class="bg-white  rounded-md me-2">
            <h2 class="text-2xl border-b-2 border-black/20 pb-1 font-semibold mt-2 mb-4 text-vert">Ajouter une question — {{ $qcm->titre }}</h2>
            @if(session('success'))
                <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md mt-4 flex justify-between items-center">
                    <span>{{ session('success') }}</span>
                    <button type="button" onclick="document.getElementById('success-alert').remove()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                    <p class=" mb-1">Veuillez corriger les erreurs suivantes :</p>
                </div>
            @endif

            <form action="{{ route('prof.examen.qcm.question.store', [$slug, $examen->id, $qcm->id]) }}" method="POST" enctype="multipart/form-data" 
                class="bg-black/1 rounded-md border border-black/3 p-4">
                @csrf

                <div class="question-block rounded-md mb-4">
                    <div class="mb-2">
                        <label class="block font-medium">Énoncé</label>
                        <textarea name="questions[0][enonce]" rows="2" class="formulaire border bg-white border-black/20 rounded w-full p-2" required placeholder="Combien de ...example de question">{{ old('questions.0.enonce') }}</textarea>
                        @error('questions.0.enonce') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-2">
                        <label class="block  font-medium mb-1">Média (optionnel)</label>
                        <div class="flex gap-4 mb-2">
                            <label class="flex items-center gap-2 border rounded-md p-2 px-4 bg-black/3 border-black/20">
                                <input type="radio" name="media_type_0" value="aucun" class="media-type-radio" data-index="0" checked>
                                Aucun
                            </label>
                            <label class="flex items-center gap-2 border rounded-md p-2 px-4 bg-black/3 border-black/20">
                                <input type="radio" name="media_type_0" value="image" class="media-type-radio" data-index="0">
                                Image
                            </label>
                            <label class="flex items-center gap-2 border rounded-md p-2 px-4 bg-black/3 border-black/20">
                                <input type="radio" name="media_type_0" value="video" class="media-type-radio" data-index="0">
                                Vidéo
                            </label>
                        </div>

                        <div class="media-image-block hidden my-4">
                            <label for="" class="">Choisir le fichier</label>
                            <input type="file" name="questions[0][image]" accept="image/*" class="formulaire bg-white border border-black/20 rounded w-full p-2">
                            @error('questions.0.image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="media-video-block hidden my-4">
                            <label for="" class="">Choisir le fichier</label>
                            <input type="file" name="questions[0][video]" accept="video/*" class="bg-white formulaire border border-black/20 rounded w-full p-2">
                            @error('questions.0.video') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="w-[15cm] my-2 mt-3">
                        <div class="flex gap-5 ">
                            <div class=" w-[3cm]">
                                <label class="block  font-medium">Points</label>
                                <input type="text" name="questions[0][points]" value="{{ old('questions.0.points') }}" min="0.1" step="0.1" class="border bg-white formulaire border-black/20 rounded w-full p-2" placeholder="1">
                                @error('questions.0.points') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                
                            </div>
                            <div class=" w-[3cm]">
                                <label class="block  font-medium">Duree seconde</label>
                                <input type="text" name="questions[0][duree_seconde]" value="{{ old('questions.0.duree_seconde') }}" min="0.5" step="0.1" class="bg-white formulaire border border-black/20 rounded w-full p-2" placeholder="10">
                                @error('questions.0.duree_seconde') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class=" flex-1">
                                <label class="block font-medium">Type de réponse</label>
                                <select name="questions[0][reponse_type]" class="bg-white formulaire reponse-type border border-black/20 rounded w-full p-2" onchange="toggleReponseType(this, 0)">
                                    <option value="single" {{ old('questions.0.reponse_type', 'single') == 'single' ? 'selected' : '' }}>Réponse simple</option>
                                    <option value="multiple" {{ old('questions.0.reponse_type') == 'multiple' ? 'selected' : '' }}>Réponse multiple</option>
                                    <option value="true_false" {{ old('questions.0.reponse_type') == 'true_false' ? 'selected' : '' }}>Vrai / Faux</option>
                                </select>
                                @error('questions.0.reponse_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        @error('questions')
                            <div class="text-red-500 text-sm">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <label class="block font-medium mb-1 mt-2">Choix</label>
                    <div class="choices-block border border-black/20 bg-black/2 rounded-md p-2 ">
                        <div class="choices-container space-y-2">
                            <div>
                                <div class="flex gap-2  items-center border border-black/20 rounded ps-2">
                                    <input type="radio" class="est-correcte-input p-2" name="questions[0][correct_choice]" value="0">
                                    <input type="text" name="questions[0][choices][0][texte]" placeholder="Choix 1" class="p-2 flex-1 border-s-2 bg-white outline-0 border-black/20" value="{{ old('questions.0.choices.0.texte') }}">
                                </div>
                                @error('questions.0.choices.0.texte') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <div class="flex gap-2 formulaire items-center border border-black/20 rounded ps-2">
                                    <input type="radio" class="est-correcte-input" name="questions[0][correct_choice]" value="1">
                                    <input type="text" name="questions[0][choices][1][texte]" placeholder="Choix 2" class="border-s-2 bg-white border-black/20 outline-0 p-2 flex-1" value="{{ old('questions.0.choices.1.texte') }}">
                                </div>
                                @error('questions.0.choices.1.texte') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        @error('questions.0.correct_choice') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        <button type="button" class="add-choice text-vert underline text-sm mt-2 px-2 p-1 rounded border border-black/20 bg-black/5">+ Ajouter un choix</button>
                    </div>

                    <div class="vrai-faux-block hidden mt-2">
                        <label class="block text-sm font-medium mb-1">Bonne réponse</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 border border-black/20 rounded-md p-2 px-4">
                                <input type="radio" name="questions[0][vrai_faux_correct]" value="vrai" {{ old('questions.0.vrai_faux_correct') == 'vrai' ? 'checked' : '' }}> Vrai
                            </label>
                            <label class="flex items-center gap-2 border border-black/20 rounded-md p-2 px-4">
                                <input type="radio" name="questions[0][vrai_faux_correct]" value="faux" {{ old('questions.0.vrai_faux_correct') == 'faux' ? 'checked' : '' }}> Faux
                            </label>
                        </div>
                        @error('questions.0.vrai_faux_correct') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="">
                    <button type="submit" class="bg-rouge text-white mt-2 px-4 py-2 rounded-full">Enregistrer la question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// --- Fisafidianana Image / Vidéo / Aucun (tsy azo roa miaraka) ---
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('media-type-radio')) {
        const block = e.target.closest('.question-block');
        const imageBlock = block.querySelector('.media-image-block');
        const videoBlock = block.querySelector('.media-video-block');
        const imageInput = imageBlock.querySelector('input[type="file"]');
        const videoInput = videoBlock.querySelector('input[type="file"]');

        if (e.target.value === 'image') {
            imageBlock.classList.remove('hidden');
            videoBlock.classList.add('hidden');
            videoInput.value = ''; // mamafa ny fichier video efa voafidy
        } else if (e.target.value === 'video') {
            imageBlock.classList.add('hidden');
            videoBlock.classList.remove('hidden');
            imageInput.value = ''; // mamafa ny fichier image efa voafidy
        } else {
            imageBlock.classList.add('hidden');
            videoBlock.classList.add('hidden');
            imageInput.value = '';
            videoInput.value = '';
        }
    }
});

// --- Fisafidianana reponse_type (single / multiple / true_false) ---
function toggleReponseType(select, qIndex) {
    const block = select.closest('.question-block');
    const choicesBlock = block.querySelector('.choices-block');
    const vraiFauxBlock = block.querySelector('.vrai-faux-block');
    const estCorrecteInputs = block.querySelectorAll('.est-correcte-input');

    if (select.value === 'true_false') {
        choicesBlock.classList.add('hidden');
        vraiFauxBlock.classList.remove('hidden');
    } else {
        choicesBlock.classList.remove('hidden');
        vraiFauxBlock.classList.add('hidden');

        estCorrecteInputs.forEach((input, i) => {
            if (select.value === 'single') {
                input.type = 'radio';
                input.name = `questions[${qIndex}][correct_choice]`;
                input.value = i;
            } else {
                input.type = 'checkbox';
                input.name = `questions[${qIndex}][choices][${i}][est_correcte]`;
                input.value = '1';
                input.checked = false;
            }
        });
    }
}

// --- Fanampiana choix vaovao ---
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('add-choice')) {
        const block = e.target.closest('.question-block');
        const container = block.querySelector('.choices-container');
        const choiceIndex = container.querySelectorAll('input[type="text"]').length;
        const reponseType = block.querySelector('.reponse-type').value;

        const inputType = reponseType === 'single' ? 'radio' : 'checkbox';
        const inputName = reponseType === 'single'
            ? `questions[0][correct_choice]`
            : `questions[0][choices][${choiceIndex}][est_correcte]`;
        const inputValue = reponseType === 'single' ? choiceIndex : '1';

        const div = document.createElement('div');
        div.className = 'flex gap-2 items-center';
        div.innerHTML = `
        <div class="flex gap-2 w-full items-center border border-black/20 rounded ps-2">
            <input type="${inputType}" class="est-correcte-input" name="${inputName}" value="${inputValue}">
            <input type="text" name="questions[0][choices][${choiceIndex}][texte]" placeholder="Choix ${choiceIndex + 1}" class=" p-2 flex-1 border-s-2 outline-0 bg-white border-black/20">
        </div>
        `;
        container.appendChild(div);
    }
});
</script>
@endsection