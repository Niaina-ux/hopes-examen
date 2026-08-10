@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
    <div class="py-3">
        @include('layouts.admin-layouts.examen.layout-exam')
        @if(session('success'))
            <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md my-4 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('success-alert').remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        <div class="">
            @forelse($exercices as $index => $exercice)
                <div class="flex gap-4 justify-betwee my-2 p-2 border border-black/10 rounded-md">
                    <div class="w-15 h-15 rounded-md bg-black/3 flex justify-center items-center">
                        <span class="font-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between gap-3 items-start pb-1">
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold"> {{ $exercice->titre }} </h3>
                                <div class="text-sm flex gap-3 mt-1">
                                    <span class="border border-black/10 rounded-full px-3 text-vert">
                                        {{ $exercice->questions->count() }} question(s)
                                    </span>
                                    @if($exercice->duree_minutes)
                                        <span class="border border-black/10 rounded-full px-3">
                                            {{ $exercice->duree_minutes }} min
                                        </span>
                                    @endif
                                    <span class="border border-black/10 rounded-full px-3 text-rouge">
                                        {{ $exercice->note_totale }} Points
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <a href="{{ route('prof.examen.glisserdeposer.edit', [$slug, $examen->id, $exercice->id]) }}" class="text-black/60">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('prof.examen.glisserdeposer.destroy', [$slug, $examen->id, $exercice->id]) }}" method="POST" onsubmit="return confirm('Supprimer {{ $exercice->titre }} ? Cette action supprimera aussi toutes ses questions.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                            <a href="{{ route('prof.examen.glisserdeposer.question.create', [$slug, $examen->id, $exercice->id]) }}" class="p-1 px-2 rounded-md bg-vert text-white">
                                + Créer question
                            </a>
                        </div>
                        <div class="mt-2 p-2 px-3 bg-black/3 rounded-md">
                            @forelse($exercice->questions as $qIndex => $question)
                            <div class="flex gap-3">
                                <div class="w-10 h-10 rounded-md bg-black/3 flex justify-center items-center">
                                    <span class="">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="flex-1">
                                    <div class=" flex justify-between gap-3">
                                        <div class="">
                                            <p class="">
                                                {{ $qIndex + 1 }}. {{ $question->enonce ?? 'Sans énoncé' }}
                                            </p>
                                            <div class="text-sm flex gap-3">
                                                <span class=" text-rouge">
                                                    {{ $question->points }} pts
                                                </span>
                                                <span class="">
                                                    {{ $question->zones->count() }} zone(s)
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex gap-3">
                                            <a href="{{ route('prof.examen.glisserdeposer.question.edit', [$slug, $examen->id, $exercice->id, $question->id]) }}" class="text-black/60">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <form action="{{ route('prof.examen.glisserdeposer.question.destroy', [$slug, $examen->id, $exercice->id, $question->id]) }}" method="POST" onsubmit="return confirm('Supprimer cette question ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="flex gap-5 items-start border border-black/5 rounded-md p-2 bg-white/60 mt-1 {{ !$loop->last ? 'border-b border-black/10' : '' }}">
                                        @if($question->image)
                                            <div class="relative inline-block flex-shrink-0" style="width: 200px;">
                                                <img src="{{ asset('images/glisserdeposer/' . $question->image) }}"
                                                    class="w-full rounded-md border border-black/10 cursor-zoom-in image-zoomable"
                                                    data-full-src="{{ asset('images/glisserdeposer/' . $question->image) }}"
                                                    data-zones="{{ $question->zones->map(fn($z, $i) => ['numero' => $i + 1, 'x' => $z->position_x, 'y' => $z->position_y, 'texte' => $z->item->texte ?? ''])->toJson() }}"
                                                    alt="">

                                                @foreach($question->zones as $zIndex => $zone)
                                                    <div class="absolute w-6 h-6 -ml-3 -mt-3 rounded-full bg-rouge text-white text-xs flex items-center justify-center font-bold border-2 border-white shadow pointer-events-none"
                                                        style="left: {{ $zone->position_x }}%; top: {{ $zone->position_y }}%;"
                                                        title="{{ $zone->item->texte ?? '' }}">
                                                        {{ $zIndex + 1 }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <div class="grid grid-cols-2 gap-2">
                                                @foreach($question->zones as $zIndex => $zone)
                                                    <div class="flex items-center gap-2 text-sm border-2 bg-black/2 border-black/5 rounded p-2">
                                                        <span class="w-5 h-5 rounded-full bg-rouge text-white flex items-center justify-center font-bold flex-shrink-0">
                                                            {{ $zIndex + 1 }}
                                                        </span>
                                                        <span class="text-black/50">{{ $zone->nom_zone }} :</span>
                                                        <span class="font-semibold">{{ $zone->item->texte ?? '—' }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="border-black/20 my-4">
                            @empty
                                <p class="text-sm text-black/40 italic py-2">Aucune question pour cet exercice.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 rounded-md bg-black/5 text-center mt-4">
                    <i class="fa-solid fa-box-open text-2xl"></i>
                    <p>Aucun exercice « glisser-déposer » n'a encore été créé pour cet examen.</p>
                </div>
            @endforelse

            <div class="flex justify-end mt-4 me-2 sticky bottom-5">
                <a href="{{ route('prof.examen.glisserdeposer.create', [$slug, $examen->id]) }}" class="p-2 px-3 inline-block rounded-md bg-rouge text-white">
                    Créer nouvel exercice
                </a>
            </div>
        </div>
    </div>

    {{-- ✅ Modal zoom, miaraka amin'ny zones --}}
    <div id="image-zoom-modal" class="fixed inset-0 bg-black/80 z-50 hidden items-center justify-center p-6" style="cursor: zoom-out;">
        <button type="button" id="close-zoom-modal" class="absolute top-4 right-6 text-white text-3xl">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div class="overflow-auto max-w-full max-h-full" id="zoom-image-container">
            <div class="relative inline-block" id="zoom-image-wrapper" style="transform-origin: center; transition: transform 0.2s;">
                <img id="zoom-image" src="" class="select-none block max-w-none" draggable="false">
                <div id="zoom-zones-overlay"></div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('image-zoom-modal');
        const zoomImage = document.getElementById('zoom-image');
        const zoomWrapper = document.getElementById('zoom-image-wrapper');
        const zoomZonesOverlay = document.getElementById('zoom-zones-overlay');
        const zoomContainer = document.getElementById('zoom-image-container');
        const closeBtn = document.getElementById('close-zoom-modal');

        let zoomLevel = 1;
        const zoomStep = 0.25;
        const zoomMax = 3;
        const zoomMin = 0.5;

        function ouvrirModal(src, zones) {
            zoomImage.src = src;
            zoomLevel = 1;
            zoomWrapper.style.transform = `scale(${zoomLevel})`;

            // ✅ Mamorona ny marker (zones) ao anaty modal
            zoomZonesOverlay.innerHTML = '';
            zones.forEach(function (zone) {
                const marker = document.createElement('div');
                marker.className = 'absolute w-8 h-8 -ml-4 -mt-4 rounded-full bg-rouge text-white text-sm flex items-center justify-center font-bold border-2 border-white shadow pointer-events-none';
                marker.style.left = zone.x + '%';
                marker.style.top = zone.y + '%';
                marker.innerText = zone.numero;
                marker.title = zone.texte;
                zoomZonesOverlay.appendChild(marker);
            });

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function fermerModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.querySelectorAll('.image-zoomable').forEach(function (img) {
            img.addEventListener('click', function () {
                const zones = JSON.parse(this.dataset.zones || '[]');
                ouvrirModal(this.dataset.fullSrc, zones);
            });
        });

        closeBtn.addEventListener('click', fermerModal);
        modal.addEventListener('click', function (e) {
            if (e.target === modal) fermerModal();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                fermerModal();
            }
        });

        zoomContainer.addEventListener('wheel', function (e) {
            e.preventDefault();

            if (e.deltaY < 0) {
                zoomLevel = Math.min(zoomLevel + zoomStep, zoomMax);
            } else {
                zoomLevel = Math.max(zoomLevel - zoomStep, zoomMin);
            }

            zoomWrapper.style.transform = `scale(${zoomLevel})`;
        });

        zoomWrapper.addEventListener('dblclick', function () {
            zoomLevel = zoomLevel === 1 ? 2 : 1;
            zoomWrapper.style.transform = `scale(${zoomLevel})`;
        });
    });
    </script>
@endsection