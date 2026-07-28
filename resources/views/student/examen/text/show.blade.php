@extends('layouts.student-layouts.layoutexamen')
@section('exercice-content')
<div class="pb-10">
    <div class="my-10">
        <div class="flex justify-between items-center">
            <span>Exercice</span>
            <span>{{ $index + 1 }}/{{ $total }}</span>
        </div>
        <div class="rounded-full h-3 overflow-hidden bg-black/10">
            <div class="h-full bg-sgress" style="width: {{ (($index + 1) / $total) * 100 }}%"></div>
        </div>
    </div>

    {{-- Texte de référence à lire --}}
    <div class="border border-black/10 rounded-md p-5 mb-6 bg-black/2  overflow-y-auto">
        <h2 class="text-2xl font-semibold mb-4">Compréhension du text</h2>
        <h3 class="text-base font-semibold mb-3">{{ $text->titre }}</h3>
        <div class="text-sm leading-relaxed whitespace-pre-line">{{ $text->texte }}</div>
    </div>

    <form action="{{ route('examen.text.store', ['examen' => $examen->id, 'slug' => $slug, 'text' => $text->id]) }}" method="POST">
        @csrf

        @foreach($questions as $qIndex => $question)
            <div class="border border-black/10 rounded-md p-3 mb-6">
                <div class="flex gap-3 pb-2 border-b-2 border-black/10 mb-3">
                    <div class="text-vert font-semibold bg-black/5 rounded-md w-7 h-7 flex justify-center items-center">
                        {{ $qIndex + 1 }}
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-semibold">{{ $question->enonce }}</h3>
                        <span class="text-sm rounded-full border border-black/10 text-rouge px-3">
                            {{ rtrim(rtrim(number_format($question->points, 2), '0'), '.') }} Points
                        </span>
                    </div>
                </div>

                <textarea
                    name="reponses[{{ $question->id }}]"
                    rows="4"
                    class="w-full p-3 bg-black/2 border border-black/10 rounded-md text-sm"
                    placeholder="Votre réponse..."
                >{{ $reponsesExistantes[$question->id] ?? '' }}</textarea>
            </div>
        @endforeach

        <div class="flex justify-end">
            <button type="submit" class="p-2 px-5 rounded-md bg-rouge text-white">
                {{ $index + 1 == $total ? 'Terminer' : 'Valider' }}
            </button>
        </div>
    </form>
</div>
@endsection