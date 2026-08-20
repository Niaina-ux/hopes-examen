@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
    <div class="py-3 me-2">
        <h2 class="text-2xl font-bold text-vert ">Tableau de bord</h2>

        <div class="grid grid-cols-4 gap-5 mt-4">
            <a href="" 
                class="relative bg-black/2 flex items-center  rounded-xl p-4 border border-black/2
                dark:bg-white/2 dark:border-white/3">
                <div class="text-base flex-1 px-3 border-e border-black/5">Nombre d'étudiants</div>
                <div class="w-[40%] text-center px-2 flex items-center gap-2">
                    <i class="fa-solid fa-user-graduate"></i>
                    <span class="text-3xl font-bold text-vert">{{ $totalEtudiants }} </span>
                </div>
            </a>
            <a href="" 
                class="relative bg-black/2 flex items-center  rounded-xl p-4 border border-black/2
                dark:bg-white/2 dark:border-white/3">
                <div class="text-base flex-1 px-3 border-e border-black/5">Nombre d'examens</div>
                <div class="w-[40%] text-center px-2 flex items-center gap-2">
                    <i class="fa-solid fa-book-open-reader"></i>
                    <span class="text-3xl font-bold text-vert">{{ $totalExamens }}</span>
                </div>
            </a>
        </div>

            <h3 class="text-xl font-semibold mt-6 mb-3">Statistique</h3>

        <div class="flex gap-5">
            <div 
                class="flex-1 bg-black/2 border border-black/3 rounded-xl p-4 h-[60vh]
                dark:bg-white/2 dark:border-white/3">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="text-xl">Courbe d'évaluation par mois</h4>
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
                <div class="bg-white rounded-md h-[87%] p-2
                    dark:bg-white/2">
                    @if($statistiquesParMois->contains(fn($s) => $s['moyenne'] !== null))
                        <canvas id="chart-par-mois"></canvas>
                    @else
                        <div class="h-full flex items-center justify-center text-black/40 dark:text-white/30">
                            Aucune donnée pour cette année.
                        </div>
                    @endif
                </div>
            </div>

            <div 
                class="w-[30%] bg-black/2 border border-black/3 rounded-xl p-4 h-[60vh] 
                dark:bg-white/2 dark:border-white/3">
                <h4 class="text-xl text-center mb-2">Moyenne générale</h4>
                <div class="h-65 w-65 m-auto">
                    @if($moyenneGenerale !== null)
                        <canvas id="chart-general" class="h-50 w-50"></canvas>
                        <p class="text-2xl text-center font-bold text-vert mt-2">{{ $moyenneGenerale }}%</p>
                    @else
                        <div class="flex-1 flex items-center justify-center text-black/40 h-[90%] dark:text-white/30">
                            Pas encore de moyenne.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex gap-5 my-10">
            <div 
                class="flex-1 rounded-xl bg-black/2 border border-black/3 p-4 min-h-[50vh]
                dark:bg-white/2 dark:border-white/3">
                <h3 class="text-xl mb-2">Examen pour detailer</h3>
                @forelse ($nouveauExamens as $index => $nouveauExamen)
                <div 
                    class="flex gap-3 bg-white/90 border border-black/3 p-2 rounded
                    dark:bg-white/2 dark:border-white/5">
                    <div class="font-semibold w-9 h-9 flex justify-center items-center bg-black/3 rounded-md">
                        {{$index + 1}}
                    </div>
                    <div class="flex-1">
                        <h3 class="-mt-1"> {{$nouveauExamen->titre}} </h3>
                        <p class="text-sm">Creé le {{ \Carbon\Carbon::parse($nouveauExamen->creat_at)->translatedFormat('d M Y') }}</p>
                    </div>
                    <a href="{{route('prof.examen.showtypes',[$slug, $nouveauExamen->id])}}"
                    class="text-vert"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                </div>
                @empty
                <div 
                    class="h-[37vh] flex justify-center items-center bg-white/90 rounded-md
                    dark:bg-white/2">
                    <div class="text-center">
                        <i class="fa-solid fa-box-open"></i>
                        <p>Aucun examen pour detailer</p>
                    </div>
                </div>
                @endforelse 
            </div>
            <div 
                class="flex-1 rounded-xl bg-black/2 border border-black/3 p-4 min-h-[50vh]
                dark:bg-white/2 dark:border-white/5">
                <h3 class="text-xl mb-2">Examen pour detailer</h3>
                @forelse ($examenPublies as $index => $examenPublie)
                <div 
                    class="flex gap-3 bg-white/90 border border-black/3 p-2 rounded
                    dark:bg-white/2 dark:border-white/3">
                    <div 
                        class="font-semibold w-9 h-9 flex justify-center items-center bg-black/3 rounded-md
                        dark:bg-white/3">
                        {{$index + 1}}
                    </div>
                    <div class="flex-1">
                        <h3 class=" -mt-1"> {{$examenPublie->titre}} 
                            <span class="text-sm px-2 border-2 border-black/3 text-white rounded-full {{$examenPublie->status == 'archive' ? 'bg-rouge' : 'bg-vert'}} ">
                                {{$examenPublie->status == 'archive' ? 'Archive' : 'Publié'}}
                            </span>
                        </h3>
                        <p class="text-sm">Creé le {{ \Carbon\Carbon::parse($examenPublie->creat_at)->translatedFormat('d M Y') }}</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{route('prof.examen.studentswithexamen', [$slug, $examenPublie->id])}}"><i class="fa-solid fa-user-graduate"></i></a>
                        <a href="{{route('prof.examen.showtypes',[$slug, $examenPublie->id])}}"
                        class="text-vert"><i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div 
                    class="h-[37vh] flex justify-center items-center bg-white/90 rounded-md
                    dark:bg-white/2">
                    <div class="text-center">
                        <i class="fa-solid fa-box-open"></i>
                        <p>Aucun examen detailé</p>
                    </div>
                </div>
                @endforelse 
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