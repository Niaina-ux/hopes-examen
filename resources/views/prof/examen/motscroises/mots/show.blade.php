@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="w-full py-3">
    @include('layouts.admin-layouts.examen.layout-exam')
    <div class="bg-white p-4 rounded-md">
        @if(session('success'))
            <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md mt-4 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('success-alert').remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Mots — {{ $motsCroise->titre }}</h2>
            <a href="{{ route('prof.examen.motscroises.mot.create', [$slug, $examen->id, $motsCroise->id]) }}" class="bg-rouge text-white px-4 py-2 rounded">
                + Ajouter un mot
            </a>
        </div>

        {{-- ✅ Aperçu de la grille --}}
        @if($mots->isNotEmpty())
            <div class="mb-6">
                <h3 class="font-semibold mb-2">Aperçu de la grille</h3>
                <p class="text-xs text-black/50 mb-2">
                    <span class="inline-block w-3 h-3 bg-white border border-black/20 align-middle"></span> lettre masquée pour l'étudiant
                    &nbsp;&nbsp;
                    <span class="inline-block w-3 h-3 bg-black/20 border border-black/20 align-middle"></span> lettre indice (visible pour l'étudiant)
                </p>
                <div class="inline-block border-2 border-black/20">
                    @for($y = 0; $y < $hauteur; $y++)
                        <div class="flex">
                            @for($x = 0; $x < $largeur; $x++)
                                @php $case = $grille[$y][$x]; @endphp
                                <div class="relative w-9 h-9 border border-black/10 flex items-center justify-center font-bold
                                    {{ $case['lettre'] ? ($case['lettre_visible'] ? 'bg-black/20' : 'bg-white') : 'bg-black/5' }}">
                                    @if($case['numero'])
                                        <span class="absolute top-0 left-0.5 text-[8px] text-black/50">{{ $case['numero'] }}</span>
                                    @endif
                                    @if($case['lettre'])
                                        <span>{{ $case['lettre'] }}</span>
                                    @endif
                                </div>
                            @endfor
                        </div>
                    @endfor
                </div>
            </div>
        @endif

        {{-- Liste des mots --}}
        @forelse($mots as $mot)
            <div class="border border-black/10 rounded-md p-4 mb-3 flex justify-between items-center">
                <div>
                    <h3 class="font-semibold">{{ $mot->numero }}. {{ $mot->reponse }} <span class="text-xs text-black/50">({{ $mot->direction }}, x={{ $mot->position_x }}, y={{ $mot->position_y }})</span></h3>
                    <p class="text-sm text-black/50">{{ $mot->indice }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('prof.examen.motscroises.mot.edit', [$slug, $examen->id, $motsCroise->id, $mot->id]) }}" class="text-vert">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                    <form action="{{ route('prof.examen.motscroises.mot.destroy', [$slug, $examen->id, $motsCroise->id, $mot->id]) }}" method="POST" onsubmit="return confirm('Supprimer ce mot ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-rouge">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-black/50">Aucun mot pour cet exercice.</p>
        @endforelse
    </div>
</div>
@endsection