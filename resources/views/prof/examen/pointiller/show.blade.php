@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
    <div class="py-3 me-2">
        @include('layouts.admin-layouts.examen.layout-exam')
        @forelse($pointillers as $index => $pointiller)
            <div class="p-2 flex gap-7 justify-between border rounded-md border-black/10 my-2">
                <div class="w-15 h-15 rounded-md bg-black/3 flex justify-center items-center">
                    <span class="font-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="flex-1">
                    <div class="flex items-start gap-3 mb-2">
                        <div class="flex-1">
                            <h3 class="text-base font-semibold">{{ $pointiller->titre }}</h3>
                            <p>{{ $pointiller->description }}</p>
                            <div class="flex gap-4">
                                <div class="flex text-sm text-rouge">
                                    Durée {{ $pointiller->duree_minutes ?? 'N/A' }} minutes
                                </div>
                                <div class="flex text-sm ">
                                    {{ $pointiller->note_totale }} Points
                                </div>
                                <div class="flex text-sm text-vert">
                                    Il y a {{ $pointiller->pointiller_questions_count }} questions
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <a href="{{route('prof.examen.pointiller.edit', [$slug, $examen->id, $pointiller->id])}}" class="text-black/60">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('prof.examen.pointiller.destroy', [$slug, $examen->id, $pointiller->id]) }}" method="POST" onsubmit="return confirm('Supprimer {{ $pointiller->titre }} ? Cette action supprimera aussi toutes ses questions.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                        @if ($examen->status === 'brouillon')
                        <a href="{{ route('prof.examen.pointiller.question.create', [$slug, $examen->id, $pointiller->id]) }}" 
                            class="bg-vert p-1 px-4 inline-block rounded-full text-white">
                            Créer question
                        </a>   
                        @endif
                    </div>
                    @forelse($pointiller->pointillerQuestions as $index => $question)
                        <div class="border-b border-black/3 ">
                            @if($question->image)
                                <div class="w-35 h-30 border border-black/2 rounded-md bg-black/10 mt-7 overflow-hidden">
                                    <img src="{{ asset('images/questions/' . $question->image) }}" alt="" class="w-full h-full object-cover">
                                </div>
                            @endif
        
                            @if($question->video)
                                <video controls class="w-full max-w-md rounded-md mt-3">
                                    <source src="{{ asset('videos/questions/' . $question->video) }}" type="video/mp4">
                                    Votre navigateur ne supporte pas la lecture vidéo.
                                </video>
                            @endif
        
                            <div class="flex gap-5 justify-between border-y border-black/5 py-2">
                                <div class="w-8 h-8 bg-black/5 rounded-sm flex justify-center items-center ">
                                    <span class="text-vert">{{ $index + 1 }}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-1">
                                            <h4 class="text-base ">{{ $question->enonce }}</h4>
                                            <div class="flex gap-3">
                                                <div class="flex text-sm">
                                                    Type: <span class="px-3 inline-block text-rouge">Compléter</span>
                                                </div>
                                                <div class="flex text-sm">
                                                    Point: <span class="px-3 inline-block text-vert">{{ $question->points }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex gap-3 items-center">
                                            <a href="{{ route('prof.examen.pointiller.question.edit', [$slug, $examen->id, $pointiller->id, $question->id]) }}" class="text-vert">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <form action="{{ route('prof.examen.pointiller.question.destroy', [$slug, $examen->id, $pointiller->id, $question->id]) }}" 
                                                method="POST" onsubmit="return confirm('Supprimer cette question ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rouge">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="reponse-wrapper">
                                        <div class="border border-black/3  mt-1 rounded bg-black/2 p-2 reponse ">
                                            @foreach($question->reponses as $reponse)
                                                <div class="flex justify-between gap-4 bg-white/70  border-b border-black/5 p-1 rounded">
                                                    <span>
                                                        <i class="fa-solid fa-bullhorn"></i>
                                                    </span>
                                                    <p class="flex-1">
                                                        Trou {{ $reponse->position }} — <span class="text-sm">Réponse correcte:</span>
                                                        <span class="text-vert ">{{ $reponse->reponse_correcte }}</span>
                                                    </p>
                                                    <span class=" text-black/40">
                                                        <span class="text-sm">Banque:</span> {{ $reponse->choices->pluck('texte')->implode(', ') }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 rounded-md bg-black/5 mt-2 text-center">
                            <i class="fa-solid fa-box-open text-2xl"></i>
                            <p>Aucune question n'a encore été ajoutée.</p>
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
        @if ($examen->status === 'brouillon')  
        <div class=" flex justify-end sticky bottom-5 mt-4">
            <a href="{{route('prof.examen.pointiller.create', [$slug,  $examen->id])}}" class="p-2 px-3 text-white inline-block rounded-full bg-rouge ">
                Créer nouveau quiz
            </a>
        </div>
        @endif
    </div>
@endsection