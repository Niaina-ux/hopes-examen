@extends('layouts.student-layouts.layoutexamen')
@section('exercice-content')
<div class="pb-10">
    <div class="my-10">
        <div class="flex justify-between items-center">
            <span>Exercice</span>
            <span>{{ $index + 1 }}/{{ $total }}</span>
        </div>
        <div class="rounded-full h-3 overflow-hidden bg-black/10">
            <div class="h-full bg-sgress" style="width: {{ (($index + 1) / $total) * 100 }}%"></div>
        </div>
    </div>
    <form action="{{ route('examen.code.store', ['examen' => $examen->id, 'slug' => $slug, 'code' => $code->id]) }}" method="POST">
        @csrf
        @foreach($questions as $qIndex => $question)
            <div class="rounded-md mb-6">
                <div class="flex gap-3 pb-2 border-b-2 border-black/10 mb-4">
                    <div class="text-vert font-semibold bg-black/5 rounded-md w-7 h-7 flex justify-center items-center">
                        {{ $qIndex + 1 }}
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-semibold">{{ $question->instruction }}</h3>
                        <div class="text-sm flex gap-2 mt-1">
                            <span class="rounded-full border border-black/10 text-rouge px-3">
                                {{ rtrim(rtrim(number_format($question->points, 2), '0'), '.') }} Points
                            </span>
                            <span class="rounded-full border border-black/10 text-vert px-3">
                                {{ strtoupper($question->langage) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div id="editor-{{ $question->id }}"
                    class="editor">
                </div>
                <textarea
                    id="code-{{ $question->id }}"
                    name="codes[{{ $question->id }}]"
                    class="hidden text-xs"
                >{{ $reponsesExistantes[$question->id] ?? $question->code_starter }}</textarea>
            </div>
        @endforeach
        <div class="flex justify-end">
            <button type="submit" class="p-2 px-5 rounded-md bg-rouge text-white">
                {{ $index + 1 == $total ? 'Terminer' : 'Valider' }}
            </button>
        </div>
    </form>
   <script>
    document.addEventListener("DOMContentLoaded", function(){
        @foreach($questions as $question)
        (function(){
            var editor = ace.edit("editor-{{ $question->id }}");
            editor.setTheme("ace/theme/monokai");
            editor.session.setMode("ace/mode/php");
            editor.setValue(
                document.getElementById("code-{{ $question->id }}").value,
                -1
            );
            editor.setOptions({
                fontSize:16,
                showPrintMargin:false
            });
            document.querySelector("form").addEventListener("submit",function(){
                document.getElementById("code-{{ $question->id }}").value =
                    editor.getValue();
            });
        })();
        @endforeach
    });
    </script>
</div>
<style>
.editor{
    width:100%;
    height:300px;
    border-radius: 7px;
}
.ace_editor,
.ace_editor * {
    font-family: monospace !important;
    line-height: normal !important;
    letter-spacing: normal !important;
}
.ace_editor {
    font-size: 15px !important;
}
.ace_text-layer {
    color: inherit;
}
.ace_cursor {
    opacity: 1 !important;
}
</style>
</style>
@endsection