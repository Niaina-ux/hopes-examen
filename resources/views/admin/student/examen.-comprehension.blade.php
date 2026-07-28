
@extends('layouts.admin-layouts.layouthead')
@section('contenue-admin')
<div class="">
    <div class="sticky bg-white top-0">
        <div class="flex gap-10  py-2 ">
            <div class="w-23 h-23 rounded-md overflow-hidden bg-black/2 flex justify-center items-center">
                <i class="fa-solid fa-receipt text-3xl text-rouge"></i>
            </div>
            <div class="">
                <h2 class="font-semibold text-xl border-b border-black/10"> Examen premier pressage </h2>
                <p class="my-1">
                    Par
                    <span class="font-semibold inline-block px-2">Zinarilala Safidiniaina</span> 
                </p>
                <p >Status 
                    <span class="border border-black/10 rounded-full inline-block px-5 text-vert">
                        Terminer
                    </span>
                </p>
            </div>
        </div>
        <div class=" flex gap-3 border-b border-black/10">
            <a href="" class="p-1 px-3 inline-block">Qcm</a>
            <a href="" class="p-1 px-3 inline-block">Comprehension du text</a>
            <a href="" class="p-1 px-3 inline-block">Redaction</a>
            <a href="" class="p-1 px-3 inline-block">Relier par flech</a>
            <a href="" class="p-1 px-3 inline-block">Mot croiser</a>
        </div>
    </div>
    <div class="py-3">
        <div class="flex justify-between items-center bg-black/3 border-b border-black/5 p-2">
            <h2 class="text-xl font-semibold text-vert">Comprehesion du text</h2>
            <div class="">
               Note: <span class="font-semibold text-rouge text-xl">12</span> /20
            </div>
        </div>
        <div class="">
            <span class="pb-1 pt-2 underline inline-block">Partie n°1</span>
            <div class="pb-2">
                <span class="pb-1 text-rouge inline-block">Text</span>
                <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Natus, consectetur exercitationem? Neque, perspiciatis quo? Culpa ad voluptate beatae exercitationem omnis iste corporis ut excepturi cum. Lorem ipsum dolor sit amet consectetur adipisicing elit. In blanditiis dolor ipsum sit eaque perspiciatis accusamus recusandae ullam harum magni ducimus corporis maxime repellat incidunt explicabo, non dignissimos debitis a.</p>
            </div>
            <div class="">
                <span class="pb-1 text-rouge inline-block">Question et Réponse</span>
                <div class="mb-2">
                    <div class="flex justify-between border-b border-black/7 py-1">
                        <div class="flex gap-3 ">
                            <span>01</span>
                            <h3 class="font-semibold">Quelle est l'outil qui doit uliliser dans le freelence?</h3>
                        </div>
                        <div class="text-vert">1 point</div>
                    </div>
                    <div class="bg-black/2 p-2">
                        <p class="w-[80%]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Dignissimos voluptas cumque molestiae aliquam non ipsum?</p>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="flex justify-between border-b border-black/7 py-1">
                        <div class="flex gap-3 ">
                            <span>01</span>
                            <h3 class="font-semibold">Quelle est l'outil qui doit uliliser dans le freelence?</h3>
                        </div>
                        <div class="text-vert">1 point</div>
                    </div>
                    <div class="bg-black/2 p-2">
                        <p class="w-[80%]">Lorem ipsum dolor sit amet consectetur adipisicing elit. Dignissimos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection