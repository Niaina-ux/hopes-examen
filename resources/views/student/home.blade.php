@extends('layouts.student-layouts.layouthead')
@section('contenue-student')
    
<section class=" pt-10
        md:pt-15">
    <div class="container flex justify-between flex-col md:flex-row items-center md:gap-0 lg:gap-10 py-15  ">
        <div class="flex-1 ">
            <h1 class="font-bold text-3xl lg:text-5xl animation-1">Préparez-vous, testez vos connaissances, réussissez vos examens</h1>
            <p class="my-7 md:my-4 lg:my-7 animation-1">Une plateforme simple et intuitive pour vous accompagner dans votre préparation, évaluer vos connaissances et passer vos examens en ligne en toute confiance.</p>
            <a href="{{ Auth::check() ? route('student.examen.show', $myCategorie) : route('login') }}"
            class="bg-vert hover-vert rounded-full inline-block text-center p-2 px-10 text-white animation-1">
              <i class="fa-solid fa-book-open me-2"></i>  Voir l'examen
            </a>
        </div>
        <div class=" w-full relative mt-10
            md:w-[40%] md:mt-0">
            <div class=" w-[8cm] h-[8cm] rounded-full overflow-hidden animation-1
                md:h-[7cm] md:w-[7cm] md:m-auto 
                lg:h-[10cm] lg:w-[10cm]">
                <img src="/images/graduation.jpg" alt=""
                class="w-full h-full object-cover">
            </div>
            <div class="absolute z-20 rounded-full bg-vert w-20 h-20 top-0 right-0 animation-rond rond
                lg:w-25 lg:h-25 lg:top-[40%] lg:right-0"></div>
            <div class="absolute -z-10 rounded-full bg-rouge w-30 h-30 top-[50%] right-10 animation-rond
                md:w-30 md:h-30 md:bottom-10 md:-left-5
                lg:w-35 lg:h-35 lg:bottom-10 lg:left-0"></div>
            <div class="absolute -z-20 rounded-full bg-black/50 w-25 h-25 bottom-0 left-0 animation-rond
                md:left-20 md:-bottom-5"></div>
        </div>
    </div>
</section>

