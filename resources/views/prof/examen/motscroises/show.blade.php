@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class=" py-3 me-2">
    @include('layouts.admin-layouts.examen.layout-exam')
    <div class="bg-white mt-2 rounded-md">
        @if(session('success'))
            <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md mt-4 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('success-alert').remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        @forelse($motscroises as $motCroise)
            @php $apercu = $apercus[$motCroise->id]; @endphp
            <div class="border border-black/10 rounded-md p-2 mb-4">
                    <div class="flex gap-5 justify-between">
                        <div class="w-10 h-10 rounded-md bg-black/5 flex justify-center items-center font-semibold">
                            {{$motCroise->ordre}}
                        </div>
                        <div class="flex-1 ">
                            <div class="flex justify-between gap-3 pb-2">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-lg">{{ $motCroise->titre }}</h3>
                                    <p class="text-sm text-black/50">{{ $motCroise->description }}</p>
                                    <div class="flex gap-3 text-sm">
                                        <span class="rounded-full border border-black/10 px-3 ">
                                            {{ $motCroise->mots_croises_mots_count }} mot(s)
                                        </span>
                                        @if($motCroise->duree_minutes)
                                            <span class="rounded-full border border-black/10 px-3 ">
                                                {{ $motCroise->duree_minutes }} min
                                            </span>
                                        @endif
                                        @if($motCroise->note_totale)
                                            <span class="rounded-full border border-black/10 text-rouge px-3 ">
                                                {{ $motCroise->note_totale }} pts
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if ($examen->status === 'brouillon') 
                                <div class="flex gap-4">
                                    <a href="{{ route('prof.examen.motscroises.edit', [$slug, $examen->id, $motCroise->id]) }}" class="text-vert" title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('prof.examen.motscroises.destroy', [$slug, $examen->id, $motCroise->id]) }}" method="POST" onsubmit="return confirm('Supprimer cet exercice ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rouge" title="Supprimer">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </div>
                            <div class="">
                                @if($apercu['largeur'] > 0)
                                    <div class="flex  gap-8 flex-wrap p-3 bg-black/3 rounded-md ">
                                        
                                        <div class="inline-block border border-black/10 p-1 rounded-md ">
                                            @for($y = 0; $y < $apercu['hauteur']; $y++)
                                                <div class="flex">
                                                    @for($x = 0; $x < $apercu['largeur']; $x++)
                                                        @php $case = $apercu['grille'][$y][$x]; @endphp
                                                        <div class="relative w-8 h-8 border border-black/10 rounded flex items-center justify-center font-bold text-sm z-0
                                                            {{ $case['lettre'] ? ($case['lettre_visible'] ? 'bg-black/20' : 'bg-white') : 'bg-white/30' }}">
                                                            @if($case['numero'])
                                                                <span style="z-index: -10;" class="absolute top-0 left-0.5 text-[8px] text-black/50 font-normal ">{{ $case['numero'] }}</span>
                                                            @endif
                                                            @if($case['lettre'])
                                                                <span>{{ $case['lettre'] }}</span>
                                                            @endif
                                                        </div>
                                                    @endfor
                                                </div>
                                            @endfor
                                        </div>
                                        <div class="flex-1 ">
                                            @php
                                                $motsHorizontal = $motCroise->motsCroisesMots->where('direction', 'horizontal')->sortBy('numero');
                                                $motsVertical = $motCroise->motsCroisesMots->where('direction', 'vertical')->sortBy('numero');
                                            @endphp

                                            @if($motsHorizontal->isNotEmpty())
                                            <div class="border rounded-md p-2 border-black/10 mb-2">
                                                <h4 class="font-semibold text-sm mb-1 text-vert">Horizontal</h4>
                                                <ul class="text-sm mb-3">
                                                    @foreach($motsHorizontal as $mot)
                                                        <li class="p-1 border-b border-black/5 {{ $loop->iteration == 2 ? 'bg-white ' : '' }}"><strong>{{ $mot->numero }}.</strong> {{ $mot->indice }} <span class="text-black/40">({{ strlen($mot->reponse) }} lettres)</span></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            @endif

                                            @if($motsVertical->isNotEmpty())
                                            <div class="border p-2 rounded-md border-black/10 mb-2">
                                                <h4 class="font-semibold text-sm mb-1 text-vert">Vertical</h4>
                                                <ul class="text-sm">
                                                    @foreach($motsVertical as $mot)
                                                        <li class="p-1  border-b border-black/5 {{ $loop->iteration == 2 ? 'bg-white' : '' }}"><strong>{{ $mot->numero }}.</strong> {{ $mot->indice }} <span class="text-black/40">({{ strlen($mot->reponse) }} lettres)</span></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                <div class="text-center  p-10 rounded-md bg-black/3">
                                    <i class="fa-solid fa-box-open"></i>
                                    <p class="text-xs text-black/40 ">Aucun mot ajouté pour le moment.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
            </div>
        @empty
        <div class="text-center p-10 rounded-md mt-4 bg-black/3">
            <i class="fa-solid fa-box-open text-2xl"></i>
            <p class="text-black/50">Aucun exercice mots croisés pour cet examen.</p>
        </div>
        @endforelse
        @if ($examen->status === 'brouillon')     
        <div class="flex justify-end  mt-4 sticky bottom-5 me-2">
            <a href="{{ route('prof.examen.motscroises.create', [$slug, $examen->id]) }}" class="bg-rouge text-white px-4 py-2 rounded-full">
                + Créer un exercice
            </a>
        </div>
        @endif
    </div>
</div>
@endsection