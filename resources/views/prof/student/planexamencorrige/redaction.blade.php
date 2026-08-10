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
    <div id="section-redaction">
        <h2 class="p-1 mt-2 flex gap-2 items-center text-rouge">
            <i class="fa-solid fa-feather"></i> Rédaction <i class="fa-solid fa-feather"></i>
        </h2>
        @foreach($redactions as $redaction)
            @php $reponse = $redaction->reponses[0] ?? null; @endphp
            <div class="border border-black/10 p-4 rounded-md mb-3">
                <div class="flex gap-3 mb-2">
                    <div class="w-12 h-12 rounded-md bg-black/3 flex justify-center items-center font-semibold">
                        {{ $loop->iteration }}
                    </div>
                    <div class="flex-1">
                        <div class="flex-1 flex gap-3 items-center">
                            <h3 class="text-lg font-semibold flex-1">{{ $redaction->titre }}</h3>
                            <div class="text-sm flex gap-3">
                                <span class="border border-black/20 rounded-full px-2 text-rouge">
                                    {{ $reponse->note_obtenue ?? 'en attente' }} Pts
                                </span> /
                                <span class="border border-black/20 rounded-full px-2 text-vert">
                                    {{ $redaction->note_totale }} Pts total
                                </span>
                            </div>
                        </div>

                        <div class="text-base bg-black/1 border border-black/2 rounded-md p-2 mt-2">
                            <span>Sujet</span>
                            <p class="mb-1 whitespace-pre-line">{{ $redaction->sujet }}</p>
                            <span>Instruction</span>
                            <p class="mb-1 whitespace-pre-line">{{ $redaction->instruction }}</p>
                            <span>Réponse</span>

                            <form class="redaction-form" data-reponse-id="{{ $reponse->id ?? '' }}"
                                action="{{ route('prof.correction.redaction.annoter', $reponse->id ?? 0) }}" method="POST">
                                @csrf

                                @if($errors->any() && old('_reponse_id') == ($reponse->id ?? null))
                                    <div class="mb-2 p-2 rounded-md bg-red-50 border border-rouge text-rouge text-sm">
                                        @foreach($errors->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- ✅ Champ caché pour savoir à quelle réponse appartiennent les erreurs old() --}}
                                <input type="hidden" name="_reponse_id" value="{{ $reponse->id ?? '' }}">

                                <div class="bg-white/60 rounded border border-black/3 mt-1">
                                    {{-- Toolbar --}}
                                    <div class="flex justify-between items-center p-1 border-b-2 border-black/5 rounded-t bg-black/2">
                                        <div class="flex gap-1">
                                            <button type="button" class="annot-btn px-2 rounded border border-black/10 bg-white text-sm font-bold" data-cmd="bold" title="Gras">B</button>
                                            <button type="button" class="annot-btn px-2 rounded border border-black/10 bg-white text-sm underline" data-cmd="underline" title="Souligner">U</button>
                                            <button type="button" class="annot-btn px-2 rounded border border-black/10 bg-white text-sm line-through" data-cmd="strikeThrough" title="Barrer">S</button>
                                            <button type="button" class="annot-btn px-2 rounded border border-black/10 bg-white text-sm text-rouge font-semibold" data-cmd="rouge" title="Écrire en rouge">A</button>
                                        </div>
                                        <input type="text" name="note_obtenue" min="0" max="{{ $redaction->note_totale }}"
                                            value="{{ old('_reponse_id') == ($reponse->id ?? null) ? old('note_obtenue') : $reponse->note_obtenue }}"
                                            placeholder="Note"
                                            class="border border-black/20 bg-black/3 rounded w-[2cm] text-center">
                                    </div>

                                    {{-- Zone annotable — priorité à old() si erreur de validation sur CETTE réponse --}}
                                    <div class="redaction-content whitespace-pre-line p-2 outline-none" contenteditable="true">{!! old('_reponse_id') == ($reponse->id ?? null) && old('reponse_annotee')
                                        ? old('reponse_annotee')
                                        : ($reponse->reponse_annotee ?? $reponse->reponse_texte ?? '') !!}</div>
                                </div>

                                <input type="hidden" name="reponse_annotee" class="reponse-annotee-input">

                                <div class="flex justify-end mt-2">
                                    <button type="submit" class="rounded-md p-1 px-3 bg-vert text-white">Valider</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if($typeRedaction)
            <form action="{{ route('prof.correction.storeCommentaire') }}" method="POST">
                @csrf
                <input type="hidden" name="commentable_id" value="{{ $typeRedaction->id }}">
                <input type="hidden" name="commentable_type" value="{{ \App\Models\TypeExercice::class }}">
                <input type="hidden" name="examen_id" value="{{ $examen->id }}">
                <input type="hidden" name="exam_attempt_id" value="{{ $attempt->id }}">

                <div class="border border-black/10 rounded-md p-2 bg-black/3">
                    <textarea name="contenu" rows="2"
                        class="border border-black/10 w-full rounded p-2 bg-white"
                        placeholder="Commente ici cette exercice ..">{{ old('contenu', $commentsRedaction->contenu ?? '') }}</textarea>
                    <div class="flex justify-end mt-1">
                        <button type="submit" class="p-1 px-2 rounded text-white bg-rouge">
                            {{ $commentsRedaction ? 'Modifier le commentaire' : 'Commenter' }}
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.redaction-form').forEach(function (form) {
        const contentEl = form.querySelector('.redaction-content');
        const hiddenInput = form.querySelector('.reponse-annotee-input');

        function activerRougeParDefaut() {
            document.execCommand('styleWithCSS', false, true);
            document.execCommand('foreColor', false, 'rgb(250, 131, 51)');
        }

        contentEl.addEventListener('focus', activerRougeParDefaut);
        contentEl.addEventListener('click', activerRougeParDefaut);
        contentEl.addEventListener('keyup', function (e) {
            
            if (['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'].includes(e.key)) {
                activerRougeParDefaut();
            }
        });

        form.querySelectorAll('.annot-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                contentEl.focus();
                document.execCommand('styleWithCSS', false, true);
                const cmd = btn.dataset.cmd;

                if (cmd === 'rouge') {
                    document.execCommand('foreColor', false, '#dc2626');
                } else if (cmd === 'vert') {
                    document.execCommand('foreColor', false, '#16a34a');
                } else {
                    document.execCommand(cmd, false, null); 
                }
            });
        });

        form.addEventListener('submit', function () {
            hiddenInput.value = contentEl.innerHTML;
        });
    });
});
</script>
@endsection