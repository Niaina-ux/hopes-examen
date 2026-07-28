
@extends('layouts.student-layouts.layoutexamen')
@section('exercice-content')
<div class="pb-10">
    <div class="w-[22cm] m-auto">
        <div class="my-10">
            <div class="flex justify-between items-center">
                <span>Question</span>
                <span>{{ $index + 1 }}/{{ $total }}</span>
            </div>
            <div class="rounded-full h-3 overflow-hidden bg-black/10">
                <div class="h-full bg-sgress" style="width: {{ (($index + 1) / $total) * 100 }}%"></div>
            </div>
        </div>
        <div class="pb-4">
            <div class="flex gap-3 border-b-2 pb-2 border-black/10">
                <div class="w-8 h-8 rounded-md bg-black/10 flex justify-center items-center font-semibold text-rouge">
                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                </div>
                <div class="">
                    <h3 class="text-base font-semibold">{{ $fichier->titre }}</h3>
                    <div class="flex gap-3 text-sm">
                        <span class="border border-green-600 rounded-full px-3">
                            {{ $questions->count() }} Devoirs
                        </span>
                        <span class="border border-amber-500 rounded-full px-3">
                            {{ $totalPoints }} Points
                        </span>
                    </div>
                </div>
            </div>

            @if($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded mt-4">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('examen.fichier.store', ['examen' => $examen->id, 'slug' => $slug, 'fichier' => $fichier->id]) }}" method="POST" enctype="multipart/form-data" class=" mt-4">
                @csrf

                @foreach($questions as $index => $question)
                    <div class="my-7">
                        <div class="flex  gap-3 mb-4">
                            <div class=" rounded-md  flex justify-center items-center text-sm  h-7 w-7 border-be-2 border-amber-500 font-semibold">
                                {{ $index + 1 }}
                            </div>
                            <p class="font-semibold pt-1">{{ $question->instruction }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            {{-- Carte TÉLÉCHARGER --}}
                            <div class="h-50 border-2 border-dashed border-black/10 rounded-xl p-6 flex flex-col items-center justify-center text-center bg-black/2">
                                @if($question->fichier_prof)
                                    <div class="w-12 h-12 rounded-full bg-vert/10 text-vert flex items-center justify-center ">
                                        <i class="fa-solid fa-cloud-arrow-down text-xl"></i>
                                    </div>
                                    <p class="text-sm font-medium mb-1">Fichier fourni par le professeur</p>
                                    <p class="text-xs text-black/40 mb-3 truncate max-w-full">{{ $question->fichier_prof }}</p>
                                    <a href="{{ asset('fichiers/prof/' . $question->fichier_prof) }}" target="_blank"
                                        class="inline-flex items-center gap-2 text-sm bg-vert text-white px-4 py-2 rounded-md hover:opacity-90 transition">
                                        <i class="fa-solid fa-download"></i> Télécharger
                                    </a>
                                @else
                                    <div class="w-12 h-12 rounded-full bg-black/5 text-black/30 flex items-center justify-center mb-3">
                                        <i class="fa-solid fa-file-circle-xmark text-xl"></i>
                                    </div>
                                    <p class="text-sm text-black/40">Aucun fichier fourni</p>
                                @endif
                            </div>

                            {{-- Carte UPLOAD --}}
                            <div class="relative">
                                <label for="fichier-{{ $question->id }}"
                                    class="h-50 cursor-pointer border-2 border-dashed rounded-xl p-6 flex flex-col items-center justify-center text-center transition
                                        border-black/10 hover:border-rouge hover:bg-rouge/5 upload-zone">
                                    <div class="w-12 h-12 rounded-full bg-rouge/10 text-rouge flex items-center justify-center ">
                                        <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                                    </div>
                                    <p class="text-sm font-medium mb-1 upload-label">
                                        {{ isset($reponsesExistantes[$question->id]) ? 'Remplacer votre fichier' : 'Choisir un fichier à envoyer' }}
                                    </p>
                                    <p class="text-xs text-black/40 upload-filename mb-3">
                                        Glissez ou cliquez pour sélectionner
                                    </p>
                                    <span class="inline-flex items-center gap-2 text-sm bg-rouge text-white px-4 py-2 rounded-md hover:opacity-90 transition">
                                        <i class="fa-solid fa-upload"></i> Téléverser
                                    </span>
                                </label>
                                <input id="fichier-{{ $question->id }}" type="file" name="fichiers[{{ $question->id }}]"
                                    class="hidden file-input" data-target="upload-zone-{{ $question->id }}">
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-3">
                            <p class="text-xs text-black/40">
                                <i class="fa-solid fa-circle-info"></i>
                                Formats acceptés : pdf, doc, docx, zip, rar — 10 Mo max
                            </p>

                            @if(isset($reponsesExistantes[$question->id]))
                                <p class="text-xs text-vert font-medium">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Envoyé le {{ $reponsesExistantes[$question->id]->submitted_at->format('d/m/Y à H:i') }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach



                <div class="flex justify-end border-t-2 border-black/10 pt-4">
                    <button type="submit" class="py-2 px-5 rounded-md bg-rouge text-white w-[4cm]">Valider</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.file-input').forEach(function (input) {
    input.addEventListener('change', function () {
        const label = this.previousElementSibling;
        const filenameEl = label.querySelector('.upload-filename');
        const iconWrapper = label.querySelector('div');

        if (this.files.length > 0) {
            filenameEl.textContent = this.files[0].name;
            filenameEl.classList.add('text-vert', 'font-medium');
            label.classList.remove('border-black/10');
            label.classList.add('border-vert', 'bg-vert/5');
            iconWrapper.classList.remove('bg-rouge/10', 'text-rouge');
            iconWrapper.classList.add('bg-vert/10', 'text-vert');
            iconWrapper.querySelector('i').classList.remove('fa-cloud-arrow-up');
            iconWrapper.querySelector('i').classList.add('fa-circle-check');
        }
    });
});
</script>
@endsection