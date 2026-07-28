@extends('layouts.layoutheadd')
@section('contenue')
<div class="container">
    <div class="py-10 w-[60%] m-auto">
        <div class="border-b-2 border-black/10 py-2 text-center">
            <h3 class="text-2xl text-vert font-semibold mb-2">Modifier catégorie</h3>
            <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Sunt, eos! Maxime aliquid nam assumenda eaque, officia dolorum ullam consequuntur corporis repellat iste atque incidunt quasi laborum nostrum dicta eius ipsa.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 max-w-md m-auto">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="w-[11cm] m-auto p-4 border border-black/10 mt-5 rounded-md bg-black/2">
            <form action="{{ route('admin.categorie.update', $categorie->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="py-2">
                    <span class="block">Titre</span>
                    <input type="text" name="nom" value="{{ old('nom', $categorie->nom) }}"
                        class="p-2 border border-black/10 rounded w-full bg-white"
                        placeholder="Ex: Français">
                    @error('nom') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>

                <div class="py-2">
                    <span class="block">Slug</span>
                    <input type="text" name="slug" value="{{ old('slug', $categorie->slug) }}"
                        class="p-2 border border-black/10 rounded w-full bg-white"
                        placeholder="Ex: francais">
                    @error('slug') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end mt-4">
                    <button type="submit" class="bg-rouge rounded-md px-5 py-2 text-white">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection