@extends('layouts.student-layouts.layoutexamen')
@section('exercice-content')
    <div class="">
        <div class="w-[22cm] m-auto ">
            <div class="my-10">
                <div class="flex justify-between items-center">
                    <span>Exercice</span>
                    <span>{{ $index + 1 }}/{{ $total }}</span>
                </div>
                <div class="rounded-full h-3 overflow-hidden bg-black/10">
                    <div class="h-full bg-sgress" style="width: {{ (($index + 1) / $total) * 100 }}%"></div>
                </div>
            </div>
            <div class="pb-4">
                <div class="flex gap-3 border-b-2  border-black/10 pb-3">
                    <div class="w-8 h-8 rounded-md bg-black/10 flex justify-center items-center font-semibold text-rouge">
                        01
                    </div>
                    <div class="">
                        <h3 class="text-base font-semibold">{{ $pointiller->titre }}</h3>
                        <div class="flex gap-3 text-sm">
                            <span class="border border-green-600 rounded-full px-3">
                                {{ $questions->count() }} Questions
                            </span>
                            <span class="border border-amber-500 rounded-full px-3">
                                {{ $totalPoints }} Points
                            </span>
                        </div>
                    </div>
                </div>
                <form action="{{ route('examen.pointiller.store', ['examen' => $examen->id, 'slug' => $slug, 'pointiller' => $pointiller->id]) }}" method="POST" class="rounded-md p-2 px-4 mt-4 border border-black/10">
                    @csrf
                    @foreach($questions as $index => $question)
                        @if($question->reponses)
                            <div class="border-b border-black/5 py-2">
                                <span>{{ $index + 1 }} -</span>
                                {!! $question->enonce_avec_trou !!}
                            </div>
                        @endif
                    @endforeach
                    <div class="flex justify-end mt-5">
                        <button type="submit" class="py-2 px-5 rounded-md bg-rouge text-white">Valider</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection