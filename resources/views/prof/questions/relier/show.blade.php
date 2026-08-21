@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
    <div class="py-3">
        @include('layouts.prof-layouts.proflayoutesexamquestion')
        @if(session('success'))
            <div id="success-alert" class="bg-green-100/10 text-green-700 px-4 py-2 rounded-md my-4 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('success-alert').remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        <div class="">
            @forelse($reliers as $index => $relier)
                <div class="p-2 flex gap-5 justify-between border rounded-md border-black/10 my-2
                    dark:border-white/10">
                    <div class="w-12 h-12 rounded-md bg-black/5 flex justify-center items-center
                        dark:bg-white/5">
                        <span class="font-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex-1 ">
                        <div class="flex gap-3 pb-2 border-b border-black/5
                            dark:border-white/5">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold">{{ $relier->titre }}</h3>
                                <p>{{ $relier->description }}</p>
                                @php
                                    $noteTotaleRelier = $relier->relierQuestions->sum('points');
                                    $questionTotal = $relier->relierQuestions->count();
                                @endphp
                                <div class="flex gap-4">
                                    <div class="flex text-sm text-rouge">
                                         {{$noteTotaleRelier}} Pts
                                    </div>
                                    <div class="flex text-sm text-vert">
                                        Il y a {{ $questionTotal }} Questions 
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">  
                                <div class="flex gap-4">
                                    <a href="{{route('prof.question.relier.edit', [$slug, $relier->id])}}" class="">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <button type="button" onclick="openModal('delete-relier-modal-{{ $relier->id }}')" class="text-red-600">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>

                                    <x-confirm-modal
                                        id="delete-relier-modal-{{ $relier->id }}"
                                        title="Supprimer le QCM"
                                        action="{{ route('prof.question.relier.destroy', [$slug, $relier->id]) }}"
                                        method="DELETE"
                                        confirmText="Oui, supprimer"
                                        cancelText="Annuler">
                                        Confirmez-vous la suppression de <span class="text-rouge font-semibold">{{ $relier->titre }}</span> ? Cette action supprimera aussi toutes ses questions.
                                    </x-confirm-modal>
                                </div>
                                <a href="{{route('prof.relier.question.create', [$slug, $relier->id])}}" 
                                    class="bg-vert p-1 px-4 inline-block rounded-full text-white">
                                    + Créer question
                                </a>
                            </div>
                        </div> 
                        @forelse($relier->relierQuestions as $index => $question)    
                        <div class="py-2 border-t border-black/5 
                            dark:border-white/5">
                            <div class="flex gap-5">
                                <div class="w-8 h-8 bg-black/5 rounded-md flex font-semibold text-vert justify-center items-center
                                dark:bg-white/4">
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
                                        <div class="flex items-end gap-3">
                                            <a href="{{route('prof.relier.question.edit', [$slug,$relier->id, $question->id])}}" class="text-vert">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <button type="button" onclick="openModal('delete-question-modal-{{ $question->id }}')" class="text-rouge">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                            <x-confirm-modal
                                                id="delete-question-modal-{{ $question->id }}"
                                                title="Supprimer la question"
                                                action="{{ route('prof.relier.question.destroy', [$slug, $relier->id, $question->id]) }}"
                                                method="DELETE"
                                                confirmText="Oui, supprimer"
                                                cancelText="Annuler">
                                                Confirmez-vous la suppression de cette question ? Cette action est irréversible.
                                            </x-confirm-modal>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-15 mt-1 bg-black/2 border border-black/3 p-2 px-3 rounded-md
                                        dark:bg-white/2 dark:border-white/3">
                                        <div>
                                            <p class=" text-sm border-b-2 border-black/10 pb-1
                                                dark:border-white/10">Colonne gauche</p>
                                            @foreach ($question->paires as $paire)
                                                <div class="py-1 flex justify-between gap-2 border-b border-black/5 bg-white/70 rounded
                                                    dark:bg-white/2 dark:border-white/5">
                                                    <div class="flex-1">
                                                        - {{ $paire->element_gauche }} 
                                                    </div>
                                                    <span class="w-7 h-6 flex justify-center items-center rounded-md bg-black/30">{{ $paire->ordre_gauche }}</span>
                                                    <i class="fa-solid fa-arrow-right-long "></i> 
                                                </div>
                                            @endforeach
                                        </div>
                                        <div>
                                            <p class="text-sm border-b-2 border-black/10 pb-1
                                                dark:border-white/10">Colonne droite</p>
                                            @foreach ($question->paires as $paire)
                                                <div class="py-1 flex justify-between gap-2 text-left  border-b border-black/5 bg-white/70 rounded
                                                    dark:border-white/5 dark:bg-white/2">
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
                        <div class="p-10 rounded-md  bg-black/5 text-center
                            dark:bg-white/3">
                            <i class="fa-solid fa-box-open text-2xl"></i>
                            <p>Aucun question n'a encore été créé pour cet examen.</p>
                        </div>  
                        @endforelse   
                    </div>
                </div>
            @empty
                <div class="p-10 rounded-md bg-black/5 text-center mt-4
                dark:bg-white/3">
                    <i class="fa-solid fa-box-open text-2xl"></i>
                    <p>Aucun QCM n'a encore été créé pour cet examen.</p>
                </div>
            @endforelse   
            <div class=" flex justify-end mt-4 me-2 sticky bottom-5">
                <a href="{{route('prof.question.relier.create', $slug)}}" class="p-2 text-white px-3 inline-block rounded-full bg-rouge ">
                    Créer nouveau exercice
                </a>
            </div>
        </div>
    </div>
@endsection