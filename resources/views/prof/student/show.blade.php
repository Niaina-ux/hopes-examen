@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof') 
<div class="bg-white rounded-md py-3 me-2">
    <div class="flex gap-3 items-center">
        <span class="">
            Etudiants /
        </span>
    </div>
    <div class="bg-white sticky top-0">
        <div class="flex justify-between items-end">
            <div class="w-[70%]">
                <h2 class="text-vert text-2xl mt-1 font-semibold">Tous les étudiants</h2>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Repudiandae aliquid, delectus modi dolore consequatur at?</p>
            </div>
        </div>
    </div>
    <div class="w-full  mt-4 border border-black/3 rounded-md p-2 bg-black/2">
        @forelse ($students as $student)    
        <div class="flex justify-between gap-7 p-2 border rounded bg-white/70 border-black/3">
            <div class="w-12 h-12 rounded-md bg-black/5 overflow-hidden">
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
                <a href="{{route('student.statexam',[$slug, $student->student->id] )}}" class="text-vert bg-black/3 w-7 h-7 flex justify-center items-center rounded p-1">
                    <i class="fa-solid fa-info"></i>
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