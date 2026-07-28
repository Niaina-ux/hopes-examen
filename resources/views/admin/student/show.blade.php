@extends('layouts.admin-layouts.layouthead')
@section('contenue-admin')
    <div class="flex gap-10 border-b-2 border-black/10 py-2 sticky top-0">
        <div class="w-30 h-30 rounded-md overflow-hidden">
            <img src="{{$student->image ? asset('images/'. $student->image) : asset('images/avatar.jpg')}}" 
                alt="{{ $student->name }}" class="w-full h-full object-cover">
        </div>
        <div class="">
            <h2 class="font-semibold text-xl border-b border-black/10"> {{$student->name}} </h2>
            <p class="my-1">
                <span class="text-sm text-black/40">Email</span> 
                <span class="inline-block px-4 rounded bg-black/3">{{$student->email}} </span>
            </p>
            <p >Domaine 
                <span class="border border-black/10 rounded-full inline-block px-5 text-rouge">
                    {{ $student->student->categorie->nom ?? 'Pas de domaine' }}
                </span>
            </p>
        </div>
    </div>
    <div class="py-2">
        <h3 class="text-xl text-vert font-semibold ">Examen travailé</h3>
        <div class="flex justify-between gap-7 p-2 border-b border-black/10">
            <div class="w-15 h-15 rounded-md bg-black/5 overflow-hidden">
                <img src=""
                    alt=""
                    class="w-full h-full object-cover">
            </div>
            <div class="flex-1">
                <h3 class="font-semibold">Examen premiere pressage</h3>
                <p class="text-sm">  </p>
                <div class="flex gap-4 text-sm">
                    <div class="flex   ">
                        Il y a <span class="inline-block  px-2 text-vert">3</span> types d'exercice
                    </div>
                    <div class="flex ">
                        Durée  <span class="rounded-4xl border border-black/10  px-3 text-rouge">2h</span>
                    </div>
                    <div class="flex">
                        Finis dans <span class="rounded-4xl border border-black/10  px-3 text-vert">3h</span>
                    </div>
                </div>
                <div class="flex gap-3 text-sm">
                    <div class="flex">
                        Status <span class="rounded-4xl border border-black/10  px-3 text-vert">corigé</span>
                    </div>
                </div>
            </div>
            <div class="">
                <div class="flex gap-3 items-center">  
                    <a href="{{ route('admin.student.examen.qcm', 11) }}" class="text-vert">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection