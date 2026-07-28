@extends('layouts.student-layouts.layouthead')
@section('contenue-student')
    <section class="bg-black/5">
        <div class="container flex justify-between py-15 gap-20 items-center">
            <div class="w-[70%] m-auto text-center">
                <h2 class="font-bold text-4xl mb-4">Pour le <span class="text-vert"> {{ $categorie->nom }} </span>, voici l'examen que tu dois faire maintenant.</h2>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsum, provident!</p>
            </div>
        </div>
    </section>
    <section>
        <div class="container gap-10 py-15 items-center">
            @if ($examen)
            <div class="flex-1 w-[17cm] m-auto border border-black/10 rounded-md bg-black/2">
                <img src="/images/devv.png" alt="" class="w-[70%] m-auto">
                <div class="flex-1 text-center p-15 pt-0">
                    <h3 class="text-3xl font-semibold text-vert mb-3"> {{ $examen->titre }} </h3>
                    <p class="pb-3"> {{ $examen->description }} </p>
                    <div class="flex justify-center gap-3">
                        <div class="border border-black/10 rounded-sm px-3 py-1">
                            <span class="text-rouge">{{ $examen->duree_minutes }}</span> Minutes
                        </div>
                        <div class="border border-black/10 rounded-sm px-3 py-1">
                            <span class="text-rouge">{{ $examen->typesExercice->count() }}</span> Types d'exercice
                        </div>
                        <div class="border border-black/10 rounded-sm px-3 py-1">
                            Date d'examen: {{ $studentExamen->date_examen?->format('d/m/Y') ?? '-' }}
                        </div>
                    </div>
                    <button type="button" onclick="openModal('confirm-start-modal')" class="bg-rouge rounded-md text-white uppercase p-3 px-7 mt-5">
                        Commencer maintenant
                    </button>
                </div>
            </div>

            <x-confirm-modal
                id="confirm-start-modal"
                title="Es-tu prêt(e) ?"
                :action="route('student.examen.start', $examen->id)"
                confirmText="Oui, commencer"
            >
                Une fois commencé, le chronomètre de <span class="text-rouge font-semibold">{{ $examen->duree_minutes }} minutes</span> démarre immédiatement et ne peut pas être mis en pause.
            </x-confirm-modal>
        @else
            <div class="flex-1 w-[17cm] m-auto border border-black/10 rounded-md bg-black/2 p-15 text-center">
                <i class="fa-solid fa-box-open text-5xl"></i>
                <p class="text-xl font-semibold text-vert">Aucun examen disponible pour le moment.</p>
            </div>
        @endif
        </div>
    </section>
@endsection