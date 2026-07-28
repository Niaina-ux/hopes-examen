@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
    <div class="py-3">
        <div class="flex gap-3 items-center my-2">
            <a href="" class="">
                Examen /
            </a>
            <span class="font-semibold">Details</span>
        </div>
        @include('layouts.admin-layouts.examen.layout-exam')
        @if(session('success'))
            <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md mt-4 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('success-alert').remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        <div class="">  
            @forelse($fichiers as $index => $fichier)
                <div class="p-2 flex gap-7 justify-between border-b border-black/10 my-2">
                    <div class="w-15 h-15 rounded-md bg-black/3 flex justify-center items-center">
                        <span class="font-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base  font-semibold">{{ $fichier->titre }}</h3>
                        <p>{{ $fichier->description }}</p>
                        <div class="flex gap-4">
                            <div class="flex text-sm text-rouge">
                                Durée Libre
                            </div>
                            <div class="flex text-sm ">
                                {{ $fichier->note_totale }} Points
                            </div>
                            <div class="flex text-sm text-vert">
                                Il y a {{ $fichier->fichier__questions_count }} questions
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <a href="{{route('prof.examen.fichier.qeustion.show', [$slug, $examen->id, $fichier->id])}}" class="text-vert">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                        <a href="{{route('prof.examen.fichier.edit', [$slug, $examen->id, $fichier->id])}}" class="text-black/60">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <form action="{{ route('prof.examen.fichier.destroy', [$slug, $examen->id, $fichier->id]) }}" method="POST" onsubmit="return confirm('Supprimer {{ $fichier->titre }} ? Cette action supprimera aussi toutes ses questions.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-10 rounded-md bg-black/5 text-center mt-4">
                    <i class="fa-solid fa-box-open text-2xl"></i>
                    <p>Aucun QCM n'a encore été créé pour cet examen.</p>
                </div>
            @endforelse
            <div class=" flex justify-end mt-4">
                <a href="{{route('prof.examen.fichier.create', [$slug,$examen->id])}}" class="p-1 px-3 inline-block rounded-md bg-rouge ">
                    Créer nouveau exercice
                </a>
            </div>
        </div>
    </div>
@endsection