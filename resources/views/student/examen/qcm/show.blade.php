@extends('layouts.student-layouts.layoutexamen')
@section('exercice-content')
<div class="">
    <div class="">
        <div class="my-10">
            <div class="flex justify-between items-center">
                <span>Question</span>
                <span>{{ $index + 1 }}/{{ $total }}</span>
            </div>
            <div class="rounded-full h-3 overflow-hidden bg-black/10">
                <div class="h-full bg-sgress" style="width: {{ (($index + 1) / $total) * 100 }}%"></div>
            </div>
        </div>
        <div class="">
            @if($errors->any())
                <div class="mb-4 p-3 rounded-md bg-red-50 border border-rouge text-rouge text-sm">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="flex justify-between gap-5">
                <div class="flex-1 relative pb-5">
                    <h3 class="mb-2 text-base font-semibold">{{ $question->enonce }}</h3>
                    <div class="flex gap-3">
                        <span class="inline-block text-xs px-3 py-1 rounded-full border
                            {{ $question->reponse_type === 'multiple' ? 'border-rouge text-rouge' : 'border-vert text-vert' }}">
                            <i class="fa-solid {{ $question->reponse_type === 'multiple' ? 'fa-list-check' : 'fa-circle-dot' }} me-1"></i>
                            {{ $question->reponse_type === 'multiple' ? 'Réponse multiple' : 'Réponse simple' }}
                        </span>
                        @if($question->duree_seconde)
                            <div id="question-timer" class="border border-rouge text-rouge px-3 py-1 rounded-full w-[1.6cm] text-center text-sm font-bold">
                                --:--
                            </div>
                        @endif
                    </div>
                    @if($question->reponse_type === 'multiple')
                        <p class="text-xs text-gray-500 mt-1">
                            Sélectionnez {{ $question->qcmChoices->where('est_correcte', true)->count() }} réponse(s) maximum.
                        </p>
                    @endif
                </div>

                {{-- ✅ Manisy image NA video, arakaraka izay misy --}}
                @if($question->image)
                    <div class="flex-1">
                        <img src="{{ asset('images/questions/' . $question->image) }}" alt=""
                            class="w-full border border-black/10 rounded-md overflow-hidden mb-2">
                    </div>
                @elseif($question->video)
                    <div class="flex-1">
                        <video controls class="w-full border border-black/10 rounded-md overflow-hidden mb-2">
                            <source src="{{ asset('videos/questions/' . $question->video) }}" type="video/mp4">
                            Votre navigateur ne supporte pas la lecture de cette vidéo.
                        </video>
                    </div>
                @endif
            </div>

            <div class="border-t-2 rounded-md border-black/10 my-2 py-3 shadow">
                <form id="qcm-form" action="{{ route('examen.qcm.answer', ['examen' => $examen->id, 'slug' => $examen->categorie->slug, 'qcm' => $qcm->id]) }}?q={{ $index }}" method="POST" class="p-4">
                    @csrf
                    <input type="hidden" name="question_id" value="{{ $question->id }}">
                    <input type="hidden" name="index" value="{{ $index }}">
                    <input type="hidden" name="timeout" id="timeout-input" value="0">

                    @foreach($question->qcmChoices as $choice)
                        <label class="flex gap-3 py-2 border-b border-black/10">
                            <input type="{{ $question->reponse_type === 'multiple' ? 'checkbox' : 'radio' }}"
                                name="{{ $question->reponse_type === 'multiple' ? 'choice_ids[]' : 'choice_id' }}"
                                value="{{ $choice->id }}"
                                @if($question->reponse_type === 'multiple')
                                    class="qcm-multiple-checkbox"
                                    data-max="{{ $question->qcmChoices->where('est_correcte', true)->count() }}"
                                @endif
                            >
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

@if($question->reponse_type === 'multiple')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.qcm-multiple-checkbox');

    function updateCheckboxesState() {
        const max = parseInt(checkboxes[0]?.dataset.max, 10) || 0;
        const checkedCount = document.querySelectorAll('.qcm-multiple-checkbox:checked').length;

        checkboxes.forEach(function (checkbox) {
            if (!checkbox.checked) {
                checkbox.disabled = checkedCount >= max;
            } else {
                checkbox.disabled = false;
            }
        });
    }

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', updateCheckboxesState);
    });

    updateCheckboxesState();
});
</script>
@endif

@if($question->duree_seconde)
<script>
document.addEventListener('DOMContentLoaded', function () {
    let secondesQuestion = {{ $question->duree_seconde }};
    const timerEl = document.getElementById('question-timer');
    const form = document.getElementById('qcm-form');
    const timeoutInput = document.getElementById('timeout-input');
    let dejaEnvoye = false;

    function updateQuestionTimer() {
        const minutes = Math.floor(secondesQuestion / 60);
        const secondes = secondesQuestion % 60;
        timerEl.innerText = String(minutes).padStart(2, '0') + ':' + String(secondes).padStart(2, '0');

        if (secondesQuestion <= 0) {
            clearInterval(questionTimerInterval);
            if (!dejaEnvoye) {
                dejaEnvoye = true;
                timeoutInput.value = '1';
                form.submit();
            }
            return;
        }

        secondesQuestion--;
    }

    updateQuestionTimer();
    const questionTimerInterval = setInterval(updateQuestionTimer, 1000);
});
</script>
@endif
@endsection