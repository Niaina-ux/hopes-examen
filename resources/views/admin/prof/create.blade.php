@extends('layouts.admin-layouts.layouthead')

@section('contenue-admin')
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
        <label class="block text-sm font-medium">Email</label>
        <input type="file" name="image" value="{{ old('image') }}" class="border rounded w-full p-2">
        @error('image') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium">Créer le mot de passe</label>
        <input type="text" name="password" value="{{ old('email') }}" class="border rounded w-full p-2">
        @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </div>
    <button type="submit" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer</button>
</form> 

@endsection


