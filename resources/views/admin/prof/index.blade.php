@extends('layouts.admin-layouts.layouthead')
@section('contenue-admin')  
<div class="bg-white h-full rounded-md py-3 me-2">
    <div class="bg-white sticky top-0">
        <div class="flex justify-between items-end">
            <div class="w-[70%]">
                <h2 class="text-vert text-2xl font-semibold">Tous les proffesseures</h2>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Repudiandae aliquid, delectus modi dolore consequatur at?</p>
            </div>
            <div class="">
                <a href="{{ route('admin.prof.create') }}" 
                    class="bg-rouge p-2 px-4 rounded-full text-white bg-rouge-hover mt-1">
                    Ajouter nouveau prof
                </a>
            </div>
        </div>
        @if(session('success'))
            <div id="success-alert" class="mt-2 bg-green-100/50 text-green-700 px-4 py-2 rounded-md mb-4 flex justify-between items-center">
                <span>
                    {{ session('success') }}
                </span>
                <button type="button" onclick="document.getElementById('success-alert').remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div id="error-alert" class="bg-red-100 text-red-700 border border-red-300 px-4 py-2 rounded-md mb-4 flex justify-between items-center">
                <span>
                    {{ session('error') }}
                </span>
                <button type="button" onclick="document.getElementById('error-alert').remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
    </div>
    <div class="flex gap-2 border-b-2 border-black/10 py-2 mt-2">
        <a href="{{ route('admin.prof.index') }}"
            class="p-1 px-3 rounded-sm border border-black/10 bg-black/2 inline-block hover:bg-black/5 {{ !request('categorie_id') ? 'bg-vert text-white' : '' }}">
            Tous
        </a>
        @foreach ($categories as $categorie)
            <a href="{{ route('admin.prof.index', ['categorie_id' => $categorie->id]) }}"
                class=" p-1 px-3 rounded-sm border border-black/10 bg-black/2 inline-block hover:bg-black/5 {{ request('categorie_id') == $categorie->id ? 'bg-vert text-white' : '' }}">
                {{ $categorie->nom }}
            </a>
        @endforeach
    </div>
    <div class="w-full  mt-4 rounded py-1">
        @forelse ($profs as $prof)    
        <div class="flex justify-between gap-7 p-2 border-b border-black/10">
            <div class="w-15 h-15 rounded-md bg-black/5 overflow-hidden">
                <img src="{{ $prof->image ? asset('images/' . $prof->image) : asset('images/default-avatar.png') }}"
                    alt="{{ $prof->name }}"
                    class="w-full h-full object-cover">
            </div>
            <div class="flex-1">
                <h3 class="font-semibold"> {{ $prof->name }} </h3>
                <p class="text-sm"> {{$prof->email}} </p>
                <div class="flex gap-3 text-sm">
                    <div class="flex ">
                        Domaine  <span class="rounded-4xl border border-black/10  px-3 text-rouge">{{ $prof->prof->categorie->nom ?? 'Vide' }}</span>
                    </div>
                    <div class="flex">
                        Status <span class="rounded-4xl border border-black/10  px-3 text-vert">actif</span>
                    </div>
                </div>
            </div>
            <div class="">
                <div class="flex gap-3 items-center">
                    @unless($prof->prof && $prof->prof->categorie)
                        <a href="{{ route('admin.prof.assignCategorie', $prof->id) }}" class="p-1 px-4 rounded-md text-rouge underline">
                            + Ajouter au domaine
                        </a>
                    @endunless
                    <a href="" class="text-vert">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <form action="{{ route('admin.prof.destroy', $prof->id) }}" method="POST" onsubmit="return confirm('Vous êtes sur d\'effacer {{ $prof->name }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
            <div class="p-20 border border-black/3 rounded-md bg-black/2 text-center">
                <i class="fa-solid fa-box-open text-3xl"></i>
                <p class="">Il n'y a pas encore de Proffesseur!</p>
            </div>
        @endforelse
        
    </div>
</div>
@endsection