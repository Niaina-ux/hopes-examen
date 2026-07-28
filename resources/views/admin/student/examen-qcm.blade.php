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
        <div class="flex justify-between items-center bg-black/3 border-b border-black/5 p-2 px-5">
            <h2 class="text-xl font-semibold text-vert">Question à choix multiple</h2>
            <div class="">
               Note: <span class="font-semibold text-rouge text-xl">12</span> /20
            </div>
        </div>
        <div class="">
            <span class="py-2 inline-block">Parie n°1</span>
            <div class="pb-2">
                <div class="flex justify-between border-b border-black/7 py-1">
                    <div class="flex gap-3 ">
                        <span>01</span>
                        <h3 class="font-semibold">Quelle est l'outil qui doit uliliser dans le freelence?</h3>
                    </div>
                    <div class="text-vert">1 point</div>
                </div>
                <div class="bg-black/3 p-2">
                    <div class="flex gap-3 justify-between py-1">
                        <div class="flex gap-3">
                            <span>A</span>
                            <h3>Quelle est l'outil qui doit uliliser dans le freelence?</h3>
                        </div>
                        <div class="text-red-600">
                            <i class="fa-solid fa-xmark"></i>
                        </div>
                    </div>
                    <div class="flex gap-3 justify-between  py-1">
                        <div class="flex gap-3">
                            <span>A</span>
                            <h3>Quelle est l'outil qui doit uliliser dans le freelence?</h3>
                        </div>
                        <div class="text-vert">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pb-2">
                <div class="flex justify-between border-b border-black/7 py-1">
                    <div class="flex gap-3 ">
                        <span>01</span>
                        <h3 class="font-semibold">Quelle est l'outil qui doit uliliser dans le freelence?</h3>
                    </div>
                    <div class="text-vert">1 point</div>
                </div>
                <div class="bg-black/3 p-2">
                    <div class="flex gap-3 justify-between py-1">
                        <div class="flex gap-3">
                            <span>A</span>
                            <h3>Vrai</h3>
                        </div>
                        <div class="text-vert">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pb-2">
                <div class="w-30 h-25 rounded-md bg-black/5 mt-4">
                    <img src="/images/call.png" alt="" class="w-full h-full object-cover">
                </div>
                <div class="flex justify-between border-b border-black/7 py-1">
                    <div class="flex gap-3 ">
                        <span>01</span>
                        <h3 class="font-semibold">Quelle est l'outil qui doit uliliser dans le freelence?</h3>
                    </div>
                    <div class="text-vert">1 point</div>
                </div>
                <div class="bg-black/3 p-2">
                    <div class="flex gap-3 justify-between py-1">
                        <div class="flex gap-3">
                            <span>A</span>
                            <h3>Vrai</h3>
                        </div>
                        <div class="text-vert">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection