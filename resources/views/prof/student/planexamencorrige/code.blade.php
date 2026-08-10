@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="py-3 me-2">
    @include('layouts.prof-layouts.proflayoutsexamcorrige')

    @if(session('success'))
        <div id="success-alert" class="bg-green-100/50 text-green-700 px-4 mt-2 py-2 rounded-md mb-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="document.getElementById('success-alert').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <div id="section-code">
        <h2 class="p-1 flex mt-2 gap-2 items-center text-rouge">
            <i class="fa-solid fa-code"></i> Exercice de code <i class="fa-solid fa-code"></i>
        </h2>
        @foreach($codes as $code)
            @php
                $reponsesCode = $code->codeQuestions->flatMap(fn($q) => $q->reponses);
                $obtenusCode = $reponsesCode->sum('points_obtenus');
                $estCorrigeCode = $reponsesCode->isNotEmpty() && $reponsesCode->every(fn($r) => $r->points_obtenus !== null);
            @endphp
            <div class="border border-black/10 p-4 rounded-md mb-3">
                <div class="flex gap-3 mb-2">
                    <div class="w-12 h-12 rounded-md bg-black/3 flex justify-center items-center font-semibold">
                        {{ $loop->iteration }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <h3 class="text-lg font-semibold flex-1">{{ $code->titre }}</h3>
                            <div class="text-sm flex gap-3">
                                <span class="border text-sm border-black/20 rounded-full px-2 {{ $estCorrigeCode ? 'text-rouge' : 'text-black/40' }}">
                                    {{ $estCorrigeCode ? $obtenusCode . ' Pts obtenus' : 'En attente' }}
                                </span>
                                <span class="border text-sm border-black/20 rounded-full px-2 text-vert">
                                    {{ $code->note_totale }} Pts total
                                </span>
                            </div>
                        </div>

                        <form class="code-annot-form" action="{{ route('prof.correction.code.annoter', $code->id) }}" method="POST">
                            @csrf

                            @if($errors->any())
                                <div class="mb-3 p-3 mt-2 rounded-md bg-red-50 border border-rouge text-rouge text-sm">
                                    @foreach($errors->all() as $error)
                                        <p>{{ $error }}</p>
                                    @endforeach
                                </div>
                            @endif

                            <div class="mt-2">
                                @foreach ($code->codeQuestions as $question)
                                    @php $reponse = $question->reponses->first(); @endphp
                                    <div class="p-2 rounded-md bg-black/2 border border-black/3 my-1">
                                        <div class="flex gap-3 justify-between">
                                            <p>{{ $question->ordre }} - {{ $question->instruction }}</p>
                                        </div>

                                        <div class="max-w-full bg-white border border-black/3 rounded overflow-x-auto">
                                            <div class="flex justify-between items-center p-1 border-b-2 border-black/5 rounded-t">
                                                <div class="flex gap-1">
                                                    <button type="button" class="annot-btn px-2 rounded border border-black/10 bg-white text-sm underline" data-cmd="underline" title="Souligner">U</button>
                                                    <button type="button" class="annot-btn px-2 rounded border border-black/10 bg-white text-sm line-through" data-cmd="strikeThrough" title="Barrer">S</button>
                                                    <button type="button" class="annot-btn px-2 rounded border border-black/10 bg-white text-sm text-red-500 font-semibold" data-cmd="rouge" title="Écrire en rouge">A</button>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <input type="text" name="reponses[{{ $reponse->id ?? 0 }}][points_obtenus]"
                                                        min="0" max="{{ $question->points }}"
                                                        value="{{ old('reponses.' . ($reponse->id ?? 0) . '.points_obtenus', $reponse->points_obtenus) }}"
                                                        placeholder="Note"
                                                        class="border border-black/20 bg-black/3 rounded w-[2cm] text-center">
                                                    <span class="text-sm text-black/40">/ {{ $question->points }} Pts</span>
                                                </div>
                                            </div>

                                            <pre class="code-annot-content p-2 inline-block min-w-full outline-none" contenteditable="true">{!! old(
                                                'reponses.' . ($reponse->id ?? 0) . '.code_annote',
                                                $reponse?->code_annote ?? e($reponse?->code_soumis ?? 'Aucun code soumis')
                                            ) !!}</pre>

                                            <input type="hidden" name="reponses[{{ $reponse->id ?? 0 }}][code_annote]" class="code-reponse-annotee-input"
                                                value="{{ old('reponses.' . ($reponse->id ?? 0) . '.code_annote') }}">
                                        </div>

                                        @if($reponse?->commentaire_prof)
                                            <div class="mt-2 pt-2 border-t border-black/10 text-sm">
                                                <span class="text-black/50">Commentaire du professeur :</span>
                                                <p class="whitespace-pre-line">{{ $reponse->commentaire_prof }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                                <div class="flex justify-end mt-2">
                                    <button type="submit" class="rounded-md p-2 px-3 bg-vert text-white">Valider</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        @if($typeCode)
            <form action="{{ route('prof.correction.storeCommentaire') }}" method="POST">
                @csrf
                <input type="hidden" name="commentable_id" value="{{ $typeCode->id }}">
                <input type="hidden" name="commentable_type" value="{{ \App\Models\TypeExercice::class }}">
                <input type="hidden" name="examen_id" value="{{ $examen->id }}">
                <input type="hidden" name="exam_attempt_id" value="{{ $attempt->id }}">

                <div class="border border-black/10 rounded-md p-2 bg-black/3">
                    <textarea name="contenu" rows="2"
                        class="border border-black/10 w-full rounded p-2 bg-white"
                        placeholder="Commente ici cette exercice ..">{{ old('contenu', $commentsCode->contenu ?? '') }}</textarea>
                    <div class="flex justify-end mt-1">
                        <button type="submit" class="p-1 px-2 rounded text-white bg-rouge">
                            {{ $commentsCode ? 'Modifier le commentaire' : 'Commenter' }}
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.code-annot-form').forEach(function (form) {

        form.querySelectorAll('.code-annot-content').forEach(function (contentEl) {
            const hiddenInput = contentEl.parentElement.querySelector('.code-reponse-annotee-input');
            const toolbar = contentEl.previousElementSibling;

            hiddenInput.value = contentEl.innerHTML;

            function activerCouleurParDefaut() {
                const selection = window.getSelection();
                if (!selection.rangeCount || !selection.isCollapsed) {
                    return;
                }
                document.execCommand('styleWithCSS', false, true);
                document.execCommand('foreColor', false, 'rgb(250, 131, 51)');
            }

            contentEl.addEventListener('focus', activerCouleurParDefaut);
            contentEl.addEventListener('keyup', function (e) {
                if (['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'].includes(e.key)) {
                    activerCouleurParDefaut();
                }
            });
            contentEl.addEventListener('mouseup', activerCouleurParDefaut);

            toolbar.querySelectorAll('.annot-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    contentEl.focus();
                    document.execCommand('styleWithCSS', false, true);
                    const cmd = btn.dataset.cmd;

                    if (cmd === 'rouge') {
                        document.execCommand('foreColor', false, 'rgb(220, 38, 38)');
                    } else {
                        document.execCommand(cmd, false, null);
                    }
                });
            });

            contentEl.addEventListener('input', function () {
                hiddenInput.value = contentEl.innerHTML;
            });
        });

        form.addEventListener('submit', function () {
            form.querySelectorAll('.code-annot-content').forEach(function (contentEl) {
                const hiddenInput = contentEl.parentElement.querySelector('.code-reponse-annotee-input');
                hiddenInput.value = contentEl.innerHTML;
            });
        });
    });
});
</script>
@endsection