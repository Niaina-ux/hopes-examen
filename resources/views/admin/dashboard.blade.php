@extends('layouts.admin-layouts.layouthead')

@section('contenue-admin')

<div class="py-3 ">
    <h2 class="text-2xl font-bold text-vert">Tableau de bord</h2>

    {{-- CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-5 mt-4">
        <a href="" class="relative bg-black/2 flex items-center rounded-xl p-4 border border-black/2">
            <div class="text-base flex-1 text-black/50 px-3 border-e border-black/5">
                Nombre d'étudiants
            </div>
            <div class="w-[40%] text-center px-2 flex items-center gap-2">
                <i class="fa-solid fa-graduation-cap"></i>
                <span class="text-3xl font-bold text-vert">{{ $totalEtudiants }}</span>
            </div>
        </a>
        <a href="" class="relative bg-black/2 flex items-center rounded-xl p-4 border border-black/2">
            <div class="text-base flex-1 text-black/50 px-3 border-e border-black/5">
                Nombre des professeurs
            </div>
            <div class="w-[40%] text-center px-2 flex items-center gap-2">
                <i class="fa-solid fa-user-tie"></i>
                <span class="text-3xl font-bold text-vert">{{ $totalProfs }}</span>
            </div>
        </a>
        <a href="" class="relative bg-black/2 flex items-center rounded-xl p-4 border border-black/2">
            <div class="text-base flex-1 text-black/50 px-3 border-e border-black/5">
                Nombre d'examens
            </div>
            <div class="w-[40%] text-center px-2 flex items-center gap-2">
                <i class="fa-solid fa-book-open-reader"></i>
                <span class="text-3xl font-bold text-vert">{{ $totalExamens }}</span>
            </div>
        </a>
        <a href="" class="relative bg-black/2 flex items-center rounded-xl p-4 border border-black/2">
            <div class="text-base flex-1 text-black/50 px-3 border-e border-black/5">
                Nombre des catégories
            </div>
            <div class="w-[40%] text-center px-2 flex items-center gap-2">
                <i class="fa-solid fa-arrows-turn-right"></i>
                <span class="text-3xl font-bold text-vert">{{ $totalCategories }}</span>
            </div>
        </a>
    </div>

    {{-- STATISTIQUE --}}
    <h3 class="text-xl font-semibold mt-6 mb-3 flex items-center gap-3">Courbe d'évaluation
        <hr class="border-2 rounded-full border-orange-400 w-[1cm] mt-1  ">
    </h3>
    {{-- COURBE PAR MOIS + MOYENNE --}}
    <div class="flex flex-col lg:flex-row gap-5">
        <div class="flex-1 min-w-0 bg-black/2 border border-black/3 rounded-xl p-2 h-[450px] lg:h-[60vh]">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                <h4 class="text-xl">Courbe d'évaluation par mois</h4>
                <form method="GET" action="{{ url()->current() }}">
                    <input type="hidden" name="mois" value="{{ $moisSelectionne }}">
                    <select name="annee" onchange="this.form.submit()"
                        class="border rounded border-black/20 bg-white p-1 text-sm">
                        @foreach($anneesDisponibles as $annee)
                            <option value="{{ $annee }}" {{ $annee == $anneeSelectionnee ? 'selected' : '' }}>
                                {{ $annee }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="bg-white p-2 rounded-md mt-2 h-[calc(100%-3.5rem)]">
                @if($statistiquesParMois->contains(fn($s) => $s['moyenne'] !== null))
                    <canvas id="chart-par-mois"></canvas>
                @else
                    <div class="h-full flex items-center justify-center text-black/40">
                        Aucune donnée pour cette année.
                    </div>
                @endif
            </div>
        </div>


        {{-- MOYENNE GENERALE --}}
        <div class="w-full lg:w-[30%] bg-black/2 border border-black/3 rounded-xl p-4 h-[450px] lg:h-[60vh]">
            <h4 class="text-xl mb-2 text-center">
                Moyenne générale
            </h4>
            @if($moyenneGenerale !== null)
                <div class="h-[calc(100%-2rem)] flex flex-col items-center justify-center">
                    <div class="w-60 h-60 max-w-full">
                        <canvas id="chart-general"></canvas>
                    </div>
                    <p class="text-2xl font-bold text-vert mt-2">
                        {{ $moyenneGenerale }}%
                    </p>
                </div>
            @else
                <div class="h-[calc(100%-2rem)] flex items-center justify-center text-black/40">
                    Pas encore de moyenne.
                </div>
            @endif
        </div>
    </div>


    {{-- COURBE PAR CATEGORIE + TOP 5 --}}
    <div class="flex flex-col lg:flex-row gap-5 mt-10">
        {{-- COURBE PAR CATEGORIE --}}
        <div class="flex-1 min-w-0 bg-black/2 border border-black/3 rounded-xl p-2 h-[450px] lg:h-[60vh]">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                <h4 class="text-xl">Courbe d'évaluation par catégorie</h4>
                <form method="GET" action="{{ url()->current() }}" class="flex gap-2">
                    <select name="mois" onchange="this.form.submit()"
                        class="border border-black/20 bg-white rounded p-1 text-sm">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $m == $moisSelectionne ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                    <select name="annee" onchange="this.form.submit()"
                        class="border border-black/20 bg-white rounded p-1 text-sm">
                        @foreach($anneesDisponibles as $annee)
                            <option value="{{ $annee }}" {{ $annee == $anneeSelectionnee ? 'selected' : '' }}>
                                {{ $annee }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="bg-white p-2 rounded-md mt-2 h-[calc(100%-3.5rem)]">
                @if($statistiquesParCategorie->isNotEmpty())
                    <canvas id="chart-par-categorie"></canvas>
                @else
                    <div class="h-full flex items-center justify-center text-black/40">
                        Aucune donnée pour ce mois.
                    </div>
                @endif
            </div>
        </div>
        {{-- TOP 5 --}}
        <div class="w-full lg:w-[30%] bg-black/2 border border-black/3 rounded-xl p-4
                    h-auto lg:h-[60vh] overflow-y-auto">
            <h4 class="text-xl mb-3">
                Top 5 des meilleurs élèves —
                {{ \Carbon\Carbon::create()->month((int) $moisSelectionne)->translatedFormat('F') }}
            </h4>

            @if($top5Eleves->isNotEmpty())
                <div class="space-y-2">
                    @foreach($top5Eleves as $index => $eleve)
                        <div class="flex items-center gap-3 p-2 rounded-md bg-white/80 border border-black/3">
                            <div class="w-8 h-8 shrink-0 rounded-full bg-black/5 flex justify-center items-center font-semibold text-sm">
                                {{ $index + 1 }}
                            </div>
                            <div class="w-8 h-8 shrink-0 rounded-full overflow-hidden">
                                <img src="{{ $eleve['image'] ? asset('images/' . $eleve['image']) : asset('images/default-avatar.png') }}"
                                    alt="" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0 truncate">
                                {{ $eleve['nom'] }}
                            </div>
                            <div class="font-semibold text-vert shrink-0">
                                {{ $eleve['moyenne'] }}%
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 text-center text-black/40">
                    Aucun élève classé pour ce mois.
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
            scales: {
                y: {
                    min: 0,
                    max: 100,
                    ticks: { callback: (val) => val + '%' }
                }
            },
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


    @if($statistiquesParCategorie->isNotEmpty())

    const categories = @json($statistiquesParCategorie->pluck('categorie'));
    const moyennesCategorie = @json($statistiquesParCategorie->pluck('moyenne'));

    function couleurSelonPourcentage(valeur) {
        if (valeur >= 70) return 'rgb(104, 167, 2)';
        if (valeur >= 50) return 'rgb(249, 115, 22)';
        return 'rgb(220, 38, 38)';
    }

    new Chart(document.getElementById('chart-par-categorie'), {
        type: 'bar',
        data: {
            labels: categories,
            datasets: [{
                label: 'Moyenne (%)',
                data: moyennesCategorie,
                backgroundColor: moyennesCategorie.map(couleurSelonPourcentage),
                borderRadius: 4,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    min: 0,
                    max: 100,
                    ticks: { callback: (val) => val + '%' }
                }
            },
        },
    });

    @endif

});
</script>

@endpush

@endsection