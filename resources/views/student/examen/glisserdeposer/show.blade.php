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
    <div class="flex justify-between items-center mb-2 border-b-2 border-black/10 pb-1">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-black/5 font-semibold text-vert rounded-md flex justify-center items-center">
                {{$glisserdeposer->ordre}}
            </div>  
            <h2 class="text-lg font-semibold">{{ $glisserdeposer->titre }}</h2>
        </div>
        <span class="text-sm text-black/50">Question {{ $qIndex + 1 }}/{{ $totalQuestions }}</span>
    </div>

    <form id="glisser-deposer-form" 
        action="{{ route('examen.glisserdeposer.store', ['examen' => $examen->id, 'slug' => $slug, 'glisserdeposer' => $glisserdeposer->id]) }}?q={{ $qIndex }}" method="POST">
        @csrf
        <input type="hidden" name="question_id" value="{{ $question->id }}">
        <input type="hidden" name="q_index" value="{{ $qIndex }}">

        <div class="mb-6 ">
            @if($question->enonce)
                <p class="font-semibold mb-3">{{ $question->enonce }}</p>
            @endif

            <div class="flex  gap-2 border border-black/10 rounded-md p-2">
                <div class="bg-black/5 w-[30%] rounded-md">
                    <div class="dd-items-pool sticky  self-start top-15  gap-2 p-2 content-star">
                        @foreach($question->items as $item)
                            @if(!isset($reponsesExistantes[$item->id]))
                                <span class="dd-item bg-white/90 inline-block w-full border border-black/10 shadow rounded px-3 py-2 cursor-move" draggable="true" data-item-id="{{ $item->id }}">
                                    {{ $item->texte }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="relative rounded-md overflow-hidden border border-black/10" style="width: {{ $question->image_largeur ?? 500 }}px;">
                    <img src="{{ asset('images/glisserdeposer/' . $question->image) }}" 
                        class="w-full block h-full " 
                        draggable="false">
                        @foreach($question->zones as $zone)
                        <div
                            class="dd-zone absolute border-2 border-dashed border-vert/50 bg-vert/5 rounded-md flex items-center justify-center text-xs text-vert"
                            style="left: {{ $zone->position_x }}%; top: {{ $zone->position_y }}%; width: 80px; height: 40px; transform: translate(-50%, -50%);"
                            data-zone-id="{{ $zone->id }}"
                        >
                            @php
                                $itemDejaPlace = collect($question->items)->first(fn($i) => ($reponsesExistantes[$i->id] ?? null) == $zone->id);
                            @endphp
                            @if($itemDejaPlace)
                                <span class="dd-item-place bg-white border border-black/20 rounded px-2 py-1 cursor-move" draggable="true" data-item-id="{{ $itemDejaPlace->id }}">
                                    {{ $itemDejaPlace->texte }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>

            </div>
        </div>

        <div id="hidden-inputs-container"></div>

        <div class="flex justify-end mt-6">
            <button type="submit" class="p-2 px-5 rounded-md bg-rouge text-white">
                {{ ($qIndex + 1 == $totalQuestions) && ($index + 1 == $total) ? 'Terminer' : 'Suivant' }}
            </button>
        </div>
    </form>
</div>

<style>
    .dd-item.dragging, .dd-item-place.dragging {
        opacity: 0.4;
    }
    .dd-zone.drag-over {
        background-color: rgba(22, 163, 74, 0.15);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let draggedItem = null;

    function attacherDragEvents(item) {
        item.addEventListener('dragstart', function () {
            draggedItem = this;
            this.classList.add('dragging');
        });
        item.addEventListener('dragend', function () {
            this.classList.remove('dragging');
        });
    }

    document.querySelectorAll('.dd-item, .dd-item-place').forEach(attacherDragEvents);

    document.querySelectorAll('.dd-zone').forEach(function (zone) {
        zone.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });

        zone.addEventListener('dragleave', function () {
            this.classList.remove('drag-over');
        });

        zone.addEventListener('drop', function (e) {
            e.preventDefault();
            this.classList.remove('drag-over');

            if (!draggedItem) return;

            const itemExistant = this.querySelector('.dd-item-place, .dd-item');
            if (itemExistant && itemExistant !== draggedItem) {
                document.querySelector('.dd-items-pool').appendChild(itemExistant);
                itemExistant.classList.remove('dd-item-place');
                itemExistant.classList.add('dd-item');
            }

            this.appendChild(draggedItem);
            draggedItem.classList.remove('dd-item');
            draggedItem.classList.add('dd-item-place');

            draggedItem = null;
        });
    });

    const pool = document.querySelector('.dd-items-pool');
    if (pool) {
        pool.addEventListener('dragover', function (e) {
            e.preventDefault();
        });
        pool.addEventListener('drop', function (e) {
            e.preventDefault();
            if (!draggedItem) return;
            this.appendChild(draggedItem);
            draggedItem.classList.remove('dd-item-place');
            draggedItem.classList.add('dd-item');
            draggedItem = null;
        });
    }

    document.getElementById('glisser-deposer-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const container = document.getElementById('hidden-inputs-container');
        container.innerHTML = '';

        document.querySelectorAll('.dd-zone').forEach(function (zone) {
            const item = zone.querySelector('.dd-item-place');
            if (item) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `reponses[${item.dataset.itemId}]`;
                input.value = zone.dataset.zoneId;
                container.appendChild(input);
            }
        });

        this.submit();
    });
});
</script>
@endsection