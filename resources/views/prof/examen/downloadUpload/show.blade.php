@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
    <div class="py-3 me-2">
        @include('layouts.admin-layouts.examen.layout-exam')
        @if(session('success'))
            <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md mt-4 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('success-alert').remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        <div class="border border-black/10 rounded-md p-2 mt-2">  
            @forelse($fichiers as $index => $fichier)
                <div class="p-2 flex gap-7 justify-between my-2">
                    <div class="w-15 h-15 rounded-md bg-black/3 flex justify-center items-center">
                        <span class="font-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex gap-3">
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
                            <div class="flex items-start gap-4">
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
                                <a href="{{ route('prof.examen.fichier.qeustion.create', [$slug, $examen->id, $fichier->id]) }}" 
                                    class="bg-vert p-1 px-4 inline-block rounded-md text-white">
                                    + Créer question
                                </a>
                            </div>
                        </div>
                        @forelse($fichier->fichierQuestions as $index => $question)
                        <div class="p-2 rounded-md bg-black/3">
                            <div class="flex gap-5 justify-between">
                                <div class="w-10 h-10 bg-black/5 rounded-sm flex justify-center items-center">
                                    <span class="text-vert">{{ $index + 1 }}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex  justify-between gap-3">
                                        <div class="">
                                            <h4 class="text-base">{{ $question->instruction }}</h4>
                                            <div class="flex gap-3">
                                                <div class="flex text-sm text-vert">
                                                    Temps libre
                                                </div>
                                                <div class="flex text-sm text-rouge">
                                                    {{ $question->points }} points
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex gap-3">
                                            <a href="{{ route('prof.examen.fichier.qeustion.edit', [$slug, $examen->id, $fichier->id, $question->id]) }}" class="text-black/60">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <form action="{{ route('prof.examen.fichier.qeustion.destroy', [$slug, $examen->id, $fichier->id, $question->id]) }}" method="POST" onsubmit="return confirm('Supprimer ce devoir ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    @if($question->fichier_prof)
                                        <div class="p-2  px-3 rounded-md bg-white/90 mt-1">
                                            <p> {{$question->fichier_prof}} </p>
                                            <a href="{{ asset('fichiers/prof/' . $question->fichier_prof) }}" target="_blank" class="text-vert text-sm underline mt-1 inline-block ">
                                                <i class="fa-solid fa-paperclip"></i> Télécharger le fichier fourni
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 rounded-md bg-black/5 text-center">
                            <i class="fa-solid fa-box-open text-2xl"></i>
                            <p>Aucun devoir n'a encore été créé.</p>
                        </div>
                    @endforelse
                    </div>
                </div>
            @empty
                <div class="p-10 rounded-md bg-black/5 text-center mt-4">
                    <i class="fa-solid fa-box-open text-2xl"></i>
                    <p>Aucun QCM n'a encore été créé pour cet examen.</p>
                </div>
            @endforelse
            <div class=" flex justify-end mt-4 sticky bottom-5">
                <a href="{{route('prof.examen.fichier.create', [$slug,$examen->id])}}" class="p-2 px-3 text-white inline-block rounded-md bg-rouge ">
                    Créer nouveau exercice
                </a>
            </div>
        </div>
    </div>
@endsection