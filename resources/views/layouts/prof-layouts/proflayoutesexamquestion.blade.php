<div class="">
    <div class="flex justify-between items-end">
        <div class="w-[70%]">
            <h2 class="text-2xl font-semibold text-vert">Tous les questions</h2>
            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Atque consequatur, aliquam autem laboriosam optio sequi?</p>
        </div>
    </div>
</div>

<div class="border-b-2 flex justify-between  border-black/10 mt-2 py-2
    dark:border-white/10">
    <div class="flex gap-1 ">
        @foreach ($types as $type)
            <a href="{{ route('prof.question.' . $type->slug, $slug) }}"
                class="inline-block p-1 px-3 rounded-full border bg-black/3 dark:bg-white/3 border-black/10 dark:border-white/10
                {{ request()->routeIs('prof.question.' . $type->slug) ? 'bg-vert text-white ' : ' ' }}">
                {{ $type->nom }}
            </a>
        @endforeach
    </div>
    @php
        $categorie = \App\Models\Categorie::where('slug', $slug)->first();

        $totalQuestions = 0;
        $totalPoints = 0;
        // -----------
        $qcms = \App\Models\Qcm::where('categorie_id', $categorie->id)
            ->with('qcmQuestions')
            ->get();
        $totalQuestions += $qcms->sum(fn($qcm) => $qcm->qcmQuestions->count());
        $totalPoints += $qcms->sum(fn($qcm) => $qcm->qcmQuestions->sum('points'));
        // ----------
        $reliers = \App\Models\Relier::where('categorie_id', $categorie->id)
            ->with('relierQuestions')
            ->get();
        $totalQuestions += $reliers->sum(fn($relier) => $relier->relierQuestions->count());
        $totalPoints += $reliers->sum( fn($relier) => $relier->relierQuestions->sum('points'));
        // ----------
    @endphp
    <div class="">
        <div class="flex gap-2">
            <div class="p-1 px-2 rounded-md border border-black/20
                dark:border-white/20">
                Total:
                <span class="text-rouge">{{ $totalPoints }}</span>
                Pts
            </div>

            <div class="p-1 px-2 rounded-md border border-black/20
                dark:border-white/20">
                Questions:
                <span class="text-rouge">{{ $totalQuestions }}</span>
                Qs
            </div>
        </div>
    </div>
</div>