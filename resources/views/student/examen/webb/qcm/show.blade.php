@extends('layouts.student-layouts.layoutexamen')
@section('exercice-content')
<div class="">
    <div class="">
        <div class="my-10">
            <div class="flex justify-between">
                <span>Question</span>
                <span>{{ $index + 1 }}/{{ $total }}</span>
            </div>
            <div class="rounded-full h-3 overflow-hidden bg-black/10">
                <div class="h-full bg-sgress" style="width: {{ (($index + 1) / $total) * 100 }}%"></div>
            </div>
        </div>
        <div class="">
            <div class="flex justify-between gap-5">
                <div class="flex-1 relative pb-5">
                    <h3 class="mb-2 text-base font-semibold">{{ $question->enonce }}</h3>
                    <span class="inline-block text-xs px-3 py-1 rounded-full border
                        {{ $question->reponse_type === 'multiple' ? 'border-rouge text-rouge' : 'border-vert text-vert' }}">
                        <i class="fa-solid {{ $question->reponse_type === 'multiple' ? 'fa-list-check' : 'fa-circle-dot' }} me-1"></i>
                        {{ $question->reponse_type === 'multiple' ? 'Réponse multiple' : 'Réponse simple' }}
                    </span>
                </div>
                @if($question->image)
                    <div class="flex-1">
                        <img src="{{ asset('images/questions/' . $question->image) }}" alt=""
                            class="w-full border border-black/10 rounded-md overflow-hidden mb-2">
                    </div>
                @endif
            </div>

            <div class="border-t-2 rounded-md border-black/10 my-2 py-3 shadow">
                <form action="{{ route('student.examen.web.qcm.answer', [$examen->id, $qcmWeb->id]) }}?q={{ $index }}" method="POST" class="p-4">
                    @csrf
                    <input type="hidden" name="question_id" value="{{ $question->id }}">

                    @foreach($question->qcmWebChoices as $choice)
                        <label class="flex gap-3 py-2 border-b border-black/10">
                            <input type="{{ $question->reponse_type === 'multiple' ? 'checkbox' : 'radio' }}"
                                name="choice_ids[]" value="{{ $choice->id }}">
                            <p>{{ $choice->texte }}</p>
                        </label>
                    @endforeach

                    <div class="flex justify-end mt-7">
                        <button type="submit" class="p-2 px-5 rounded-md bg-rouge text-white">
                            {{ $index + 1 == $total ? 'Terminer' : 'Valider' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-sgress {
        background: linear-gradient(rgb(80, 80, 80), rgb(160, 160, 160), rgb(104, 104, 104));
    }
</style>
@endsection

