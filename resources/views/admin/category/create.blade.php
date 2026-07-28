@extends('layouts.layoutheadd')
@section('contenue')
    <div class="container">
        <div class="py-10 w-[60%] m-auto ">
            <div class="border-b-2 border-black/10 py-2 text-center">
                <h3 class="text-2xl text-vert font-semibold mb-2">Creation catégorie</h3>
                <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Incidunt nam fugit blanditiis consequuntur adipisci. Reiciendis, iusto! Officiis nulla nam amet voluptate qui placeat, consectetur minima quidem? Eos neque dolor dolorum?</p>
            </div>
            <div class="w-[11cm] m-auto p-4 border border-black/10 mt-5 rounded-md bg-black/2">
                <form action="{{ route('admin.categorie.store') }}" method="POST">
                    @csrf
                    <div class="py-2">
                        <span class="block">Titre</span>
                        <input type="text" name="nom" value="{{ old('nom') }}"
                            class="p-2 border border-black/10 rounded w-full bg-white"
                            placeholder="Ex: Français">
                        @error('nom') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="py-2">
                        <span class="block">Slug</span>
                        <input type="text" name="slug" value="{{ old('slug') }}"
                            class="p-2 border border-black/10 rounded w-full bg-white"
                            placeholder="Ex: francais, anglais, web, python, design, bureautique">
                        @error('slug') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end mt-4">
                        <button type="submit" class="bg-rouge rounded-md px-5 py-2 text-white">
                            Créer la catégorie
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection