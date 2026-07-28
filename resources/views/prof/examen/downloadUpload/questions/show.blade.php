@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="py-3">
    <div class="flex gap-3 items-center my-2">
        <a href="{{ route('prof.examen.fichier',[$slug, $examen->id] ) }}" 
            class="w-7 h-7 rounded-sm bg-vert flex justify-center items-center text-white">
            <i class="fa-solid fa-chevron-left"></i>
        </a>
        <span class="text-black/30">Retour</span>
    </div>
    @include('layouts.admin-layouts.examen.layout-exam')
    @if(session('success'))
        <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md mt-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="document.getElementById('success-alert').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif
    <div class=" m-auto mt-2 min-h-[60vh] rounded-md overflow-hidden border border-black/10">
        <div class="flex justify-between gap-5 bg-black/3  p-2 ">
            <div class="flex-1 flex gap-5">
                <div class="">
                    <h3 class="text-xl font-semibold">{{ $fichier->titre }}</h3>
                    <div class="flex gap-4">
                        <div class="flex text-sm text-rouge">
                            Durée Libre
                        </div>
                        <div class="flex text-sm text-vert">
                            Il y a  {{ $questions->count() }} questions
                        </div>
                    </div>
                </div>
            </div>
            <div class="">
                <a href="{{ route('prof.examen.fichier.qeustion.create', [$slug, $examen->id, $fichier->id]) }}" 
                    class="bg-rouge p-1 px-4 inline-block rounded-md text-white">
                    Créer nouvelle question
                </a>
            </div>
        </div>
        <div class="p-2">
        @forelse($questions as $index => $question)
            <div class="py-3 border-b border-black/10">
                <div class="flex gap-5 justify-between">
                    <div class="w-10 h-10 bg-black/5 rounded-sm flex justify-center items-center">
                        <span class="text-vert">{{ $index + 1 }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex  justify-between gap-3">
                            <div class="">
                                <h4 class="text-base">{{ $question->instruction }}</h4>
                                <div class="flex gap-3">
                                    <div class="flex text-sm text-vert">
                                        Temps libre
                                    </div>
                                    <div class="flex text-sm text-rouge">
                                        {{ $question->points }} points
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <a href="{{ route('prof.examen.fichier.qeustion.edit', [$slug, $examen->id, $fichier->id, $question->id]) }}" class="text-black/60">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('prof.examen.fichier.qeustion.destroy', [$slug, $examen->id, $fichier->id, $question->id]) }}" method="POST" onsubmit="return confirm('Supprimer ce devoir ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @if($question->fichier_prof)
                            <div class="p-2  px-3 rounded-md bg-black/5 mt-1">
                                <p> {{$question->fichier_prof}} </p>
                                <a href="{{ asset('fichiers/prof/' . $question->fichier_prof) }}" target="_blank" class="text-vert text-sm underline mt-1 inline-block ">
                                    <i class="fa-solid fa-paperclip"></i> Télécharger le fichier fourni
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-10 rounded-md bg-black/5 text-center">
                <i class="fa-solid fa-box-open text-2xl"></i>
                <p>Aucun devoir n'a encore été créé.</p>
            </div>
        @endforelse
    </div>
    </div>
</div>

    <style>
        .reponse-wrapper {
            overflow: hidden;
            max-height: 0;
            opacity: 0;
            transform: translateY(-8px);
            transition: max-height 0.35s ease, opacity 0.3s ease, transform 0.3s ease;
        }

        .reponse-wrapper.open {
            opacity: 1;
            transform: translateY(0);
        }

        .rotate-icon {
            transform: rotate(180deg);
        }
    </style>
    <script>
    function toggleReponse(button) {
        const block = button.closest('.border-b');
        const wrapper = block.querySelector('.reponse-wrapper');
        const inner = block.querySelector('.reponse');
        const icon = button.querySelector('i');

        const isOpen = wrapper.classList.contains('open');

        if (isOpen) {
            wrapper.style.maxHeight = '0px';
            wrapper.classList.remove('open');
            icon.classList.remove('rotate-icon');
        } else {
            wrapper.style.maxHeight = inner.scrollHeight + 'px';
            wrapper.classList.add('open');
            icon.classList.add('rotate-icon');
        }
    }
    </script>
@endsection