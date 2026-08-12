@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
    <div class="py-3 me-2">
        @include('layouts.admin-layouts.examen.layout-exam')
        @if(session('success'))
            <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md my-4 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('success-alert').remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        <div class=" mt-2">
            @forelse($qcms as $index => $qcm)
                <div class="p-2  flex gap-5 justify-between border border-black/10 rounded-md my-2">
                    <div class="w-15 h-15 rounded-md bg-black/3 flex justify-center items-center">
                        <span class="font-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between gap-3">
                            <div class="">
                                <h3 class="text-xl font-semibold">{{ $qcm->titre }}</h3>
                                <p>{{ $qcm->description }}</p>
                                <div class="flex gap-3">
                                    <div class="flex text-sm text-rouge">
                                        Durée {{ $qcm->duree_minutes ?? 'N/A' }} minutes
                                    </div>
                                    <div class="flex text-sm">
                                        {{ $qcm->note_totale }} Points
                                    </div>
                                    <div class="flex text-sm text-vert">
                                        Il y a 
                                        {{ $qcm->qcm_questions_count }}
                                        Questions
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-5 items-start">
                                @if ($examen->status === 'brouillon') 
                                <div class="flex gap-4">
                                    <a href="{{route('prof.examen.qcm.edit', [$slug, $examen->id, $qcm->id])}}" class="text-black/60">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('prof.examen.qcm.destroy', [$slug, $examen->id, $qcm->id]) }}" method="POST" onsubmit="return confirm('Supprimer {{ $qcm->titre }} ? Cette action supprimera aussi toutes ses questions.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                                <a href="{{ route('prof.examen.qcm.question.create', [$slug, $examen->id, $qcm->id]) }}" 
                                    class="bg-vert p-1 px-4 inline-block rounded-full text-white">
                                    Créer question
                                </a>
                                @endif
                            </div>
                        </div>
                        <div class="">
                            @forelse($qcm->qcmQuestions as $index => $question)
                                <div class="border-b border-black/5 bg-black/3 mt-2 rounded-md p-2">
                                    <div class="flex gap-5 justify-between py-2">
                                        <div class="w-10 h-10 bg-black/5 rounded-sm flex justify-center items-center">
                                            <span class="text-vert">{{ $index + 1 }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex justify-between gap-3">
                                                <div class="">
                                                    <h4 class="text-base">{{ $question->enonce }}</h4>
                                                    <div class="flex gap-3">
                                                        <div class="flex text-sm">
                                                            {{ $question->reponse_type }}, 
                                                        </div>                                                       
                                                        <div class="flex text-sm">
                                                            Duree: <span class="px-3 inline-block text-rouge">{{ $question->duree_seconde }} sec,</span>
                                                        </div>                                                       
                                                        <div class="flex text-sm">
                                                            Point: <span class="px-3 inline-block text-vert">{{ $question->points }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if ($examen->status === 'brouillon') 
                                                <div class="flex gap-3 items-center">
                                                    <a href="{{ route('prof.examen.qcm.question.edit', [$slug, $examen->id, $qcm->id, $question->id]) }}" class="text-vert">
                                                        <i class="fa-solid fa-pen"></i>
                                                    </a>
                                                    <form action="{{ route('prof.examen.qcm.question.destroy', [$slug, $examen->id, $qcm->id, $question->id]) }}" method="POST" class="text-rouge" onsubmit="return confirm('Supprimer cette question ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-rouge">
                                                            <i class="fa-regular fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="mb-4">
                                                @if($question->image)
                                                    <div class="w-40 h-30 border border-black/2 rounded-md bg-black/10 mt-3 overflow-hidden">
                                                        <img src="{{ asset('images/questions/' . $question->image) }}" alt="" class="w-full h-full object-cover">
                                                    </div>
                                                @endif
                            
                                                @if($question->video)
                                                    <video controls class="w-full max-w-md rounded-md mt-3">
                                                        <source src="{{ asset('videos/questions/' . $question->video) }}" type="video/mp4">
                                                        Votre navigateur ne supporte pas la lecture vidéo.
                                                    </video>
                                                @endif
                                            </div>
                                            <div class=" rounded-md p-2  px-3 reponse bg-black/3 mt-1">
                                                @foreach($question->qcmChoices as $choice)
                                                    <div class="flex justify-between gap-4 border-b border-black/5 py-1 {{ $loop->even ? 'bg-white/60' : '' }}">
                                                        <p class="flex-1 text-sm">- {{ $choice->texte }}</p>
                                                        <span class="{{ $choice->est_correcte ? 'text-vert' : 'text-black/40' }}">
                                                            @if($choice->est_correcte)
                                                                <i class="fa-solid fa-check"></i>
                                                            @else
                                                                <i class="fa-solid fa-xmark"></i>
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-10  mt-2 rounded-md bg-black/5 text-center">
                                    <i class="fa-solid fa-box-open text-2xl"></i>
                                    <p>Aucune question n'a encore été ajoutée.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 rounded-md bg-black/5 text-center mt-4">
                    <i class="fa-solid fa-box-open text-2xl"></i>
                    <p>Aucun QCM n'a encore été créé pour cet examen.</p>
                </div>
            @endforelse
        </div>
        @if ($examen->status === 'brouillon')     
        <div class=" flex justify-end sticky bottom-5 mt-4 pe-2">
            <a href="{{route('prof.examen.qcm.create', [$slug, $examen->id])}}" class="p-2 text-white px-5 inline-block rounded-full bg-rouge ">
                Créer nouveau quiz
            </a>
        </div>
        @endif
        
    </div>
@endsection