@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="py-3">
    <div class="flex items-center mb-2">
        <a href="{{route('prof.examen.qcm', [$slug, $examen->id])}}" class="hover:underline flex items-center gap-2"><i class="fa-solid fa-angle-left"></i> QCM</a>
    </div>
    <div class="flex gap-3 justify-between items-end mb-3 border-b-2 border-white/10 pb-2">
        <div class="max-w-[70%]">
            <h3 class="text-3xl font-semibold text-vert">{{ $examen->titre }}</h3>
            <p class=" ">Cochez les questions à inclure dans cet examen.</p>
        </div>
        <div class="border border-black/10 rounded-md flex items-center mb-1 p-2 px-5  text-sm
            dark:border-white/10">
            <div class="flex items-center gap-2 border-e-2 border-black/3 px-2
                dark:border-white/10">
                <span>Questions ajouté :</span>
                <span class="font-semibold text-rouge">{{ $questionsAjoutees }}</span>

            </div>
            <div class="flex items-center gap-2  px-2">
                <span>Questions restans :</span>
                <span class="font-semibold text-rouge">{{ $questionsRestantes }}</span>

            </div>
        </div>
    </div>

    <form action="{{ route('prof.examen.qcm.selectQuestions.store', [$slug, $examen->id]) }}" method="POST">
        @csrf
        @forelse($qcms as $qcmIndex => $qcm)
            <div class="flex gap-4 border border-black/10 rounded-md p-3 mb-3
            dark:border-white/10 ">
                <div class="w-12 h-12 rounded-md bg-black/3 flex justify-center items-center
                    dark:bg-white/3">
                    <span class="font-bold">{{ $qcmIndex + 1 }}</span>
                </div>
                <div class="flex-1">
                    <h4 class="text-lg mb-2">{{ $qcm->titre }}</h4>
                    <div class="border-y border-black/5 rounded  mb-1 
                    dark:border-white/5 ">
                        @forelse($qcm->qcmQuestions as $qIndex => $question)
                            <div class="flex gap-4 justify-between border-t border-black/5 
                                dark:border-white/5 py-2">
                                <div class="w-9 h-9 bg-black/5 rounded-md flex justify-center items-center
                                    dark:bg-white/5">
                                    <span class="text-vert">{{ $qIndex + 1 }}</span>
                                </div>
                                <div class="flex-1">
                                    <label class="flex justify-between gap-3 cursor-pointer hover:bg-black/2 dark:hover:bg-white/2">
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
                                        <input type="checkbox" name="question_ids[]" value="{{ $question->id }}"
                                            {{ in_array($question->id, $questionsSelectionneesIds) ? 'checked' : '' }} class="mt-1">
                                    </label>
                                    <div class="mb-2">
                                        @if($question->image)
                                            <div class="w-40 h-30 border border-black/2 rounded-md bg-black/10 mt-3 overflow-hidden">
                                                <img src="{{ asset('images/questions/' . $question->image) }}" alt="" class="w-full h-full object-cover">
                                            </div>
                                        @elseif($question->video)
                                            <div class="w-40 h-30 border border-black/2 rounded-md bg-black/10 mt-3 overflow-hidden">
                                                <video class="w-full h-full object-cover" muted>
                                                    <source src="{{ asset('videos/questions/' . $question->video) }}">
                                                </video>
                                            </div>
                                        @endif
                                    </div>
                                    <div class=" rounded-md p-2   px-3 reponse bg-black/2 border border-black/3 
                                        dark:bg-white/2 dark:border-white/3">
                                        @foreach($question->qcmChoices as $choice)
                                            <div class="flex justify-between gap-4 border-b border-black/5 py-1 {{ $loop->even ? 'bg-white/60 dark:bg-white/2' : '' }}">
                                                <p class="flex-1 text-sm">- {{ $choice->texte }}</p>
                                                <span class="{{ $choice->est_correcte ? 'text-vert' : '' }}">
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
                        @empty
                            <p class="text-sm text-black/40 italic">Aucune question dans ce QCM.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="mt-4">
                {{ $qcms->links() }}
            </div>
        @empty
            <div class="p-10 rounded-md bg-black/5 text-center">
                <i class="fa-solid fa-box-open text-2xl"></i>
                <p>Aucun QCM créé pour cette catégorie.</p>
                <a href="{{ route('prof.qcm.create', $slug) }}" class="text-vert underline text-sm mt-2 inline-block">
                    + Créer un QCM
                </a>
            </div>
        @endforelse

        @if($qcms->isNotEmpty())
        <div class="flex justify-end sticky bottom-5">
            <button type="submit" class="bg-rouge cursor-pointer text-white px-5 hover-rouge py-2 rounded-full mt-2">
                Enregistrer la sélection
            </button>
        </div>
        @endif
    </form>
</div>
@endsection