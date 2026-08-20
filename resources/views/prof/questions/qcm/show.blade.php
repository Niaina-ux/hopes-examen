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
        <div class=" mt-2">
            @forelse($qcms as $index => $qcm)
                <div class="p-2  flex gap-5 justify-between border border-black/10 rounded-md my-2
                    dark:border-white/10">
                    <div class="w-12 h-12 rounded-md bg-black/3 flex justify-center items-center
                        dark:bg-white/3">
                        <span class="font-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between gap-3">
                            <div class="">
                                <h3 class="text-lg font-semibold">{{ $qcm->titre }}</h3>
                                <p>{{ $qcm->description }}</p>
                                @php
                                    $noteTotaleQcm = $qcm->qcmQuestions->sum('points');
                                    $dureeTotaleSecondes = $qcm->qcmQuestions->sum('duree_seconde');
                                    $dureeTotaleMinutes = round($dureeTotaleSecondes / 60, 1);
                                @endphp

                                <div class="flex gap-3">
                                    <div class="flex text-sm text-vert">
                                        Durée {{ $dureeTotaleMinutes }} minutes
                                    </div>
                                    <div class="flex text-sm text-rouge">
                                        {{ $noteTotaleQcm }} Pts
                                    </div>
                                    <div class="flex text-sm">
                                        Il y a {{ $qcm->qcmQuestions->count() }} Questions
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-5 items-start"> 
                                <div class="flex gap-4">
                                    <a href=" {{route('prof.question.qcm.edit', [$slug, $qcm->id])}} " class="">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <button type="button" onclick="openModal('delete-qcm-modal-{{ $qcm->id }}')" class="text-red-600">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>

                                    <x-confirm-modal
                                        id="delete-qcm-modal-{{ $qcm->id }}"
                                        title="Supprimer le QCM"
                                        action="{{ route('prof.question.qcm.destroy', [$slug, $qcm->id]) }}"
                                        method="DELETE"
                                        confirmText="Oui, supprimer"
                                        cancelText="Annuler">
                                        Confirmez-vous la suppression de <span class="text-rouge font-semibold">{{ $qcm->titre }}</span> ? Cette action supprimera aussi toutes ses questions.
                                    </x-confirm-modal>
                                </div>
                                <a href=" {{route('prof.qcm.question.create', [$slug, $qcm->id])}} " 
                                    class="bg-vert p-1 px-4 inline-block rounded-full text-white">
                                    Créer question
                                </a>
                            </div>
                        </div>
                        <div class="mt-1">
                            @forelse($qcm->qcmQuestions as $index => $question)
                                <div class="border-y border-black/5 d py-2
                                    dark:border-white/5">
                                    <div class="flex gap-4 justify-between ">
                                        <div class="w-9 h-9 bg-black/5 rounded-sm flex justify-center items-center
                                            dark:bg-white/5">
                                            <span class="text-vert">{{ $index + 1 }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex justify-between gap-3">
                                                <div class="">
                                                    <h4 class="text-base">{{ $question->enonce }}</h4>
                                                    <div class="flex gap-3">
                                                        <div class="flex text-sm">
                                                            {{ $question->reponse_type }}, 
                                                        </div>                                                       
                                                        <div class="flex text-sm">
                                                            Duree: <span class="px-3 inline-block text-rouge">{{ $question->duree_seconde }} sec,</span>
                                                        </div>                                                       
                                                        <div class="flex text-sm">
                                                            Point: <span class="px-3 inline-block text-vert">{{ $question->points }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex gap-3 items-center">
                                                    <a href=" {{route('prof.qcm.question.edit', [$slug,$qcm->id,$question->id] )}} " class="text-vert">
                                                        <i class="fa-solid fa-pen"></i>
                                                    </a>
                                                    <button type="button" onclick="openModal('delete-question-modal-{{ $question->id }}')" class="text-rouge">
                                                        <i class="fa-regular fa-trash-can"></i>
                                                    </button>
                                                    <x-confirm-modal
                                                        id="delete-question-modal-{{ $question->id }}"
                                                        title="Supprimer la question"
                                                        action="{{ route('prof.qcm.question.destroy', [$slug, $qcm->id, $question->id]) }}"
                                                        method="DELETE"
                                                        confirmText="Oui, supprimer"
                                                        cancelText="Annuler">
                                                        Confirmez-vous la suppression de cette question ? Cette action est irréversible.
                                                    </x-confirm-modal>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                @if($question->image)
                                                    <div class="w-40 h-30 border border-black/2 rounded-md bg-black/10 mt-3 overflow-hidden">
                                                        <img src="{{ asset('images/questions/' . $question->image) }}" alt="" class="w-full h-full object-cover">
                                                    </div>
                                                @endif
                            
                                                @if($question->video)
                                                    <video controls class="w-full max-w-md rounded-md mt-3">
                                                        <source src="{{ asset('videos/questions/' . $question->video) }}" type="video/mp4">
                                                        Votre navigateur ne supporte pas la lecture vidéo.
                                                    </video>
                                                @endif
                                            </div>
                                            <div class=" rounded-md p-2   px-3 reponse bg-black/2 border border-black/3
                                                dark:bg-white/2 dark:border-white/3">
                                                @foreach($question->qcmChoices as $choice)
                                                    <div class="flex justify-between gap-4 border-b border-black/5 py-1 {{ $loop->even ? 'bg-white/60 dark:bg-white/2' : '' }}">
                                                        <p class="flex-1 text-sm">- {{ $choice->texte }}</p>
                                                        <span class="{{ $choice->est_correcte ? 'text-vert' : 'text-black/40 dark:text-white/30' }}">
                                                            @if($choice->est_correcte)
                                                                <i class="fa-solid fa-check"></i>
                                                            @else
                                                                <i class="fa-solid fa-xmark"></i>
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-10  mt-2 rounded-md bg-black/5 text-center
                                    dark:bg-white/2">
                                    <i class="fa-solid fa-box-open text-2xl"></i>
                                    <p>Aucune question n'a encore été ajoutée.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 rounded-md bg-black/5 text-center mt-4">
                    <i class="fa-solid fa-box-open text-2xl"></i>
                    <p>Aucun QCM n'a encore été créé pour cet examen.</p>
                </div>
            @endforelse
        </div>   
        <div class=" flex justify-end sticky bottom-5 mt-4 pe-2">
            <a href=" {{route('prof.question.qcm.create', $slug)}} " class="p-2 text-white px-5 inline-block rounded-full bg-rouge ">
                Créer nouveau quiz
            </a>
        </div>
        
    </div>
@endsection