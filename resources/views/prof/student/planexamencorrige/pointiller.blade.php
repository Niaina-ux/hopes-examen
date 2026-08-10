@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="py-3 me-2">
    @include('layouts.prof-layouts.proflayoutsexamcorrige')

    @if(session('success'))
        <div id="success-alert" class="bg-green-100/50 mt-2 text-green-700 px-4 py-2 rounded-md mb-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="document.getElementById('success-alert').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif
    <div id="section-pointiller">
        <h2 class="p-1 mt-2 flex gap-2 items-center text-rouge">
            <i class="fa-solid fa-pen"></i> Complèter le pointiller <i class="fa-solid fa-pen"></i>
        </h2>
        @foreach($pointillers as $pointiller)
            @php
                // Mikajy ny points_obtenus totaly an'ity pointiller ity manontolo
                $pointsObtenusTotal = \App\Models\PointillerEtudiantReponse::whereIn(
                    'pointiller_reponse_id',
                    $pointiller->pointillerQuestions->pluck('reponses')->flatten()->pluck('id')
                )
                ->where('exam_attempt_id', $attempt->id)
                ->sum('points_obtenus');
            @endphp
            <div class="border border-black/10 p-4 rounded-md mb-3 text-base">
                <div class="flex gap-3  mb-2">
                    <div class="w-12 h-12 rounded-md flex justify-center items-center font-semibold bg-black/3">
                        {{ $loop->iteration }}
                    </div>
                    <div class="flex-1">
                        <div class="flex gap-3 items-start">
                            <h3 class="flex-1 text-lg font-semibold">{{ $pointiller->titre }}</h3>
                            <div class="text-sm flex gap-3">
                                <span class="border border-black/20 rounded-full px-2 text-rouge">
                                    {{ $pointsObtenusTotal }} Pts obtenus
                                </span>
                                <span class="border border-black/20 rounded-full px-2 text-vert">
                                    {{ $pointiller->note_totale }} Pts total
                                </span>
                            </div> 
                        </div>

                        <div class="bg-black/2 border border-black/3 mt-2 p-2 rounded-md">
                            @foreach($pointiller->pointillerQuestions as $qIndex => $question)
                                @php
                                    $reponsesParTrou = [];
                                    foreach ($question->reponses as $rep) {
                                        $reponseEtudiant = \App\Models\PointillerEtudiantReponse::where('pointiller_reponse_id', $rep->id)
                                            ->where('exam_attempt_id', $attempt->id)
                                            ->first();
                                        $reponsesParTrou[$rep->position] = [
                                            'donnee'    => $reponseEtudiant?->reponse_donnee,
                                            'correcte'  => $rep->reponse_correcte,
                                            'est_bon'   => $reponseEtudiant?->est_correcte ?? false,
                                        ];
                                    }
        
                                    // ✅ Points obtenus pour CETTE question précise (somme de tous ses trous)
                                    $pointsQuestionObtenus = \App\Models\PointillerEtudiantReponse::whereIn(
                                        'pointiller_reponse_id',
                                        $question->reponses->pluck('id')
                                    )
                                    ->where('exam_attempt_id', $attempt->id)
                                    ->sum('points_obtenus');
        
                                    $texteAffiche = preg_replace_callback('/\[(\d+)\]/', function ($matches) use ($reponsesParTrou) {
                                        $numero = (int) $matches[1];
                                        $trou = $reponsesParTrou[$numero] ?? null;
                                        if (!$trou) {
                                            return $matches[0];
                                        }
                                        $estBon = $trou['est_bon'];
                                        $donnee = $trou['donnee'] ?: '—';
                                        $couleurBordure = $estBon ? 'border-black/5' : 'border-black/5';
                                        $icone = $estBon
                                            ? '<i class="fa-solid fa-check text-green-600 ms-1"></i>'
                                            : '<i class="fa-solid fa-xmark text-red-600 ms-1"></i>';
                                        return '<span class="inline-flex items-center border rounded bg-black/3 px-1 mx-1 ' . $couleurBordure . '">'
                                            . e($donnee) . $icone . '</span>';
                                    }, e($question->enonce));
                                @endphp
                                <div class="p-1 bg-white/90 rounded px-2 my-1 flex justify-between gap-3">
                                    <p class="mb-1 leading-loose">{{ $qIndex + 1 }} - {!! $texteAffiche !!}</p>
                                    <span class="text-sm">{{ $pointsQuestionObtenus }} / {{ $question->points }} Pts</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        @if($typePointiller)
            <form action="{{ route('prof.correction.storeCommentaire') }}" method="POST" class="">
                @csrf
                <input type="hidden" name="commentable_id" value="{{ $typePointiller->id }}">
                <input type="hidden" name="commentable_type" value="{{ \App\Models\TypeExercice::class }}">
                <input type="hidden" name="examen_id" value="{{ $examen->id }}">
                <input type="hidden" name="exam_attempt_id" value="{{ $attempt->id }}">

                <div class="border border-black/10 rounded-md p-2 bg-black/3">
                    
                    <textarea name="contenu" rows="2"
                        class="border border-black/10 w-full rounded p-2 bg-white"
                        placeholder="Commente ici cette exercice ..">{{ old('contenu', $commentsPointiller->contenu ?? '') }}</textarea>
                    <div class="flex justify-end mt-1">
                        <button type="submit" class="p-1 px-2 rounded text-white bg-rouge">
                            {{ $commentsPointiller ? 'Modifier' : 'Commenter' }}
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection