@extends('layouts.student-layouts.layouthead')
@section('contenue-student')
<section class="pt-15">
    <div class="container py-10">
        <div class="flex justify-between gap-5 h-[70vh]">
            <div class="bg-black/3 rounded-md p-2 flex-1 h-full">
                <h4 class="font-semibold mb-2">Évolution par examen</h4>
                @if($statistiques->isNotEmpty())
                    <div id="chart-scroll-container" class="overflow-x-auto h-[calc(100%-2rem)]">
                        <canvas id="chart-par-examen"></canvas>
                    </div>
                @else
                    <div class="h-full flex items-center justify-center text-black/40">
                        Aucun examen corrigé pour l'instant.
                    </div>
                @endif
            </div>
            <div class="w-[30%] rounded-md p-2 bg-black/3 flex flex-col items-center justify-center">
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
</section>


<section>
    <div class="container  flex gap-5 pb-10">
        <div class="flex-1 min-h-60 border border-black/3 bg-black/2 rounded-md p-4">
            <h3 class="font-semibold text-xl mb-3">Examens planifiés</h3>
            @php
                $today = \Carbon\Carbon::today();
            @endphp
            @forelse ($examen_planifie as $examen)
                @php
                    $isFirst = $loop->first;
                    $isExpired = \Carbon\Carbon::parse($examen->date_examen)->lt($today);
                @endphp
                <div class="border border-black/5 rounded-md p-2 flex gap-4 bg-black/2">
                    <div class="w-10 h-10 rounded-md bg-black/5 flex justify-center items-center font-semibold">
                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                    </div>
                    <div class="flex-1">
                        <h4>
                            {{ $examen->examen->titre }}
                           @php
                                $canStart = \Carbon\Carbon::parse($examen->date_examen)->isToday()
                                    || \Carbon\Carbon::parse($examen->date_examen)->isPast();
                            @endphp
                            <span class="border border-black/10 rounded-full text-sm text-white px-2 {{ $canStart ? 'bg-vert' : 'bg-orange-500' }}">
                                {{ $canStart ? 'Commencez' : 'En attente' }}
                            </span>
                        </h4>
                        <div class="flex gap-3 text-sm">
                            <span>
                                Date d'examen :
                                <span class="{{ $isExpired ? 'text-red-600 ' : 'text-rouge' }}">
                                    {{ \Carbon\Carbon::parse($examen->date_examen)->translatedFormat('d M Y') }}
                                </span>
                            </span>
                        </div>
                        @if($isExpired)
                            <p class=" text-xs mt-1">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                La date prévue de cet examen est dépassée.
                            </p>
                        @endif
                    </div>
                    @if($isFirst)
                        <a href="{{ route('student.examen.show', $examen->examen->categorie->nom) }}"
                        class="w-8 h-8 bg-white rounded-md flex justify-center items-center text-vert">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    @else
                        <span class="w-8 h-8 bg-gray-100 rounded-md flex justify-center items-center text-gray-400 cursor-not-allowed">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                    @endif
                </div>
            @empty
                <div class="p-10 py-20 bg-white/90 border border-black/3 rounded-md text-center">
                    <i class="fa-solid fa-box-open"></i>
                    <p>Aucun !</p>
                </div>
            @endforelse
        </div>
        <div class="flex-1 min-h-60 bg-black/3 border border-black/3 rounded-md p-4">
            <h3 class="font-semibold text-xl mb-3">Mes examens terminés</h3>
            @forelse ($attempts as $attempt)
                <div class=" border border-black/5 rounded-md p-2 flex gap-4 bg-white/80">
                    <div class="w-10 h-10 rounded-md bg-black/5 flex justify-center items-center font-semibold">
                        01
                    </div>
                    <div class="flex-1">
                        <h4>{{$attempt->examen->titre}}
                            <span class="border border-black/10 rounded-full text-sm text-white px-2 
                            {{$attempt->status == 'corrige' ? 'bg-rouge' : 'bg-vert'}}">
                                {{$attempt->status == 'termine' ? 'Terminé' : 'Corrigé'}}
                            </span></h4>
                        <div class="flex gap-3 text-sm">
                            <span> Finis le {{\Carbon\Carbon::parse($attempt->date_fin)->translatedFormat('d M Y \à H\hi');}} </span>
                        </div>
                    </div>
                    <a href="{{route('student.examen.historique.show', $attempt->id)}}" class="w-8 h-8 bg-black/3 rounded-md flex justify-center items-center text-vert">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                </div>
            @empty
                <div class="p-10 py-20 bg-white/90 border-black/3 rounded-md text-center">
                    <i class="fa-solid fa-box-open"></i>
                    <p>Aucun !</p>
                </div>
            @endforelse
            
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
    const couleurs = ['rgba(0,0,0,0)', ...data.map(couleurSelonPourcentage)];

    const container = document.getElementById('chart-scroll-container');
    const canvas = document.getElementById('chart-par-examen');

    const largeurMaxParPoint = 37.8;
    const largeurContainer = container.clientWidth;
    const largeurSouhaitee = dataAvecDepart.length * largeurMaxParPoint;
    canvas.style.width = Math.max(largeurContainer, largeurSouhaitee) + 'px';
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
                pointRadius: [0, ...data.map(() => 5)],
                pointHoverRadius: [0, ...data.map(() => 7)],
                tension: 0.3,
                fill: true,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { min: 0, max: 100, ticks: { callback: (val) => val + '%' } },
            },
        },
    });

    container.scrollLeft = container.scrollWidth;
});
</script>
@endif

@if($moyenneGenerale !== null)
<script>
document.addEventListener('DOMContentLoaded', function () {
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
});
</script>
@endif
@endpush
@endsection