@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
    <div class=" py-3 min-h-[101vh]">
        <div class="flex gap-3 items-center my-2">
            <a href="#" 
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
        <div class="border border-black/5 mt-2 rounded-md overflow-hidden min-h-[60vh]">
            <div class="flex justify-between gap-5 p-2 bg-black/3 ">
                <div class="flex-1 flex gap-5">
                    <div class="">
                        <h3 class="text-xl font-semibold">{{ $pointiller->titre }}</h3>
                        <div class="flex gap-4">
                            <div class="flex text-sm text-rouge">
                                Durée {{ $pointiller->duree_minutes ?? 'N/A' }} minutes 
                            </div>
                            <div class="flex text-sm ">
                                {{ $pointiller->note_totale }} Points
                            </div>
                            <div class="flex text-sm text-vert">
                                Il y a {{ $questions->count() }} questions
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <a href="{{ route('prof.examen.pointiller.question.create', [$slug, $examen->id, $pointiller->id]) }}" 
                        class="bg-rouge p-1 px-4 inline-block rounded-md text-white">
                        Créer nouvelle question
                    </a>
                </div>
            </div>
    
            
    
            <div class="p-2 ">
                @forelse($questions as $index => $question)
                    <div class="border-b border-black/3 ">
                        @if($question->image)
                            <div class="w-35 h-30 border border-black/2 rounded-md bg-black/10 mt-7 overflow-hidden">
                                <img src="{{ asset('images/questions/' . $question->image) }}" alt="" class="w-full h-full object-cover">
                            </div>
                        @endif
    
                        @if($question->video)
                            <video controls class="w-full max-w-md rounded-md mt-3">
                                <source src="{{ asset('videos/questions/' . $question->video) }}" type="video/mp4">
                                Votre navigateur ne supporte pas la lecture vidéo.
                            </video>
                        @endif
    
                        <div class="flex gap-5 justify-between py-2">
                            <div class="w-10 h-10 bg-black/5 rounded-sm flex justify-center items-center ">
                                <span class="text-vert">{{ $index + 1 }}</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-base ">{{ $question->enonce }}</h4>
                                <div class="flex gap-3">
                                    <div class="flex text-sm">
                                        Type: <span class="px-3 inline-block text-rouge">Compléter</span>
                                    </div>
                                    <div class="flex text-sm">
                                        Point: <span class="px-3 inline-block text-vert">{{ $question->points }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-3 items-center">
                                <button type="button" class="affiche" onclick="toggleReponse(this)">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                                <a href="{{ route('prof.examen.pointiller.question.edit', [$slug, $examen->id, $pointiller->id, $question->id]) }}" class="text-vert">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('prof.examen.pointiller.question.destroy', [$slug, $examen->id, $pointiller->id, $question->id]) }}" 
                                    method="POST" onsubmit="return confirm('Supprimer cette question ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rouge">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="reponse-wrapper">
                            <div class="border border-black/5 rounded-md p-3 reponse ">
                                @foreach($question->reponses as $reponse)
                                    <div class="flex justify-between gap-4 border-b border-black/5 py-1 mb-1">
                                        <span>
                                            <i class="fa-solid fa-clover text-rouge"></i>
                                        </span>
                                        <p class="flex-1">
                                            Trou {{ $reponse->position }} — Réponse correcte:
                                            <span class="text-vert font-semibold">{{ $reponse->reponse_correcte }}</span>
                                        </p>
                                        <span class="text-xs text-black/40">
                                            Banque: {{ $reponse->choices->pluck('texte')->implode(', ') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-10 rounded-md bg-black/5 text-center">
                        <i class="fa-solid fa-box-open text-2xl"></i>
                        <p>Aucune question n'a encore été ajoutée.</p>
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