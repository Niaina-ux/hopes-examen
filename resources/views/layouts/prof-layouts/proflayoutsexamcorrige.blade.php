
<div class="flex items-center">
    <a href="{{ route('prof.examen.examenwherestudent', [$slug, $examen->id, $student->id]) }}" class="hover:underline">
        Retour/
    </a>
    <span class="font-semibold">{{  $student->name }}</span>
</div>
@if(session('error'))
<div id="error-alert" class="bg-red-100/50 text-rouge px-4 py-2 rounded-md mt-2 flex justify-between items-center">
    <span>{{ session('error') }}</span>
    <button type="button" onclick="document.getElementById('error-alert').remove()">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>
@endif
<div class="flex gap-3 justify-between items-center">
    <div class="w-[70%] my-2">
        <h2 class="text-2xl font-semibold text-vert">{{ $examen->titre }}</h2>
        <p>{{ $examen->description }}</p>
    </div>

    @if($attempt->status === 'corrige')
        <span class="p-2 px-3 rounded-md bg-vert/10 text-vert font-semibold">
            <i class="fa-solid fa-circle-check"></i> Déjà corrigé
        </span>
    @else
        <button type="button" onclick="openModal('terminer-correction-modal')" class="p-2 px-3 rounded-md bg-rouge text-white">
            Terminer la correction
        </button>

        <x-confirm-modal
            id="terminer-correction-modal"
            title="Terminer la correction"
            action="{{ route('prof.correction.terminer', [$slug, $examen->id, $student->id]) }}"
            confirmText="Oui, terminer"
            cancelText="Annuler">
            Confirmez-vous la fin de la correction pour {{ $student->name }} ? Cette action est définitive.
        </x-confirm-modal>
    @endif
</div>
@if($examen->typesExercice->isNotEmpty())
    <div class="flex gap-3 border-b-2 border-black/10 py-2">
        @foreach($examen->typesExercice as $type)
            @if(\Illuminate\Support\Facades\Route::has('prof.examen.showtache.' . $type->slug))
                <a href="{{ route('prof.examen.showtache.' . $type->slug, [$slug, $examen->id, $student->id]) }}"
                    class="inline-block p-1 px-2 rounded-sm border-2 border-black/10 {{ request()->routeIs('prof.examen.showtache.' . $type->slug . '*') ? 'bg-vert text-white ' : 'bg-black/5 border-black/5' }}">
                    {{ $type->nom }}
                </a>
            @else
                <span class="inline-block p-1 px-2 border-2 border-black/10 bg-black/5 text-black/40 rounded" title="Bientôt disponible">
                    {{ $type->nom }}
                </span>
            @endif
        @endforeach
    </div>
@else
    <div class="p-10 rounded-md bg-black/3 mt-4">
        <i class="fa-solid fa-box-open text-3xl"></i>
        <p>Aucun type d'exercice n'a encore été ajouté à cet examen.</p>
    </div>
@endif


<script>
function openModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal(id) {
    const modal = document.getElementById(id);
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
