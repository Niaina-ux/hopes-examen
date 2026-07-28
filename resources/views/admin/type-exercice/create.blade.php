@extends('layouts.layoutheadd')
@section('contenue')
<div class="bg-white p-4 rounded-md">
    <h2 class="text-2xl font-semibold text-vert mb-4">Créer un type d'exercice</h2>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.typeExercice.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium">Nom</label>
            <input type="text" name="nom" value="{{ old('nom') }}"
                class="border rounded w-full p-2" placeholder="Ex: QCM, Relier par flèche">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Slug</label>
            <input type="text" name="slug" value="{{ old('slug') }}"
                class="border rounded w-full p-2" placeholder="Ex: qcm, relier, completer, code">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Icône (optionnel)</label>
            <input type="text" name="icone" value="{{ old('icone') }}"
                class="border rounded w-full p-2" placeholder="Ex: fa-solid fa-list-check">
        </div>

        <button type="submit" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer</button>
    </form>
</div>
@endsection