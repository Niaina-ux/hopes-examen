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

    <div id="section-image">
        <h2 class="p-1 mt-2 flex gap-2 items-center text-rouge">
            <i class="fa-solid fa-image"></i> Devoir image <i class="fa-solid fa-image"></i>
        </h2>
        @foreach($image as $imageExercice)
            @php
                $reponsesImage = $imageExercice->questions->flatMap(fn($q) => $q->reponses);
                $obtenusImage = $reponsesImage->sum('points_obtenus');
                $estCorrigeImage = $reponsesImage->isNotEmpty()
                    && $reponsesImage->every(fn($r) => $r->points_obtenus !== null);
            @endphp
            <div class="border border-black/10 p-4 rounded-md mb-3 text-base">
                <div class="flex gap-3 mb-2">
                    <div class="w-12 h-12 rounded-md bg-black/3 flex justify-center items-center font-semibold">
                        {{ $loop->iteration }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <h3 class="text-lg font-semibold flex-1">{{ $imageExercice->titre }}</h3>
                            <div class="text-sm flex gap-3">
                                <span class="border border-black/20 rounded-full px-2 {{ $estCorrigeImage ? 'text-rouge' : 'text-black/40' }}">
                                    {{ $estCorrigeImage ? $obtenusImage . ' Pts obtenus' : 'En attente' }}
                                </span>
                                <span class="border border-black/20 rounded-full px-2 text-vert">
                                    {{ $imageExercice->note_totale }} Pts total
                                </span>
                            </div>
                        </div>

                        <form class="image-annot-form" action="{{ route('prof.correction.image.annoter', $imageExercice->id) }}" method="POST">
                            @csrf

                            @if($errors->any())
                                <div class="mb-3 p-3 mt1 rounded-md bg-red-50 border border-rouge text-rouge text-sm">
                                    @foreach($errors->all() as $error)
                                        <p>{{ $error }}</p>
                                    @endforeach
                                </div>
                            @endif

                            @foreach($imageExercice->questions as $question)
                                @php $reponse = $question->reponses->first(); @endphp
                                <div class="p-2 rounded-md bg-black/3 my-2">
                                    <div class="flex justify-between items-start mb-2">
                                        <p class="flex-1">{{ $question->ordre }} - {{ $question->instruction }}</p>
                                        <span class="text-sm text-nowrap">
                                            {{ $reponse?->points_obtenus ?? 'En attente' }} / {{ $question->points }} Pts
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-white rounded-md p-2 border border-black/10">
                                            <p class="text-sm font-medium mb-2">Sujet</p>
                                            @if($question->image)
                                                <img src="{{ asset('images/image_exercice/' . $question->image) }}"
                                                    class="w-full h-56 object-contain rounded border border-black/20">
                                            @else
                                                <div class="text-sm italic text-black/40">Aucune image.</div>
                                            @endif
                                        </div>

                                        <div class="bg-white rounded-md p-2 border border-black/10">
                                            <p class="text-sm font-medium mb-2">Votre réponse</p>
                                            @if($reponse?->image_soumise)
                                                <img src="{{ asset('images/image_reponses/' . $reponse->image_soumise) }}"
                                                    class="w-full h-56 object-contain rounded border border-black/20">
                                            @else
                                                <div class="text-sm italic text-black/40">Aucune image envoyée.</div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- ✅ Remarque optionnelle + note requise --}}
                                    <div class="flex justify-end mt-2 gap-2 items-start">
                                        <textarea name="reponses[{{ $reponse->id ?? 0 }}][commentaire_prof]" rows="2"
                                            placeholder="Remarque (facultatif)"
                                            class="border bg-white/90 border-black/20 rounded p-2 flex-1 text-sm">{{ old('reponses.' . ($reponse->id ?? 0) . '.commentaire_prof', $reponse?->commentaire_prof) }}</textarea>
                                        <input type="text" name="reponses[{{ $reponse->id ?? 0 }}][points_obtenus]"
                                            min="0" max="{{ $question->points }}" step="0.1"
                                            value="{{ old('reponses.' . ($reponse->id ?? 0) . '.points_obtenus', $reponse?->points_obtenus) }}"
                                            placeholder="Note"
                                            class="border p-1 border-black/20 bg-black/3 rounded w-[2cm] text-center">
                                        <span>Pts</span>
                                    </div>
                                </div>
                            @endforeach

                            <div class="flex justify-end mt-2">
                                <button type="submit" class="rounded-md p-1 px-3 bg-vert text-white">Valider</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        @if($typeImage)
            <form action="{{ route('prof.correction.storeCommentaire') }}" method="POST">
                @csrf
                <input type="hidden" name="commentable_id" value="{{ $typeImage->id }}">
                <input type="hidden" name="commentable_type" value="{{ \App\Models\TypeExercice::class }}">
                <input type="hidden" name="examen_id" value="{{ $examen->id }}">
                <input type="hidden" name="exam_attempt_id" value="{{ $attempt->id }}">

                <div class="border border-black/10 rounded-md p-2 bg-black/3">
                    <textarea name="contenu" rows="2"
                        class="border border-black/10 w-full rounded p-2 bg-white"
                        placeholder="Commente ici cette exercice ..">{{ old('contenu', $commentsImage->contenu ?? '') }}</textarea>
                    <div class="flex justify-end mt-1">
                        <button type="submit" class="p-1 px-2 rounded text-white bg-rouge">
                            {{ $commentsImage ? 'Modifier' : 'Commenter' }}
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection