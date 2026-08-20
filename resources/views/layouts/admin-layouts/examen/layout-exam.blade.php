<div class="">
    {{-- @if(session('success'))
        <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md mb-2 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="document.getElementById('success-alert').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif --}}

    @if(session('error'))
        <div id="error-alert" class="bg-red-100/50 text-rouge px-4 py-2 rounded-md mb-2 flex justify-between items-center">
            <span>{{ session('error') }}</span>
            <button type="button" onclick="document.getElementById('error-alert').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <div class="flex items-center mb-2">
        <a href="{{ route('prof.examen.show', $slug) }}" class="hover:underline">
            Examen/
        </a>
        <span class="font-semibold">Details</span>
    </div>
    <div class="flex justify-between items-end">
        <div class="w-[70%]">
            <h2 class="text-2xl font-semibold text-vert">{{ $examen->titre }}</h2>
            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Atque consequatur, aliquam autem laboriosam optio sequi?</p>
        </div>

       @if($examen->status === 'brouillon')
            <button type="button" onclick="openModal('terminer-creation-modal')" class="rounded-md px-3 p-2 bg-vert text-white">
                Terminer
            </button>

            <x-confirm-modal
                id="terminer-creation-modal"
                title="Terminer la création"
                action="{{ route('prof.examen.terminerCreation', [$slug, $examen->id]) }}"
                confirmText="Oui, terminer"
                cancelText="Annuler">
                Confirmez-vous la fin de la création de cet examen ? Une fois finalisé, il ne sera plus modifiable.
            </x-confirm-modal>
        @else
        <div class="">
            <div class="p-2 flex items-center gap-3 px-5 rounded-md bg-vert/10 text-vert ">
                @if($examen->status === 'archive')
                    <button type="button" onclick="openModal('remettre-brouillon-modal')" class="rounded-full w-8 h-8 flex justify-center items-center bg-black/2 border border-black/3">
                        <i class="fa-solid fa-repeat"></i>
                    </button>
    
                    <x-confirm-modal
                        id="remettre-brouillon-modal"
                        title="Modifier l'examen"
                        action="{{ route('prof.examen.remettreEnBrouillon', [$slug, $examen->id]) }}"
                        confirmText="Oui, modifier"
                        cancelText="Annuler">
                        Cet examen est finalisé. Le remettre en modification le rendra à nouveau accessible aux étudiants comme "en préparation" — voulez-vous continuer ?
                    </x-confirm-modal>
                @endif
                <i class="fa-solid fa-circle-check"></i> Finalisé
            </div>

        </div>
        @endif
    </div>
</div>

@if($examen->typesExercice->isNotEmpty())
    <div class="border-b flex justify-between  border-black/10 mt-2 py-2">
        <div class="flex gap-1 ">
            @foreach($examen->typesExercice as $type)
                @if(\Illuminate\Support\Facades\Route::has('prof.examen.' . $type->slug))
                    <a href="{{ route('prof.examen.' . $type->slug, [$slug, $examen->id]) }}"
                        class="inline-block p-1 px-3 rounded-full border {{ request()->routeIs('prof.examen.' . $type->slug .'*') ? 'bg-vert text-white border-vert border-transparent' : 'bg-black/5 border-black/5' }}">
                        {{ $type->nom }}
                    </a>
                @else
                    <span class="inline-block p-1 px-5 border border-black/10 bg-black/5 text-black/40 rounded-md" title="Bientôt disponible">
                        {{ $type->nom }}
                    </span>
                @endif
            @endforeach
        </div>
        @php
            $totalPointsExamen = collect([
                \App\Models\Pointiller::class,
                \App\Models\Relier::class,
                \App\Models\Code::class,
                \App\Models\Text::class,
                \App\Models\Redaction::class,
                \App\Models\Fichier::class,
                \App\Models\ImageExercice::class,
                \App\Models\GlisserDeposer::class,
                \App\Models\MotsCroises::class,
            ])->sum(function ($modelClass) use ($examen) {
                return $modelClass::where('examen_id', $examen->id)->sum('note_totale');
            });

            // ✅ QCM géré séparément — points des questions sélectionnées pour CET examen, via la banque
            $totalPointsExamen += $examen->qcmQuestionsSelectionnees()->sum('points');
        @endphp
        <div class="p-1 px2 rounded-md border border-black/20 ">
            Total: <span class="text-rouge">{{ $totalPointsExamen }}</span> Pts
        </div>
    </div>
@else
    <div class="p-10 rounded-md bg-black/3 mt-4">
        <i class="fa-solid fa-box-open text-3xl"></i>
        <p>Aucun type d'exercice n'a encore été ajouté à cet examen.</p>
    </div>
@endif