@extends('layouts.admin-layouts.layouthead')
@section('contenue-admin')
    <div class="py-3 me-2">
        <a href="">
            Examen /
        </a>
        <div class="w-[60%] mt-1">
            <h2 class="text-2xl font-semibold text-vert ">{{ $examen->titre }}</h2>
            <p>{{ $examen->description }}</p>
        </div>

        {{-- ✅ Lisitry ny daty, mifototra amin'ny data tena misy --}}
        <div class="flex justify-between items-end border-b-2 border-black/10 mt-2 pb-1">
            <div class="flex gap-2 flex-wrap">
                @forelse($datesDisponibles as $date)
                    <a href="{{ route('admin.examen.student.show', [$slug, $examen->id, 'date' => $date]) }}"
                        class="border-2 rounded-sm p-1 px-2 flex items-center gap-2
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
        </div>

        <div class="mt-2 border border-black/3 rounded-md p-2 bg-black/2 ">
            @forelse($studentwithexam as $se)
                @php
                    $user = $se->user;
                    $studentId = $students[$user->id] ?? null;
                    $attempt = $studentId ? $attempts->get($studentId) : null;

                    if ($attempt?->status === 'corrige') {
                        $statutLabel = 'Corrigé';
                        $statutClass = 'text-vert';
                    } elseif ($attempt?->status === 'termine') {
                        $statutLabel = 'Finis';
                        $statutClass = 'text-vert';
                    } else {
                        $statutLabel = 'En attente';
                        $statutClass = 'text-black/40';
                    }
                @endphp
                <div class="flex gap-5 border border-black/2 p-2 last:border-b-0 rounded bg-white/80">
                    <div class="w-12 h-12 rounded-md bg-black/3 overflow-hidden">
                        <img src="{{ $user->image ? asset('images/' . $user->image) : '' }}" alt="" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base">{{ $user->name }}</h3>
                        <p class="text-sm">{{ $user->email }}</p>
                        <div class="flex gap-3 text-sm mt-1">
                            <span class="border border-black/10 rounded-full px-3 text-rouge">
                                Id: {{ $user->student->matricule ?? '-' }}
                            </span>
                            <span class="border border-black/10 rounded-full px-3 {{ $statutClass }}">
                                {{ $statutLabel }}
                            </span>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <a href="mailto:{{ $user->email }}">
                            <i class="fa-solid fa-envelope"></i>
                        </a>
                        @if($attempt)
                        {{-- {{ route('admin.examen.student.detail', [$slug, $examen->id, $user->id]) }} --}}
                            <a href="#">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                        @endif
                        <form action="{{ route('admin.examen.student.destroy', [$slug, $examen->id, $se->id]) }}" method="POST" onsubmit="return confirm('Retirer cet étudiant ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
            <div class=" bg-white/70 border border-black/3 p-20 text-center rounded-md">
                <i class="fa-solid fa-user-xmark"></i>
                <p class="text-black/50  text-center">Aucun étudiant pour cette date.</p>
            </div>
            @endforelse
        </div>

        <div class="flex justify-end mt-5 sticky bottom-10">
            <a href="{{ route('admin.examen.student.create', [$slug, $examen->id]) }}" class="p-2 px-5 rounded-full bg-rouge text-white">
                + Ajouter Student
            </a>
        </div>
    </div>
@endsection