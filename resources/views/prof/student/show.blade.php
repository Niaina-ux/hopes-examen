@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof') 
<div class="bg-white h-full rounded-md py-3">
    <div class="flex gap-3 items-center my-2">
        <span class="font-semibold">
            Etudiants /
        </span>
    </div>
    <div class="bg-white sticky top-0">
        <div class="flex justify-between items-end">
            <div class="w-[70%]">
                <h2 class="text-vert text-2xl font-semibold">Tous les étudiants</h2>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Repudiandae aliquid, delectus modi dolore consequatur at?</p>
            </div>
            <div class="">
                <a href="{{ route('admin.student.create') }}" 
                    class="bg-rouge p-1 px-4 rounded-md bg-rouge-hover mt-1">
                    Ajouter étudiant
                </a>
            </div>
        </div>
    </div>
    <div class="w-full  mt-4 border border-black/10 rounded-md p-2 bg-black/3">
        @forelse ($students as $student)    
        <div class="flex justify-between gap-7 p-2 border-b border-black/10 {{ $loop->iteration == 2 ? 'bg-white' : '' }}">
            <div class="w-15 h-15 rounded-md bg-black/5 overflow-hidden">
                <img src="{{ $student->image ? asset('images/' . $student->image) : asset('images/avatar.jpg') }}"
                    alt="{{ $student->name }}"
                    class="w-full h-full object-cover">
            </div>
            <div class="flex-1">
                <a href="" class="font-semibold hover:underline"> {{ $student->name }} </a>
                <p class="text-sm"> {{$student->email}} </p>
                <div class="flex gap-3 text-sm">
                    <div class="flex">
                        Matricule <span class="rounded-4xl border border-black/10  px-3 text-vert">{{ $student->student->matricule ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <div class="">
                <a href="{{route('admin.student.show', $student->id)}}" class="text-vert border border-black/10 rounded px-1">
                    <i class="fa-solid fa-book-open"></i>
                </a>
            </div>
        </div>
        @empty
            <div class="p-20 rounded-md bg-black/1">
                <i class="fa-solid fa-box-open text-3xl"></i>
                <p class="">Il n'y a pas encore de Proffesseur!</p>
            </div>
        @endforelse
    </div>
    <div class="mt-4">
        {{ $students->links() }}
    </div>
</div>
@endsection