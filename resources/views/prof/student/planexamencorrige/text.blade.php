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
    <div id="section-text" class="text-base">
        <h2 class="p-1 mt-2 flex gap-2 items-center text-rouge">
            <i class="fa-solid fa-align-left"></i> Compréhension du texte <i class="fa-solid fa-align-left"></i>
        </h2>
        @foreach($texts as $text)
            @php
                $reponsesText = $text->textQuestions->flatMap(fn($q) => $q->reponses);
                $obtenusText = $reponsesText->sum('note_obtenue');
                $estCorrigeText = $reponsesText->isNotEmpty() && $reponsesText->every(fn($r) => $r->note_obtenue !== null);
            @endphp
            <div class="border border-black/10 p-4 rounded-md mb-3">
                <div class="flex gap-3 mb-2">
                    <div class="w-12 h-12 rounded-md bg-black/3 flex justify-center items-center font-semibold">
                        {{ $loop->iteration }}
                    </div>
                    <div class="flex-1">
                        <div class="flex gap-3 items-center">
                            <h3 class="text-lg font-semibold flex-1">{{ $text->titre }}</h3>
                            <div class="text-sm flex gap-3">
                                <span class="border border-black/20 rounded-full px-2 {{ $estCorrigeText ? 'text-rouge' : 'text-black/40' }}">
                                    {{ $estCorrigeText ? $obtenusText . ' Pts obtenus' : 'En attente' }}
                                </span>
                                <span class="border border-black/20 rounded-full px-2 text-vert">
                                    {{ $text->note_totale }} Pts total
                                </span>
                            </div>
                        </div>

                        <div class="p-2 rounded-md bg-black/2 border border-black/2 mt-2">
                            <span>Text</span>
                            <p class="whitespace-pre-line p-2 rounded bg-white/40 border border-black/2">{{ $text->texte }}</p>
                            <span class="my-2 inline-block">Questions & Réponses</span>

                            <form class="text-annot-form" action="{{ route('prof.correction.text.annoter', $text->id) }}" method="POST">
                            @csrf

                            @if($errors->any())
                                <div class="mb-3 p-3 rounded-md bg-red-50 border border-rouge text-rouge text-sm">
                                    @foreach($errors->all() as $error)
                                        <p>{{ $error }}</p>
                                    @endforeach
                                </div>
                            @endif

                                @foreach ($text->textQuestions as $index => $textQuestion)
                                    @php $reponse = $textQuestion->reponses->first(); @endphp
                                    <div class="mb-2 border border-black/4 bg-white/30 p-2 rounded">
                                        <div class="flex justify-between gap-3">
                                            <p>{{ $index + 1 }} - {{ $textQuestion->enonce }}</p>
                                        </div>

                                        <div class="rounded bg-white border border-black/3 mt-1">
                                            <div class="flex justify-between items-center p-1 border-b-2 border-black/5 rounded-t">
                                                <div class="flex gap-1">
                                                    <button type="button" class="annot-btn px-2 rounded border border-black/10 bg-white text-sm font-bold" data-cmd="bold" title="Gras">B</button>
                                                    <button type="button" class="annot-btn px-2 rounded border border-black/10 bg-white text-sm underline" data-cmd="underline" title="Souligner">U</button>
                                                    <button type="button" class="annot-btn px-2 rounded border border-black/10 bg-white text-sm line-through" data-cmd="strikeThrough" title="Barrer">S</button>
                                                    <button type="button" class="annot-btn px-2 rounded border border-black/10 bg-white text-sm text-red-500 font-semibold" data-cmd="rouge" title="Écrire en rouge">A</button>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <input type="text" name="reponses[{{ $reponse->id ?? 0 }}][note_obtenue]"
                                                        min="0" max="{{ $textQuestion->points }}"
                                                        value="{{ old('reponses.' . ($reponse->id ?? 0) . '.note_obtenue', $reponse->note_obtenue) }}"
                                                        placeholder="Note"
                                                        class="border border-black/20 bg-black/3 rounded w-[2cm] text-center">
                                                    <span class="text-sm text-black/40">/ {{ $textQuestion->points }} Pts</span>
                                                </div>
                                            </div>

                                            <div class="text-annot-content p-2 outline-none" contenteditable="true">{!! old(
                                                'reponses.' . ($reponse->id ?? 0) . '.reponse_annotee',
                                                $reponse?->reponse_annotee ?? e($reponse?->reponse_texte ?? 'Réponse vide !!')
                                            ) !!}</div>

                                            {{-- ✅ Nafindra ato anaty, mitovy parentElement amin'ny .text-annot-content --}}
                                            <input type="hidden" name="reponses[{{ $reponse->id ?? 0 }}][reponse_annotee]" class="text-reponse-annotee-input"
                                                value="{{ old('reponses.' . ($reponse->id ?? 0) . '.reponse_annotee') }}">
                                        </div>

                                        @if($reponse?->commentaire_prof)
                                            <div class="mt-2 pt-2 border-t border-black/10 text-sm">
                                                <span class="text-black/50">Commentaire du professeur :</span>
                                                <p class="whitespace-pre-line">{{ $reponse->commentaire_prof }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                                <div class="flex justify-end mt-1">
                                    <button type="submit" class="rounded-md p-2 px-4 bg-vert text-white text-sm">Valider</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Commentaire du prof — un seul, pour toute la section Text de cet examen+attempt --}}
        @if($typeText)
            <form action="{{ route('prof.correction.storeCommentaire') }}" method="POST">
                @csrf
                <input type="hidden" name="commentable_id" value="{{ $typeText->id }}">
                <input type="hidden" name="commentable_type" value="{{ \App\Models\TypeExercice::class }}">
                <input type="hidden" name="examen_id" value="{{ $examen->id }}">
                <input type="hidden" name="exam_attempt_id" value="{{ $attempt->id }}">

                <div class="border border-black/10 rounded-md p-2 bg-black/3">
                    <textarea name="contenu" rows="2"
                        class="border border-black/10 w-full rounded p-2 bg-white"
                        placeholder="Commente ici cette exercice ..">{{ old('contenu', $commentsText->contenu ?? '') }}</textarea>
                    <div class="flex justify-end mt-1">
                        <button type="submit" class="p-1 px-2 rounded text-white bg-rouge">
                            {{ $commentsText ? 'Modifier le commentaire' : 'Commenter' }}
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.text-annot-form').forEach(function (form) {

        form.querySelectorAll('.text-annot-content').forEach(function (contentEl) {
            const hiddenInput = contentEl.parentElement.querySelector('.text-reponse-annotee-input');
            const toolbar = contentEl.previousElementSibling;

            hiddenInput.value = contentEl.innerHTML;

            // ✅ Typing color automatique — ampiharina RAHA TSY MISY sélection (curseur fotsiny)
            function activerCouleurParDefaut() {
                const selection = window.getSelection();
                if (!selection.rangeCount || !selection.isCollapsed) {
                    return; // misy teny voafidy — aza mikasika izany, avelao hisafidy amin'ny bokotra ny prof
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
            // ✅ mouseup fa tsy click — mba hahafahana mamantatra raha vao vita ny sélection
            contentEl.addEventListener('mouseup', activerCouleurParDefaut);

            // Bokotra manokana — ny prof no misafidy manokana ny mise en forme
            toolbar.querySelectorAll('.annot-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    contentEl.focus();
                    document.execCommand('styleWithCSS', false, true);
                    const cmd = btn.dataset.cmd;

                    if (cmd === 'rouge') {
                        document.execCommand('foreColor', false, 'rgb(220, 38, 38)'); // ✅ red-600, hafa amin'ny typing color
                    } else {
                        document.execCommand(cmd, false, null); // bold, underline, strikeThrough
                    }
                });
            });

            contentEl.addEventListener('input', function () {
                hiddenInput.value = contentEl.innerHTML;
            });
        });

        form.addEventListener('submit', function () {
            form.querySelectorAll('.text-annot-content').forEach(function (contentEl) {
                const hiddenInput = contentEl.parentElement.querySelector('.text-reponse-annotee-input');
                hiddenInput.value = contentEl.innerHTML;
            });
        });
    });
});
</script>
@endsection