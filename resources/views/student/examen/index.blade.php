
@extends('layouts.student-layouts.layouthead')
@section('contenue-student')
<section class="pt-25 pb-16">
    <div class="container">
        @if ($examen)
            @php
                $today = \Carbon\Carbon::today();
                $dateExamen = $studentExamen->date_examen
                    ? \Carbon\Carbon::parse($studentExamen->date_examen)
                    : null;
                $pasEncoreArrive = $dateExamen && $dateExamen->isAfter($today);
                $peutCommencer = !$pasEncoreArrive;
            @endphp
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center mb-10">
                <div class="py-2 animation-1">
                    <p class="text-rouge font-semibold text-lg mb-2">
                        Bonjour,
                    </p>
                    <h1 class="text-3xl lg:text-4xl font-bold leading-tight mb-4">
                        Voici l'examen à réaliser
                        pour la catégorie
                        <span class="text-vert">
                            {{ $categorie->nom }}.
                        </span>
                    </h1>
                    <p class="text-black/60 text-base leading-relaxed max-w-xl">
                        Lisez attentivement les informations et les consignes
                        avant de commencer votre examen.
                    </p>
                </div>
                <div class=" rounded-2xl  animation-1
                    border border-black/10  md:p-4 lg:p-2">
                    <div class="grid grid-cols-1 m-4
                        sm:grid-cols-2
                        lg:m-2">
                        <div class="flex gap-5 items-center px-4 py-3 border-b border-black/10">
                            <div class="w-12 h-12 rounded-full bg-black/3 flex items-center justify-center">
                                <i class="fa-regular fa-clock text-vert text-xl"></i>
                            </div>
                            <div class="">
                                <p class="text-sm  mb-1"> Durée </p>
                                <p class="font-bold ">{{ $examen->duree_minutes }} minutes </p>
                            </div>
                        </div>
                        <div class="flex gap-5 items-center px-4 py-3 border-b border-black/10
                            sm:border-s">
                            <div class="w-12 h-12 rounded-full bg-black/3 flex items-center justify-center">
                                <i class="fa-solid fa-list-check text-vert text-xl"></i>
                            </div>
                            <div class="">
                                <p class="text-sm text-black/60 mb-1">Types d'exercice</p>
                                <p class="font-bold"> {{ $examen->typesExercice->count() }} types</p>
                            </div>
                        </div>
                        <div class=" flex gap-5 items-center px-4 py-3 border-b border-black/10
                            sm:border-b-0">
                            <div class="w-12 h-12 rounded-full bg-black/5 flex items-center justify-center">
                                <i class="fa-regular fa-calendar text-vert text-xl"></i>
                            </div>
                            <div class="">
                                <p class="text-sm text-black/60 mb-1">  Date d'examen</p>
                                <p class="font-bold">{{ $dateExamen?->format('d/m/Y') ?? '-' }}</p>
                            </div>
                        </div>
                        <div class=" flex gap-5 items-center px-4 py-3 border-black/10
                            sm:border-s">
                            <div class="w-12 h-12  rounded-full bg-black/5 flex items-center justify-center
                            ">
                                <i class="fa-solid fa-circle-check text-vert text-xl"></i>
                            </div>
                            <div class="">
                                <p class="text-sm text-black/60 mb-1"> Statut</p>
                                <p class="font-bold text-vert">
                                    {{ $examen->status == 'brouillon'
                                        ? 'Brouillon'
                                        : ($examen->status == 'publie'
                                            ? 'Publié'
                                            : 'Archivé') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-black/2 border-s-4 border-black/40 rounded-e-xl mb-10 animation-1 examen">
                <div class="p-7 ">
                    <div class="grid grid-cols-1 lg:grid-cols-[1fr_350px] gap-10 items-center">
                        <div class="lg:border-r border-black/10 lg:pr-10">
                            <span class="inline-block font-bold text-rouge bg-black/3 px-3 py-1.5 rounded-md mb-5">
                                Examen
                            </span>
                            <h2 class="text-3xl lg:text-3xl font-bold mb-5"> {{ $examen->titre }}  </h2>
                            <p class="text-black/60 leading-relaxed max-w-2xl"> {{ $examen->description }} </p>
                        </div>
                        <div>
                            <p class="font-bold text-lg mb-4"> Statut de l'examen </p>
                            @if($peutCommencer)
                                <div class="inline-flex items-center gap-2 bg-black/3 text-vert rounded-xl px-4 py-2 mb-5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-vert"></span>
                                    <span class="font-semibold">
                                        Disponible
                                    </span>
                                </div>
                                <button
                                    type="button"
                                    onclick="openModal('confirm-start-modal')"
                                    class="w-full bg-rouge  hover:bg-rouge/90  text-white font-semibold rounded-full px-6 py-3.5  transition-all duration-200 flex items-center justify-center gap-3">
                                    <span> Commencer maintenant </span>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </button>
                                <div class="flex items-start gap-3 mt-5 text-sm text-black/50">
                                    <i class="fa-solid fa-shield-halved mt-0.5"></i>
                                    <p> Une fois commencé, le chronomètre démarre et ne peut pas être mis en pause.</p>
                                </div>
                            @else
                                <div class="inline-flex items-center gap-2 bg-black/3 text-yellow-600  rounded-md px-4 py-2 mb-5">
                                    <i class="fa-regular fa-clock"></i>
                                    <span class="font-semibold"> Pas encore disponible </span>
                                </div>
                                <button
                                    type="button"
                                    disabled
                                    class="w-full bg-black/10 text-black/40  rounded-full px-6 py-3.5  font-semibold  cursor-not-allowed flex items-center justify-center gap-3">
                                    <span>  Commencer maintenant</span>
                                    <i class="fa-solid fa-lock"></i>
                                </button>
                                <div class="flex items-start gap-3   mt-5 text-sm text-black/50">
                                    <i class="fa-solid fa-circle-info mt-0.5"></i>
                                    <p>
                                        Disponible le
                                        <strong>
                                            {{ $dateExamen->format('d/m/Y') }}
                                        </strong>.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            @if($peutCommencer)
                <x-confirm-modal
                    id="confirm-start-modal"
                    title="Es-tu prêt(e) ?"
                    :action="route('student.examen.start', $examen->id)"
                    confirmText="Oui, commencer"
                >
                    Une fois commencé, le chronomètre de
                    <span class="text-rouge font-semibold">
                        {{ $examen->duree_minutes }} minutes
                    </span>
                    démarre immédiatement et ne peut pas être mis
                    en pause.
                </x-confirm-modal>
            @endif
        @else
            <div class="border mb-7 border-black/3 rounded-xl
                        bg-black/2 p-16 text-center">
                <div class="w-20 h-20 mx-auto mb-5
                            rounded-full bg-black/5
                            flex items-center justify-center">
                    <i class="fa-solid fa-box-open
                              text-3xl text-black/40"></i>
                </div>
                <p class="text-xl font-semibold text-vert">
                    Aucun examen disponible pour le moment.
                </p>
                <p class="text-black/50 mt-2">
                    Veuillez revenir plus tard.
                </p>
            </div>
        @endif
        <div class="mb-8 animation-1 consigne">
            <div class="mb-6">
                <h2 class="text-2xl font-bold"> Consignes à respecter</h2>
                <div class="w-12 h-1 bg-rouge  rounded-full mt-3"> </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
                <div class="bg-white border border-black/10
                            rounded-xl p-6 text-center
                            hover:shadow-md
                            transition-all duration-200">
                    <div class="w-14 h-14 mx-auto mb-5
                                rounded-full bg-rouge/10
                                flex items-center justify-center">
                        <i class="fa-regular fa-clock
                                  text-vert text-2xl"></i>
                    </div>
                    <h3 class="font-bold mb-3">
                        Gestion du temps
                    </h3>
                    <p class="text-sm text-black/60 leading-relaxed">
                        La durée totale de l'examen est de
                        <strong class="text-rouge">
                            {{ $examen->duree_minutes ?? '__'}} minutes
                        </strong>.
                        Gérez bien votre temps.
                    </p>
                </div>
                <div class="bg-white border border-black/10
                            rounded-xl p-6 text-center
                            hover:shadow-md
                            transition-all duration-200">
                    <div class="w-14 h-14 mx-auto mb-5
                                rounded-full bg-rouge/10
                                flex items-center justify-center">
                        <i class="fa-solid fa-user
                                  text-vert text-2xl"></i>
                    </div>
                    <h3 class="font-bold mb-3">
                        Travail personnel
                    </h3>
                    <p class="text-sm text-black/60 leading-relaxed">
                        Cet examen doit être réalisé
                        individuellement.
                        Toute forme de triche est interdite.
                    </p>
                </div>
                <div class="bg-white border border-black/10
                            rounded-xl p-6 text-center
                            hover:shadow-md
                            transition-all duration-200">
                    <div class="w-14 h-14 mx-auto mb-5
                                rounded-full bg-rouge/10
                                flex items-center justify-center">
                        <i class="fa-solid fa-wifi
                                  text-vert text-2xl"></i>
                    </div>
                    <h3 class="font-bold mb-3">
                        Connexion stable
                    </h3>
                    <p class="text-sm text-black/60 leading-relaxed">
                        Assurez-vous d'avoir une bonne connexion
                        Internet pendant toute la durée de l'examen.
                    </p>
                </div>
                <div class="bg-white border border-black/10
                            rounded-xl p-6 text-center
                            hover:shadow-md
                            transition-all duration-200">
                    <div class="w-14 h-14 mx-auto mb-5
                                rounded-full bg-rouge/10
                                flex items-center justify-center">
                        <i class="fa-solid fa-clipboard-check
                                  text-vert text-2xl"></i>
                    </div>
                    <h3 class="font-bold mb-3">
                        Réponses
                    </h3>
                    <p class="text-sm text-black/60 leading-relaxed">
                        Lisez attentivement les questions
                        avant de valider vos réponses.
                    </p>
                </div>
                <div class="bg-white border border-black/10
                            rounded-xl p-6 text-center
                            hover:shadow-md
                            transition-all duration-200">
                    <div class="w-14 h-14 mx-auto mb-5
                                rounded-full bg-rouge/10
                                flex items-center justify-center">
                        <i class="fa-solid fa-ban
                                  text-vert text-2xl"></i>
                    </div>
                    <h3 class="font-bold mb-3">
                        Interdictions
                    </h3>
                    <p class="text-sm text-black/60 leading-relaxed">
                        Il est interdit d'utiliser des ressources
                        externes ou de quitter l'examen.
                    </p>
                </div>
            </div>
            <div class="mt-5 bg-yellow-500/10
                        border border-yellow-500/20
                        rounded-lg px-5 py-4
                        flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation
                          text-yellow-600"></i>
                <p class="text-sm text-black/70">
                    En cliquant sur
                    <strong>
                        « Commencer maintenant »
                    </strong>,
                    vous acceptez de respecter toutes les
                    consignes ci-dessus.
                </p>
            </div>
        </div>
    </div>
</section>
@endsection