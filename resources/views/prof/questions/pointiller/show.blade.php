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
        @forelse($pointillers as $index => $pointiller)
            <div class="p-2 flex gap-5 justify-between border rounded-md border-black/10 my-2
                dark:border-white/10">
                <div class="w-12 h-12 rounded-md bg-black/5 flex justify-center items-center
                    dark:bg-white/5">
                    <span class="font-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="flex-1">
                    <div class="flex items-start gap-3 pb-2 border-b border-black/5 *@auth
                        dark:border-white/5
                    @endauth">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold">{{ $pointiller->titre }}</h3>
                            <p>{{ $pointiller->description }}</p>
                            <div class="flex gap-4">
                                <div class="flex text-sm text-rouge">
                                    {{ $pointiller->note_totale }} Pts
                                </div>
                                <div class="flex text-sm text-vert">
                                    Il y a {{ $pointiller->pointiller_questions_count }} questions
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <a href="{{route('prof.question.pointiller.edit', [$slug, $pointiller->id])}}" class="">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <button type="button" onclick="openModal('delete-pointiller-modal-{{ $pointiller->id }}')" class="text-red-600">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                            <x-confirm-modal
                                id="delete-pointiller-modal-{{ $pointiller->id }}"
                                title="Supprimer le QCM"
                                action="{{ route('prof.question.pointiller.destroy', [$slug, $pointiller->id]) }}"
                                method="DELETE"
                                confirmText="Oui, supprimer"
                                cancelText="Annuler">
                                Confirmez-vous la suppression de <span class="text-rouge font-semibold">{{ $pointiller->titre }}</span> ? Cette action supprimera aussi toutes ses questions.
                            </x-confirm-modal>
                        </div>
                        <a href=" {{route('prof.pointiller.question.create',[$slug, $pointiller->id])}} " 
                            class="bg-vert p-1 px-4 inline-block rounded-full text-white">
                            Créer question
                        </a>   
                    </div>
                    @forelse($pointiller->pointillerQuestions as $index => $question)
                        <div class="border-t border-black/3 
                            dark:border-white/3">
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
        
                            <div class="flex gap-4 justify-between border-y border-black/5 py-2">
                                <div class="w-9 h-9 bg-black/4 rounded-sm flex justify-center items-center 
                                    dark:bg-white/4">
                                    <span class="text-vert">{{ $index + 1 }}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start gap-3">
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
                                            <a href="{{route('prof.pointiller.question.edit', [$slug,$pointiller->id, $question->id])}}" class="text-vert">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <button type="button" onclick="openModal('delete-question-modal-{{ $question->id }}')" class="text-rouge">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                            <x-confirm-modal
                                                id="delete-question-modal-{{ $question->id }}"
                                                title="Supprimer la question"
                                                action="{{ route('prof.pointiller.question.destroy', [$slug, $pointiller->id, $question->id]) }}"
                                                method="DELETE"
                                                confirmText="Oui, supprimer"
                                                cancelText="Annuler">
                                                Confirmez-vous la suppression de cette question ? Cette action est irréversible.
                                            </x-confirm-modal>
                                            </div>
                                    </div>
                                    <div class="reponse-wrapper">
                                        <div class="border border-black/3  mt-1 rounded bg-black/2 p-2 reponse 
                                            dark:border-white/3 dark:bg-white/2">
                                            @foreach($question->reponses as $reponse)
                                                <div class="flex justify-between gap-4 bg-white/70  border-b border-black/5 p-1
                                                    dark:bg-white/2 dark:border-white/5">
                                                    <span>
                                                        <i class="fa-solid fa-bullhorn"></i>
                                                    </span>
                                                    <p class="flex-1">
                                                        Trou {{ $reponse->position }} — <span class="text-sm">Réponse correcte:</span>
                                                        <span class="text-vert ">{{ $reponse->reponse_correcte }}</span>
                                                    </p>
                                                    <span class=" ">
                                                        <span class="text-sm">Banque:</span> {{ $reponse->choices->pluck('texte')->implode(', ') }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 rounded-md bg-black/5 mt-2 text-center
                            dark:bg-white/3">
                            <i class="fa-solid fa-box-open text-2xl"></i>
                            <p>Aucune question n'a encore été ajoutée.</p>
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
        <div class=" flex justify-end sticky bottom-5 mt-4">
            <a href="{{route('prof.question.pointiller.create', $slug)}}" class="p-2 px-3 text-white inline-block rounded-full bg-rouge ">
                Créer nouveau exercice
            </a>
        </div>
    </div>
@endsection