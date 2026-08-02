@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="py-3 me-2">
    <div class="">
        <a href="" 
            class="flex gap-3 items-center font-semibold">
            <span class="">Examens</span>
        </a>
        <div class="flex justify-between items-end">    
            <div class="w-[70%]">
                <h2 class="text-2xl flex gap-2 items-center font-semibold text-vert my-1">
                    {{ $examen->titre }}
                </h2>
                <p>{{ $examen->description }}</p>
                <div class="flex gap-3 text-sm">
                    <div class="flex   ">
                        Il y a <span class="inline-block  px-2 text-vert">3</span> types d'exercice
                    </div>
                    <div class="flex ">
                        Durée:  <span class=" px-3 text-rouge"> {{$examen->duree_minutes}} Minutes</span>
                    </div>
                </div>
                <div class="flex text-sm mt-1">
                    Status
                    <span @class([
                        'rounded-4xl border border-black/10 px-3',
                        'text-vert' => $examen->status == 'publie',
                        'text-black/50' => $examen->status == 'brouillon',
                        'text-rouge' => $examen->status == 'archive',
                    ])>
                        {{ $examen->status }}
                    </span>
                </div> 
            </div>
            <div class="flex justify-end mt-4 text-white">
                <a href="{{ route('prof.examen.assignTypes', [$slug, $examen->id]) }}" class="inline-block p-1 px-5 rounded-md bg-rouge">
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
        <div class="gap-3 py-2 border border-black/3 bg-black/3 rounded-md p-2 mt-4 ">
            @foreach($examen->typesExercice as $type)
                @if(\Illuminate\Support\Facades\Route::has('prof.examen.' . $type->slug))
                    <a href="{{ route('prof.examen.' . $type->slug, [$slug, $examen->id]) }}"
                        class="w-full  py-3 px-2 border-b hover:bg-black/3 border-black/10 flex gap-3 items-center justify-between {{ $loop->iteration == 2 ? 'bg-white/60' : '' }}">
                        <div class="font-semibold text-rouge w-8 h-8 rounded-md bg-black/5 flex justify-center items-center">
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
                    <span class="w-full py-3 px-2 border-b border-black/10 text-black/30 flex gap-3 items-center" title="Bientôt disponible">
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