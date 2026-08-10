@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="py-3 me-2">
    @include('layouts.prof-layouts.proflayoutsexamcorrige')
    @if(session('success'))
        <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 mt-2 py-2 rounded-md mb-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="document.getElementById('success-alert').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif
    <div class="">
        <div id="section-qcm" class="text-base">
            <h2 class="p-1 mt-2 flex gap-2 items-center text-rouge">
                <i class="fa-brands fa-letterboxd"></i>
                Question à choix multiple
                <i class="fa-brands fa-letterboxd"></i>
            </h2>
            @foreach($qcms as $qIndex => $qcm)
                <div class="border border-black/10 p-4 rounded-md mb-3">
                    <div class="flex gap-3">
                        <div class="w-12 h-12 bg-black/3 rounded-md flex justify-center items-center font-semibold">
                            {{ $qIndex + 1 }}
                        </div>
                        <div class="flex-1">
                            <div class="flex-1 flex items-center gap-3">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold">{{ $qcm->titre }}</h3>
                                </div>
                                <div class="text-sm flex gap-3">
                                    <span class="border border-black/20 rounded-full px-2 text-rouge">
                                        {{ $qcm->qcmQuestions->flatMap(fn($q) => $q->qcmReponsesEtudiants)->sum('points_obtenus') }} Pts obtenus
                                    </span>
                                    <span class="border border-black/20 rounded-full px-2 text-vert">{{ $qcm->note_totale }} Pts total</span>
                                </div>
                            </div>
                            @foreach($qcm->qcmQuestions as $index => $question)
                                @php
                                    $studentAnswers = $question->qcmReponsesEtudiants->pluck('qcm_choice_id')->toArray();
                                @endphp
                                <div class="p-2 bg-black/3 rounded my-2">
                                    <div class="flex justify-between">
                                        <h4 class="">{{ $index + 1 }} - {{ $question->enonce }}</h4>
                                        <div class="text-sm text-gray-500">
                                            {{ $question->qcmReponsesEtudiants->sum('points_obtenus') }} / {{ $question->points }} pts
                                        </div>
                                    </div>
                                    @foreach($question->qcmChoices as $choice)
                                        @php $isStudentAnswer = in_array($choice->id, $studentAnswers); @endphp
                                        @if($isStudentAnswer)
                                            <div class="flex items-center gap-3 bg-white/90 my-1 p-2 rounded">
                                                @if($choice->est_correcte)
                                                    <i class="fa-solid fa-circle-check text-green-600"></i>
                                                @else
                                                    <i class="fa-solid fa-circle-xmark text-red-600"></i>
                                                @endif
                                                <span class="flex-1">{{ $choice->texte }}</span>
                                                <span class="text-xs {{ $choice->est_correcte ? 'text-green-600' : 'text-red-600' }}">
                                                    Votre réponse
                                                </span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Commentaire du prof — un seul, pour toute la section QCM de cet examen+attempt --}}
            @if($typeQcm)
                <form action="{{ route('prof.correction.storeCommentaire') }}" method="POST" class="mt-2">
                    @csrf
                    <input type="hidden" name="commentable_id" value="{{ $typeQcm->id }}">
                    <input type="hidden" name="commentable_type" value="{{ \App\Models\TypeExercice::class }}">
                    <input type="hidden" name="examen_id" value="{{ $examen->id }}">
                    <input type="hidden" name="exam_attempt_id" value="{{ $attempt->id }}">

                    <div class="border border-black/10 rounded-md p-2 bg-black/3">
                        <textarea name="contenu" rows="2"
                            class="border border-black/10 w-full rounded p-2 bg-white"
                            placeholder="Commente ici cette exercice ..">{{ old('contenu', $commentsQcm->contenu ?? '') }}</textarea>
                        <div class="flex justify-end mt-1">
                            <button type="submit" class="p-1 px-2 rounded text-white bg-rouge">
                                {{ $commentsQcm ? 'Modifier le commentaire' : 'Commenter' }}
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection