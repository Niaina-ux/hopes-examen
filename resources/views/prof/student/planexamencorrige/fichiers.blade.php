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

    <div id="section-fichier">
        <h2 class="p-1 mt-2 flex gap-2 items-center text-rouge">
            <i class="fa-solid fa-file-arrow-up"></i> Devoir à rendre <i class="fa-solid fa-file-arrow-up"></i>
        </h2>
        @foreach($fichiers as $fichier)
            @php
                $reponsesFichier = $fichier->fichierQuestions->flatMap(fn($q) => $q->reponses);
                $obtenusFichier = $reponsesFichier->sum('points_obtenus');
                $estCorrigeFichier = $reponsesFichier->isNotEmpty() && $reponsesFichier->every(fn($r) => $r->points_obtenus !== null);
            @endphp
            <div class="border border-black/10 p-4 rounded-md mb-3 text-base">
                <div class="flex gap-3 mb-2">
                    <div class="w-12 h-12 rounded-md bg-black/3 flex justify-center items-center font-semibold">
                        {{ $loop->iteration }}
                    </div>
                    <div class="flex-1">
                        <div class="flex gap-3 items-center">
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

                        <form class="fichier-annot-form" action="{{ route('prof.correction.fichier.annoter', $fichier->id) }}" method="POST">
                            @csrf

                            @if($errors->any())
                                <div class="mb-3 p-3 mt-2 rounded-md bg-red-50 border border-rouge text-rouge text-sm">
                                    @foreach($errors->all() as $error)
                                        <p>{{ $error }}</p>
                                    @endforeach
                                </div>
                            @endif

                            @foreach($fichier->fichierQuestions as $question)
                                @php $reponse = $question->reponses->first(); @endphp
                                <div class="p-2 rounded-md bg-black/2 border border-black/3 mt-2">
                                    <div class="flex gap-3 justify-between items-start mb-2">
                                        <p class="flex-1">{{ $question->ordre }} - {{ $question->instruction }}</p>
                                        <span class="text-sm text-nowrap">
                                            {{ $reponse?->points_obtenus ?? 'En attente' }} / {{ $question->points }} Pts
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="h-50 border bg-white/30 border-black/10 rounded-xl p-6 flex flex-col items-center justify-center text-center">
                                            @if($question->fichier_prof)
                                                <div class="w-12 h-12 rounded-full bg-vert/10 text-vert flex items-center justify-center">
                                                    <i class="fa-solid fa-cloud-arrow-down text-xl"></i>
                                                </div>
                                                <p class="text-sm font-medium mb-1">Fichier fourni par le professeur</p>
                                                <p class="text-xs text-black/40 mb-3 truncate max-w-full">{{ $question->fichier_prof }}</p>
                                                <a href="{{ asset('fichiers/prof/' . $question->fichier_prof) }}" target="_blank"
                                                    class="inline-flex items-center gap-2 text-sm bg-black/50 text-white px-4 py-2 rounded-md hover:opacity-90 transition">
                                                    <i class="fa-solid fa-download"></i> Télécharger
                                                </a>
                                            @else
                                                <div class="w-12 h-12 rounded-full bg-black/5 text-black/30 flex items-center justify-center mb-3">
                                                    <i class="fa-solid fa-file-circle-xmark text-xl"></i>
                                                </div>
                                                <p class="text-sm text-black/40">Aucun fichier fourni</p>
                                            @endif
                                        </div>

                                        <div class="h-50 border-2 border-dashed border-black/10 rounded-xl p-6 flex flex-col items-center justify-center text-center bg-black/2">
                                            @if($reponse?->fichier_etudiant)
                                                <div class="w-12 h-12 rounded-full bg-vert/10 text-vert flex items-center justify-center">
                                                    <i class="fa-solid fa-cloud-arrow-down text-xl"></i>
                                                </div>
                                                <p class="text-sm font-medium mb-1">Fichier fourni par l'étudiant</p>
                                                <p class="text-xs text-black/40 mb-3 truncate max-w-full">{{ $reponse->fichier_etudiant }}</p>
                                                <a href="{{ asset('fichiers/etudiants/' . $reponse->fichier_etudiant) }}" target="_blank"
                                                    class="inline-flex items-center gap-2 text-sm bg-black/50 text-white px-4 py-2 rounded-md hover:opacity-90 transition">
                                                    <i class="fa-solid fa-download"></i> Télécharger
                                                </a>
                                            @else
                                                <div class="w-12 h-12 rounded-full bg-black/5 text-black/30 flex items-center justify-center mb-3">
                                                    <i class="fa-solid fa-file-circle-xmark text-xl"></i>
                                                </div>
                                                <p class="text-sm text-black/40">Aucun fichier fourni</p>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- ✅ Remarque optionnelle + note requise --}}
                                    <div class="flex justify-end mt-2 gap-3 items-start">
                                        <textarea name="reponses[{{ $reponse->id ?? 0 }}][commentaire_prof]" rows="2"
                                            placeholder="Remarque (facultatif)"
                                            class="border border-black/20 bg-white/90 rounded p-2 flex-1 text-sm">{{ old('reponses.' . ($reponse->id ?? 0) . '.commentaire_prof', $reponse?->commentaire_prof) }}</textarea>
                                        <input type="text" name="reponses[{{ $reponse->id ?? 0 }}][points_obtenus]"
                                            min="0" max="{{ $question->points }}" step="0.1"
                                            value="{{ old('reponses.' . ($reponse->id ?? 0) . '.points_obtenus', $reponse?->points_obtenus) }}"
                                            placeholder="Note"
                                            class="border border-black/20 bg-black/3 rounded w-[2cm] p-1 text-center">
                                        <span>Pts</span>
                                    </div>
                                </div>
                            @endforeach

                            <div class="flex justify-end mt-2">
                                <button type="submit" class="rounded-md p-2 px-3 bg-vert text-white">Valider</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        @if($typeFichier)
            <form action="{{ route('prof.correction.storeCommentaire') }}" method="POST">
                @csrf
                <input type="hidden" name="commentable_id" value="{{ $typeFichier->id }}">
                <input type="hidden" name="commentable_type" value="{{ \App\Models\TypeExercice::class }}">
                <input type="hidden" name="examen_id" value="{{ $examen->id }}">
                <input type="hidden" name="exam_attempt_id" value="{{ $attempt->id }}">

                <div class="border border-black/10 rounded-md p-2 bg-black/3">
                    <textarea name="contenu" rows="2"
                        class="border border-black/10 w-full rounded p-2 bg-white"
                        placeholder="Commente ici cette exercice ..">{{ old('contenu', $commentsFichier->contenu ?? '') }}</textarea>
                    <div class="flex justify-end mt-1">
                        <button type="submit" class="p-1 px-2 rounded text-white bg-rouge">
                            {{ $commentsFichier ? 'Modifier' : 'Commenter' }}
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection