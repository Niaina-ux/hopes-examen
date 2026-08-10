@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="py-3 me-2">
    @include('layouts.prof-layouts.proflayoutsexamcorrige')
    <div id="section-motscroises">
        <h2 class="p-1 mt-2 flex gap-2 items-center text-rouge">
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
                <div class="flex gap-3 mb-3">
                    <div class="w-12 h-12 rounded-md bg-black/3 flex justify-center items-center font-semibold">
                        {{ $loop->iteration }}
                    </div>
                    <div class="flex-1">
                        <div class="flex gap-3 items-center">
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
                        <div class="gap-8 flex justify-center  flex-wrap bg-black/2 p-2 mt-2 rounded-md">
                            <div class="flex justify-center items-center rounded-md  p-2">
                                <div class="inline-block border border-black/3 bg-white/30 p-2 rounded">
                                    @for($y = 0; $y < $hauteur; $y++)
                                        <div class="flex">
                                            @for($x = 0; $x < $largeur; $x++)
                                                @php $case = $grille[$y][$x]; @endphp
                                                @if(!$case['active'])
                                                    {{-- ✅ Case tsy misy litera mihitsy: tsy aseho --}}
                                                    <div class="w-8 h-8"></div>
                                                @else
                                                    <div class="relative w-8 h-8 border border-black/10 rounded flex items-center justify-center font-bold text-sm
                                                        {{ $case['est_hint']
                                                            ? 'bg-black/5 text-black/60'
                                                            : ($case['correcte'] === true
                                                                ? 'bg-green-50 text-black/50'
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
                                <div class="p-2 px-5 my-2 rounded-md bg-white/60 border border-black/3">
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
                                <div class="p-2 px-5 my-2 rounded-md bg-white/60 border border-black/3">
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
                </div>
    
            </div>
        @endforeach

        Commentaire
        @if($typeMotsCroises)
            <form action="{{ route('prof.correction.storeCommentaire') }}" method="POST" class="">
                @csrf
                <input type="hidden" name="commentable_id" value="{{ $typeMotsCroises->id }}">
                <input type="hidden" name="commentable_type" value="{{ \App\Models\TypeExercice::class }}">
                <input type="hidden" name="examen_id" value="{{ $examen->id }}">
                <input type="hidden" name="exam_attempt_id" value="{{ $attempt->id }}">

                <div class="border border-black/10 rounded-md p-2 bg-black/3">
                    
                    <textarea name="contenu" rows="2"
                        class="border border-black/10 w-full rounded p-2 bg-white"
                        placeholder="Commente ici cette exercice ..">{{ old('contenu', $commentsMotsCroises->contenu ?? '') }}</textarea>
                    <div class="flex justify-end mt-1">
                        <button type="submit" class="p-1 px-2 rounded text-white bg-rouge">
                            {{ $commentsMotsCroises ? 'Modifier' : 'Commenter' }}
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection