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
    <div id="section-relier">
        <h2 class="p-1 mt-2 flex gap-2 items-center text-rouge">
            <i class="fa-solid fa-link"></i> Relier par flèche <i class="fa-solid fa-link"></i>
        </h2>
        @foreach($reliers as $relier)
            <div class="border border-black/10 rounded-md mb-3 p-4">
                @php
                    $pointsObtenusTotal = \App\Models\RelierReponse::whereIn('relier_paire_id', $relier->relierQuestions->pluck('paires')->flatten()->pluck('id'))
                        ->where('exam_attempt_id', $attempt->id)
                        ->sum('points_obtenus');
                @endphp
                <div class="flex gap-3 mb-2">
                    <div class="w-12 h-12 rounded-md bg-black/3 flex justify-center items-center font-semibold">
                        {{ $loop->iteration }}
                    </div>
                    <div class="flex-1">
                        <div class="flex gap-3 items-center">
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
                            <div class=" mt-2 bg-black/2 border border-black/3 rounded p-2">
                                <div class="flex justify-between mb-1">
                                    <h4 class="text-base"> {{$question->ordre}} - {{ $question->enonce }}</h4>
                                    <div class="text-sm text-gray-500 text-nowrap">
                                        {{ $reponsesEtudiant->sum('points_obtenus') }} / {{ $question->points }} pts
                                    </div>
                                </div>
                                <div class="relative flex justify-between gap-16" >
                                    <div class="flex-1 flex flex-col gap-2">
                                        @foreach($paires as $paire)
                                            <div class="relier-item-gauche p-1 px-2 border border-black/3 rounded bg-white/90  border-e " data-paire-id="{{ $paire->id }}">
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
                                            <div class="relier-item-droite p-1 px-2 rounded bg-white/90  border border-black/3" data-paire-id="{{ $paire->id }}">
                                                {{ $paire->element_droite }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Commentaire du prof — un seul, pour toute la section Relier de cet examen+attempt --}}
        @if($typeRelier)
            <form action="{{ route('prof.correction.storeCommentaire') }}" method="POST" class="">
                @csrf
                <input type="hidden" name="commentable_id" value="{{ $typeRelier->id }}">
                <input type="hidden" name="commentable_type" value="{{ \App\Models\TypeExercice::class }}">
                <input type="hidden" name="examen_id" value="{{ $examen->id }}">
                <input type="hidden" name="exam_attempt_id" value="{{ $attempt->id }}">

                <div class="border border-black/10 rounded-md p-2 bg-black/3">
                    
                    <textarea name="contenu" rows="2"
                        class="border border-black/10 w-full rounded p-2 bg-white"
                        placeholder="Commente ici cette exercice ..">{{ old('contenu', $commentsRelier->contenu ?? '') }}</textarea>
                    <div class="flex justify-end mt-1">
                        <button type="submit" class="p-1 px-2 rounded text-white bg-rouge">
                            {{ $commentsRelier ? 'Modifier le commentaire' : 'Commenter' }}
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.relier-fleche').forEach(function (line) {
            const svg = line.closest('svg');
            const containerRect = svg.getBoundingClientRect();

            const gaucheEl = svg.parentElement.querySelector(`.relier-item-gauche[data-paire-id="${line.dataset.gaucheId}"]`);
            const droiteEl = svg.parentElement.querySelector(`.relier-item-droite[data-paire-id="${line.dataset.droiteId}"]`);

            if (!gaucheEl || !droiteEl) return;

            const gaucheRect = gaucheEl.getBoundingClientRect();
            const droiteRect = droiteEl.getBoundingClientRect();

            const x1 = gaucheRect.right - containerRect.left;
            const y1 = gaucheRect.top + gaucheRect.height / 2 - containerRect.top;
            const x2 = droiteRect.left - containerRect.left;
            const y2 = droiteRect.top + droiteRect.height / 2 - containerRect.top;

            line.setAttribute('x1', x1);
            line.setAttribute('y1', y1);
            line.setAttribute('x2', x2);
            line.setAttribute('y2', y2);
        });
    });
</script>
@endsection