@extends('layouts.admin-layouts.layouthead')
@section('contenue-admin')
<div class="py-3">
    @if(session('success'))
        <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md mb-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="document.getElementById('success-alert').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- FORM 1 : Création du compte prof — mijanona hita mandrakariva --}}
    <form action="{{ route('admin.prof.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium">Anarana</label>
            <input type="text" name="name" value="{{ old('name', $profACompleter->name ?? '') }}"
                {{ $profACompleter ? 'readonly' : '' }}
                class="border rounded w-full p-2 {{ $profACompleter ? 'bg-black/5' : '' }}">
            @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email', $profACompleter->email ?? '') }}"
                {{ $profACompleter ? 'readonly' : '' }}
                class="border rounded w-full p-2 {{ $profACompleter ? 'bg-black/5' : '' }}">
            @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Image</label>
            @if($profACompleter && $profACompleter->image)
                <img src="{{ asset('images/' . $profACompleter->image) }}" class="w-16 h-16 rounded-md object-cover mb-2">
            @endif
            <input type="file" name="image" class="border rounded w-full p-2" {{ $profACompleter ? 'disabled' : '' }}>
            @error('image') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Créer le mot de passe</label>
            @if($profACompleter)
                <input type="text" value="{{ \Illuminate\Support\Facades\Crypt::decrypt($profACompleter->password_affiche) }}" readonly class="border rounded w-full p-2 bg-black/5">
                <p class="text-xs text-black/40 mt-1">Notez ce mot de passe pour le transmettre au professeur.</p>
            @else
                <input type="text" name="password" class="border rounded w-full p-2">
            @endif
            @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        @unless($profACompleter)
            <button type="submit" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer</button>
        @else
            <a href="{{ route('admin.prof.create') }}" class="text-vert underline text-sm">+ Ajouter un autre professeur</a>
        @endunless
    </form>

    @if($profACompleter)
        {{-- FORM 2 : Assignation catégorie — aseho FOTSINY raha misy $profACompleter --}}
        <div class="mt-6">
            <div class="bg-green-50 border border-green-200 rounded-md p-3 mb-4">
                <p class="text-green-700 text-sm">
                    <i class="fa-solid fa-circle-check"></i>
                    Complétez maintenant la catégorie de <strong>{{ $profACompleter->name }}</strong>.
                </p>
            </div>

            <form action="{{ route('admin.prof.storeCategorie', $profACompleter->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium">Catégorie</label>
                    <select name="categorie_id" class="border rounded w-full p-2">
                        <option value="">-- Safidio categorie --</option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie->id }}" {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>
                                {{ $categorie->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('categorie_id') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="bg-rouge text-white px-4 py-2 rounded">Enregistrer</button>
            </form>
        </div>
    @endif
</div>
@endsection