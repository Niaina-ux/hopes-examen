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
    @if($errors->any())
        <div class="mb-4 p-3 rounded-md bg-red-50 border border-rouge text-rouge text-sm">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
    <form id="relier-form" action="{{ route('examen.relier.store', ['examen' => $examen->id, 'slug' => $slug, 'relier' => $relier->id]) }}" method="POST">
        @csrf
        @foreach($questions as $qIndex => $question)
            <div class="relier-question my-7" data-question-id="{{ $question->id }}" data-nb-paires="{{ $question->paires->count() }}">
                <div class="flex gap-3 pb-2 border-b-2 border-black/10 mb-4">
                    <div class="text-vert font-semibold bg-black/5 rounded-md w-7 h-7 flex justify-center items-center">
                        {{ $qIndex + 1 }}
                    </div>
                    <div class="">
                        <h3 class="text-base font-semibold">{{ $question->enonce }}</h3>
                        <div class="text-sm flex gap-2">
                            <span class="rounded-full border border-black/10 text-rouge px-3">
                                {{ rtrim(rtrim(number_format($question->points, 2), '0'), '.') }} Points
                            </span>
                            <span class="rounded-full border border-black/10 text-vert px-3">
                                {{ $question->paires->count() }} {{ $question->paires->count() > 1 ? 'paires' : 'paire' }} à relier
                            </span>
                        </div>
                    </div>
                </div>
                <div class="relative flex justify-between gap-15 my-2">
                    {{-- Colonne gauche --}}
                    <div class="flex-1 flex flex-col gap-3">
                        @foreach($question->paires_gauche as $paire)
                            <button type="button"
                                class="relier-item relier-gauche text-left px-3 py-1 border rounded-md border-black/10 hover:border-vert transition"
                                data-paire-id="{{ $paire->id }}"
                                data-side="gauche">
                                {{ $paire->element_gauche }}
                                @if($paire->image_gauche)
                                    <img src="{{ asset('images/relier/' . $paire->image_gauche) }}" class="w-full mt-2 rounded" alt="">
                                @endif
                            </button>
                        @endforeach
                    </div>
                    {{-- SVG pour les traits --}}
                    <svg class="absolute top-0 left-0 w-full h-full pointer-events-none" style="z-index: 1;">
                        <g class="lignes-container" data-question-id="{{ $question->id }}"></g>
                    </svg>
                    {{-- Colonne droite (mifangaro) --}}
                    <div class="flex-1 flex flex-col gap-3">
                        @foreach($question->paires_droite as $paire)
                            <button type="button"
                                class="relier-item relier-droite text-left px-3 py-1 border rounded-md border-black/10 hover:border-vert transition"
                                data-paire-id="{{ $paire->id }}"
                                data-side="droite">
                                {{ $paire->element_droite }}
                                @if($paire->image_droite)
                                    <img src="{{ asset('images/relier/' . $paire->image_droite) }}" class="w-full mt-2 rounded" alt="">
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="hidden-inputs-container" data-question-id="{{ $question->id }}"></div>
            </div>
        @endforeach
        <div class="flex justify-end mt-7">
            <button type="submit" class="p-2 px-5 rounded-md bg-rouge text-white">
                {{ $index + 1 == $total ? 'Terminer' : 'Valider' }}
            </button>
        </div>
    </form>
</div>

<style>
    .relier-item.selected {
        border-color: #03a811 !important;
        background-color: rgba(38, 220, 53, 0.05);
    }
    .relier-item.linked {
        border-color: #a5a5a5 !important;
        background-color: rgba(160, 160, 160, 0.05);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const liaisons = {};

    document.querySelectorAll('.relier-question').forEach(function (questionEl) {
        const questionId = questionEl.dataset.questionId;
        liaisons[questionId] = {};

        let selectionGauche = null;

        const items = questionEl.querySelectorAll('.relier-item');

        items.forEach(function (item) {
            item.addEventListener('click', function () {
                const side = this.dataset.side;
                const paireId = this.dataset.paireId;

                if (side === 'gauche') {
                    items.forEach(i => { if (i.dataset.side === 'gauche') i.classList.remove('selected'); });

                    if (this.classList.contains('linked')) {
                        delete liaisons[questionId][paireId];
                        this.classList.remove('linked');
                        redessinerLignes(questionId, questionEl);
                        updateHiddenInputs(questionId, questionEl);
                    }

                    this.classList.add('selected');
                    selectionGauche = paireId;

                } else if (side === 'droite' && selectionGauche !== null) {
                    const ancienDroiteId = liaisons[questionId][selectionGauche];
                    if (ancienDroiteId) {
                        const ancienDroiteEl = questionEl.querySelector(`.relier-droite[data-paire-id="${ancienDroiteId}"]`);
                        if (ancienDroiteEl) ancienDroiteEl.classList.remove('linked');
                    }

                    for (const g in liaisons[questionId]) {
                        if (liaisons[questionId][g] === paireId) {
                            delete liaisons[questionId][g];
                            const gEl = questionEl.querySelector(`.relier-gauche[data-paire-id="${g}"]`);
                            if (gEl) gEl.classList.remove('linked');
                        }
                    }

                    liaisons[questionId][selectionGauche] = paireId;

                    const gaucheEl = questionEl.querySelector(`.relier-gauche[data-paire-id="${selectionGauche}"]`);
                    gaucheEl.classList.remove('selected');
                    gaucheEl.classList.add('linked');
                    this.classList.add('linked');

                    selectionGauche = null;

                    redessinerLignes(questionId, questionEl);
                    updateHiddenInputs(questionId, questionEl);
                }
            });
        });
    });

    function redessinerLignes(questionId, questionEl) {
        const svg = questionEl.querySelector('.lignes-container');
        svg.innerHTML = '';

        for (const gaucheId in liaisons[questionId]) {
            const droiteId = liaisons[questionId][gaucheId];
            const gaucheEl = questionEl.querySelector(`.relier-gauche[data-paire-id="${gaucheId}"]`);
            const droiteEl = questionEl.querySelector(`.relier-droite[data-paire-id="${droiteId}"]`);

            if (!gaucheEl || !droiteEl) continue;

            const containerRect = questionEl.querySelector('svg').getBoundingClientRect();
            const gaucheRect = gaucheEl.getBoundingClientRect();
            const droiteRect = droiteEl.getBoundingClientRect();

            const x1 = gaucheRect.right - containerRect.left;
            const y1 = gaucheRect.top + gaucheRect.height / 2 - containerRect.top;
            const x2 = droiteRect.left - containerRect.left;
            const y2 = droiteRect.top + droiteRect.height / 2 - containerRect.top;

            const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            line.setAttribute('x1', x1);
            line.setAttribute('y1', y1);
            line.setAttribute('x2', x2);
            line.setAttribute('y2', y2);
            line.setAttribute('stroke', '#027c0c');
            line.setAttribute('stroke-width', '2');
            svg.appendChild(line);
        }
    }

    function updateHiddenInputs(questionId, questionEl) {
        const container = questionEl.closest('.relier-question').querySelector('.hidden-inputs-container');
        container.innerHTML = '';

        for (const gaucheId in liaisons[questionId]) {
            const droiteId = liaisons[questionId][gaucheId];

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `liaisons[${gaucheId}]`;
            input.value = droiteId;
            container.appendChild(input);
        }
    }
});
</script>
@endsection