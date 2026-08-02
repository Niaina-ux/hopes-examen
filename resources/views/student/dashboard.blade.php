@extends('layouts.student-layouts.layouthead')
@section('contenue-student')
<section>
    <div class="container py-10">
        <div class="flex justify-between gap-5 h-[70vh] ">
            <div class="bg-black/3 rounded-md p-2 flex-1 h-full">
                
            </div>
            <div class="w-[30%] rounded-md p-2 bg-black/3">
    
            </div>
        </div>
    </div>
</section>
<section>
    <div class="container  flex gap-5">
        <div class="flex-1 min-h-80 border border-black/5 rounded-md p-4">
            <h3 class="font-semibold text-xl mb-3">Mes examens terminés</h3>
            <div class=" border border-black/5 rounded-md p-2 flex gap-4 bg-black/2">
                <div class="w-10 h-10 rounded-md bg-black/5 flex justify-center items-center font-semibold">
                    01
                </div>
                <div class="flex-1">
                    <h4>Examen premiere session 
                        <span class="border border-black/10 rounded-full bg-vert text-white px-2">
                            Terminé
                        </span></h4>
                    <div class="flex gap-3 text-sm">
                        <span>Finis le 22 Janv 2026</span>
                    </div>
                </div>
                <a href="" class="w-8 h-8 bg-black/3 rounded-md flex justify-center items-center text-vert">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
            </div>
        </div>
        <div class="flex-1 min-h-80 bg-black/3 rounded-md p-4">
            <h3 class="font-semibold text-xl mb-3">Examens planifiés</h3>
            @forelse ($attempts as $attempt)
                <div class=" border border-black/5 rounded-md p-2 flex gap-4 bg-white/80">
                    <div class="w-10 h-10 rounded-md bg-black/5 flex justify-center items-center font-semibold">
                        01
                    </div>
                    <div class="flex-1">
                        <h4>{{$attempt->examen->titre}}
                            <span class="border border-black/10 rounded-full bg-vert text-white px-2">
                                {{$attempt->status}}
                            </span></h4>
                        <div class="flex gap-3 text-sm">
                            <span> Finis le {{\Carbon\Carbon::parse($attempt->date_fin)->translatedFormat('d M Y \à H\hi');}} </span>
                        </div>
                    </div>
                    <a href="{{route('student.examen.historique.show', $attempt->id)}}" class="w-8 h-8 bg-black/3 rounded-md flex justify-center items-center text-vert">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                </div>
            @empty
                <div class="">
                    <i class="fa-solid fa-box-open"></i>
                    <p>Aucun !</p>
                </div>
            @endforelse
            
        </div>
    </div>
</section>
@endsection