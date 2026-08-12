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
        <div class="p-2 border border-black/10  mt-2 rounded-md">
            @forelse($exercices as  $exercice)
                <div class="border-b border-black/3 bg-white/70  p-2  flex gap-4 ">
                    <div class="w-12 h-12 bg-black/3 font-semibold flex justify-center items-center rounded">
                        {{$exercice->ordre}}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-start gap-3">
                            <div class="flex-1">
                                <h3 class="font-semibold">{{ $exercice->titre }}</h3>
                                <p class="text-sm text-black/50">{{ $exercice->questions_count }} image(s) — {{ $exercice->note_totale }} pts</p>
                            </div>
                            @if ($examen->status === 'brouillon')    
                            <div class="flex gap-3">
                                <a href="{{ route('prof.examen.image.edit', [$slug, $examen->id, $exercice->id]) }}" class="text-vert">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('prof.examen.image.destroy', [$slug, $examen->id, $exercice->id]) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-rouge"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                            <a href="{{ route('prof.examen.image.question.create', [$slug, $examen->id, $exercice->id]) }}" class="bg-vert text-white px-4 py-1 rounded-full">
                                + Ajouter image
                            </a>
                            @endif
                        </div>
                        <div class="mt-2">
                            @forelse($exercice->questions as $question)
                            <div class="p-2 flex gap-4 rounded-md bg-black/2 border border-black/3 mb-2">
                                <img src="{{ asset('images/image_exercice/' . $question->image) }}" class="w-[30%] object-cover rounded-md border border-black/10" alt="">
                                <div class="flex-1">
                                    <div class="flex justify-between bg-white/70 rounded-md border border-black/3 p-2">
                                        <div class="flex-1">
                                            <span class="text-sm text-black/50">Instruction</span>
                                            <p>{{ $question->instruction }}</p>
                                            <span class="text-xs text-rouge">{{ $question->points }} pts</span>
                                        </div>
                                        @if ($examen->status === 'brouillon') 
                                        <div class="flex gap-3">
                                            <a href="{{ route('prof.examen.image.question.edit', [$slug, $examen->id, $exercice->id, $question->id]) }}" class="text-vert">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <form action="{{ route('prof.examen.image.question.destroy', [$slug, $examen->id, $exercice->id, $question->id]) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-rouge"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="p-10 rounded-md bg-black/5 text-center">
                                <i class="fa-solid fa-box-open text-2xl"></i>
                                <p>Aucun image n'a encore été créé pour cet examen.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 rounded-md bg-black/5 text-center">
                    <i class="fa-solid fa-box-open text-2xl"></i>
                    <p>Aucun exercice n'a encore été créé pour cet examen.</p>
                </div>
            @endforelse
        </div>
        @if ($examen->status === 'brouillon')   
        <div class="flex justify-end gap-3 mt-4 sticky bottom-5">
            <a href="{{ route('prof.examen.image.create', [$slug, $examen->id]) }}" class="bg-rouge text-white px-4 py-2 rounded-full">
                + Créer un exercice
            </a>
        </div>
        @endif
    </div>
@endsection