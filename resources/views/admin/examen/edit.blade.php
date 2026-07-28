@extends('layouts.admin-layouts.layouthead')
@section('contenue-admin')
<div class="bg-white p-4 rounded-md">
    <h2 class="text-2xl font-semibold text-vert mb-4">Modifier l'examen</h2>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.examen.update', [$slug, $examen->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium">Titre</label>
            <input type="text" name="titre" value="{{ old('titre', $examen->titre) }}" class="border rounded w-full p-2">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Description</label>
            <textarea name="description" rows="3" class="border rounded w-full p-2">{{ old('description', $examen->description) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Durée (minutes)</label>
            <input type="number" name="duree_minutes" value="{{ old('duree_minutes', $examen->duree_minutes) }}" class="border rounded w-full p-2">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Statut</label>
            <select name="status" class="border rounded w-full p-2">
                <option value="brouillon" {{ old('status', $examen->status) == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                <option value="publie" {{ old('status', $examen->status) == 'publie' ? 'selected' : '' }}>Publié</option>
                <option value="archive" {{ old('status', $examen->status) == 'archive' ? 'selected' : '' }}>Archivé</option>
            </select>
        </div>

        <button type="submit" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer les modifications</button>
    </form>
</div>
@endsection