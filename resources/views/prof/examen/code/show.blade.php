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
        <div class="">
            @forelse($codes as $index => $code)
                <div class="p-2 flex gap-7 justify-between border border-black/10 rounded-md my-2">
                    <div class="w-15 h-15 rounded-md bg-black/3 flex justify-center items-center">
                        <span class="font-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start gap-3">
                            <div class="flex-1">
                                <h3 class="text-xl  font-semibold">{{ $code->titre }}</h3>
                                <p>{{ $code->description }}</p>
                                <div class="flex gap-4">
                                    <div class="flex text-sm text-rouge">
                                        Durée Libre
                                    </div>
                                    <div class="flex text-sm text-vert">
                                        Il y a {{ $code->code_questions_count }} questions      
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <a href="{{route('prof.examen.code.edit', [$slug, $examen->id, $code->id])}}" 
                                    class="text-black/60">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('prof.examen.code.destroy', [$slug, $examen->id, $code->id]) }}" 
                                    method="POST" 
                                    onsubmit="return confirm('Supprimer {{ $code->titre }} ? Cette action supprimera aussi toutes ses questions.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                            @if ($examen->status === 'brouillon')     
                            <a href="{{ route('prof.examen.code.question.create', [$slug, $examen->id, $code->id]) }}" 
                                class="bg-vert p-1 px-4 rounded-full text-white">
                                + Créer code
                            </a>
                            @endif
                        </div>
                        <div class="bg-black/3 rounded-md p-2 px-3 mt-2">
                            @forelse($code->codeQuestions as $index => $question)
                                <div class=" border-b border-black/10 pb-2">
                                    <div class="flex gap-5 justify-between">
                                        <div class="w-10 h-10 bg-black/5 rounded-sm flex justify-center items-center">
                                            <span class="text-vert">{{ $index + 1 }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex justify-between">
                                                <h4 class="text-base font-semibold">{{ $question->instruction }}</h4>
                                                <div class="flex gap-3 items-center">
                                                    <a href="{{ route('prof.examen.code.question.edit', [$slug, $examen->id, $code->id, $question->id]) }}" class="text-black/60">
                                                        <i class="fa-solid fa-pen"></i>
                                                    </a>
                                                    <form action="{{ route('prof.examen.code.question.destroy', [$slug, $examen->id, $code->id, $question->id]) }}" method="POST" onsubmit="return confirm('Supprimer cet exercice ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600">
                                                            <i class="fa-regular fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="flex gap-3 mt-1">
                                                <div class="flex text-sm">
                                                    Langage: <span class="border border-black/10 rounded-full px-3 text-rouge">{{ strtoupper($question->langage) }}</span>
                                                </div>
                                                <div class="flex text-sm">
                                                    Point: <span class="border border-black/10 rounded-full px-3 text-vert">{{ $question->points }}</span>
                                                </div>
                                                {{-- <div class="flex text-sm">
                                                    Rendus: <span class="border border-black/10 rounded-full px-3 text-black/50">{{ $question->reponses_count }}</span>
                                                </div> --}}
                                            </div>
                                            @if($question->code_starter)
                                                <pre class="bg-black/5 rounded-md p-3 text-sm mt-2 overflow-x-auto"><code>{{ $question->code_starter }}</code></pre>
                                            @endif
                                        </div>
                                        
                                    </div>
                                </div>
                            @empty
                                <div class="p-10 my-2 rounded-md bg-black/5 text-center">
                                    <i class="fa-solid fa-box-open text-2xl"></i>
                                    <p>Aucun exercice de code n'a encore été créé.</p>
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
            @if ($examen->status === 'brouillon')    
            <div class=" flex sticky bottom-5 justify-end mt-4 me-2">
                <a href="{{route('prof.examen.code.create', [$slug,$examen->id])}}" class="p-2 text-white px-3 inline-block rounded-full bg-rouge ">
                    Créer nouveau exercice
                </a>
            </div>
            @endif
        </div>
    </div>
@endsection