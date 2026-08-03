
<div class="flex items-center">
    <a href="{{ route('prof.examen.examenwherestudent', [$slug, $examen->id, $student->id]) }}" class="hover:underline">
        Correction/
    </a>
    <span class="font-semibold">{{  $student->name }}</span>
</div>
<div class="flex gap-3 justify-between items-center">
    <div class="w-[70%] my-2">
        <h2 class="text-2xl font-semibold text-vert">{{ $examen->titre }}</h2>
        <p>{{ $examen->description }}</p>
    </div>
    <button class="p-2 px-3 rounded-md bg-rouge text-white ">
        Terminer la correction
    </button>
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

