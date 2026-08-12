@extends('layouts.admin-layouts.layouthead')
@section('contenue-admin')
<div class="bg-white py-3 rounded-md">
    <div class="mb-2">
        <a href="{{ route('admin.categorie.index') }}" class="hover:underline">Catégories/</a>
        <span class="font-semibold">{{ $categorie->nom }} — Types d'exercice</span>
    </div>

    @if(session('success'))
        <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md mb-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="document.getElementById('success-alert').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <p class="text-black/60 mb-4">
        Sélectionnez les types d'exercice disponibles pour les examens de la catégorie <strong>{{ $categorie->nom }}</strong>.
    </p>

    <form action="{{ route('admin.categorie.updateTypesExercice', $categorie->id) }}" method="POST">
        @csrf

        <div class="grid grid-cols-2 gap-3">
            @foreach($typesExerciceTous as $type)
                @php
                    $estCoche = $categorie->typesExerciceAutorises->contains($type->id);
                @endphp
                <label class="flex items-center gap-3 border border-black/10 rounded-md p-3 bg-black/2 cursor-pointer">
                    <input type="checkbox" name="types[]" value="{{ $type->id }}" {{ $estCoche ? 'checked' : '' }}>
                    <div class="w-8 h-8 bg-black/5 rounded-md flex justify-center items-center">
                        <i class="{{ $type->icone ?? 'fa-solid fa-shapes' }} text-vert"></i>
                    </div>
                    <span>{{ $type->nom }}</span>
                </label>
            @endforeach
        </div>

        <button type="submit" class="bg-rouge text-white px-4 py-2 rounded mt-4">Enregistrer</button>
    </form>
</div>
@endsection