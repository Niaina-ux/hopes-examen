@extends('layouts.admin-layouts.layouthead')

@section('contenue-admin')

@if(!isset($prof))
    {{-- Formulaire d'inscription du prof --}}
    <form action="{{ route('admin.prof.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium">Anarana</label>
            <input type="text" name="name" value="{{ old('name') }}" class="border rounded w-full p-2">
            @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="border rounded w-full p-2">
            @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Photo</label>
            <input type="file" name="image" class="border rounded w-full p-2">
            @error('image') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Créer le mot de passe</label>
            <input type="password" name="password" class="border rounded w-full p-2">
            @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer</button>
    </form>
    
@else
    {{-- Prof créé : affichage infos + assignation de catégorie --}}
    <div class="">
        <div class="w-20 h-20">
            <img src="{{ $prof->image ? asset('images/' . $prof->image) : asset('images/default-avatar.png') }}"
                 alt="{{ $prof->name }}"
                 class="w-full h-full object-cover">
        </div>
        <div class="">
            <h2>{{ $prof->name }}</h2>
            <p>{{ $prof->email }}</p>
        </div>
        <div class="">
            <form action="{{ route('admin.prof.storeCategorie', $prof->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium">Catégorie</label>
                    <select name="categorie_id" class="border rounded w-full p-2">
                        <option value="">-- Safidio categorie --</option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie->id }}"
                                {{ old('categorie_id', $prof->prof->categorie_id ?? '') == $categorie->id ? 'selected' : '' }}>
                                {{ $categorie->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('categorie_id') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer</button>
            </form>
        </div>
    </div>
@endif

@endsection