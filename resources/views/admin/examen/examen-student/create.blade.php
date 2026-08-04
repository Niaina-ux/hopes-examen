@extends('layouts.admin-layouts.layouthead')
@section('contenue-admin')
<div class="w-full py-3">
    <div class="bg-white rounded-md">
        <a href="">
            <i class="fa-solid fa-arrow-left-long"></i>
        </a>
        <div class="w-[60%]">
            <h2 class="text-2xl font-semibold mb-1 text-vert">Assigner des étudiants — {{ $examen->titre }}</h2>
            <p class="text-black/50">
                Seuls les étudiants de la catégorie <strong>{{ $examen->categorie->nom ?? '' }}</strong> sont affichés ci-dessous.
            </p>
        </div>
        
        @if(session('success'))
            <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md mt-4 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('success-alert').remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        <form action="{{ route('admin.examen.student.store', [$slug, $examen->id]) }}" method="POST"
            class="mt-2">
            @csrf
            <div class="flex justify-between pb-2 items-center">
                <div class="w-[10cm] relative border-2 border-black/20 bg-black/3 rounded-md">
                    <label class="block text-base font-medium absolute top-[50%] right-3 translate-y-[-50%]">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </label>
                    <input type="text" id="filtre-nom" placeholder="Nom d'etudiant .."
                        class="border-2 rounded-md p-1  w-full pe-5  border-white">
                </div>
                <div class="border-2 border-black/20 bg-black/3 rounded-md">
                    <input type="date" name="date_examen" id="date-examen" value="{{ old('date_examen') }}" class="border-2 rounded-md p-1  w-full  border-white">
                    @error('date_examen') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <hr class="border border-black/10">
            <div class="my-2">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-base  font-semibold">Étudiants</label>
                    <div class="flex gap-3 text-sm">
                        <button type="button" id="select-all" class="text-vert underline">Tout sélectionner</button>
                        <button type="button" id="deselect-all" class="text-rouge underline">Tout désélectionner</button>
                    </div>
                </div>
                @error('student_user_ids') <p class="text-red-500 text-sm mb-2">{{ $message }}</p> @enderror
                <div class="border border-black/10 rounded-md overflow-y-auto p-2" id="students-list">
                    @forelse($students as $student)
                        <label class="student-item flex justify-between gap-4 p-2 border-b border-black/10 last:border-b-0 hover:bg-black/3" data-nom="{{ strtolower($student->user->name) }}">
                            <div class="w-10 h-10 rounded-md overflow-hidden border border-black/10">
                                <img src="{{ $student->user->image ? asset('images/' . $student->user->image) : asset('images/default-avatar.png') }}"
                                alt="{{ $student->name }}"
                                class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base">{{ $student->user->name }}</h3>
                                <div class="text-sm">
                                    <span class=""> {{$student->user->email}} </span>
                                    (<span class=" text-rouge">{{ $student->matricule }}</span>)
                                </div>
                            </div>
                            <input
                                type="checkbox"
                                name="student_user_ids[]"
                                value="{{ $student->user_id }}"
                                class="student-checkbox custom-checkbox"
                                data-date="{{ $assignations[$student->user_id] ?? '' }}"
                            >
                        </label>
                    @empty
                        <p class="text-black/50 p-4">Aucun étudiant dans cette catégorie.</p>
                    @endforelse
                    <p id="no-result" class="text-black/50 p-4 hidden">Aucun étudiant ne correspond à votre recherche.</p>
                </div>
            </div>

            <div class="flex justify-end mt-4 sticky bottom-10">
                <button type="submit" id="submit-btn" class="bg-rouge text-white px-4 py-2 rounded">
                    Assigner l'examen
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('select-all').addEventListener('click', function () {
    document.querySelectorAll('.student-checkbox:not(.hidden-by-filter)').forEach(cb => cb.checked = true);
});

document.getElementById('deselect-all').addEventListener('click', function () {
    document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
});

document.getElementById('filtre-nom').addEventListener('keyup', function () {
    const recherche = this.value.toLowerCase().trim();
    const items = document.querySelectorAll('.student-item');
    let auMoinsUnVisible = false;

    items.forEach(function (item) {
        const nom = item.dataset.nom;
        if (nom.includes(recherche)) {
            item.classList.remove('hidden');
            auMoinsUnVisible = true;
        } else {
            item.classList.add('hidden');
        }
    });

    document.getElementById('no-result').classList.toggle('hidden', auMoinsUnVisible);
});


function mettreAJourChecksSelonDate() {
    const dateChoisie = document.getElementById('date-examen').value;

    document.querySelectorAll('.student-checkbox').forEach(function (cb) {
        const dateAssignee = cb.dataset.date;

        if (dateChoisie && dateAssignee === dateChoisie) {
            cb.checked = true;
        } else {
            cb.checked = false;
        }
    });
}

document.getElementById('date-examen').addEventListener('change', mettreAJourChecksSelonDate);

document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('date-examen').value) {
        mettreAJourChecksSelonDate();
    }
});

document.querySelector('form').addEventListener('submit', function () {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerText = 'Enregistrement...';
});
</script>
@endsection