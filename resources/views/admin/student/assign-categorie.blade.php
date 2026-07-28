@extends('layouts.admin-layouts.layouthead')
@section('contenue-admin')
    <div class="">
        <div class="w-20 h-20">
            <img src="{{ $student->image ? asset('images/' . $student->image) : asset('images/default-avatar.png') }}"
                        alt="{{ $student->name }}"
                        class="w-full h-full object-cover">
        </div>
        <div class="">
            <h2> {{$student->name}} </h2>
            <p> {{$student->email}} </p>
        </div>
        <div class="">
            <form action="{{ route('admin.student.storeCategorie', $student->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium">Matricule</label>
                    <input name="matricule" type="text"
                        value="{{ old('matricule', $student->student->matricule ?? '') }}"
                        class="border rounded w-full p-2">
                    @error('matricule') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium">Catégorie</label>
                    <select name="categorie_id" class="border rounded w-full p-2">
                        <option value="">-- Safidio categorie --</option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie->id }}"
                                {{ old('categorie_id', $student->student->categorie_id ?? '') == $categorie->id ? 'selected' : '' }}>
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
@endsection