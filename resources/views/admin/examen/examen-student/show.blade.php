@extends('layouts.admin-layouts.layouthead')
@section('contenue-admin')
    <div class="py-3">
        <a href="">Examen /</a>
        <div class="w-[60%] mt-1">
            <h2 class="text-2xl font-semibold text-vert">{{ $examen->titre }}</h2>
            <p>{{ $examen->description }}</p>
            <p>Date d'examen le <span class="underline font-semibold">{{ \Carbon\Carbon::parse($examen->date_examen)
                ->translatedFormat('d M Y') }}</span> </p>
        </div>

        @if(session('success'))
            <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 py-2 rounded-md mt-2 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('success-alert').remove()"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        @if(session('error'))
            <div id="error-alert" class="bg-red-100/50 text-rouge px-4 py-2 rounded-md mt-2 flex justify-between items-center">
                <span>{{ session('error') }}</span>
                <button type="button" onclick="document.getElementById('error-alert').remove()"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif


        <form id="bulk-notify-form" action="{{ route('admin.examen.student.notifierGroupe', [$slug, $examen->id]) }}" method="POST"
            class="mt-2">
            @csrf
            <div class="border border-black/3 rounded-md p-2 bg-black/2">
                <div class="flex justify-between items-center  mb-2 border-b border-black/10 pb-1">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" id="select-all">
                        Tout sélectionner
                    </label>
                    <button type="button" onclick="openBulkSendModal()" id="bulk-send-btn" class="bg-vert text-white px-4 py-1 rounded-full text-sm" disabled>
                        <i class="fa-solid fa-paper-plane"></i> Envoyer aux sélectionnés (<span id="selected-count">0</span>)
                    </button>

                    <div id="confirm-bulk-send-modal" class="fixed inset-0 bg-black/20 hidden items-center justify-center z-160 backdrop-blur-xs">
                        <div class="bg-white rounded-md p-8 w-[12cm] text-center">
                            <i class="fa-solid fa-circle-exclamation text-4xl text-rouge mb-3"></i>
                            <h3 class="text-xl font-semibold mb-2">Envoyer les invitations</h3>
                            <p class="text-black/60 mb-5">
                                Envoyer l'invitation à <span id="confirm-selected-count" class="font-semibold text-rouge">0</span> étudiant(s) sélectionné(s) ?
                            </p>
                            <div class="flex justify-center gap-3">
                                <button type="button" onclick="closeModal('confirm-bulk-send-modal')" class="border border-black/10 rounded-md px-5 py-2">
                                    Annuler
                                </button>
                                <button type="button" id="confirm-bulk-send-btn" class="bg-rouge text-white rounded-md px-5 py-2">
                                    Oui, envoyer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @forelse($studentwithexam as $se)
                    @php
                        $user = $se->user;
                        $studentId = $students[$user->id] ?? null;
                        $attempt = $studentId ? $attempts->get($studentId) : null;

                        if ($attempt?->status === 'corrige') {
                            $statutLabel = 'Corrigé';
                            $statutClass = 'bg-rouge';
                        } elseif ($attempt?->status === 'termine') {
                            $statutLabel = 'Finis';
                            $statutClass = 'bg-vert';
                        } else {
                            $statutLabel = 'En attente';
                            $statutClass = 'bg-black/40';
                        }
                    @endphp
                    @php
                        $aDejaFaitExamen = (bool) $attempt;
                    @endphp
                    <div class="flex gap-5 border border-black/2 p-3 last:border-b-0 rounded bg-white/80 ">
                        <div class="w-10 h-10 rounded-md bg-black/3 overflow-hidden border border-black/2">
                            <img src="{{ $user->image ? asset('images/' . $user->image) : '' }}" alt="" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base -mt-1">{{ $user->name }}
                                <span class="border border-black/10 text-white rounded-full px-2 text-sm {{ $statutClass }}">
                                    {{ $statutLabel }}
                                </span>
                            </h3>
                            <p class="text-sm">{{ $user->email }}</p>
                        </div>
                        <div class="flex gap-3 items-start">
                            <label for="emailenv"
                                class="flex rounded-full px-2 p-1 border border-black/5
                                        shadow gap-2">

                                <input type="checkbox"
                                    id="emailenv"
                                    name="student_ids[]"
                                    value="{{ $user->id }}"
                                    class="student-checkbox"
                                    {{ $aDejaFaitExamen ? 'disabled' : '' }}>

                                @if($aDejaFaitExamen)
                                    <i class="fa-solid fa-envelope-circle-check text-black/20"
                                    title="Étudiant ayant déjà passé l'examen — invitation non modifiable">
                                    </i>
                                @elseif(in_array($user->id, $emailsEnvoyes))
                                    <i class="fa-solid fa-envelope-circle-check text-vert"
                                    title="Invitation déjà envoyée">
                                    </i>
                                @else
                                    <i class="fa-solid fa-envelope text-black/50"
                                    title="Invitation non envoyée">
                                    </i>
                                @endif

                            </label>

                            @if($attempt)
                            <a href="{{ route('admin.examen.student.examenwherestudent', [$slug, $examen->id, $user->id]) }}">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                            @endif

                            <button type="submit" form="destroy-form-{{ $se->id }}" onclick="return confirm('Retirer cet étudiant ?')">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="bg-white/70 border border-black/3 p-20 text-center rounded-md">
                        <i class="fa-solid fa-user-xmark"></i>
                        <p class="text-black/50 text-center">Aucun étudiant pour cette date.</p>
                    </div>
                @endforelse
            </div>
        </form>

        @foreach($studentwithexam as $se)
            <form id="destroy-form-{{ $se->id }}" action="{{ route('admin.examen.student.destroy', [$slug, $examen->id, $se->id]) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        <div class="flex justify-end mt-5 sticky bottom-10">
            <a href="{{ route('admin.examen.student.create', [$slug, $examen->id]) }}" class="p-2 px-5 rounded-full bg-rouge text-white">
                + Ajouter Student
            </a>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.student-checkbox:not(:disabled)'); 
        const sendBtn = document.getElementById('bulk-send-btn');
        const countLabel = document.getElementById('selected-count');
        const bulkForm = document.getElementById('bulk-notify-form'); 

        function updateState() {
            const checked = document.querySelectorAll('.student-checkbox:checked:not(:disabled)').length;
            countLabel.textContent = checked;
            sendBtn.disabled = checked === 0;
        }

        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateState();
        });

        checkboxes.forEach(cb => cb.addEventListener('change', updateState));

        bulkForm.addEventListener('submit', function (e) {
            const checked = document.querySelectorAll('.student-checkbox:checked:not(:disabled)').length;
            if (checked === 0) {
                e.preventDefault();
                return;
            }
            if (!confirm(`Envoyer l'invitation à ${checked} étudiant(s) sélectionné(s) ?`)) {
                e.preventDefault();
            }
        });

        updateState();

        document.getElementById('confirm-bulk-send-btn').addEventListener('click', function () {
            document.getElementById('bulk-notify-form').submit(); // ✅ mandefa ilay formulaire tena misy checkbox
        });
    });

    function openBulkSendModal() {
        const checked = document.querySelectorAll('.student-checkbox:checked:not(:disabled)').length;
        document.getElementById('confirm-selected-count').textContent = checked;
        openModal('confirm-bulk-send-modal');
    }
    </script>
@endsection