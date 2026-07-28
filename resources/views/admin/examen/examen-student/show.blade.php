@extends('layouts.admin-layouts.layouthead')
@section('contenue-admin')
    <div class="py-3">
        <a href="">
            <i class="fa-solid fa-arrow-left-long"></i>
        </a>
        <div class="w-[60%]">
            <h2 class="text-2xl font-semibold text-vert">Examen - Premier vague d'examen</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed, maiores?</p>
        </div>
        <div class="flex justify-between items-end border-b-2 border-black/10 mt-2 pb-1"> 
            <div class=" flex gap-2 ">
                <a href="" class="border-2 border-black/10 rounded-sm p-1 px-3 bg-black/2">
                    12 -Janv- 2026
                </a>
                <a href="" class="border-2 border-black/10 rounded-sm p-1 px-3 bg-black/2">
                    22 -Janv- 2026
                </a>
            </div>
            <div class="border-2 border-black/10 rounded-md bg-black/5">
                <input type="date" name="" id=""
                class="border-2 border-white rounded-md p-1 px-2 bg-black/3">
                <button class="bg-vert text-white rounded-md px-3 p-1 border-2 border-transparent">Filtrer</button>
            </div>
        </div>
        <div class="mt-2 border border-black/10 rounded-md p-2">
            <div class="flex gap-5 border-b border-black/10 p-2 ">
                <div class="w-12 h-12 rounded-md bg-black/3 overflow-hidden">
                    <img src="" alt="" class="w-full h-full object-cover">
                </div>
                <div class="flex-1">
                    <h3 class="text-base ">Zinarilala Safidiniaina</h3>
                    <p class="text-sm">niaina@gmail.com</p>
                    <div class="flex gap-3 text-sm mt-1">
                        <span class="border border-black/10 rounded-full px-3 text-rouge">Id:1235125</span>
                        <span class="border border-black/10 rounded-full px-3 text-vert">Terminé</span>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="">
                        <i class="fa-solid fa-envelope"></i>
                    </a>
                    <a href="">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <a href="">
                        <i class="fa-regular fa-trash-can"></i>
                    </a>
                </div>
            </div>
            <div class="flex gap-5 border-b border-black/10 p-2 ">
                <div class="w-12 h-12 rounded-md bg-black/3 overflow-hidden">
                    <img src="" alt="" class="w-full h-full object-cover">
                </div>
                <div class="flex-1">
                    <h3 class="text-base ">Zinarilala Safidiniaina</h3>
                    <p class="text-sm">niaina@gmail.com</p>
                    <div class="flex gap-3 text-sm mt-1">
                        <span class="border border-black/10 rounded-full px-3 text-rouge">Id:1235125</span>
                        <span class="border border-black/10 rounded-full px-3 text-vert">Terminé</span>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="">
                        <i class="fa-solid fa-envelope"></i>
                    </a>
                    <a href="">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <a href="">
                        <i class="fa-regular fa-trash-can"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-5 sticky bottom-10">
            <a href="{{route('admin.examen.student.create', [$slug, $examen->id])}}" class="p-2 px-5 rounded-md bg-rouge text-white">
               + Ajouter Student
            </a>
        </div>
    </div>
@endsection