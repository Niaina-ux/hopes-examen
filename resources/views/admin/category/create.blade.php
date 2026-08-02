@extends('layouts.admin-layouts.layouthead')
@section('contenue-admin')
    <div class="">
        <div class="my-3 me-2">
            <div class="border-b-2 border-black/10 pb-2">
                <h3 class="text-2xl text-vert font-semibold mb-2">Creation catégorie</h3>
            </div>
            <div class=" mt-5 rounded-md bg-black/2 p-3">
                <form action="{{ route('admin.categorie.store') }}" method="POST">
                    @csrf
                    <div class="py-1">
                        <span class="block">Titre</span>
                        <input type="text" name="nom" value="{{ old('nom') }}"
                            class="p-2 border border-black/10 rounded w-full bg-white outline-0 focus:border-[rgb(104,167,2)]"
                            placeholder="Ex: Français">
                        @error('nom') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="py-2 w-[60%]">
                        <span class="block">Slug: (Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt, earum.)</span>
                        <input type="text" name="slug" value="{{ old('slug') }}"
                            class="p-2 border border-black/10 rounded bg-white w-[10cm] outline-0 focus:border-[rgb(104,167,2)]"
                            placeholder="Ex: francais, anglais, ....">
                        @error('slug') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex mt-4">
                        <button type="submit" class="bg-rouge rounded-md px-5 py-2 text-white">
                            Créer la catégorie
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection