@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="py-3 me-2">
    <div class="">
        <div class="flex justify-between items-end">    
            <div class="w-[70%]">
                <h2 class="text-2xl flex gap-2 items-center font-semibold text-vert mb-1">
                    {{ $examen->titre }}
                </h2>
                <p>{{ $examen->description }}</p>
                <div class="flex gap-3 text-sm">
                    <div class="flex   ">
                        Il y a <span class="inline-block  px-2 text-vert"> {{$examen->typesExercice->count()}} </span> types d'exercice
                    </div>
                    <div class="flex ">
                        Durée:  <span class=" px-3 text-rouge"> {{$examen->duree_minutes}} Minutes</span>
                    </div>
                </div>
            </div>
            <div class="flex justify-end mt-4 text-white">
                <a href="{{ route('prof.examen.assignTypes', [$slug, $examen->id]) }}" class="inline-block p-1 px-5 rounded-full bg-rouge">
                    @if($examen->typesExercice->isEmpty())
                        + Ajouter type d'exercice
                    @else
                        Modifier les types
                    @endif
                </a>
            </div>
        </div>
    </div>
    @if(session('success'))
        <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md my-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="document.getElementById('success-alert').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif
    
    @if($examen->typesExercice->isNotEmpty())
        <div class="gap-3 py-2 border border-black/3 bg-black/2 rounded-md p-2 mt-4 
            dark:bg-white/2 dark:border-white/3">
            @foreach($examen->typesExercice as $type)
                @if(\Illuminate\Support\Facades\Route::has('prof.examen.' . $type->slug))
                    <a href="{{ route('prof.examen.' . $type->slug, [$slug, $examen->id]) }}"
                        class="w-full  p-2 border hover:bg-black/3 rounded border-black/3 bg-white/70 flex gap-3 items-center justify-between 
                        dark:bg-white/2 dark:border-white/3">
                        <div class="font-semibold text-rouge w-8 h-8 rounded bg-black/5 flex justify-center items-center">
                            <i class="{{ $type->icone ?? 'fa-solid fa-chart-simple' }} text-vert"></i>
                        </div>
                        <div class="flex-1">
                            <p>{{ $type->nom }}</p>
                            <div class="">
                                <span class="text-sm">Ordre n°: {{ $type->pivot->ordre }}</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-up-right-from-square text-vert"></i>
                    </a>
                @else
                    <span class="w-full py-3 px-2 border border-black/3 rounded bg-white/70 text-black/30 flex gap-3 items-center" title="Bientôt disponible">
                        <div class="font-semibold text-rouge w-8 h-8 rounded-md bg-black/5 flex justify-center items-center">
                            {{ $type->pivot->ordre }}
                        </div>
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
</div>
@endsection