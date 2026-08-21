@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof') 
<div class="py-3 ">
    <div class="animation-11">
        <div class="flex justify-between items-end">
            <div class="w-[70%]">
                <h2 class="text-vert text-2xl mb-1 font-semibold">Les étudiants</h2>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Repudiandae aliquid, delectus modi dolore consequatur at?</p>
            </div>
        </div>
    </div>
    <div class="w-full  mt-4 border border-black/3 rounded-md p-2 bg-black/2 animation-11
        dark:border-white/3 dark:bg-white/2">
        @forelse ($students as $student)    
        <div class="flex justify-between gap-7 p-3 border rounded bg-white/70 border-black/3">
            <div class="w-10 h-10 rounded-md bg-black/5 border border-black/2 overflow-hidden">
                <img src="{{ $student->image ? asset('images/' . $student->image) : asset('images/avatar.jpg') }}"
                    alt="{{ $student->name }}"
                    class="w-full h-full object-cover">
            </div>
            <div class="flex-1">
                <a href="" class="hover:underline block -mt-1"> {{ $student->name }}
                    <span class="rounded-4xl border-2 border-black/5  px-2 text-sm bg-rouge text-white">
                        {{ $student->student->matricule ?? 'N/A' }}
                    </span>
                </a>
                <p class="text-sm"> {{$student->email}} </p>
            </div>
            <div class="">
                <a href="{{route('student.statexam',[$slug, $student->student->id] )}}" class="text-vert bg-black/3 w-7 h-7 flex justify-center items-center rounded p-1">
                    <i class="fa-solid fa-info"></i>
                </a>
            </div>
        </div>
        @empty
            <div class="p-20 rounded-md bg-black/2 text-center
            dark:bg-white/2">
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