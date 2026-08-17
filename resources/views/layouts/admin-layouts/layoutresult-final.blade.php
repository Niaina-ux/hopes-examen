<div class="w-[22cm] bg-white m-auto rounded-xl overflow-hidden">
    <div class="flex items-center justify-between p-1 px-4 border-b-2 border-black/10 bg-vert text-white">
        <h3 class="text-xl">Note Général d'examen</h3>
        <div class="flex items-center gap-3">
            @if($statistiques->isNotEmpty())
                <button type="button" onclick="openModal('envoyer-bulletin-modal')" title="Envoyer le résultat final par email">
                    <i class="fa-solid fa-envelope"></i>
                </button>

                <x-confirm-modal
                    id="envoyer-bulletin-modal"
                    title="Envoyer le résultat"
                    action="{{ route('admin.student.resultatFinal.envoyer', [$slug, $student->id]) }}"
                    confirmText="Oui, envoyer"
                    cancelText="Annuler">
                    Envoyer ce résultat final par email à <span class="text-rouge font-semibold">{{ $student->name }}</span> ?
                </x-confirm-modal>
                <a href="{{ route('admin.student.resultatFinal.download', [$slug, $student->id]) }}">
                    <i class="fa-solid fa-download"></i>
                </a>
            @endif
            <button type="button" onclick="closeModal('resultat-final-modal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
    </div>
    <div class="p-4 sm:p-10 bg-white h-[85vh] overflow-y-auto">
        <div class="flex items-center gap-5 pb-2 border-s-3 border-black/30 ps-4">
            <div class="w-15 h-15 rounded-full overflow-hidden border border-black/5 flex justify-center items-center">
                @if (isset($student->image))   
                <img src="{{$student->image ? asset('images/users/'. $student->image) : ''}}" alt="">
                @else
                <i class="fa-solid fa-user-graduate text-2xl bg-black/70"></i>
                @endif
            </div>
            <div class="min-w-0 text-left">
                <h3 class="font-bold text-base sm:text-lg truncate">{{ $student->name }}</h3>
                <p>Matricule : <span class="">{{ $userStudent->matricule ?? '—' }}</span></p>
                <p>Domaine : <span class=""> {{ $userStudent->categorie->nom ?? '—' }}</span></p>
            </div>
        </div>
    
        <div class="relative py-7 text-center">
            <div class="absolute left-0 top-1/2 w-[25%] h-px bg-black/40"></div>
            <div class="absolute right-0 top-1/2 w-[25%] h-px bg-black/40"></div>
            <h2 class="relative inline-block px-5 text-xl sm:text-2xl font-extrabold uppercase text-vert">
                Résultat final 
            </h2>
            <div class="w-28 h-[2px] bg-black/20 mx-auto mt-3"></div>
        </div>
    
        <div class="overflow-x-auto">
            <table class="w-full min-w-[500px] border-collapse">
                <thead>
                    <tr class="bg-black/50 text-white border-2 border-black/20">
                        <th class="w-[15%] px-4 py-1 text-center border-r-2 border-white/30">N°</th>
                        <th class="px-4 py-1 text-center border-r-2 border-white/30">Titre</th>
                        <th class="w-[25%] px-4 py-1 text-center">Note</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($statistiques as $index => $s)
                        <tr class="border-2 border-black/20">
                            <td class="px-4 py-2 text-center font-semibold border-r-2 border-black/20">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 border-r-2 border-black/20">{{ $s['titre'] }}</td>
                            <td class="px-4 py-2 text-center font-bold">{{ $s['pourcentage'] }} %</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-black/40">Aucun examen corrigé pour l'instant.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    
    
        <div class="mt-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <p><strong>Moyenne générale : {{ $moyenneGenerale ?? 0 }} %</strong> ({{ $statistiques->count() }} examens)</p>
            <div class="text-center">
                <p class="italic font-medium text-orange-500">Félicitations et courage pour la suite !</p>
                <div class="w-40 h-[2px] bg-orange-500 mx-auto mt-2"></div>
            </div>
        </div>
        <div class="text-sm flex justify-end items-center gap-2 mt-10">
            <i class="fa-regular fa-calendar text-xl"></i>
            <span>Date d'édition : <strong>{{ now()->translatedFormat('d F Y') }}</strong></span>
        </div>
    </div>
    </div>