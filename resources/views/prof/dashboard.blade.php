@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
    <div class="py-3 me-2">
        <h2 class="text-2xl font-bold text-vert ">Tableau de bord</h2>

        <div class="grid grid-cols-4 gap-5 mt-4">
            <a href="" class="relative bg-black/2 flex items-center  rounded-xl p-4 border border-black/2">
                <div class="text-base flex-1 text-black/50 px-3 border-e border-black/5">Nombre d'étudiants</div>
                <div class="w-[40%] text-center px-2 flex items-center gap-2">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <span class="text-3xl font-bold text-vert">{{ $totalEtudiants }} </span>
                </div>
            </a>
            <a href="" class="relative bg-black/2 flex items-center  rounded-xl p-4 border border-black/2">
                <div class="text-base flex-1 text-black/50 px-3 border-e border-black/5">Nombre d'examens</div>
                <div class="w-[40%] text-center px-2 flex items-center gap-2">
                    <i class="fa-solid fa-book-open-reader"></i>
                    <span class="text-3xl font-bold text-vert">{{ $totalExamens }}</span>
                </div>
            </a>
        </div>

        <h3 class="text-xl font-semibold mt-6 mb-3">Statistique</h3>

        <div class="flex gap-5">
            <div class="flex-1 bg-black/2 border border-black/3 rounded-md p-4 h-[60vh]">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-semibold">Courbe d'évaluation par mois</h4>
                    <form method="GET" action="{{ url()->current() }}">
                        <select name="annee" onchange="this.form.submit()" class="border rounded p-1 text-sm">
                            @foreach($anneesDisponibles as $annee)
                                <option value="{{ $annee }}" {{ $annee == $anneeSelectionnee ? 'selected' : '' }}>
                                    {{ $annee }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="bg-white rounded-md h-[80%] p-2">
                    @if($statistiquesParMois->contains(fn($s) => $s['moyenne'] !== null))
                        <canvas id="chart-par-mois"></canvas>
                    @else
                        <div class="h-full flex items-center justify-center text-black/40">
                            Aucune donnée pour cette année.
                        </div>
                    @endif
                </div>
            </div>

            <div class="w-[30%] bg-black/3 border border-black/3 rounded-md p-4 h-[50vh] flex flex-col items-center justify-center">
                <h4 class="font-semibold mb-2 self-start">Moyenne générale</h4>
                @if($moyenneGenerale !== null)
                    <canvas id="chart-general" class="max-h-[80%]"></canvas>
                    <p class="text-2xl font-bold text-vert mt-2">{{ $moyenneGenerale }}%</p>
                @else
                    <div class="flex-1 flex items-center justify-center text-black/40">
                        Pas encore de moyenne.
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        @if($statistiquesParMois->contains(fn($s) => $s['moyenne'] !== null))
        const mois = @json($statistiquesParMois->pluck('mois'));
        const moyennesMois = @json($statistiquesParMois->pluck('moyenne'));

        new Chart(document.getElementById('chart-par-mois'), {
            type: 'line',
            data: {
                labels: mois,
                datasets: [{
                    label: 'Moyenne (%)',
                    data: moyennesMois,
                    borderColor: 'rgb(104, 167, 2)',
                    backgroundColor: 'rgba(104, 167, 2, 0.08)',
                    tension: 0.3,
                    fill: true,
                    spanGaps: true,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { min: 0, max: 100, ticks: { callback: (val) => val + '%' } } },
            },
        });
        @endif

        @if($moyenneGenerale !== null)
        new Chart(document.getElementById('chart-general'), {
            type: 'doughnut',
            data: {
                labels: ['Obtenu', 'Restant'],
                datasets: [{
                    data: [{{ $moyenneGenerale }}, {{ 100 - $moyenneGenerale }}],
                    backgroundColor: ['rgb(104, 167, 2)', 'rgb(230, 230, 230)'],
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
            },
        });
        @endif
    });
    </script>
    @endpush
@endsection