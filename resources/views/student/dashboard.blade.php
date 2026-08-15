@extends('layouts.student-layouts.layouthead')
@section('contenue-student')
<section class="pt-20">
    <div class="container py-6 sm:py-8 lg:py-10">
        {{-- TITRE --}}
        <div class="flex items-center mb-5">
            <h2 class="font-bold text-xl sm:text-2xl flex items-center gap-3">
                Courbe d'évaluation
                <hr class="border-2 border-black/40 mt-1 rounded-full w-16 sm:w-[2cm]">
            </h2>
        </div>
        {{-- GRAPHIQUES --}}
        <div class="flex flex-col lg:flex-row gap-5
                    min-h-0 lg:h-[70vh]">
            {{-- Courbe --}}
            <div class="bg-black/[0.02] border border-black/[0.03]
                        rounded-xl p-3 sm:p-4
                        flex-1 min-w-0
                        min-h-[350px] lg:h-full">
                <h4 class="font-normal text-lg sm:text-xl mb-3">
                    Évolution par examen
                </h4>
                @if($statistiques->isNotEmpty())
                    <div id="chart-scroll-container"
                         class="overflow-x-auto overflow-y-hidden
                                bg-white/90 p-2 rounded-md
                                h-[300px] sm:h-[350px] lg:h-[calc(100%-2rem)]">
                        <canvas id="chart-par-examen"></canvas>
                    </div>
                @else
                    <div class="h-[300px] lg:h-full flex items-center justify-center
                                text-black/40 text-center px-5">
                        Aucun examen corrigé pour l'instant.
                    </div>
                @endif
            </div>
            {{-- Moyenne générale --}}
            <div class="w-full lg:w-[30%]
                        min-h-[300px] lg:h-full
                        rounded-xl p-4
                        bg-black/[0.02]
                        border border-black/[0.03]
                        flex flex-col items-center justify-center">
                <h4 class="font-normal text-lg sm:text-xl text-center mb-3">
                    Moyenne générale
                </h4>
                @if($moyenneGenerale !== null)
                    <div class="relative w-full max-w-[260px] h-[220px] sm:h-[250px]">
                        <canvas id="chart-general"></canvas>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-vert mt-3">
                        {{ $moyenneGenerale }}%
                    </p>
                @else
                    <div class="flex-1 flex items-center justify-center
                                text-black/40 text-center">
                        Pas encore de moyenne.
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- EXAMENS --}}
<section>
    <div class="container flex flex-col lg:flex-row gap-5 pb-8 sm:pb-10">
        {{-- EXAMENS PLANIFIÉS --}}
        <div class="flex-1 min-w-0
                    min-h-60
                    border border-black/[0.03]
                    bg-black/[0.02]
                    rounded-md p-3 sm:p-4">
            <h3 class="font-semibold text-lg sm:text-xl mb-3">
                Examens planifiés
            </h3>
            @php
                $today = \Carbon\Carbon::today();
            @endphp
            <div class="space-y-2">
                @forelse ($examen_planifie as $examen)
                    @php
                        $isFirst = $loop->first;
                        $isExpired = \Carbon\Carbon::parse($examen->date_examen)->lt($today);
                        $canStart = \Carbon\Carbon::parse($examen->date_examen)->isToday()
                            || \Carbon\Carbon::parse($examen->date_examen)->isPast();
                    @endphp
                    <div class="border border-black/5
                                rounded-md p-2 sm:p-3
                                flex items-start gap-3
                                bg-black/[0.02]
                                min-w-0">
                        {{-- Numéro --}}
                        <div class="w-9 h-9 sm:w-10 sm:h-10
                                    shrink-0
                                    rounded-md bg-black/5
                                    flex justify-center items-center
                                    font-semibold text-sm sm:text-base">
                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </div>
                        {{-- Informations --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row
                                        sm:items-center gap-1 sm:gap-2">
                                <h4 class="font-medium text-sm sm:text-base
                                           truncate">
                                    {{ $examen->examen->titre }}
                                </h4>
                                <span class="self-start
                                             border border-black/10
                                             rounded-full
                                             text-xs text-white
                                             px-2 py-0.5
                                             whitespace-nowrap
                                             {{ $canStart ? 'bg-vert' : 'bg-black/40' }}">
                                    {{ $canStart ? 'Commencez' : 'En attente' }}
                                </span>
                            </div>
                            <div class="mt-1 text-xs sm:text-sm">
                                <span>
                                    Date d'examen :
                                </span>
                                <span class="{{ $isExpired ? 'text-red-600' : 'text-rouge' }}">
                                    {{ \Carbon\Carbon::parse($examen->date_examen)->translatedFormat('d M Y') }}
                                </span>
                            </div>
                            @if($isExpired)
                                <p class="text-xs mt-1 text-black/60">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    La date prévue de cet examen est dépassée.
                                </p>
                            @endif
                        </div>
                        {{-- Action --}}
                        @if($isFirst)
                            <a href="{{ route('student.examen.show', $examen->examen->categorie->nom) }}"
                               class="w-8 h-8 shrink-0
                                      bg-white rounded-md
                                      flex justify-center items-center
                                      text-vert
                                      border border-black/5">
                                <i class="fa-solid fa-arrow-up-right-from-square text-sm"></i>
                            </a>
                        @else
                            <span class="w-8 h-8 shrink-0
                                         bg-gray-100 rounded-md
                                         flex justify-center items-center
                                         text-gray-400
                                         cursor-not-allowed">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </span>
                        @endif
                    </div>
                @empty
                    <div class="p-10 sm:p-16
                                bg-white/90
                                border border-black/[0.03]
                                rounded-md
                                text-center text-black/50">
                        <i class="fa-solid fa-box-open text-xl"></i>
                        <p class="mt-2">
                            Aucun !
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
        {{-- EXAMENS TERMINÉS --}}
        <div class="flex-1 min-w-0
                    min-h-60
                    bg-black/[0.03]
                    border border-black/[0.03]
                    rounded-md p-3 sm:p-4">
            <h3 class="font-semibold text-lg sm:text-xl mb-3">
                Mes examens terminés
            </h3>
            <div class="space-y-2">
                @forelse ($attempts as $attempt)
                    <div class="border border-black/5
                                rounded-md p-2 sm:p-3
                                flex items-start gap-3
                                bg-white/80
                                min-w-0">
                        {{-- Numéro --}}
                        <div class="w-9 h-9 sm:w-10 sm:h-10
                                    shrink-0
                                    rounded-md bg-black/5
                                    flex justify-center items-center
                                    font-semibold text-sm">
                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </div>
                        {{-- Informations --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row
                                        sm:items-center gap-1 sm:gap-2">
                                <h4 class="font-medium text-sm sm:text-base truncate">
                                    {{ $attempt->examen->titre }}
                                </h4>
                                <span class="self-start
                                             border border-black/10
                                             rounded-full
                                             text-xs text-white
                                             px-2 py-0.5
                                             whitespace-nowrap
                                             {{ $attempt->status == 'corrige'
                                                ? 'bg-rouge'
                                                : 'bg-vert' }}">
                                    {{ $attempt->status == 'termine'
                                        ? 'Terminé'
                                        : 'Corrigé' }}
                                </span>
                            </div>
                            <div class="mt-1 text-xs sm:text-sm">
                                Finis le
                                {{ \Carbon\Carbon::parse($attempt->date_fin)
                                    ->translatedFormat('d M Y \à H\hi') }}
                            </div>
                        </div>
                        {{-- Action --}}
                        <a href="{{ route('student.examen.historique.show', $attempt->id) }}"
                           class="w-8 h-8 shrink-0
                                  bg-black/[0.03]
                                  rounded-md
                                  flex justify-center items-center
                                  text-vert">
                            <i class="fa-solid fa-arrow-up-right-from-square text-sm"></i>
                        </a>
                    </div>
                @empty
                    <div class="p-10 sm:p-16
                                bg-white/90
                                border border-black/[0.03]
                                rounded-md
                                text-center text-black/50">
                        <i class="fa-solid fa-box-open text-xl"></i>
                        <p class="mt-2">
                            Aucun !
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>


@if($statistiques->isNotEmpty())
<script>
document.addEventListener('DOMContentLoaded', function () {

    const dates = @json($statistiques->pluck('date'));
    const data = @json($statistiques->pluck('pourcentage'));

    function couleurSelonPourcentage(valeur) {
        if (valeur >= 80) return 'rgb(104, 167, 2)';
        if (valeur >= 70) return 'rgb(104, 167, 2)';
        if (valeur >= 50) return 'rgb(160, 248, 194)';
        if (valeur >= 40) return 'rgb(92, 91, 91)';
        if (valeur >= 30) return 'rgb(249, 115, 22)';

        return 'rgb(220, 38, 38)';
    }

    const datesAvecDepart = ['', ...dates];
    const dataAvecDepart = [0, ...data];

    const couleurs = [
        'rgba(0,0,0,0)',
        ...data.map(couleurSelonPourcentage)
    ];
    const container = document.getElementById('chart-scroll-container');
    const canvas = document.getElementById('chart-par-examen');
    const largeurMaxParPoint = 37.8;
    const largeurContainer = container.clientWidth;
    const largeurSouhaitee =
        dataAvecDepart.length * largeurMaxParPoint;
    canvas.style.width =
        Math.max(largeurContainer, largeurSouhaitee) + 'px';
    canvas.style.height = '100%';
    new Chart(canvas, {
        type: 'line',
        data: {
            labels: datesAvecDepart,
            datasets: [{
                label: 'Pourcentage (%)',
                data: dataAvecDepart,
                borderColor: 'rgb(104, 167, 2)',
                backgroundColor: 'rgba(104, 167, 2, 0.08)',
                pointBackgroundColor: couleurs,
                pointBorderColor: couleurs,
                pointRadius: [
                    0,
                    ...data.map(() => 5)
                ],
                pointHoverRadius: [
                    0,
                    ...data.map(() => 7)
                ],
                tension: 0.3,
                fill: true,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    min: 0,
                    max: 100,
                    ticks: {
                        callback: (val) => val + '%'
                    }
                }
            }
        }
    });
    container.scrollLeft = container.scrollWidth;
});
</script>
@endif

@if($moyenneGenerale !== null)
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Chart(
        document.getElementById('chart-general'),
        {
            type: 'doughnut',
            data: {
                labels: [
                    'Obtenu',
                    'Restant'
                ],
                datasets: [{
                    data: [
                        {{ $moyenneGenerale }},
                        {{ 100 - $moyenneGenerale }}
                    ],
                    backgroundColor: [
                        'rgb(104, 167, 2)',
                        'rgb(230, 230, 230)'
                    ],
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                cutout: '70%',
            }
        }
    );
});
</script>
@endif
@endpush
@endsection