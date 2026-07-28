@extends('layouts.admin-layouts.layouthead')
@section('contenue-admin')
    <div class="flex justify-between items-end mb-2">
        <div class="w-[70%]">
            <h2 class="text-2xl font-semibold text-vert ">Developpement web</h2>
            <P>Lorem ipsum dolor sit amet consectetur adipisicing elit. Provident eveniet porro, nulla amet ullam esse ea hic asperiores .</P>
        </div>
        <div class="">
            <a href="{{route('admin.examen.create')}}" class="inline-block rounded-md p-1 px-5 bg-rouge">
                Créer examen
            </a>
        </div>
    </div>
    @if(session('success'))
        <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md mb-4 flex justify-between items-center">
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
    <div class="py-2">  
        @forelse ($examens as $index => $examen)
        <div class="flex justify-between gap-7 p-2 border-b border-black/10">
            <div class="w-15 h-15 rounded-md bg-black/5 overflow-hidden flex justify-center items-center">
                <span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold"> {{$examen->titre}} </h3>
                <p class="text-sm">  </p>
                <div class="flex gap-4 text-sm">
                    <div class="flex   ">
                        Il y a <span class="inline-block  px-2 text-vert">3</span> types d'exercice
                    </div>
                    <div class="flex ">
                        Durée:  <span class=" px-3 text-rouge"> {{$examen->duree_minutes}} Minutes</span>
                    </div>
                </div>
                <div class="flex gap-3 text-sm">
                    <div class="flex">
                        Status
                        <span @class([
                            'rounded-4xl border border-black/10 px-3',
                            'text-vert' => $examen->status == 'publie',
                            'text-black/50' => $examen->status == 'brouillon',
                            'text-rouge' => $examen->status == 'archive',
                        ])>
                            {{ $examen->status }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="">
                <div class="flex gap-3 items-center">  
                    <a href="{{route('prof.examen-web.show', $examen->id)}}" class="text-vert">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <form action="{{ route('admin.examen.destroy', $examen->id) }}" method="POST" onsubmit="return confirm('Vous êtes sur d\'effacer {{ $examen->titre }}?')">
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
            <div class="p-20 rounded-md bg-black/1">
                <i class="fa-solid fa-box-open text-3xl"></i>
                <p class="">Il n'y a pas encore d'examen créé!</p>
            </div>
        @endforelse  
        <div class="mt-4">
            {{ $examens->links() }}
        </div>       
    </div>
@endsection