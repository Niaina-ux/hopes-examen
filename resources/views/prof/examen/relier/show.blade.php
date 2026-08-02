@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
    <div class="py-3">
        @include('layouts.admin-layouts.examen.layout-exam')
        <div class="">
            @forelse($reliers as $index => $relier)
                <div class="p-2 flex gap-7 justify-between border-b border-black/10 my-2">
                    <div class="w-15 h-15 rounded-md bg-black/3 flex justify-center items-center">
                        <span class="font-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <h3 class="text-base font-semibold">{{ $relier->titre }}</h3>
                                <p>{{ $relier->description }}</p>
                                <div class="flex gap-4">
                                    <div class="flex text-sm text-rouge">
                                        Durée Libre
                                    </div>
                                    <div class="flex text-sm text-vert">
                                        Il y a {{ $relier->relier_questions_count }} Questions 
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="flex gap-4">
                                    <a href="{{route('prof.examen.relier.question.show', [$slug, $examen->id, $relier->id])}}" class="text-vert">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>
                                    <a href="" class="text-black/60">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('prof.examen.relier.destroy', [$slug, $examen->id, $relier->id]) }}" method="POST" onsubmit="return confirm('Supprimer {{ $relier->titre }} ? Cette action supprimera aussi toutes ses questions.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                                <a href="{{ route('prof.examen.relier.question.create', [$slug, $examen->id, $relier->id]) }}" 
                                    class="bg-vert p-1 px-4 inline-block rounded-md text-white">
                                    + Créer question
                                </a>
                            </div>
                        </div> 
                        @forelse($relier->relierQuestions as $index => $question)    
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
                        <div class="p-10 rounded-md  bg-black/5 text-center">
                            <i class="fa-solid fa-box-open text-2xl"></i>
                            <p>Aucun question n'a encore été créé pour cet examen.</p>
                        </div>  
                        @endforelse   
                    </div>
                </div>
            @empty
                <div class="p-10 rounded-md bg-black/5 text-center mt-4">
                    <i class="fa-solid fa-box-open text-2xl"></i>
                    <p>Aucun QCM n'a encore été créé pour cet examen.</p>
                </div>
            @endforelse

            <div class=" flex justify-end mt-4 me-2 sticky bottom-5">
                <a href="{{route('prof.examen.relier.create', [$slug, $examen->id])}}" class="p-2 text-white px-3 inline-block rounded-md bg-rouge ">
                    Créer nouveau exercice
                </a>
            </div>
        </div>
    </div>
@endsection