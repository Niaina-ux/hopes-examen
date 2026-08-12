@extends('layouts.admin-layouts.layouthead')
@section('contenue-admin')
<div class="py-3 me-2">
    <div class="flex justify-between items-end">
        <div class="w-[60%]">
            <h3 class="text-3xl font-semibold text-vert">Examens — {{ $categorie->nom }}</h3>
            <p>Liste des examens créés pour cette catégorie.</p>
        </div>
        <div>
            <a href="{{ route('admin.examen.create', $slug) }}" class="p-1 px-5 rounded-full bg-rouge inline-block text-white">
                + Créer examen
            </a>
        </div>
    </div>

    @if(session('success'))
        <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md mt-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="document.getElementById('success-alert').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <div class="p-2 border border-black/3 rounded-md mt-4 bg-black/2">
        @forelse($examens as $index => $examen)
            <div class="flex justify-between gap-7 border  border-black/3 rounded bg-white/70 p-2 {{ $loop->iteration == 2 ? 'bg-white' : '' }}">
                <div class="w-10 h-10 rounded bg-black/5 flex justify-center items-center font-semibold">
                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold">{{ $examen->titre }}</h3>
                    <p class="text-black/50">{{ $examen->description }}</p>
                    <div class="flex gap-3 text-sm mt-1">
                        <span class="border border-black/10 rounded-full px-3 {{$examen->status == 'brouillon' ? 'text-black/70' : 'text-rouge'}} ">
                            {{ $examen->status }}
                        </span>
                        <span class="border border-black/10 rounded-full text-vert px-3">{{ $examen->types_exercice_count }} types d'exercice</span>
                    </div>
                </div>
                <div class="flex gap-4">
                    <a href="{{route('admin.examen.student.show', [$slug, $examen->id])}}" class="text-vert">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </a>
                    <a href="{{ route('admin.examen.edit', [$slug, $examen->id]) }}" class="text-black/60">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                    <form action="{{ route('admin.examen.destroy', [$slug, $examen->id]) }}" method="POST" onsubmit="return confirm('Supprimer {{ $examen->titre }} ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-10 rounded-md bg-black/5 text-center">
                <i class="fa-solid fa-box-open text-2xl"></i>
                <p>Aucun examen créé pour cette catégorie.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $examens->links() }}
    </div>
</div>
@endsection