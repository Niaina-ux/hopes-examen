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
            $ExamenisFnis = $attempt && $attempt->status !== 'en_cours';
        @endphp

        <div class="border border-black/10 rounded-xl p-2 mt-4">
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
                                class="p-2 px-3 rounded-full bg-rouge text-white">
                                @if ($attempt->status === 'corrige')  
                                Voir la correction
                                @else
                                Corriger
                                @endif
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="mt-2 bg-black/3 p-2 rounded-md">
                        @if($ExamenisFnis)
                        <div class="min-h-[50vh] p-2">
                            @if ($attempt->status === 'corrige')    
                            <h2 class="text-xl font-bold uppercase">Résumé des notes</h2>
                                <div class="py-3">

                                    @if(empty($resumeParType))
                                        <div class="h-full flex items-center justify-center text-black/40 py-10">
                                            Aucun exercice noté pour cet examen.
                                        </div>
                                    @else
                                        <table class="w-full text-base border-collapse border border-black/10 bg-white/90">
                                            <thead>
                                                <tr class="bg-black/5">
                                                    <th class="py-2 px-3 border border-black/10 text-left w-[2cm]">N°</th>
                                                    <th class="py-2 px-3 border border-black/10 text-left">Exercice</th>
                                                    <th class="py-2 px-3 border border-black/10 text-right">Note</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($resumeParType as $key => $r)
                                                    <tr>
                                                        <td class="py-2 px-3 border border-black/10">{{ $loop->iteration }}</td>
                                                        <td class="py-2 px-3 border border-black/10">{{ $r['nom'] }}</td>
                                                        <td class="py-2 px-3 border border-black/10 text-right">
                                                            {{ $r['obtenus'] }} / {{ $r['total'] }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="flex justify-between gap-3 mt-4">
                                            <h3 class=" font-semibold">Total Général:  <span class="mx-2">{{ $totalPointsGlobalObtenus }} / {{ $totalNoteGlobal }}</span></h3>
                                            @php
                                                $pourcentage = $totalNoteGlobal > 0 ? ($totalPointsGlobalObtenus / $totalNoteGlobal) * 100 : 0;
    
                                                $mention = match(true) {
                                                    $pourcentage >= 90 => 'Excellent!',
                                                    $pourcentage >= 80 => 'Très Bien!',
                                                    $pourcentage >= 70 => 'Bien!',
                                                    $pourcentage >= 60 => 'Assez Bien!',
                                                    $pourcentage >= 50 => 'Passable!',
                                                    $pourcentage >= 0 => 'Fait le meilleur!',
                                                    default => 'Insuffisant',
                                                };
    
                                                $couleurMention = match(true) {
                                                    $pourcentage >= 70 => 'text-vert',
                                                    $pourcentage >= 50 => 'text-orange-500',
                                                    default => 'text-rouge',
                                                };
                                            @endphp
    
                                            <div class="text-right">
                                                <p class="{{ $couleurMention }}">{{ $mention }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @else
                                    <div class="h-full flex flex-col items-centerjustify-center text-black/70 text-center py-10">
                                        <i class="fa-solid fa-hourglass-half text-2xl mb-2"></i>
                                        <p>{{ !$attempt ? "L'étudiant n'a pas encore commencé cet examen." : "Examen en attente de correction ..." }}</p>
                                    </div>
                                @endif
                            </div>
                        @else
                        <div class="h-full flex flex-col items-center justify-center text-black/40 py-10">
                            <i class="fa-solid fa-hourglass-half text-2xl mb-2"></i>
                            <p>{{ !$attempt ? "L'étudiant n'a pas encore commencé cet examen." : "Examen en cours..." }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection