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
    <div class="border border-black/10 rounded-md p-5 mb-6 bg-black/2">
        <h2 class="text-2xl font-semibold mb-2">Rédaction</h2>
        <h3 class="text-lg font-semibold mb-2">{{ $redaction->titre }}</h3>
        <p class="text-base mb-3">{{ $redaction->sujet }}</p>
        @if($redaction->instruction)
            <p class="text-base text-black/50">{{ $redaction->instruction }}</p>
        @endif
        <div class="flex gap-3 mt-3">
            @if($redaction->nombre_mots_min || $redaction->nombre_mots_max)
                <span class="text-xs rounded-full border border-black/10 px-3 py-1">
                    @if($redaction->nombre_mots_min && $redaction->nombre_mots_max)
                        {{ $redaction->nombre_mots_max }} mots maximum
                    @elseif($redaction->nombre_mots_min)
                        Minimum {{ $redaction->nombre_mots_min }} mots
                    @else
                        Maximum {{ $redaction->nombre_mots_max }} mots
                    @endif
                </span>
            @endif
            <span class="text-xs rounded-full border border-black/10 text-rouge px-3 py-1">
                {{ rtrim(rtrim(number_format($redaction->note_totale, 2), '0'), '.') }} Points
            </span>
        </div>
    </div>
    @error('reponse_texte')
        <div class="mb-4 p-3 rounded-md bg-red-50 border border-rouge text-rouge text-sm">
            {{ $message }}
        </div>
    @enderror
    <div class="border border-black/10 rounded-md p-3">
        <div class="">
            <h4 class="border-b-2 border-black/10 pb-1 text-base font-semibold mb-2">Votre réponse</h4>
        </div>
        <form id="redaction-form" action="{{ route('examen.redaction.store', ['examen' => $examen->id, 'slug' => $slug, 'redaction' => $redaction->id]) }}" method="POST">
            @csrf
            <textarea
                id="reponse-texte"
                name="reponse_texte"
                rows="7"
                class="w-full p-4 border border-black/10 bg-black/2 rounded-md text-base resize-none overflow-hidden"
                placeholder="Écrivez votre réponse ici..."
                data-max="{{ $redaction->nombre_mots_max ?? '' }}"
                data-min="{{ $redaction->nombre_mots_min ?? '' }}"
                spellcheck="false"
            >{{ old('reponse_texte', $reponseExistante->reponse_texte ?? '') }}</textarea>
            <div class="flex justify-between gap-3">
                <div class="flex justify-between items-center -mt-10">
                    <span id="word-count" class="text-sm text-black/50 border-b border-black/10 px-2">0 mots</span>
                </div>
                <div class="flex justify-end mt-4">
                    <button type="submit" id="submit-btn" class="p-2 px-5 rounded-md bg-rouge text-white ">
                        {{ $index + 1 == $total ? 'Terminer' : 'Valider' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const textarea = document.getElementById('reponse-texte');
    const wordCountEl = document.getElementById('word-count');
    const form = document.getElementById('redaction-form');
    const submitBtn = document.getElementById('submit-btn');

    const max = textarea.dataset.max ? parseInt(textarea.dataset.max, 10) : null;
    const min = textarea.dataset.min ? parseInt(textarea.dataset.min, 10) : null;

    function countWords(text) {
        const trimmed = text.trim();
        if (trimmed === '') return 0;
        return trimmed.split(/\s+/).length;
    }

    function updateWordCount() {
        const count = countWords(textarea.value);

        let label = count + ' mots';
        if (max) label += ' / ' + max;
        wordCountEl.innerText = label;

        if (max && count > max) {
            wordCountEl.classList.add('text-rouge', 'font-semibold');
            wordCountEl.classList.remove('text-black/50');
        } else {
            wordCountEl.classList.remove('text-rouge', 'font-semibold');
            wordCountEl.classList.add('text-black/50');
        }
    }

    function autoResize() {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }

    textarea.addEventListener('input', function () {
        updateWordCount();
        autoResize();
    });

    updateWordCount();
    autoResize(); 

    form.addEventListener('submit', function (e) {
        const count = countWords(textarea.value);
        submitBtn.disabled = true;
        submitBtn.innerText = 'Enregistrement...';
    });
});
</script>
@endsection