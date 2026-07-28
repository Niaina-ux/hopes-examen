@extends('layouts.student-layouts.layouthead')
@section('contenue-student')
    
<section>
    <div class="container flex justify-between flex-col md:flex-row items-center md:gap-0 lg:gap-10 py-15  lg:pb-3">
        <div class="flex-1 mb-10 md:mb-0">
            <h1 class="text-vert font-bold text-3xl lg:text-4xl ">Maîtrisez vos compétences pour réussir l'examen</h1>
            <p class="my-7 md:my-4 lg:my-7">Une plateforme simple et intuitive pour réviser vos cours, passer vos examens en ligne et suivre votre évolution académique facilement.</p>
            <button class="bg-rouge rounded-md p-2 px-5 w-[6cm] uppercase font-semibold ">
                Voir l'examen 
            </button>
        </div>
        <div class="flex-1">
            <img src="/images/undraw_essay-writing_nlru.png" alt="">
        </div>
    </div>
</section>
<section>
    <div class="container">
        <div class="bg-black/5 p-2 py-4 flex flex-wrap gap-x-2 lg:justify-center">
            @foreach ($categories as $categorie)
                <a href="{{ route('student.examen.show', $categorie->slug) }}" class="border-2 border-black/7 bg-white rounded-md uppercase font-semibold p-2 px-5">
                    {{ $categorie->nom }}
                </a>
            @endforeach
        </div>
    </div>
</section>
<section>
    <div class="container py-20 flex flex-col-reverse md:flex-row justify-between gap-10 items-center">
        <div class=" flex-1 border md:border-0 border-black/10 rounded-xl">
            <img src="/images/call.png" alt="" class="w-full">
        </div>
        <div class="flex-1">
            <h2 class="text-2xl lg:text-3xl font-bold text-vert">Évaluation Call Center International</h2>
            <p class="py-4">Deux options s'offrent à vous : testez vos aptitudes en français ou en anglais pour le poste de conseiller.</p>
            <div class="flex gap-5 mt-3">
                <a href="" class="inline-block rounded-md p-2 px-5 bg-rouge uppercase font-semibold">
                    Français
                </a>
                <a href="" class="inline-block rounded-md p-2 px-5 bg-vert uppercase font-semibold">
                    Angalais
                </a>
            </div>
        </div>
    </div>
</section>
<section class="bg-black/3">
    <div class="container  py-20 flex flex-col md:flex-row justify-between gap-10 items-center">
        
        <div class="flex-1 ">
            <h2 class="text-2xl lg:text-3xl font-bold text-vert">Test de Compétences – Dev Web & Python</h2>
            <p class="py-4">Un examen conçu pour évaluer votre maîtrise du développement web et de Python, indispensables pour les métiers de la tech.</p>
            <div class="flex gap-5  mt-3">
                <a href="" class="inline-block rounded-md p-2 px-5 bg-rouge uppercase font-semibold w-[3cm] text-center">
                    Dev
                </a>
                <a href="" class="inline-block rounded-md p-2 px-5 bg-vert uppercase font-semibold w-[3cm] text-center">
                    Pyton
                </a>
            </div>
        </div>
        <div class=" flex-1 border md:border-0 border-black/10 rounded-xl">
            <img src="/images/code.png" alt="" class="w-full">
        </div>
    </div>
</section>
<section class="">
    <div class="container py-20  flex flex-col-reverse md:flex-row justify-between gap-10 items-center">
        <div class=" flex-1 border md:border-0 border-black/10 rounded-xl">
            <img src="/images/desig.png" alt="" class="w-full">
        </div>
        <div class="flex-1 ">
            <h2 class="text-2xl lg:text-3xl font-bold text-vert">Graphique  Design & Créativité</h2>
            <p class="py-4">Un examen dédié aux passionnés de design, évaluant votre créativité, votre rigueur visuelle et votre maîtrise des outils graphiques.</p>
            <div class="flex gap-5  mt-3">
                <a href="" class="inline-block rounded-md p-2 px-5 bg-vert uppercase font-semibold">
                   Graphique Design
                </a>
            </div>
        </div>
    </div>
</section>
<section class="bg-black/3">
    <div class="container py-20 flex flex-col md:flex-row justify-between gap-10 items-center">
        
        <div class="flex-1 ">
            <h2 class="text-2xl lg:text-3xl font-bold text-vert">Informatique bureautique</h2>
            <p class="py-4">Un examen conçu pour évaluer votre efficacité avec Word, Excel et PowerPoint, piliers de la productivité en entreprise aujourd'hui.</p>
            <div class="flex gap-5  mt-3">
                <a href="" class="inline-block rounded-md p-2 px-5 bg-rouge uppercase font-semibold">
                   Info bureautique
                </a>
            </div>
        </div>
        <div class=" flex-1 border md:border-0 border-black/10 rounded-xl">
            <img src="/images/bureau.png" alt="" class="w-full">
        </div>
    </div>
</section>
<section class="">
    <div class="container py-20  flex flex-col-reverse md:flex-row justify-between gap-10 items-center">
        <div class=" flex-1 border md:border-0 border-black/10 rounded-xl">
            <img src="/images/metier (2).png" alt="" class="w-full">
        </div>
        <div class="flex-1 ">
            <h2 class="text-2xl lg:text-3xl font-bold text-vert">Métier Call Center – Anglais & Françai</h2>
            <p class="py-4">Un examen évaluant vos compétences relationnelles et linguistiques, en français et en anglais, pour exceller dans le métier du call center.</p>
            <div class="flex gap-5  mt-3">
                <a href="" class="inline-block rounded-md p-2 px-5 bg-vert uppercase font-semibold">
                   Métier anglais
                </a>
                <a href="" class="inline-block rounded-md p-2 px-5 bg-rouge uppercase font-semibold">
                   Métier frnaçais
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

