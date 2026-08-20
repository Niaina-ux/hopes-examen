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
        <a href="#"
            class="inline-block p-1 px-3 rounded-full border ">
            {{ $type->nom }}
        </a>
        @endforeach
    </div>
</div>