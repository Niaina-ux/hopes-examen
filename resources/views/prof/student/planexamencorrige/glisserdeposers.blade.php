@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="py-3 me-2">
    @include('layouts.prof-layouts.proflayoutsexamcorrige')
    <div id="section-glisserdeposer">
        <h2 class="p-1 mt-2 flex gap-2 items-center text-rouge">
            <i class="fa-solid fa-arrows-up-down-left-right"></i> Glisser-déposer <i class="fa-solid fa-arrows-up-down-left-right"></i>
        </h2>
        @foreach($glisserDeposers as $glisserDeposer)
            <div class="border border-black/10 p-4 rounded-md mb-2">
                <div class="flex gap-3 mb-2">
                    <div class="w-12 h-12 rounded-md bg-black/3 flex justify-center items-center font-semibold">
                        {{ $loop->iteration }}
                    </div>
                    <div class="flex-1">
                        <div class="flex gap-3 items-center">
                            <h3 class="text-lg font-semibold flex-1">{{ $glisserDeposer->titre }}</h3>
                            <div class="text-sm flex gap-3">
                                <span class="border border-black/20 rounded-full px-2 text-rouge">
                                    01 Pts obtenus
                                </span>
                                <span class="border border-black/20 rounded-full px-2 text-vert">
                                    {{ $glisserDeposer->note_totale }} Pts total
                                </span> 
                            </div>
                        </div>
                        @foreach($glisserDeposer->questions as $question)
                            <div class="mb-2 border-b border-black/5 mt-2  p-2 rounded-md bg-black/3 text-base">
                                @if($question->enonce)
                                <div class="flex justify-between gap-3 mb-2">
                                    <p class="">{{$question->ordre}} - {{ $question->enonce }}</p>
                                    @php
                                        $totalCorrect = $question->items->filter(fn($i) => ($i->reponses->first()->est_correcte ?? false))->count();
                                        $totalItems = $question->items->count();
                                        $pointsCalcules = $totalItems > 0 ? round(($totalCorrect / $totalItems) * $question->points, 2) : 0;
                                    @endphp
                                    <span>{{ $pointsCalcules }} / {{ $question->points }}</span>
                                </div>  
                                @endif
                                <div class="flex gap-7">
                                    <div class="relative w-[30%] inline-block border border-black/10 rounded ">
                                        <img src="{{ asset('images/glisserdeposer/' . $question->image) }}" class="w-full rounded block">
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
                                    <div class="flex-1 p-2 bg-white/70 rounded border border-black/3 ">
                                        <span class=" mb-2 inline-block">Itmes</span>
                                        <div class=" flex gap-2 flex-wrap items-start">
                                            @foreach ($question->items as $item)
                                            <span class="border-2 border-black/10 rounded bg-white/70 p-1 px-5"> {{$item->texte}} </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
        @if($typeGlissesDeposer)
            <form action="{{ route('prof.correction.storeCommentaire') }}" method="POST" class="">
                @csrf
                <input type="hidden" name="commentable_id" value="{{ $typeGlissesDeposer->id }}">
                <input type="hidden" name="commentable_type" value="{{ \App\Models\TypeExercice::class }}">
                <input type="hidden" name="examen_id" value="{{ $examen->id }}">
                <input type="hidden" name="exam_attempt_id" value="{{ $attempt->id }}">
    
                <div class="border border-black/10 rounded-md p-2 bg-black/3">
                    
                    <textarea name="contenu" rows="2"
                        class="border border-black/10 w-full rounded p-2 bg-white"
                        placeholder="Commente ici cette exercice ..">{{ old('contenu', $commentsGlissesDeposer->contenu ?? '') }}</textarea>
                    <div class="flex justify-end mt-1">
                        <button type="submit" class="p-1 px-2 rounded text-white bg-rouge">
                            {{ $commentsGlissesDeposer ? 'Modifier' : 'Commenter' }}
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>        
</div>
@endsection