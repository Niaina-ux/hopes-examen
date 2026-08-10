
@extends('layouts.student-layouts.layouthead')
@section('contenue-student')
<div class="container py-25">
    <div class="bg-white z-50 sticky top-0">
        <div class="flex justify-between py-2">
            <div class="rounded-md">
                <h2 class="text-2xl font-semibold text-vert">{{ $examen->titre }}</h2>
                <p class="py-1">{{ $examen->description }}</p>
                <p class="text-sm text-black/50">Finis le {{ $attempt->date_fin?->format('d/m/Y à H:i') }}</p>
            </div>
            <div class="mt-2">
                <span class="border border-black/10 rounded-full p-1 px-4 
                    {{ $attempt->status === 'corrige' ? 'text-vert' : 'text-rouge' }}">
                    {{ $attempt->status === 'corrige' ? 'Corrigé' : 'En attente de correction' }}
                </span>
            </div>
        </div>
        {{-- Navigation rapide entre les sections --}}
       <div class="flex flex-wrap gap-2 items-center  z-50 bg-white pt-3 pb-2 border-b-2 border-black/10">
            @foreach($examen->typesExercice as $type)
                <a href="#section-{{ $type->slug }}"
                class="menu-section p-1 flex items-center px-2 rounded border border-black/5 bg-black/3 transition-all duration-200 hover:bg-rouge hover:text-white hover:border-rouge"
                data-target="section-{{ $type->slug }}">
                    {{ $type->nom }}
                </a>
            @endforeach
        </div>
    </div>
    <div class="flex justify-between  gap-5 compare-section">    
        <div class="w-[70%]" >

            {{-- Section QCM --}}
            @if($qcms->isNotEmpty())
                <div id="section-qcm" class="text-base">
                    <h2 class="p-1 mt-2 flex gap-2 items-center text-rouge">
                        <i class="fa-brands fa-letterboxd"></i>
                        Question à choix multiple
                        <i class="fa-brands fa-letterboxd"></i>
                    </h2>
                    @foreach($qcms as $qIndex => $qcm)
                        <div class="border border-black/10 p-4 rounded-md mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-black/3 rounded-md flex justify-center items-center font-semibold">
                                    {{ $qIndex + 1 }}
                                </div>
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

                            @foreach($qcm->qcmQuestions as $question)
                                @php
                                    $studentAnswers = $question->qcmReponsesEtudiants->pluck('qcm_choice_id')->toArray();
                                @endphp
                                <div class="p-2 bg-black/3 rounded my-2">
                                    <div class="flex justify-between">
                                        <h4 class="">{{ $question->ordre }} - {{ $question->enonce }}</h4>
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
                    @endforeach
                    @if($typeQcm && $commentsQcm)
                        <div class="mt-2 p-3 rounded-md bg-black/3 border border-black/10 text-sm">
                            <span class="text-black/50 font-semibold">Remarque générale du professeur :</span>
                            <p class="whitespace-pre-line mt-1">{{ $commentsQcm->contenu }}</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Section Pointiller --}}
            @if($pointillers->isNotEmpty())
                <div id="section-pointiller">
                    <h2 class="p-1 mt-4 flex gap-2 items-center text-rouge">
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
                            <div class="flex gap-3 items-center mb-2">
                                <div class="w-8 h-8 rounded-md flex justify-center items-center font-semibold bg-black/3">
                                    {{ $loop->iteration }}
                                </div>
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
                            <div class="bg-black/3 p-2 rounded-md">
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
                    @endforeach
                    @if($typePointiller && $commentsPointiller)
                        <div class="mt-2 p-3 rounded-md bg-black/3 border border-black/10 text-sm">
                            <span class="text-black/50 font-semibold">Remarque générale du professeur :</span>
                            <p class="whitespace-pre-line mt-1">{{ $commentsPointiller->contenu }}</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Section Relier --}}
            @if($reliers->isNotEmpty())
                <div id="section-relier">
                    <h2 class="p-1 mt-4 flex gap-2 items-center text-rouge">
                        <i class="fa-solid fa-link"></i> Relier par flèche <i class="fa-solid fa-link"></i>
                    </h2>
                    @foreach($reliers as $relier)
                        <div class="border border-black/10 rounded-md mb-3 p-4">
                            @php
                                $pointsObtenusTotal = \App\Models\RelierReponse::whereIn('relier_paire_id', $relier->relierQuestions->pluck('paires')->flatten()->pluck('id'))
                                    ->where('exam_attempt_id', $attempt->id)
                                    ->sum('points_obtenus');
                            @endphp
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-md bg-black/3 flex justify-center items-center font-semibold">
                                    {{ $loop->iteration }}
                                </div>
                                <h3 class="text-lg font-semibold flex-1">{{ $relier->titre }}</h3>
                                <div class="text-sm flex gap-3">
                                    <span class="border border-black/20 rounded-full px-2 text-rouge">
                                        {{ $pointsObtenusTotal }} Pts obtenus
                                    </span>
                                    <span class="border border-black/20 rounded-full px-2 text-vert">
                                        {{ $relier->note_totale }} Pts total
                                    </span>
                                </div>
                            </div>
                            @foreach($relier->relierQuestions as $question)
                                @php
                                    $paires = $question->paires->sortBy('ordre_gauche')->values();
                                    $reponsesEtudiant = \App\Models\RelierReponse::where('exam_attempt_id', $attempt->id)
                                        ->whereIn('relier_paire_id', $paires->pluck('id'))
                                        ->get()
                                        ->keyBy('relier_paire_id');
                                @endphp
                                <div class=" mb-2 bg-black/3 rounded p-2">
                                    <div class="flex justify-between mb-1">
                                        <h4 class="text-base"> {{$question->ordre}} - {{ $question->enonce }}</h4>
                                        <div class="text-sm text-gray-500 text-nowrap">
                                            {{ $reponsesEtudiant->sum('points_obtenus') }} / {{ $question->points }} pts
                                        </div>
                                    </div>
                                    <div class="relative flex justify-between gap-16" >
                                        <div class="flex-1 flex flex-col gap-2">
                                            @foreach($paires as $paire)
                                                <div class="relier-item-gauche p-1 px-2 rounded bg-white/90  border-e border-black/10" data-paire-id="{{ $paire->id }}">
                                                    {{ $paire->element_gauche }}
                                                </div>
                                            @endforeach
                                        </div>
                                        <svg class="absolute top-0 left-0 w-full h-full pointer-events-none">
                                            @foreach($paires as $paire)
                                                @php
                                                    $reponse = $reponsesEtudiant->get($paire->id);
                                                    $paireChoisieId = $reponse?->paire_choisie_id;
                                                    $estCorrecte = $reponse?->est_correcte ?? false;
                                                @endphp
                                                @if($paireChoisieId)
                                                    <line
                                                        class="relier-fleche"
                                                        data-gauche-id="{{ $paire->id }}"
                                                        data-droite-id="{{ $paireChoisieId }}"
                                                        stroke="{{ $estCorrecte ? '#16a34a' : '#dc2626' }}"
                                                        stroke-width="2"
                                                    />
                                                @endif
                                            @endforeach
                                        </svg>
                                        <div class="flex-1 flex flex-col gap-2">
                                            @foreach($paires->sortBy('ordre_droite')->values() as $paire)
                                                <div class="relier-item-droite p-1 px-2 rounded bg-white/90  border-s border-black/10" data-paire-id="{{ $paire->id }}">
                                                    {{ $paire->element_droite }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                    @if($typeRelier && $commentsRelier)
                        <div class="mt-2 p-3 rounded-md bg-black/3 border border-black/10 text-sm">
                            <span class="text-black/50 font-semibold">Remarque générale du professeur :</span>
                            <p class="whitespace-pre-line mt-1">{{ $commentsRelier->contenu }}</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Section Code (correction manuelle) --}}
            @if($codes->isNotEmpty())
                <div id="section-code">
                    <h2 class="p-1 flex gap-2 items-center text-rouge">
                        <i class="fa-solid fa-code"></i> Exercice de code <i class="fa-solid fa-code"></i>
                    </h2>
                    @foreach($codes as $code)
                        @php
                            $reponsesCode = $code->codeQuestions->flatMap(fn($q) => $q->reponses);
                            $obtenusCode = $reponsesCode->sum('points_obtenus');
                            $estCorrigeCode = $reponsesCode->isNotEmpty() && $reponsesCode->every(fn($r) => $r->points_obtenus !== null);
                        @endphp
                        <div class="border border-black/10 p-4 rounded-md mb-3 ">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-md bg-black/3 flex justify-center items-center font-semibold">
                                    {{ $loop->iteration }}
                                </div>
                                <h3 class="text-lg font-semibold flex-1">{{ $code->titre }}</h3>
                                <div class="text-sm flex gap-3">
                                    <span class="border text-sm border-black/20 rounded-full px-2 {{ $estCorrigeCode ? 'text-rouge' : 'text-black/40' }}">
                                        {{ $estCorrigeCode ? $obtenusCode . ' Pts obtenus' : 'En attente' }}
                                    </span>
                                    <span class="border text-sm border-black/20 rounded-full px-2 text-vert">
                                        {{ $code->note_totale }} Pts total
                                    </span>
                                </div>
                            </div>
                            @foreach ($code->codeQuestions as $question)
                                @php $reponse = $question->reponses->first(); @endphp
                                <div class="p-2 rounded-md bg-black/3 my-1">
                                    <div class="flex gap-3 justify-between">
                                        <p>{{ $question->ordre }} - {{ $question->instruction }}</p>
                                        <span class="text-nowrap text-sm mt-1">
                                            <span class="{{ $reponse?->points_obtenus !== null ? 'text-rouge' : 'text-black/40' }}">
                                                {{ $reponse?->points_obtenus ?? 'En attente' }}
                                            </span> / {{ $question->points }} Pts
                                        </span>
                                    </div>
                                   <div class="max-w-full overflow-x-auto">
                                        <pre class="p-2 mt-1 bg-white/90 rounded inline-block min-w-full">{!! $estCorrigeCode && $reponse?->code_annote
                                            ? $reponse->code_annote
                                            : e($reponse?->code_soumis ?? 'Aucun code soumis') !!}</pre>
                                    </div>
                                    @if($reponse?->commentaire_prof)
                                        <div class="mt-2 pt-2 border-t border-black/10 text-sm">
                                            <span class="text-black/50">Commentaire du professeur :</span>
                                            <p class="whitespace-pre-line">{{ $reponse->commentaire_prof }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach  
                        </div>
                    @endforeach
                    @if($typeCode && $commentsCode)
                        <div class="mt-2 p-3 rounded-md bg-black/3 border border-black/10 text-sm">
                            <span class="text-black/50 font-semibold">Remarque générale du professeur :</span>
                            <p class="whitespace-pre-line mt-1">{{ $commentsCode->contenu }}</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Section Text --}}
            @if($texts->isNotEmpty())
                <div id="section-text" class="text-base">
                    <h2 class="p-1 flex gap-2 items-center text-rouge">
                        <i class="fa-solid fa-align-left"></i> Compréhension du texte <i class="fa-solid fa-align-left"></i>
                    </h2>
                    @foreach($texts as $text)
                        @php
                            $reponsesText = $text->textQuestions->flatMap(fn($q) => $q->reponses);
                            $obtenusText = $reponsesText->sum('note_obtenue');
                            $estCorrigeText = $reponsesText->isNotEmpty() && $reponsesText->every(fn($r) => $r->note_obtenue !== null);
                        @endphp
                        <div class="border border-black/10 p-4 rounded-md mb-3">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-md bg-black/3 flex justify-center items-center font-semibold">
                                    {{ $loop->iteration }}
                                </div>
                                <h3 class="text-lg font-semibold flex-1">{{ $text->titre }}</h3>
                                <div class="text-sm flex gap-3">
                                    <span class="border border-black/20 rounded-full px-2 {{ $estCorrigeText ? 'text-rouge' : 'text-black/40' }}">
                                        {{ $estCorrigeText ? $obtenusText . ' Pts obtenus' : 'En attente' }}
                                    </span>
                                    <span class="border border-black/20 rounded-full px-2 text-vert">
                                        {{ $text->note_totale }} Pts total
                                    </span>
                                </div>
                            </div>
                            <p class="whitespace-pre-line">{{ $text->texte }}</p>
                            <span class="my-2 inline-block text-sm">Questions & Réponses</span>
                            @foreach ($text->textQuestions as $textQuestion)
                                @php $reponse = $textQuestion->reponses->first(); @endphp
                                <div class="mb-2 bg-black/3 p-2 rounded">
                                    <div class="flex justify-between gap-3">
                                        <p>{{ $textQuestion->ordre }} - {{ $textQuestion->enonce }}</p>
                                        <div class="flex gap-2 text-sm mt-1 text-nowrap">
                                            <span class="{{ $reponse?->note_obtenue !== null ? 'text-rouge' : 'text-black/40' }}">
                                                {{ $reponse?->note_obtenue ?? 'En attente' }}
                                            </span> /
                                            <span>{{ $textQuestion->points }} Pts</span>
                                        </div>
                                    </div>
                                    <div class="p-2 rounded bg-white/90 mt-1">
                                        @if($estCorrigeText && $reponse?->reponse_annotee)
                                            {!! $reponse->reponse_annotee !!}
                                        @else
                                            {{ $reponse?->reponse_texte ?? 'Réponse vide !!' }}
                                        @endif
                                    </div>
                                    @if($reponse?->commentaire_prof)
                                        <div class="mt-2 pt-2 border-t border-black/10 text-sm">
                                            <span class="text-black/50">Commentaire du professeur :</span>
                                            <p class="whitespace-pre-line">{{ $reponse->commentaire_prof }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach  
                        </div>
                    @endforeach
                    @if($typeText && $commentsText)
                        <div class="mt-2 p-3 rounded-md bg-black/3 border border-black/10 text-sm">
                            <span class="text-black/50 font-semibold">Remarque générale du professeur :</span>
                            <p class="whitespace-pre-line mt-1">{{ $commentsText->contenu }}</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Section Rédaction --}}
            @if($redactions->isNotEmpty())
                <div id="section-redaction" >
                    <h2 class="p-1 mt-4 flex gap-2 items-center text-rouge">
                        <i class="fa-solid fa-feather"></i> Rédaction <i class="fa-solid fa-feather"></i>
                    </h2>
                    @foreach($redactions as $redaction)
                        <div class="border border-black/10 p-4 rounded-md mb-3">
                            @php $repRedaction = $redaction->reponses->first(); @endphp
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-md bg-black/3 flex justify-center items-center font-semibold">
                                    {{ $loop->iteration }}
                                </div>
                                <h3 class="text-lg font-semibold flex-1">{{ $redaction->titre }}</h3>
                                <div class="text-sm flex gap-3">
                                    <span class="border border-black/20 rounded-full px-2 text-rouge">
                                        {{ $repRedaction?->note_obtenue ?? 'en attente' }} Pts
                                    </span> /
                                    <span class="border border-black/20 rounded-full px-2 text-vert">
                                        {{ $redaction->note_totale }} Pts total
                                    </span>
                                </div>
                            </div>
                            <div class="text-base">
                                <span>Sujet</span>
                                <p class="mb-1 whitespace-pre-line"> {{$redaction->sujet}} </p>
                                <span>Instruction</span>
                                <p class="mb-1 whitespace-pre-line"> {{$redaction->instruction}} </p>
                                <div class="p-2 rounded-md bg-black/3">
                                    <span>Reponse</span>
                                    <pre class="p-2 bg-white/90 rounded whitespace-pre-line mt-1">
                                        @if($repRedaction?->note_obtenue !== null && $repRedaction?->reponse_annotee)
                                            {!! $repRedaction->reponse_annotee !!}
                                        @else
                                            {{ $repRedaction?->reponse_texte }}
                                        @endif
                                    </pre>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if($typeRedaction && $commentsRedaction)
                        <div class="mt-2 p-3 rounded-md bg-black/3 border border-black/10 text-sm">
                            <span class="text-black/50 font-semibold">Remarque générale du professeur :</span>
                            <p class="whitespace-pre-line mt-1">{{ $commentsRedaction->contenu }}</p>
                        </div>
                    @endif
                </div>
            @endif

            @if($glisserDeposers->isNotEmpty())
                <div id="section-glisserdeposer">
                    <h2 class="p-1 mt-4 flex gap-2 items-center text-rouge">
                        <i class="fa-solid fa-arrows-up-down-left-right"></i> Glisser-déposer <i class="fa-solid fa-arrows-up-down-left-right"></i>
                    </h2>
                    @foreach($glisserDeposers as $glisserDeposer)
                        @php
                            // ✅ Kajiana ny fitambaran'ny points azon'ny mpianatra amin'ity glisserDeposer ity manontolo
                            $pointsObtenusTotal = 0;
                            foreach ($glisserDeposer->questions as $q) {
                                $totalCorrect = $q->items->filter(fn($i) => ($i->reponses->first()->est_correcte ?? false))->count();
                                $totalItems = $q->items->count();
                                $pointsObtenusTotal += $totalItems > 0 ? round(($totalCorrect / $totalItems) * $q->points, 2) : 0;
                            }
                        @endphp
                        <div class="border border-black/10 p-4 rounded-md mb-2">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-md bg-black/3 flex justify-center items-center font-semibold">
                                    {{ $loop->iteration }}
                                </div>
                                <h3 class="text-lg font-semibold flex-1">{{ $glisserDeposer->titre }}</h3>
                                <div class="text-sm flex gap-3">
                                    <span class="border border-black/20 rounded-full px-2 text-rouge">
                                        {{ $pointsObtenusTotal }} Pts obtenus
                                    </span>
                                    <span class="border border-black/20 rounded-full px-2 text-vert">
                                        {{ $glisserDeposer->note_totale }} Pts total
                                    </span>
                                </div>
                            </div>
                            @foreach($glisserDeposer->questions as $question)
                                <div class="mb-2 border-b border-black/5 p-2 rounded-md bg-black/3 text-base">
                                    @if($question->enonce)
                                    <div class="flex justify-between gap-3 mb-2">
                                        <p class="">{{ $question->ordre }} - {{ $question->enonce }}</p>
                                        @php
                                            $totalCorrect = $question->items->filter(fn($i) => ($i->reponses->first()->est_correcte ?? false))->count();
                                            $totalItems = $question->items->count();
                                            $pointsCalcules = $totalItems > 0 ? round(($totalCorrect / $totalItems) * $question->points, 2) : 0;
                                        @endphp
                                        <span>{{ $pointsCalcules }} / {{ $question->points }}</span>
                                    </div>
                                    @endif
                                    <div class="relative inline-block border border-black/10 rounded overflow-hidden">
                                        <img src="{{ asset('images/glisserdeposer/' . $question->image) }}" class="w-full block">
                                        @foreach($question->zones as $zone)
                                            @php
                                                $itemDeCeZone = $question->items->firstWhere('glisser_deposer_zone_id', $zone->id);
                                                $reponseEtudiant = $itemDeCeZone?->reponses->first();
                                                $estBon = $reponseEtudiant?->est_correcte ?? false;
                                                $aRepondu = $reponseEtudiant !== null;
                                            @endphp
                                            <div
                                                class="absolute border-2 rounded-md flex items-center justify-center text-xs px-1 text-center
                                                    {{ !$aRepondu ? 'border-dashed border-black/30 bg-black/5 text-black/40' : ($estBon ? 'border-green-600 bg-green-50 text-green-700' : 'border-red-600 bg-red-50 text-red-700') }}"
                                                style="left: {{ $zone->position_x }}%; top: {{ $zone->position_y }}%; min-width: 90px; height: 40px; transform: translate(-50%, -50%);"
                                            >
                                                @if($aRepondu)
                                                    @php
                                                        $itemVoafidy = $glisserDeposer->questions
                                                            ->flatMap->items
                                                            ->first(fn($i) => $i->id === $reponseEtudiant->glisser_deposer_item_id);
                                                    @endphp
                                                    <span>
                                                        {{ $itemVoafidy->texte ?? '—' }}
                                                        <i class="fa-solid {{ $estBon ? 'fa-check' : 'fa-xmark' }} ms-1"></i>
                                                    </span>
                                                @else
                                                    <span class="italic">Non répondu</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                    @if($typeGlisserDeposer && $commentsGlisserDeposer)
                        <div class="mt-2 p-3 rounded-md bg-black/3 border border-black/10 text-sm">
                            <span class="text-black/50 font-semibold">Remarque générale du professeur :</span>
                            <p class="whitespace-pre-line mt-1">{{ $commentsGlisserDeposer->contenu }}</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Section Fichier (upload/download) --}}
            @if($fichiers->isNotEmpty())
                <div id="section-fichier">
                    <h2 class="p-1 mt-4 flex gap-2 items-center text-rouge">
                        <i class="fa-solid fa-file-arrow-up"></i> Devoir à rendre <i class="fa-solid fa-file-arrow-up"></i>
                    </h2>
                    @foreach($fichiers as $fichier)
                        @php
                            $reponsesFichier = $fichier->fichierQuestions->flatMap(fn($q) => $q->reponses);
                            $obtenusFichier = $reponsesFichier->sum('points_obtenus');
                            $estCorrigeFichier = $reponsesFichier->isNotEmpty() && $reponsesFichier->every(fn($r) => $r->points_obtenus !== null);
                        @endphp
                        <div class="border border-black/10 p-4 rounded-md mb-3 text-base">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-md bg-black/3 flex justify-center items-center font-semibold">
                                    {{ $loop->iteration }}
                                </div>
                                <h3 class="text-lg font-semibold flex-1">{{ $fichier->titre }}</h3>
                                <div class="text-sm flex gap-3">
                                    <span class="border border-black/20 rounded-full px-2 {{ $estCorrigeFichier ? 'text-rouge' : 'text-black/40' }}">
                                        {{ $estCorrigeFichier ? $obtenusFichier . ' Pts obtenus' : 'En attente' }}
                                    </span>
                                    <span class="border border-black/20 rounded-full px-2 text-vert">
                                        {{ $fichier->note_totale }} Pts total
                                    </span>
                                </div>
                            </div>

                            @foreach($fichier->fichierQuestions as $question)
                                @php $reponse = $question->reponses->first(); @endphp
                                <div class="p-2 rounded-md bg-black/3 my-1">
                                    <div class="flex gap-3 justify-between items-start mb-2">
                                        <p class="flex-1">{{ $question->ordre }} - {{ $question->instruction }}</p>
                                        <span class="text-sm text-nowrap">
                                            {{ $reponse?->points_obtenus ?? 'En attente' }} / {{ $question->points }} Pts
                                        </span>
                                    </div>

                                    @if($question->fichier_prof)
                                        <a href="{{ asset('fichiers/questions/' . $question->fichier_prof) }}" target="_blank"
                                            class="inline-flex items-center gap-2 text-sm text-vert bg-white/90 rounded px-3 py-2 mb-2">
                                            <i class="fa-solid fa-download"></i> Télécharger le sujet
                                        </a>
                                    @endif

                                    <div class="bg-white/90 rounded p-2">
                                        @if($reponse?->fichier_etudiant)
                                            <a href="{{ asset('fichiers/reponses/' . $reponse->fichier_etudiant) }}" target="_blank"
                                                class="inline-flex items-center gap-2 text-sm text-black/70">
                                                <i class="fa-solid fa-file"></i> Voir votre fichier soumis
                                            </a>
                                        @else
                                            <span class="text-sm text-black/40 italic">Aucun fichier soumis</span>
                                        @endif

                                        @if($reponse?->commentaire_prof)
                                            <div class="mt-2 pt-2 border-t border-black/10 text-sm">
                                                <span class="text-black/50">Commentaire du professeur :</span>
                                                <p class="whitespace-pre-line">{{ $reponse->commentaire_prof }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                    @if($typeFichier && $commentsFichier)
                        <div class="mt-2 p-3 rounded-md bg-black/3 border border-black/10 text-sm">
                            <span class="text-black/50 font-semibold">Remarque générale du professeur :</span>
                            <p class="whitespace-pre-line mt-1">{{ $commentsFichier->contenu }}</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Section Mots croisés --}}
            @if($motsCroisesListe->isNotEmpty())
                <div id="section-motscroises">
                    <h2 class="p-1 mt-4 flex gap-2 items-center text-rouge">
                        <i class="fa-solid fa-table-cells"></i> Mots croisés <i class="fa-solid fa-table-cells"></i>
                    </h2>
                    @foreach($motsCroisesListe as $motsCroise)
                        @php
                            $mots = $motsCroise->motsCroisesMots;
                            $reponses = $mots->flatMap(fn($m) => $m->reponses);
                            $obtenusMc = $reponses->sum('points_obtenus');

                            $largeur = 0;
                            $hauteur = 0;
                            foreach ($mots as $mot) {
                                if ($mot->direction === 'horizontal') {
                                    $largeur = max($largeur, $mot->position_x + strlen($mot->reponse));
                                    $hauteur = max($hauteur, $mot->position_y + 1);
                                } else {
                                    $largeur = max($largeur, $mot->position_x + 1);
                                    $hauteur = max($hauteur, $mot->position_y + strlen($mot->reponse));
                                }
                            }

                            // grille[y][x] = ['active' => bool, 'lettre' => str|null, 'numero' => int|null, 'correcte' => bool|null, 'est_hint' => bool]
                            $grille = [];
                            for ($y = 0; $y < $hauteur; $y++) {
                                for ($x = 0; $x < $largeur; $x++) {
                                    $grille[$y][$x] = ['active' => false, 'lettre' => null, 'numero' => null, 'correcte' => null, 'est_hint' => false];
                                }
                            }

                            foreach ($mots as $mot) {
                                $reponseEtudiant = $mot->reponses->first();
                                $reponseDonnee = $reponseEtudiant?->reponse_donnee ?? '';
                                $longueur = strlen($mot->reponse);
                                $positionsVisibles = $mot->positions_lettres_visibles ?? [];

                                for ($i = 0; $i < $longueur; $i++) {
                                    $x = $mot->direction === 'horizontal' ? $mot->position_x + $i : $mot->position_x;
                                    $y = $mot->direction === 'horizontal' ? $mot->position_y : $mot->position_y + $i;

                                    $grille[$y][$x]['active'] = true;

                                    if ($i === 0) {
                                        $grille[$y][$x]['numero'] = $mot->numero;
                                    }

                                    // ✅ Raha litera "hint" (napetraky ny prof), tsy an'ny mpianatra
                                    if (in_array($i, $positionsVisibles)) {
                                        $grille[$y][$x]['lettre'] = $mot->reponse[$i];
                                        $grille[$y][$x]['est_hint'] = true;
                                        continue;
                                    }

                                    // ✅ Litera nosoratan'ny mpianatra
                                    $lettreEtudiant = $reponseDonnee[$i] ?? '';
                                    if ($lettreEtudiant !== '') {
                                        $grille[$y][$x]['lettre'] = $lettreEtudiant;
                                        $grille[$y][$x]['correcte'] = (strtoupper($lettreEtudiant) === strtoupper($mot->reponse[$i]));
                                    }
                                    // Raha tsy nisy valiny sy tsy hint, dia mijanona "active" fotsiny (case banga, tsy fenoina)
                                }
                            }
                        @endphp

                        <div class="border border-black/10 p-4 rounded-md mb-3 text-base">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-8 h-8 rounded-md bg-black/3 flex justify-center items-center font-semibold">
                                    {{ $loop->iteration }}
                                </div>
                                <h3 class="text-lg font-semibold flex-1">{{ $motsCroise->titre }}</h3>
                                <div class="text-sm flex gap-3">
                                    <span class="border border-black/20 rounded-full px-2 text-rouge">
                                        {{ $obtenusMc }} Pts obtenus
                                    </span>
                                    <span class="border border-black/20 rounded-full px-2 text-vert">
                                        {{ $motsCroise->note_totale }} Pts total
                                    </span>
                                </div>
                            </div>

                            <div class="gap-8 flex justify-center items-center flex-wrap bg-black/3 p-2 rounded-md">
                                <div class="flex justify-center items-center rounded-md  p-2">
                                    <div class="inline-block">
                                        @for($y = 0; $y < $hauteur; $y++)
                                            <div class="flex">
                                                @for($x = 0; $x < $largeur; $x++)
                                                    @php $case = $grille[$y][$x]; @endphp
                                                    @if(!$case['active'])
                                                        {{-- ✅ Case tsy misy litera mihitsy: tsy aseho --}}
                                                        <div class="w-9 h-9"></div>
                                                    @else
                                                        <div class="relative w-9 h-9 border border-black/10 rounded flex items-center justify-center font-bold text-sm
                                                            {{ $case['est_hint']
                                                                ? 'bg-black/5 text-black/60'
                                                                : ($case['correcte'] === true
                                                                    ? 'bg-green-50 text-vert border-green-300'
                                                                    : ($case['correcte'] === false
                                                                        ? 'bg-red-50 text-red-500 border-red-300'
                                                                        : 'bg-white')) }}">
                                                            @if($case['numero'])
                                                                <span class="absolute top-0 left-0.5 text-[8px] text-black/50 font-normal">{{ $case['numero'] }}</span>
                                                            @endif
                                                            @if($case['lettre'])
                                                                <span>{{ strtoupper($case['lettre']) }}</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endfor
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                {{-- Indices --}}
                                <div class="flex-1">
                                    @php
                                        $motsHorizontal = $mots->where('direction', 'horizontal')->sortBy('numero');
                                        $motsVertical = $mots->where('direction', 'vertical')->sortBy('numero');
                                    @endphp

                                    @if($motsHorizontal->isNotEmpty())
                                    <div class="p-2 px-5 my-2 rounded-md bg-white/60">
                                        <h4 class="font-semibold text-sm mb-1">Horizontal</h4>
                                        <ul class=" mb-3">
                                            @foreach($motsHorizontal as $mot)
                                                @php $rep = $mot->reponses->first(); @endphp
                                                <li class="px-1 border-y border-black/3 items-center gap-2 flex justify-between">
                                                    <p>
                                                        <span>{{ $mot->numero }}.</span> {{ $mot->indice }}
                                                    </p>
                                                    <i class="fa-solid {{ ($rep?->est_correcte) ? 'fa-circle-check text-green-600' : 'fa-circle-xmark text-red-600' }}"></i>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif

                                    @if($motsVertical->isNotEmpty())
                                    <div class="p-2 px-5 my-2 rounded-md bg-white/60">
                                        <h4 class="font-semibold text-sm mb-1">Vertical</h4>
                                        <ul class="">
                                            @foreach($motsVertical as $mot)
                                                @php $rep = $mot->reponses->first(); @endphp
                                                <li class="px-1 border-y border-black/3 items-center gap-2 flex justify-between">
                                                    <p>
                                                        <span>{{ $mot->numero }}.</span> {{ $mot->indice }}
                                                    </p>
                                                    <i class="fa-solid {{ ($rep?->est_correcte) ? 'fa-circle-check text-green-600' : 'fa-circle-xmark text-red-600' }}"></i>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if($typeMotsCroises && $commentsMotsCroises)
                        <div class="mt-2 p-3 rounded-md bg-black/3 border border-black/10 text-sm">
                            <span class="text-black/50 font-semibold">Remarque générale du professeur :</span>
                            <p class="whitespace-pre-line mt-1">{{ $commentsMotsCroises->contenu }}</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Section Fichier (upload/download) --}}
            @if($image->isNotEmpty())
            <div id="section-image">
                <h2 class="p-1 mt-4 flex gap-2 items-center text-rouge">
                    <i class="fa-solid fa-image"></i>
                    Devoir image
                    <i class="fa-solid fa-image"></i>
                </h2>
                @foreach($image as $imageExercice)
                    @php
                        $reponsesImage = $imageExercice->questions->flatMap(fn($q) => $q->reponses);
                        $obtenusImage = $reponsesImage->sum('points_obtenus');
                        $estCorrigeImage = $reponsesImage->isNotEmpty()
                            && $reponsesImage->every(fn($r) => $r->points_obtenus !== null);
                    @endphp
                    <div class="border border-black/10 p-4 rounded-md mb-3 text-base">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 rounded-md bg-black/3 flex justify-center items-center font-semibold">
                                {{ $loop->iteration }}
                            </div>
                            <h3 class="text-lg font-semibold flex-1">
                                {{ $imageExercice->titre }}
                            </h3>
                            <div class="text-sm flex gap-3">
                                <span class="border border-black/20 rounded-full px-2 {{ $estCorrigeImage ? 'text-rouge' : 'text-black/40' }}">
                                    {{ $estCorrigeImage ? $obtenusImage.' Pts obtenus' : 'En attente' }}
                                </span>
                                <span class="border border-black/20 rounded-full px-2 text-vert">
                                    {{ $imageExercice->note_totale }} Pts total
                                </span>
                            </div>
                        </div>
                        @foreach($imageExercice->questions as $question)
                            @php
                                $reponse = $question->reponses->first();
                            @endphp
                            <div class="p-2 rounded-md bg-black/3 my-2">
                                <div class="flex justify-between items-start mb-2">
                                    <p class="flex-1">
                                        {{ $question->ordre }} -
                                        {{ $question->instruction }}
                                    </p>
                                    <span class="text-sm text-nowrap">
                                        {{ $reponse?->points_obtenus ?? 'En attente' }}
                                        /
                                        {{ $question->points }} Pts
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    {{-- Sujet du professeur --}}
                                    <div class="bg-white rounded-md p-2 border border-black/10">
                                        <p class="text-sm font-medium mb-2">
                                            Sujet
                                        </p>
                                        @if($question->image)
                                            <img
                                                src="{{ asset('images/image_exercice/'.$question->image) }}"
                                                class="w-full h-56 object-contain rounded border border-black/20">
                                        @else
                                            <div class="text-sm italic text-black/40">
                                                Aucune image.
                                            </div>
                                        @endif
                                    </div>
                                    {{-- Réponse étudiant --}}
                                    <div class="bg-white rounded-md p-2 border border-black/10">
                                        <p class="text-sm font-medium mb-2">
                                            Votre réponse
                                        </p>
                                        @if($reponse?->image_soumise)
                                            <img
                                                src="{{ asset('images/image_reponses/'.$reponse->image_soumise) }}"
                                                class="w-full h-56 object-contain rounded border border-black/20">
                                        @else
                                            <div class="text-sm italic text-black/40">
                                                Aucune image envoyée.
                                            </div>
                                        @endif
                                        @if($reponse?->commentaire_prof)
                                            <div class="mt-3 pt-3 border-t border-black/10">
                                                <span class="text-sm text-black/50">
                                                    Commentaire du professeur
                                                </span>
                                                <p class="mt-1 whitespace-pre-line">
                                                    {{ $reponse->commentaire_prof }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                             </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
                @if($typeImage && $commentsImage)
                    <div class="mt-2 p-3 rounded-md bg-black/3 border border-black/10 text-sm">
                        <span class="text-black/50 font-semibold">Remarque générale du professeur :</span>
                        <p class="whitespace-pre-line mt-1">{{ $commentsImage->contenu }}</p>
                    </div>
                @endif
            </div>
            @endif
        </div>
        <div class=" w-[30%] pt-4">
            <h2 class="text-lg font-semibold text-vert mb-1">Résumé</h2>
            <div class="rounded-md flow-right sticky top-0 self-start">
                @php
                    $resumeParType = [];
                    $totalPointsGlobalObtenus = 0;
                    $totalNoteGlobal = 0;

                    $typesCorrectionManuelle = ['code', 'text', 'redaction', 'fichier', 'image'];

                    // QCM
                    if ($qcms->isNotEmpty()) {
                        $obtenus = $qcms->flatMap(fn($q) => $q->qcmQuestions)->flatMap(fn($q) => $q->qcmReponsesEtudiants)->sum('points_obtenus');
                        $total = $qcms->sum('note_totale');
                        $resumeParType['qcm'] = ['nom' => 'QCM', 'obtenus' => $obtenus, 'total' => $total];
                    }

                    // Pointiller
                    if ($pointillers->isNotEmpty()) {
                        $obtenus = \App\Models\PointillerEtudiantReponse::whereIn(
                            'pointiller_reponse_id',
                            $pointillers->pluck('pointillerQuestions')->flatten()->pluck('reponses')->flatten()->pluck('id')
                        )->where('exam_attempt_id', $attempt->id)->sum('points_obtenus');
                        $total = $pointillers->sum('note_totale');
                        $resumeParType['pointiller'] = ['nom' => 'Pointiller', 'obtenus' => $obtenus, 'total' => $total];
                    }

                    // Relier
                    if ($reliers->isNotEmpty()) {
                        $obtenus = \App\Models\RelierReponse::whereIn(
                            'relier_paire_id',
                            $reliers->pluck('relierQuestions')->flatten()->pluck('paires')->flatten()->pluck('id')
                        )->where('exam_attempt_id', $attempt->id)->sum('points_obtenus');
                        $total = $reliers->sum('note_totale');
                        $resumeParType['relier'] = ['nom' => 'Relier par flèche', 'obtenus' => $obtenus, 'total' => $total];
                    }

                    // Code (correction manuelle)
                    if ($codes->isNotEmpty()) {
                        $reponsesCode = $codes->flatMap(fn($c) => $c->codeQuestions)->flatMap(fn($q) => $q->reponses);
                        $obtenus = $reponsesCode->sum('points_obtenus');
                        $total = $codes->sum('note_totale');
                        $estCorrige = $reponsesCode->isNotEmpty() && $reponsesCode->every(fn($r) => $r->points_obtenus !== null);
                        $resumeParType['code'] = ['nom' => 'Code', 'obtenus' => $obtenus, 'total' => $total, 'corrige' => $estCorrige];
                    }

                    // Text (correction manuelle)
                    if ($texts->isNotEmpty()) {
                        $reponsesText = $texts->flatMap(fn($t) => $t->textQuestions)->flatMap(fn($q) => $q->reponses);
                        $obtenus = $reponsesText->sum('note_obtenue');
                        $total = $texts->sum('note_totale');
                        $estCorrige = $reponsesText->isNotEmpty() && $reponsesText->every(fn($r) => $r->note_obtenue !== null);
                        $resumeParType['text'] = ['nom' => 'Compréhension de texte', 'obtenus' => $obtenus, 'total' => $total, 'corrige' => $estCorrige];
                    }

                    // Redaction (correction manuelle)
                    if ($redactions->isNotEmpty()) {
                        $reponsesRedaction = $redactions->flatMap(fn($r) => $r->reponses);
                        $obtenus = $reponsesRedaction->sum('note_obtenue');
                        $total = $redactions->sum('note_totale');
                        $estCorrige = $reponsesRedaction->isNotEmpty() && $reponsesRedaction->every(fn($r) => $r->note_obtenue !== null);
                        $resumeParType['redaction'] = ['nom' => 'Rédaction', 'obtenus' => $obtenus, 'total' => $total, 'corrige' => $estCorrige];
                    }

                    // Glisser-déposer
                    if ($glisserDeposers->isNotEmpty()) {
                        $obtenus = $glisserDeposers->flatMap(fn($g) => $g->questions)->flatMap(fn($q) => $q->items)->flatMap(fn($i) => $i->reponses)->sum('points_obtenus');
                        $total = $glisserDeposers->sum('note_totale');
                        $resumeParType['glisserdeposer'] = ['nom' => 'Glisser-déposer', 'obtenus' => $obtenus, 'total' => $total];
                    }

                    // Fichier (correction manuelle)
                    if ($fichiers->isNotEmpty()) {
                        $reponsesFichier = $fichiers->flatMap(fn($f) => $f->fichierQuestions)->flatMap(fn($q) => $q->reponses);
                        $obtenus = $reponsesFichier->sum('points_obtenus');
                        $total = $fichiers->sum('note_totale');
                        $estCorrige = $reponsesFichier->isNotEmpty() && $reponsesFichier->every(fn($r) => $r->points_obtenus !== null);
                        $resumeParType['fichier'] = ['nom' => 'Download & Upload', 'obtenus' => $obtenus, 'total' => $total, 'corrige' => $estCorrige];
                    }

                    // Image (correction manuelle)
                    if ($image->isNotEmpty()) {
                        $reponsesImage = $image
                            ->flatMap(fn($i) => $i->questions)
                            ->flatMap(fn($q) => $q->reponses);
                        $obtenus = $reponsesImage->sum('points_obtenus');
                        $total = $image->sum('note_totale');
                        $estCorrige = $reponsesImage->isNotEmpty()
                            && $reponsesImage->every(fn($r) => $r->points_obtenus !== null);
                        $resumeParType['image'] = [
                            'nom' => 'Image',
                            'obtenus' => $obtenus,
                            'total' => $total,
                            'corrige' => $estCorrige,
                        ];
                    }

                    // Mots croisés
                    if ($motsCroisesListe->isNotEmpty()) {
                        $obtenus = $motsCroisesListe->flatMap(fn($m) => $m->motsCroisesMots)->flatMap(fn($mot) => $mot->reponses)->sum('points_obtenus');
                        $total = $motsCroisesListe->sum('note_totale');
                        $resumeParType['motscroises'] = ['nom' => 'Mots croisés', 'obtenus' => $obtenus, 'total' => $total];
                    }

                    // ✅ Totaux globaux — UNE SEULE fois, après avoir rempli tout $resumeParType
                    // Le TOTAL (note max) compte toujours ; le OBTENUS ne compte que si corrigé
                    $toutEstCorrige = true;
                    foreach ($resumeParType as $key => $r) {
                        $estAutoCorrige = !in_array($key, $typesCorrectionManuelle);
                        $estCorrige = $estAutoCorrige || ($r['corrige'] ?? false);

                        // ✅ Le total (note maximale) compte toujours, corrigé ou non
                        $totalNoteGlobal += $r['total'];

                        // ✅ Les points obtenus ne comptent que si déjà corrigé
                        if ($estCorrige) {
                            $totalPointsGlobalObtenus += $r['obtenus'];
                        } else {
                            $toutEstCorrige = false;
                        }
                    }
                @endphp
             
                <div class="space-y-2 mb-4">
                    @foreach($resumeParType as $key => $r)
                        @php
                            $estAutoCorrige = !in_array($key, $typesCorrectionManuelle);
                            $estCorrige = $estAutoCorrige || ($r['corrige'] ?? false);
                        @endphp
                        <div class="flex justify-between items-center text-sm gap-3 p-4 bg-black/3 rounded-md">
                            <div class="border-e-2 border-black/10 flex-1 px-2">
                                <span class="text-xs {{ $estCorrige ? 'text-vert' : 'text-rouge' }}">
                                    {{ $estCorrige ? 'Corrigé' : 'En attente de correction' }}
                                </span>
                                <div class="text-base">{{ $r['nom'] }}</div>
                            </div>
                            <div class=" w-[35%] text-center {{ !$estCorrige ? 'font-sans text-rouge' : ($r['total'] > 0 && $r['obtenus'] >= $r['total'] ? 'text-vert' : '') }}">
                                @if($estCorrige)
                                    <span class="text-xl">{{ $r['obtenus'] }}</span> /
                                    <span class="text-black/70">{{ $r['total'] }}</span>
                                @else
                                    <span class="text-sm">En attente</span> /
                                    <span class="text-black/70">{{ $r['total'] }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
             
                <div class="border-t-2 border-black/20 pt-3">
                    <div class="flex justify-between items-center">
                         <div class="">
                             @if(!$toutEstCorrige)
                                 <span class="text-sm text-rouge">En attente de correction</span>
                             @else
                                 <span class="text-sm text-vert">Corrigé</span>
                             @endif
                             <div class="font-semibold text-lg">Total global</div>
                         </div>
                         <div class="font-bold text-lg {{ $toutEstCorrige ? 'text-vert' : 'text-rouge' }}">
                             <span class="text-2xl">{{ $totalPointsGlobalObtenus }}</span> /
                             <span>{{ $totalNoteGlobal }}</span>
                         </div>
                     </div>
                     @if($totalNoteGlobal > 0)
                         <div class="rounded-full h-1 overflow-hidden bg-black/10 mt-2">
                             <div class="h-full bg-vert" style="width: {{ min(100, ($totalPointsGlobalObtenus / $totalNoteGlobal) * 100) }}%"></div>
                         </div>
                         <p class="text-xs text-black/50 mt-1 text-center">
                             {{ round(($totalPointsGlobalObtenus / $totalNoteGlobal) * 100, 1) }}%
                         </p>
                     @endif
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    .flow-right{
        position:sticky;
        top:13px;
        max-height:calc(100vh - 40px);
        overflow:auto;
        scrollbar-width: none;
    }
    .menu-section.active{
        background: rgb(104, 167, 2);   /* rouge */
        color:white;
        border-color: rgb(104, 167, 2);
    }

</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // ===== Relier =====
    document.querySelectorAll('.relier-fleche').forEach(line => {
        const svg = line.closest('svg');
        const containerRect = svg.getBoundingClientRect();
        const gaucheEl = svg.parentElement.querySelector(
            `.relier-item-gauche[data-paire-id="${line.dataset.gaucheId}"]`
        );
        const droiteEl = svg.parentElement.querySelector(
            `.relier-item-droite[data-paire-id="${line.dataset.droiteId}"]`
        );
        if (!gaucheEl || !droiteEl) return;
        const gaucheRect = gaucheEl.getBoundingClientRect();
        const droiteRect = droiteEl.getBoundingClientRect();
        line.setAttribute('x1', gaucheRect.right - containerRect.left);
        line.setAttribute('y1', gaucheRect.top + gaucheRect.height / 2 - containerRect.top);
        line.setAttribute('x2', droiteRect.left - containerRect.left);
        line.setAttribute('y2', droiteRect.top + droiteRect.height / 2 - containerRect.top);

    });

    // ===== Menu actif =====
    const links = document.querySelectorAll(".menu-section");
    const sections = [...links].map(link => document.getElementById(link.dataset.target)).filter(Boolean);
    function updateActive() {
        let current = sections[0];
        sections.forEach(section => {
            const rect = section.getBoundingClientRect();
            if (rect.top <= window.innerHeight / 2 && rect.bottom >= window.innerHeight / 2) {
                current = section;
            }
        });
        links.forEach(link => {
            link.classList.toggle("active",current && current.id === link.dataset.target);
        });
    }
    updateActive();
    window.addEventListener("scroll", updateActive);
});
</script>
@endsection