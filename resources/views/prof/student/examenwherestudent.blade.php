@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
    <div class="py-3 me-2">
        <div class="">
            <a href="{{ route('prof.examen.studentswithexamen', [$slug, $examen->id]) }}" class="hover:underline">Retour / </a>
            <span class="font-semibold">Etudiants & Examen</span>
        </div>
        <div class="flex gap-5 items-center my-2">
            <div class="w-11 h-11 rounded-md overflow-hidden">
                <img src="{{ $student->image ? asset('images/' . $student->image) : '' }}" alt="" 
                class="w-full h-full object-cover">
            </div>
            <div class="">
                <h4 class="font-semibold text-vert"> {{$student->name}} </h4>
                <span> {{$student->email}} </span>
            </div>
        </div>

        @php
            // ✅ Nanao ilay examen ny mpianatra raha misy attempt AMBY tsy en_cours
            $ExamenisFnis = $attempt && $attempt->status !== 'en_cours';
        @endphp

        <div class="border border-black/10 rounded-md p-2 mt-4">
            <div class="flex gap-5">
                <div class="w-12 h-12 rounded-md bg-black/3 flex justify-center items-center font-semibold text-rouge">Ex</div>
                <div class="flex-1">
                    <div class="flex-1 flex gap-3">
                        <div class="flex-1">
                            <h2 class="text-xl font-semibold -mt-1"> {{$examen->titre}} </h2>
                            <div class="text-sm flex gap-4">
                                <span>{{ $attempt?->date_fin?->format('d-M-Y') ?? 'Pas encore commencé' }}</span>
                                <span>
                                    @if(!$attempt)
                                        Pas encore commencé
                                    @elseif($attempt->status === 'en_cours')
                                        Examen en cours
                                    @elseif($attempt->status === 'corrige')
                                        Corrigé
                                    @else
                                        En attente de correction
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="mt-1">
                            @if($ExamenisFnis && $premierType && \Illuminate\Support\Facades\Route::has('prof.examen.showtache.' . $premierType->slug))
                                <a href="{{ route('prof.examen.showtache.' . $premierType->slug, [$slug, $examen->id, $student->id]) }}"
                                class="p-2 px-3 rounded-md bg-rouge text-white">
                                Corriger 
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="mt-1 bg-black/3 p-2 rounded">
                        @if($ExamenisFnis)
                        <div class="border border-black/10 bg-vert rounded-t text-white flex justify-between gap-3 p-1 px-2">
                            <strong>Relévé de note</strong>
                            <div class="flex gap-4">
                                <span><i class="fa-solid fa-envelope"></i></span>
                                <span><i class="fa-solid fa-download"></i></span>
                            </div>
                        </div>
                        <div class="min-h-[50vh] bg-white/70 rounded-b p-2">
                            eto ny detail note
                        </div>
                        @else
                        <div class="h-full flex flex-col items-center justify-center text-black/40 py-10">
                            <i class="fa-solid fa-hourglass-half text-2xl mb-2"></i>
                            <p>
                                {{ !$attempt ? "L'étudiant n'a pas encore commencé cet examen." : "Examen en cours..." }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection