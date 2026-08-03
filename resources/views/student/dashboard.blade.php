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
    <div class="container  flex gap-5 pb-10">
        <div class="flex-1 min-h-80 border border-black/5 rounded-md p-4">
            <h3 class="font-semibold text-xl mb-3">Examens planifiés</h3>
            @php
                $today = \Carbon\Carbon::today();
            @endphp

            @forelse ($examen_planifie as $examen)
                @php
                    $isFirst = $loop->first;
                    $isExpired = \Carbon\Carbon::parse($examen->date_examen)->lt($today);
                @endphp
                <div class="border border-black/5 rounded-md p-2 flex gap-4 bg-black/2">
                    <div class="w-10 h-10 rounded-md bg-black/5 flex justify-center items-center font-semibold">
                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                    </div>
                    <div class="flex-1">
                        <h4>
                            {{ $examen->examen->titre }}
                           @php
                                $canStart = \Carbon\Carbon::parse($examen->date_examen)->isToday()
                                    || \Carbon\Carbon::parse($examen->date_examen)->isPast();
                            @endphp
                            <span class="border border-black/10 rounded-full text-sm text-white px-2 {{ $canStart ? 'bg-vert' : 'bg-orange-500' }}">
                                {{ $canStart ? 'Commencez' : 'En attente' }}
                            </span>
                        </h4>
                        <div class="flex gap-3 text-sm">
                            <span>
                                Date d'examen :
                                <span class="{{ $isExpired ? 'text-red-600 ' : 'text-rouge' }}">
                                    {{ \Carbon\Carbon::parse($examen->date_examen)->translatedFormat('d M Y') }}
                                </span>
                            </span>
                        </div>
                        @if($isExpired)
                            <p class=" text-xs mt-1">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                La date prévue de cet examen est dépassée.
                            </p>
                        @endif
                    </div>
                    @if($isFirst)
                        <a href="{{ route('student.examen.show', $examen->examen->categorie->nom) }}"
                        class="w-8 h-8 bg-white rounded-md flex justify-center items-center text-vert">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    @else
                        <span class="w-8 h-8 bg-gray-100 rounded-md flex justify-center items-center text-gray-400 cursor-not-allowed">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                    @endif
                </div>
            @empty
                <div class="p-10 bg-black/2 rounded-md text-center">
                    <i class="fa-solid fa-box-open"></i>
                    <p>Aucun !</p>
                </div>
            @endforelse
        </div>
        <div class="flex-1 min-h-80 bg-black/3 rounded-md p-4">
            <h3 class="font-semibold text-xl mb-3">Mes examens terminés</h3>
            @forelse ($attempts as $attempt)
                <div class=" border border-black/5 rounded-md p-2 flex gap-4 bg-white/80">
                    <div class="w-10 h-10 rounded-md bg-black/5 flex justify-center items-center font-semibold">
                        01
                    </div>
                    <div class="flex-1">
                        <h4>{{$attempt->examen->titre}}
                            <span class="border border-black/10 rounded-full text-sm text-white px-2 
                            {{$attempt->status == 'corrige' ? 'bg-rouge' : 'bg-vert'}}">
                                {{$attempt->status == 'termine' ? 'Terminé' : 'Corrigé'}}
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
                <div class="p-10 bg-white/90 rounded-md text-center">
                    <i class="fa-solid fa-box-open"></i>
                    <p>Aucun !</p>
                </div>
            @endforelse
            
        </div>
    </div>
</section>
@endsection