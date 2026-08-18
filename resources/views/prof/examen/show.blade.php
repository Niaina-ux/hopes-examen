@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="py-3 me-2">
    <div class="flex justify-between items-end mb-2 pb-2">
        <div class="w-[70%]">
            <h2 class="text-2xl font-semibold text-vert mb-2">
                Examens {{ $categorie->nom }}
            </h2>
            <p>
                Liste des examens créés pour cette catégorie.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div id="success-alert"
             class="bg-green-100/50 text-green-700 px-4 py-2
                    rounded-md mb-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button type="button"
                    onclick="document.getElementById('success-alert').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div id="error-alert"
             class="bg-red-100 text-red-700 border border-red-300
                    px-4 py-2 rounded-md mb-4 flex justify-between
                    items-center">
            <span>{{ session('error') }}</span>
            <button type="button"
                    onclick="document.getElementById('error-alert').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <div class="flex items-center justify-between gap-3
                border-b-2 border-black/10 pb-2 mb-4">
        <div class="flex items-center gap-2 overflow-x-auto min-w-0">
            <a href="{{ route('prof.examen.show', $slug) }}"
               class="shrink-0 px-5 py-1 rounded-md border
                      border-black/10 transition
                      {{ $modeTous
                          ? 'bg-vert text-white'
                          : 'bg-black/2 hover:bg-black/5' }}">
                Tous
            </a>
            @if ($moisSelectionne && !$modeTous)     
                @if($datesDisponibles->isNotEmpty())
                    @foreach($datesDisponibles as $date)
                        <a href="{{ route('prof.examen.show', [
                                $slug,
                                'date' => $date
                            ]) }}"
                        class="shrink-0 px-3 py-1 rounded-md border
                                border-black/10 transition
                                {{ $dateSelectionnee === $date
                                    ? 'bg-vert text-white'
                                    : 'bg-black/2 hover:bg-black/5' }}">
                            {{ \Carbon\Carbon::parse($date)
                                ->translatedFormat('d M Y') }}
                        </a>
                    @endforeach
                @else
                    <span class="shrink-0 px-3 py-1 rounded-md border
                                border-black/10 text-black/40">
                        Aucun examen creé!
                    </span>
                @endif
            @else
                <p class="px-5 rounded-md bg-black/2 p-1 border border-black/3">Selcetionnez le date pour filtrer <i class="fa-solid fa-arrow-right-long"></i></p>   
            @endif
        </div>
        <form method="GET"
              action="{{ route('prof.examen.show', $slug) }}"
              class="shrink-0">

            <input type="month"
                   name="mois"
                   value="{{ $moisSelectionne }}"
                   onchange="this.form.submit()"
                   class="border border-black/10 rounded-md
                          bg-black/2 p-1 px-3">

        </form>

    </div>

    @if($dateSelectionnee)
        <div class="mb-4 flex items-center gap-2">
            <span class="text-sm text-black/50">
                Examens du :
            </span>
            <span class="px-3 py-1 rounded-full bg-vert/10
                         text-vert text-sm">
                {{ \Carbon\Carbon::parse($dateSelectionnee)
                    ->translatedFormat('d F Y') }}
            </span>
        </div>
    @elseif($moisSelectionne)
        <div class="mb-4 flex items-center gap-2">
            <span class="text-sm text-black/50">
                Examens du mois :
            </span>
            <span class="px-3 py-1 rounded-full bg-vert/10
                         text-vert text-sm">
                {{ \Carbon\Carbon::parse($moisSelectionne . '-01')
                    ->translatedFormat('F Y') }}
            </span>
        </div>
    @endif

    <div class="border border-black/3 rounded-md p-2 bg-black/2">

        @forelse($examens as $index => $examen)

            <div class="flex justify-between gap-7 p-3
                        border border-black/3 bg-white/80 rounded">

                <div class="w-10 h-10 shrink-0 rounded-md bg-black/5
                            overflow-hidden font-semibold
                            flex justify-center items-center">
                    <span>
                        {{ str_pad(
                            $examens->firstItem() + $index,
                            2,
                            '0',
                            STR_PAD_LEFT
                        ) }}
                    </span>
                </div>

                <div class="flex-1 min-w-0">

                    <a href="{{ route(
                            'prof.examen.showtypes',
                            [$slug, $examen->id]
                        ) }}"
                       class="hover:underline font-medium block -mt-1">

                        {{ $examen->titre }}

                        <span @class([
                            'rounded-4xl text-white border-2
                             font-normal text-sm border-black/5 px-2',
                            'bg-vert' => $examen->status == 'publie',
                            'bg-black/40' => $examen->status == 'brouillon',
                            'bg-rouge' => $examen->status == 'archive',
                        ])>
                            {{ $examen->status }}
                        </span>

                    </a>

                    <div class="flex gap-4 text-sm flex-wrap">

                        <div class="flex">
                            Il y a
                            <span class="inline-block px-2 text-vert">
                                {{ $examen->types_exercice_count ?? 0 }}
                            </span>
                            types d'exercice
                        </div>

                        <div class="flex">
                            Durée:
                            <span class="px-3 text-rouge">
                                {{ $examen->duree_minutes }}
                                Minutes
                            </span>
                        </div>

                        <div class="flex">
                            Date:
                            <span class="px-3 text-vert">
                                {{ $examen->date_examen
                                    ? \Carbon\Carbon::parse(
                                        $examen->date_examen
                                    )->translatedFormat('d M Y')
                                    : 'Non planifié' }}
                            </span>
                        </div>

                    </div>

                </div>

                <div class="shrink-0">
                    <div class="flex gap-3 items-center">

                        <a href="{{ route(
                                'prof.examen.showtypes',
                                [$slug, $examen->id]
                            ) }}"
                           class="text-vert rounded px-1">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>

                        <a href="{{ route(
                                'prof.examen.studentswithexamen',
                                [$slug, $examen->id]
                            ) }}"
                           class="rounded px-1">
                            <i class="fa-solid fa-user-graduate"></i>
                        </a>

                    </div>
                </div>

            </div>

        @empty

            <div class="p-20 rounded-md bg-black/1 text-center">
                <i class="fa-solid fa-box-open text-3xl"></i>
                <p>Il n'y a pas encore d'examen créé !</p>
            </div>

        @endforelse

        <div class="mt-2">
            {{ $examens->links() }}
        </div>

    </div>
</div>
@endsection