<section class=" py-5 md:py-7">
    <div class="container pb-6">
        <div class="flex justify-between items-center mb-6 flex-wrap
            md:flex-nowrap">
            <div class="flex flex-1 items-center ">
                <div class="flex items-center">
                    @foreach($etudiantsApercu as $etudiant)
                        <div  class="w-10 h-10 rounded-full border-3 border-white
                                   shadow overflow-hidden
                                   {{ !$loop->first ? '-ms-3 z-10' : '' }}">
                            <img src="{{ $etudiant->image
                                    ? asset('images/' . $etudiant->image)
                                    : asset('images/default-avatar.png') }}"
                                alt=""
                                class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>
                <div class="ms-4">
                    <div class="font-semibold">
                        +{{ $totalEtudiants }} Etudiants
                    </div>
                    <div class="text-sm text-black/50">
                        nous font déjà confiance
                    </div>
                </div>

            </div>
            <div class="flex gap-3 items-center mt-5 justify-between w-full 
                md:w-auto md:mt-0">
                <div class="rounded-full px-5 py-1 bg-white 
                            md:py-2 border border-black/5 shadow-sm
                            flex items-center gap-2">
                    <i class="fa-solid fa-graduation-cap text-vert"></i>
                    <span class="font-semibold">
                        + {{ $totalTypesExamens }} Examen
                    </span>
                </div>
                <div class="flex gap-2">
                    <button type="button"
                        id="categorie-swiper-prev"
                        class="w-9 h-9 rounded-full bg-white
                               md:w-10 md:h-10  border border-black/5 shadow-sm
                               flex justify-center items-center
                               cursor-pointer
                               hover:bg-vert hover:text-white
                               transition-all duration-300">
                        <i class="fa-solid fa-angle-left"></i>
                    </button>
                    <button type="button"
                        id="categorie-swiper-next"
                        class="w-9 h-9 rounded-full bg-vert text-white
                               md:w-10 md:h-10 border border-vert shadow-sm
                               flex justify-center items-center
                               cursor-pointer
                               hover:scale-105
                               transition-all duration-300">
                        <i class="fa-solid fa-angle-right"></i>
                    </button>
                </div>
            </div>
        </div>
        {{-- Swiper --}}
        <div class="relative">
            <div class="swiper categorie-swiper !py-3 !px-1">
                <div class="swiper-wrapper">
                    @forelse ($categories as $categorie)
                        <div class="swiper-slide h-auto">
                            <div class="category-card group relative h-full min-h-[285px]
                                       bg-white rounded-2xl
                                       border border-black/10
                                       overflow-hidden
                                       transition-all duration-300
                                       hover:-translate-y-1
                                       hover:shadow-lg">
                                <div class="absolute top-5 right-5
                                            grid grid-cols-3 gap-1 opacity-40">
                                    @for($i = 0; $i < 9; $i++)
                                        <span class="w-1 h-1 rounded-full bg-vert bg-current"></span>
                                    @endfor
                                </div>
                                <div class="pt-9 flex justify-center">
                                    <div  class="category-icon
                                               relative w-24 h-24
                                               rounded-full
                                               bg-green-600/5
                                               flex items-center justify-center
                                               transition-all duration-300
                                               group-hover:scale-105" >
                                        <div class="absolute inset-2 rounded-full
                                                   border-4 border-black/5"></div>
                                        
                                        <i class=" {{$categorie->icon ? $categorie->icon : 'fa-solid fa-book-open-reader'}} 
                                                   text-4xl text-vert
                                                   relative z-10"></i>
                                    </div>
                                </div>
                                <div class="text-center px-4 pt-6 relative z-10">
                                    <h3 class="font-bold text-lg  truncate">
                                        {{ $categorie->nom }}
                                    </h3>
                                    <div class="mt-2 text-sm text-black/50">
                                        {{ $categorie->examens_count }}
                                        Examen créé(s)
                                    </div>
                                    <div class="w-10 h-1 rounded-full
                                               bg-rouge
                                               m-auto mt-5
                                               transition-all duration-300
                                               group-hover:w-16"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="w-full p-20 bg-black/[0.03]
                                    text-center border border-black/5
                                    rounded-2xl">
                            <i class="fa-solid fa-box-open text-3xl text-black/30"></i>
                            <p class="mt-3 text-black/50">
                                Aucune catégorie ajoutée !
                            </p>
                        </div>
                    @endforelse
                </div>
                <div class="swiper-pagination !static mt-5"></div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.css">
<style>
    .categorie-swiper .swiper-slide {
        height: auto;
    }
    .categorie-swiper .swiper-pagination {
        position: static;
    }
    .categorie-swiper .swiper-pagination-bullet {
        width: 7px;
        height: 7px;
        opacity: 0.35;
        transition: all 0.3s ease;
    }
    .categorie-swiper .swiper-pagination-bullet-active {
        width: 24px;
        border-radius: 20px;
        opacity: 1;
    }
</style>
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Swiper('.categorie-swiper', {
        slidesPerView: 1,
        spaceBetween: 14,
        navigation: {
            nextEl: '#categorie-swiper-next',
            prevEl: '#categorie-swiper-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        speed: 700,
        grabCursor: true,
        breakpoints: {
            0: {
                slidesPerView: 2,
                spaceBetween: 12,
            },
            640: {
                slidesPerView: 2,
                spaceBetween: 14,
            },
            820: {
                slidesPerView: 3,
                spaceBetween: 14,
            },
            1024: {
                slidesPerView: 4,
                spaceBetween: 16,
            },
            1280: {
                slidesPerView: 5,
                spaceBetween: 18,
            },
        },
    });
});
</script>
@endpush
@endsection

