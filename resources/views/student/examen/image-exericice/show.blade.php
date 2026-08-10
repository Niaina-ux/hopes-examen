@extends('layouts.student-layouts.layoutexamen')
@section('exercice-content')
<div class="pb-10">
    <div class="my-10">
        <div class="flex justify-between items-center">
            <span>Exercice</span>
            <span>{{ $index + 1 }}/{{ $total }}</span>
        </div>
        <div class="rounded-full h-3 overflow-hidden bg-black/10">
            <div class="h-full bg-sgress" style="width: {{ (($index + 1) / $total) * 100 }}%"></div>
        </div>
    </div>
    <div class="">
        <div class="flex gap-3 items-center ">
            <div class="bg-black/5 w-8 h-8 rounded flex justify-center items-center font-semibold">
                1
            </div>
            <h2 class="font-semibold text-xl flex-1"> {{$image->titre}} </h2>
            <span class="border border-black/5 rounded-full px-4"> {{$image->note_totale}} Pts </span>
        </div>

<form action="{{ route('examen.image.store', [$examen, $slug, $image]) }}"
      method="POST"
      enctype="multipart/form-data"
      class="pb-10">
    @csrf

    @foreach ($questions as $index => $question)

    <div class="border border-black/3 bg-black/2 rounded-md my-4 p-2">

        <div class="flex justify-between mb-2">
            <h3 class="text-base">
                {{ $index + 1 }} - {{ $question->instruction }}
            </h3>
            <span class="text-sm rounded-full text-black/50">
                {{ $question->points }} Pts
            </span>
        </div>

        <div class="flex gap-5">

            {{-- Image modèle --}}
            <div class="flex-1">

                <div class="border w-full h-[8cm] border-black/3 rounded-md overflow-hidden">
                    <img src="{{ asset('images/image_exercice/' . $question->image) }}"
                         class="w-full h-full object-cover bg-white"
                         alt="Image exercice">
                </div>

                <div class="border border-black/5 rounded-md mt-2 bg-white/90 py-2 text-center">

                    <div class="text-black/50 flex items-center justify-center">
                        <i class="fa-solid fa-cloud-arrow-down text-xl"></i>
                    </div>

                    <p class="text-sm font-medium my-2">
                        Télécharger l'image
                    </p>

                    <a href="{{ asset('images/image_exercice/' . $question->image) }}"
                       download
                       class="inline-flex items-center gap-2 text-sm bg-black/50 text-white px-4 py-2 rounded-md hover:opacity-90 transition">
                        <i class="fa-solid fa-download"></i>
                        Télécharger
                    </a>

                </div>
            </div>

            <div class="flex-1">
    <div class="border-2 border-dashed w-full h-[8cm] border-black/20 rounded-md overflow-hidden bg-white/90">

        <img id="preview-{{ $question->id }}"
             src="{{ isset($reponsesExistantes[$question->id]) ? asset('images/reponses_etudiants/'.$reponsesExistantes[$question->id]->image_soumise) : '' }}"
             class="w-full h-full object-cover {{ isset($reponsesExistantes[$question->id]) ? '' : 'hidden' }}">

        <div id="placeholder-{{ $question->id }}"
             class="w-full h-full flex flex-col justify-center items-center text-black/30 {{ isset($reponsesExistantes[$question->id]) ? 'hidden' : '' }}">

            <i class="fa-solid fa-image text-5xl mb-3"></i>
            <p>Aucune image</p>

        </div>

    </div>

    <div class="border border-black/5 rounded-md mt-2 bg-white/90 py-2">

        <label for="image-{{ $question->id }}"
            class="cursor-pointer text-center block transition hover:border-rouge hover:bg-rouge/5">

            <div class="text-rouge flex justify-center">
                <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
            </div>

            <p class="text-sm font-medium my-2">
                {{ isset($reponsesExistantes[$question->id]) ? 'Remplacer votre image' : 'Choisir une image' }}
            </p>

            <div class="flex justify-center">
                <span class="inline-flex gap-2 text-sm bg-vert text-white px-4 py-2 rounded-md">
                    <i class="fa-solid fa-upload"></i>
                    Téléverser
                </span>
            </div>

        </label>

        <input
            id="image-{{ $question->id }}"
            type="file"
            name="images[{{ $question->id }}]"
            accept="image/*"
            class="hidden image-input"
            data-preview="preview-{{ $question->id }}"
            data-placeholder="placeholder-{{ $question->id }}">

        @error("images.$question->id")
            <p class="text-red-500 text-xs text-center mt-2">
                {{ $message }}
            </p>
        @enderror

    </div>
</div>
            

        </div>
    </div>

    @endforeach

    <div class="flex justify-end mt-4">
        <button type="submit"
                class="bg-rouge px-4 py-2 text-white rounded-md hover:opacity-90 transition">
            Valider
        </button>
    </div>

</form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.image-input').forEach(input => {

        input.addEventListener('change', function () {

            if (!this.files.length) return;

            const file = this.files[0];

            const preview = document.getElementById(this.dataset.preview);
            const placeholder = document.getElementById(this.dataset.placeholder);

            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');

            placeholder.classList.add('hidden');

        });

    });

});
</script>
@endsection