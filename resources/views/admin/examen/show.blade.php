@extends('layouts.admin-layouts.layouthead')
@section('contenue-admin')
<div class="py-3">
    <div class="flex justify-between items-end gap-4">
        <div class="w-[60%]">
            <h3 class="text-3xl font-semibold text-vert">
                Examens — {{ $categorie->nom }}
            </h3>
            <p>Liste des examens créés pour cette catégorie.</p>
        </div>
        <div>
            <a href="{{ route('admin.examen.create', $slug) }}"
               class="p-1 px-5 rounded-full bg-rouge
                      inline-block text-white">
                + Créer examen
            </a>
        </div>
    </div>

    @if(session('success'))
        <div id="success-alert"
             class="bg-green-100/50 text-green-700 px-4 py-2
                    rounded-md mt-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button type="button"
                    onclick="document.getElementById('success-alert').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <div class="flex items-center justify-between gap-3
                border-b-2 border-black/10 pb-2 mt-4
                dark:border-white/10">
        <div class="flex items-center gap-2 overflow-x-auto min-w-0">
            <a href="{{ route('admin.examen.show', $slug) }}"
               class="shrink-0 px-5 py-1 rounded-md border
                      border-black/10 transition
                      dark:border-white/10
                      {{ $modeTous
                          ? 'bg-vert text-white'
                          : 'bg-black/2 hover:bg-black/5 dark:bg-white/3 dark:hover:bg-white/5' }}">
                Tous
            </a>
            @if ($moisSelectionne && !$modeTous)
            @foreach($datesDisponibles as $date)
            <a href="{{ route('admin.examen.show', [
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
            <p class="px-5 rounded-md bg-black/2 p-1 border border-black/3
            dark:bg-white/2 dark:border-white/3">
                Selcetionnez la date et mois pour filtrer <i class="fa-solid fa-arrow-right-long"></i>
            </p>   
            @endif
        </div>

        <form method="GET"
              action="{{ route('admin.examen.show', $slug) }}"
              class="shrink-0">
            <input type="month"
                   name="mois"
                   value="{{ $moisSelectionne }}"
                   onchange="this.form.submit()"
                   class="border border-black/10 rounded-md bg-black/2 p-1 px-3
                    dark:border-white/10 dark:bg-white/2">
        </form>
    </div>

    @if($dateSelectionnee)
        <div class="mt-4 flex items-center gap-2">
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
        <div class="mt-4 flex items-center gap-2">
            <span class="text-sm">
                Examens du mois :
            </span>
            <span class="px-3 py-1 rounded-full bg-vert/10
                         text-vert text-sm">
                {{ \Carbon\Carbon::parse($moisSelectionne . '-01')
                    ->translatedFormat('F Y') }}
            </span>
        </div>
    @endif

    <div class="p-2 border border-black/10 rounded-md mt-4 bg-black/2
        dark:border-white/3 dark:bg-white/2">
        @forelse($examens as $index => $examen)
            <div class="flex justify-between gap-7 border
                        border-black/10 rounded bg-white/70 p-2
                        hover:bg-white transition
                        dark:border-white/3 dark:bg-white/2 dark:hover:bg-white/4">
                <div class="w-10 h-10 shrink-0 rounded-md bg-black/5
                            flex justify-center items-center font-semibold
                            dark:bg-white/5">
                    {{ str_pad($examens->firstItem() + $index,2, '0', STR_PAD_LEFT) }}
                </div>

                <div class="flex-1 min-w-0">
                    <a href="" class="text-lg -mt-1">
                        {{ $examen->titre }}
                    </a>

                    <div class="flex gap-3 text-sm  flex-wrap">
                        <span class=" {{ $examen->status == 'brouillon'
                                         ? 'text-black/70 dark:text-white/50'
                                         : 'text-rouge' }}">
                            {{ match($examen->status) {
                                'brouillon' => 'Brouillon',
                                'publie' => 'Publié',
                                'archive' => 'Archivé',
                                default => 'Inconnu'
                            } }}
                        </span>

                        <span class=" text-vert px-3">
                            {{ $examen->types_exercice_count }}
                            types d'exercice
                        </span>

                        <span class="">Le 
                            {{ $examen->date_examen
                                ? \Carbon\Carbon::parse($examen->date_examen)
                                    ->translatedFormat('d M Y')
                                : 'Non planifié' }}
                        </span>
                    </div>
                </div>

                <div class="flex gap-4 shrink-0">
                    <a href="{{ route('admin.examen.student.show', [
                            $slug,
                            $examen->id
                        ]) }}"
                       class="text-vert">
                        <i class="fa-solid fa-user-graduate"></i>
                    </a>

                    <a href="{{ route('admin.examen.edit', [
                            $slug,
                            $examen->id
                        ]) }}"
                       class="">
                        <i class="fa-solid fa-pen"></i>
                    </a>

                    <form action="{{ route('admin.examen.destroy', [
                                $slug,
                                $examen->id
                            ]) }}"
                          method="POST"
                          onsubmit="return confirm(
                              'Supprimer {{ $examen->titre }} ?'
                          )">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-10 rounded-md bg-black/5 text-center
                dark:bg-white/2">
                <i class="fa-solid fa-box-open text-2xl"></i>
                <p>
                    Aucun examen créé pour cette catégorie
                    à cette date.
                </p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $examens->links() }}
    </div>
</div>
@endsection