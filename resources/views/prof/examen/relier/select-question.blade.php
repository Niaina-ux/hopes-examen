@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="py-3">
    <div class="flex items-center mb-2">
        <a href="" class="hover:underline">Relier/</a>
        <span class="font-semibold">Sélectionner des questions</span>
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
    <form action="{{ route('prof.examen.relier.selectQuestions.store', [$slug, $examen->id]) }}" method="POST">
        @csrf

        @forelse($reliers as $relierIndex => $relier)
            <div class="flex gap-4 border border-black/10 rounded-md p-3 mb-3 bg-black/2
            dark:border-white/10 dark:bg-white/2">
                <div class="w-12 h-12 rounded-md bg-black/3 flex justify-center items-center
                    dark:bg-white/3">
                    <span class="font-bold">{{ $relierIndex + 1 }}</span>
                </div>
                <div class="flex-1">
                    <h4 class="text-lg mb-2">{{ $relier->titre }}</h4>
                    <div class="p-2 border border-black/5 rounded bg-white/70 mb-1
                    dark:border-white/5 dark:bg-white/2">
                        @forelse($relier->relierQuestions as $qIndex => $question)
                            <label class="flex items-start justify-between gap-3 p-2 border-b border-black/5 mb-1 cursor-pointer
                                dark:border-white/5">
                                <div class="flex flex-1 gap-3">
                                    <div class="w-9 h-9 bg-black/5 rounded-md flex justify-center items-center flex-shrink-0
                                        dark:bg-white/5">
                                        <span class="text-vert">{{ $qIndex + 1 }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="-mt-1">{{ $question->enonce }}</p>
                                        <div class="flex gap-3 text-sm mt-1">
                                            <span>{{ $question->points }} pts</span>
                                            <span>{{ $question->paires->count() }} paires</span>
                                        </div>
                                        {{-- Aperçu ny paires --}}
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @foreach($question->paires->take(3) as $paire)
                                                <span class="text-sm bg-black/5 rounded px-2 py-1 dark:bg-white/5">
                                                    {{ $paire->element_gauche }} → {{ $paire->element_droite }}
                                                </span>
                                            @endforeach
                                            @if($question->paires->count() > 3)
                                                <span class="text-xs text-black/40">+{{ $question->paires->count() - 3 }} autres</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <input type="checkbox" name="question_ids[]" value="{{ $question->id }}"
                                    {{ in_array($question->id, $questionsSelectionneesIds) ? 'checked' : '' }} class="mt-1">
                            </label>
                        @empty
                            <p class="text-sm text-black/40 italic">Aucune question dans ce relier.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="p-10 rounded-md bg-black/5 text-center">
                <i class="fa-solid fa-box-open text-2xl"></i>
                <p>Aucun exercice « Relier » créé pour cette catégorie.</p>
                <a href="{{ route('prof.relier.create', $slug) }}" class="text-vert underline text-sm mt-2 inline-block">
                    + Créer un exercice Relier
                </a>
            </div>
        @endforelse

        @if($reliers->isNotEmpty())
            <button type="submit" class="bg-rouge text-white px-4 py-2 rounded mt-2">Enregistrer la sélection</button>
        @endif
    </form>
</div>
@endsection