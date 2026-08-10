@extends('layouts.student-layouts.layouthead')
@section('contenue-student')
    
<section class="pt-15">
    <div class="container flex justify-between flex-col md:flex-row items-center md:gap-0 lg:gap-10 py-15  ">
        <div class="flex-1 ">
            <h1 class="font-bold text-3xl lg:text-5xl ">Maîtrisez vos compétences pour réussir l'examen Lorem ipsum</h1>
            <p class="my-7 md:my-4 lg:my-7">Une plateforme simple et intuitive pour réviser vos cours, passer vos examens en ligne et suivre votre évolution académique facilement. Lorem ipsum dolor sit amet consectetur.</p>
            <a href="{{ Auth::check() ? route('student.examen.show', $myCategorie) : route('login') }}"
            class="bg-vert rounded-full inline-block text-center p-2 px-10 text-white">
                Voir l'examen
            </a>
        </div>
        <div class="w-[40%] relative">
            <div class="h-[10cm] w-[10cm] m-auto rounded-full overflow-hidden">
                <img src="/images/graduation.jpg" alt=""
                class="w-full h-full object-cover">
            </div>
            <div class="absolute w-25 h-25 z-20 rounded-full bg-vert top-[40%] right-0"></div>
            <div class="absolute w-35 h-35 -z-10 rounded-full bg-rouge bottom-10 left-0"></div>
            <div class="absolute w-25 h-25 -z-20 rounded-full bg-black/50 bottom-0 left-20"></div>
        </div>
    </div>
</section>
<section>
    <div class="container pb-10">
        <div class="flex justify-between items-center mb-5">
            <div class="flex items-center">
                @foreach($etudiantsApercu as $etudiant)
                    <div class="w-10 h-10 rounded-full border-3 border-white shadow overflow-hidden {{ !$loop->first ? '-ms-3 z-10' : '' }}">
                        <img src="{{ $etudiant->image ? asset('images/' . $etudiant->image) : asset('images/default-avatar.png') }}" alt=""
                        class="w-full h-full object-cover">
                    </div>
                @endforeach
                <div class="ms-3">
                    +{{ $totalEtudiants }} Etudiants
                </div>
            </div>
            <div class="flex gap-3 items-center">
                <span class=" rounded-full p-1 px-4 border border-black/20">+ {{ $totalTypesExamens }} Examen</span>
                <div class="flex gap-3">
                    <div id="categorie-swiper-prev" class="w-10 h-8 rounded bg-black/2 shadow flex justify-center items-center border border-black/3 cursor-pointer">
                        <i class="fa-solid fa-angle-left"></i>
                    </div>
                    <div id="categorie-swiper-next" class="w-10 h-8 rounded bg-black/2 shadow flex justify-center items-center border border-black/3 cursor-pointer">
                        <i class="fa-solid fa-angle-right"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- ✅ Swiper --}}
        <div class="swiper categorie-swiper">
            <div class="swiper-wrapper">
                @foreach($categories as $categorie)
                    <div class="swiper-slide">
                        <div class="p-3 m-2 border border-black/5 rounded-xl bg-black/3">
                            <img src="{{ $categorie->image ? asset('images/' . $categorie->image) : asset('images/call.PNG') }}" alt="" class="rounded-md w-full">
                            <div class="p-2 text-center mt-2 border border-black/5 rounded-md bg-black/3">
                                <h3 class="font-semibold text-xl">{{ $categorie->nom }}</h3>
                                <div class="">
                                    {{ $categorie->examens_count }} Examen créé(s)
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Swiper('.categorie-swiper', {
        slidesPerView: 4,
        spaceBetween: 16,
        navigation: {
            nextEl: '#categorie-swiper-next',
            prevEl: '#categorie-swiper-prev',
        },
        autoplay: {
            delay: 4000,              
            disableOnInteraction: false, 
        },
        breakpoints: {
            0: { slidesPerView: 1 },
            640: { slidesPerView: 2 },
            1024: { slidesPerView: 3 },
            1280: { slidesPerView: 4 },
        },
    });
});
</script>
@endpush
@endsection

