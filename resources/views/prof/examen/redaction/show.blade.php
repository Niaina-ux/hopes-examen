@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
    <div class="py-3 me-2">
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
            @forelse($redactions as $index => $redaction)
                <div class="flex gap-4 justify-betwee my-2 p-2 border border-black/10 rounded-md">
                    <div class="w-15 h-15 rounded-md bg-black/3 flex justify-center items-center">
                        <span class="font-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between gap-3 items-start  pb-1">
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold"> {{$redaction->titre}} </h3>
                                <div class="text-sm flex gap-3 mt-1">
                                    <span class="border border-black/10 rounded-full px-3 text-vert"> {{$redaction->nombre_mots_max}} Mots max  </span>
                                    <span class="border border-black/10 rounded-full px-3 text-rouge "> {{$redaction->note_totale}} Points  </span>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <a href="{{route('prof.examen.redaction.edit', [$slug, $examen->id, $redaction->id])}}" class="text-black/60">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('prof.examen.redaction.destroy', [$slug, $examen->id, $redaction->id]) }}" method="POST" onsubmit="return confirm('Supprimer {{ $redaction->titre }} ? Cette action supprimera aussi toutes ses questions.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="mt-2 p-2 px-3 bg-black/3 rounded-md ">
                            <h3 class="text-base font-semibold mt-2">Sujet</h3>
                            <p class=" whitespace-pre-line "> {{$redaction->sujet}} </p>
                            <h3 class="text-base font-semibold mt-2">Instruction</h3>
                            <p class=""> {{$redaction->instruction}}</p>
                            
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 rounded-md bg-black/5 text-center mt-4">
                    <i class="fa-solid fa-box-open text-2xl"></i>
                    <p>Aucun QCM n'a encore été créé pour cet examen.</p>
                </div>
            @endforelse
            @if ($examen->status === 'brouillon')       
            <div class=" flex justify-end mt-4 me-2 sticky bottom-5">
                <a href="{{route('prof.examen.redaction.create', [$slug, $examen->id])}}" class="p-2 px-3 inline-block rounded-full bg-rouge text-white">
                    Créer nouveau redaction
                </a>
            </div>
            @endif
        </div>
    </div>
@endsection