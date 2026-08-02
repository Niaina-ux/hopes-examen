@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
    <div class="py-3">
        @include('layouts.admin-layouts.examen.layout-exam')
        @if(session('success'))
            <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md my-2 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('success-alert').remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        <div class="">
            @forelse($texts as $index => $text)
                <div class="p-2 flex gap-5 justify-between border border-black/10 rounded-md my-2">
                    <div class="w-15 h-15 rounded-md bg-black/3 flex justify-center items-center">
                        <span class="font-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between gap-3">
                            <div class="">
                                <h3 class="text-xl font-semibold">{{ $text->titre }}</h3>
                                <div class="flex gap-4">
                                    <div class="flex text-sm text-rouge">
                                        Durée Libre
                                    </div>
                                    <div class="flex text-sm text-vert">
                                        Il y a {{ $text->textQuestions->count() }} Questions             
                                    </div>
                                    <div class="flex text-sm ">
                                        {{ $text->note_totale }} Points             
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <a href="{{route('prof.examen.text.edit', [$slug, $examen->id, $text->id])}}" class="text-black/60">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('prof.examen.text.destroy', [$slug, $examen->id, $text->id]) }}" method="POST" onsubmit="return confirm('Supprimer {{ $text->titre }} ? Cette action supprimera aussi toutes ses questions.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="bg-black/3 rounded-md p-2 px-3 mt-2">
                            <span class="font-semibold">Text</span>
                            <p class="whitespace-pre-line bg-white/60 rounded-md p-2 mb-2">{{$text->texte}}</p>
                            <div class="flex justify-between items-center my-2">
                                <span class="font-semibold">Question</span>
                                <a href="{{ route('prof.examen.text.question.create', [$slug, $examen->id, $text->id]) }}" class="bg-vert text-white px-4 py-1 rounded-md text-nowrap">
                                    + Ajouter question
                                </a>
                            </div>
                            <div class="border border-black/10 rounded-md p-2 bg-white/60">
                                @forelse ($text->textQuestions as $question)  
                                <div class="border-b border-black/10 p-2 gap-3 flex justify-between items-center ">
                                    <div class="flex-1 flex gap-3 justify-between">
                                        <span class="text-vert font-semibold">{{ $index + 1 }}.</span>
                                        <p class="flex-1">{{ $question->enonce }}</p>
                                        <span class="text-sm mt-1 text-black/40 ml-2">({{ $question->points }} pts)</span>
                                    </div>
                                    <div class="flex gap-3">
                                        <a href="{{ route('prof.examen.text.question.edit', [$slug, $examen->id, $text->id, $question->id]) }}" class="text-black/60">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <form action="{{ route('prof.examen.text.question.destroy', [$slug, $examen->id, $text->id, $question->id]) }}" method="POST" onsubmit="return confirm('Supprimer cette question ?')">
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
                                    <p>Aucun question n'a encore été créé pour cet examen.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 rounded-md bg-black/5 text-center mt-4">
                    <i class="fa-solid fa-box-open text-2xl"></i>
                    <p>Aucun comprehension du text n'a encore été créé pour cet examen.</p>
                </div>
            @endforelse

            <div class="sticky bottom-5 flex justify-end mt-4 me-2">
                <a href="{{route('prof.examen.text.create', [$slug, $examen->id])}}" class="p-2 text-white px-3 inline-block rounded-md bg-rouge ">
                    Créer nouveau exercice
                </a>
            </div>
        </div>
    </div>
@endsection