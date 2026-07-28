@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
    <div class="py-3">
        <div class="flex gap-3 items-center my-2">
            <a href="" 
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
        <div class="py-3">
            <div class="border border-black/10 rounded-md overflow-hidden">
                <div class="flex justify-between gap-5 bg-black/3 p-2">
                    <div class="flex-1 flex gap-5">
                        <div class="">
                            <h3 class="text-xl font-semibold ">{{ $relier->titre }}</h3>
                            <div class="flex gap-4">
                                <div class="flex text-sm ">
                                    Temps Libre
                                </div>
                                <div class="flex text-sm text-vert">
                                    Il y a  {{ $questions->count() }} questions
                                </div>
                                <div class="flex text-sm text-rouge">
                                    {{ $relier->note_totale }} Points
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <a href="{{ route('prof.examen.relier.question.create', [$slug, $examen->id, $relier->id]) }}" 
                            class="bg-rouge p-1 px-4 inline-block rounded-md text-white">
                            Créer nouvelle question
                        </a>
                    </div>
                </div>
                <div class="p-2">
                    @forelse($questions as $index => $question)    
                    <div class="py-4 border-b border-black/5 ">
                        <div class="flex gap-5">
                            <div class="w-8 h-8 bg-black/5 rounded-md flex font-semibold text-rouge justify-center items-center">
                                {{ $loop->iteration }} 
                            </div>
                            <div class="flex-1">
                                <div class=" flex justify-between gap-3">
                                    <div class="">
                                        <h3 class="">{{ $question->enonce }}</h3>
                                        <div class="flex gap-5">
                                            <div class="text-vert text-sm">
                                                {{$question->points}} Points
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex gap-3">
                                        <a href="{{ route('prof.examen.relier.question.edit', [$slug, $examen->id, $relier->id, $question->id]) }}" class="text-vert">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <form action="{{ route('prof.examen.relier.question.destroy', [$slug, $examen->id, $relier->id, $question->id]) }}" method="POST" onsubmit="return confirm('Supprimer cette question ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-15 mt-1 bg-black/3 p-2 px-3 rounded-md">
                                    <div>
                                        <p class="text-black/50 text-sm border-b-2 border-black/10 pb-1">Colonne gauche</p>
                                        @foreach ($question->paires as $paire)
                                            <div class="py-1 flex justify-between gap-2 border-b border-black/5 {{ $loop->even ? 'bg-white/60' : '' }}">
                                                <div class="flex-1">
                                                    - {{ $paire->element_gauche }} 
                                                </div>
                                                <span class="w-7 h-6 flex justify-center items-center rounded-md bg-black/30">{{ $paire->ordre_gauche }}</span>
                                                <i class="fa-solid fa-arrow-right-long text-black/30"></i> 
                                            </div>
                                        @endforeach
                                    </div>
                                    <div>
                                        <p class="text-black/50 text-sm border-b-2 border-black/10 pb-1">Colonne droite</p>
                                        @foreach ($question->paires as $paire)
                                            <div class="py-1 flex justify-between gap-2 text-left  border-b border-black/5 {{ $loop->even ? 'bg-white/60' : '' }}">
                                                <span class="w-7 h-6 flex justify-center items-center text-white rounded-md bg-vert">{{ $paire->ordre_droite }}</span>
                                                <div class="flex-1">
                                                    - {{ $paire->element_droite }}
                                                </div> 
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    @empty
                    <div class="p-10 rounded-md bg-black/5 text-center">
                        <i class="fa-solid fa-box-open text-2xl"></i>
                        <p>Aucun question n'a encore été créé pour cet examen.</p>
                    </div>  
                    @endforelse
                </div>
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