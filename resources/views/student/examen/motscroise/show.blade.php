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
    <h2 class="text-lg font-semibold">{{ $motscroises->titre }}</h2>
    <form id="mots-croises-form" action="{{ route('examen.motscroises.store', ['examen' => $examen->id, 'slug' => $slug, 'motscroises' => $motscroises->id]) }}" method="POST">
        @csrf
        <div class="">
            <div class="flex  border border-black/10 rounded-md bg-black/3 my-5">
                <div class="flex-1 p-2">
                    <h4 class="font-semibold mb-2 p-1 px-2 border-b-2 border-black/10 text-vert">Horizontal</h4>
                    <ul class="text-sm mb-4">
                        @foreach($mots->where('direction', 'horizontal') as $mot)
                            <li class="p-1 px-2 border-b border-black/5 {{ $loop->iteration == 2 ? 'bg-white/70 ' : '' }}"><strong>{{ $mot->numero }}.</strong> {{ $mot->indice }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="flex-1  p-2 border-s border-black/10">
                    <h4 class="font-semibold mb-2 p-1 px-2 border-b-2 border-black/10 text-vert">Vertical</h4>
                    <ul class="text-sm">
                        @foreach($mots->where('direction', 'vertical') as $mot)
                            <li class="p-1 px-2 border-b border-black/5 {{ $loop->iteration == 2 ? 'bg-white/70 ' : '' }}"><strong>{{ $mot->numero }}.</strong> {{ $mot->indice }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            {{-- Grille --}}
            <div class="flex justify-center">
                <div class="inline-block  p-2 rounded-md  border-black/20">
                    @for($y = 0; $y < $hauteur; $y++)
                        <div class="flex">
                            @for($x = 0; $x < $largeur; $x++)
                                @php $case = $grille[$y][$x]; @endphp
                                <div class="relative w-8 h-8 {{ $case['active'] ? 'border border-black/25 hover:bg-black/5 rounded bg-white' : 'border-0 bg-transparent' }}">
                                    @if($case['active'])
                                        @if($case['numero'])
                                            <span class="absolute top-0 left-0.5 text-[9px] text-black/50">{{ $case['numero'] }}</span>
                                        @endif

                                        @if($case['lettre'])
                                            {{-- Case "lettre visible" (indice) — azo vakina ihany, tsy azo ovaina --}}
                                            <input
                                                type="text"
                                                maxlength="1"
                                                value="{{ $case['lettre'] }}"
                                                readonly
                                                class="mc-case mc-lettre-visible w-full text-black/40 h-full text-center uppercase font-bold border-0 bg-vert/10 bg-black/5 focus:outline-none"
                                                data-x="{{ $x }}"
                                                data-y="{{ $y }}"
                                                data-mots-ids="{{ implode(',', $case['mots_ids']) }}"
                                            >
                                        @else
                                            {{-- Case tokony fenoin'ny étudiant --}}
                                            <input
                                                type="text"
                                                maxlength="1"
                                                class="mc-case w-full h-full text-center uppercase font-semibold border-0 bg-transparent focus:outline-none focus:bg-vert/10"
                                                data-x="{{ $x }}"
                                                data-y="{{ $y }}"
                                                data-mots-ids="{{ implode(',', $case['mots_ids']) }}"
                                            >
                                        @endif
                                    @endif
                                </div>
                            @endfor
                        </div>
                    @endfor
                </div>
            </div>

            {{-- Indices --}}
            
        </div>

        {{-- Hidden inputs générés en JS avant submit --}}
        <div id="hidden-inputs-container"></div>

        <div class="flex justify-end mt-6">
            <button type="submit" class="p-2 px-5 rounded-md bg-rouge text-white">
                {{ $index + 1 == $total ? 'Terminer' : 'Valider' }}
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const reponsesExistantes = @json($reponsesExistantes);
    const cases = document.querySelectorAll('.mc-case:not(.mc-lettre-visible)');

    // ✅ Mameno mialoha ny "case" tsirairay araka ny litera efa voatahiry (raha nisy)
    cases.forEach(function (input) {
        const motsIds = input.dataset.motsIds.split(',');
        for (const motId of motsIds) {
            if (reponsesExistantes[motId]) {
                // Maka ny litera mifanaraka amin'ity case ity avy amin'ny reponse voatahiry
                // (satria samy manana reponse_donnee feno isaky ny mot, dia mila kajy ny position)
            }
        }
    });

    // ✅ Auto-focus amin'ny case manaraka rehefa misoratra litera
    cases.forEach(function (input, index) {
        input.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
            if (this.value.length === 1) {
                const next = cases[index + 1];
                if (next) next.focus();
            }
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && this.value === '') {
                const prev = cases[index - 1];
                if (prev) prev.focus();
            }
        });
    });

    // ✅ Mandrafitra ny reponse an'ny mot tsirairay avy amin'ny litera an'ireo case rehetra alohan'ny "submit"
    document.getElementById('mots-croises-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const reponsesParMot = {};

        // Mandinika ny case rehetra araka ny x/y, mba hamorona ny reponse an'ny mot tsirairay
        const grille = @json($grille);
        const mots = @json($mots);

        mots.forEach(function (mot) {
            let reponse = '';
            const longueur = mot.reponse.length;

            for (let i = 0; i < longueur; i++) {
                const x = mot.direction === 'horizontal' ? mot.position_x + i : mot.position_x;
                const y = mot.direction === 'horizontal' ? mot.position_y : mot.position_y + i;

                const caseInput = document.querySelector(`.mc-case[data-x="${x}"][data-y="${y}"]`);
                reponse += caseInput ? caseInput.value.toUpperCase() : '';
            }

            reponsesParMot[mot.id] = reponse;
        });

        const container = document.getElementById('hidden-inputs-container');
        container.innerHTML = '';

        for (const motId in reponsesParMot) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `reponses[${motId}]`;
            input.value = reponsesParMot[motId];
            container.appendChild(input);
        }

        this.submit();
    });
});
</script>
@endsection