@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
    <div class="me-2 py-3">
        <div class="">
            <a href="{{ route('prof.examen.show', [$slug]) }}" class="hover:underline">Retour / </a>
            <span class="font-semibold">Etudiants</span>
        </div>
        <div class="mb-2">
            <h2 class="text-2xl font-semibold text-vert">{{ $examen->titre }}</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Velit, earum.</p>
        </div>

        {{-- ✅ Lisitry ny daty, mifototra amin'ny data tena misy --}}
        <div class="flex gap-2 flex-wrap">
            @forelse($datesDisponibles as $date)
                <a href="{{ route('prof.examen.studentswithexamen', [$slug, $examen->id, 'date' => $date]) }}"
                    class="border-2 border-black/10 rounded-sm p-1 px-2 flex items-center gap-2
                        {{ $date === $dateSelectionnee ? 'bg-vert text-white' : 'border-black/10 bg-black/2' }}">
                    {{ \Carbon\Carbon::parse($date)->translatedFormat('d-M-Y') }}
                    <span class="text-xs w-5 h-5 border border-black/5 rounded-full flex justify-center items-center bg-white text-black/60">
                        {{ $nombreParDate[$date] ?? 0 }}
                    </span>
                </a>
            @empty
                <p class="text-black/50 text-sm">Aucune date d'examen assignée.</p>
            @endforelse
        </div>

        <div class="mt-2 border border-black/10 rounded-md bg-black/3 p-2">
            @forelse($studentwithexam as $se)
                @php
                    $user = $se->user;
                    $studentId = $students[$user->id] ?? null;
                    $attempt = $studentId ? $attempts->get($studentId) : null;

                    // ✅ Manamarina ny status: corrige > termine > en_cours/tsy misy
                    if ($attempt?->status === 'corrige') {
                        $statutLabel = 'Corrigé';
                        $statutClass = 'text-rouge';
                    } elseif ($attempt?->status === 'termine') {
                        $statutLabel = 'Finis';
                        $statutClass = 'text-vert';
                    } else {
                        $statutLabel = 'En attente';
                        $statutClass = 'text-black/40';
                    }
                @endphp
                <div class="flex gap-5 mb-1 p-2 rounded bg-white/70 border border-black/3">
                    <div class="w-11 h-11 rounded-md bg-black/3 overflow-hidden">
                        <img src="{{ $user->image ? asset('images/' . $user->image) : '' }}" alt="" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base  font-semibold">{{ $user->name }} 
                            <span class="border font-light text-sm border-black/10 rounded-full px-3 {{ $statutClass }}">
                                {{ $statutLabel }}
                            </span>
                        </h3>
                        <p class="text-sm">{{ $user->email }}</p>
                        <span class=" font-light text-sm ">
                            N° Matricule: {{$user->student->matricule}}
                        </span>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{route('prof.examen.examenwherestudent', [$slug, $examen->id, $user->id])}}">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                        <form action="" method="POST" onsubmit="return confirm('Retirer cet étudiant ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-black/50 text-sm p-4 text-center">Aucun étudiant pour cette date.</p>
            @endforelse
        </div>
    </div>
@endsection