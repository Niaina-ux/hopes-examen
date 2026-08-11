<div class="">
    @if(session('success'))
        <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md mb-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="document.getElementById('success-alert').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div id="error-alert" class="bg-red-100/50 text-rouge px-4 py-2 rounded-md mb-4 flex justify-between items-center">
            <span>{{ session('error') }}</span>
            <button type="button" onclick="document.getElementById('error-alert').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <div class="flex items-center my-2">
        <a href="{{ route('prof.examen.show', $slug) }}" class="hover:underline">
            Examen/
        </a>
        <span class="font-semibold">Details</span>
    </div>
    <div class="flex justify-between items-end">
        <div class="w-[70%]">
            <h2 class="text-2xl font-semibold text-vert">{{ $examen->titre }}</h2>
            <p>{{ $examen->description }}</p>
        </div>

        @if($examen->status !== 'archive')
            <form action="{{ route('prof.examen.terminerCreation', [$slug, $examen->id]) }}" method="POST">
                @csrf
                <button type="submit" class="p-2 px-5 rounded-md bg-black/10 border-2 border-black/20">
                    Terminer la création
                </button>
            </form>
        @else
            <span class="p-2 px-5 rounded-md bg-vert/10 text-vert font-semibold">
                <i class="fa-solid fa-circle-check"></i> Finalisé
            </span>
        @endif
    </div>
</div>

@if($examen->typesExercice->isNotEmpty())
    <div class="flex gap-1 border-b border-black/10 mt-2 py-2">
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
@else
    <div class="p-10 rounded-md bg-black/3 mt-4">
        <i class="fa-solid fa-box-open text-3xl"></i>
        <p>Aucun type d'exercice n'a encore été ajouté à cet examen.</p>
    </div>
@endif