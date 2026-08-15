<div class="bg-black/3 w-full h-screen flex text-black/60 fixed top-0 left-0 ">
    <div class="h-full p-2 w-full flex gap-3 relative">
        <div class="absolute w-[6cm] h-full top-0 left-0 bg-white z-150 border border-black/10 rounded-xl m-2  
        md:static md:m-0 md:border-0  md:block ">
            @include('layouts.admin-layouts.layoutnav')
        </div>
        <div class="flex-1 min-w-0 bg-white px-3 sm:px-4 py-2 rounded-md">
            <div class="overflow-y-auto h-full">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 border-b-5 border-black/5 pb-2 sticky top-0 bg-white z-110">
                    <div class="hidden sm:block flex-1"></div>
                    <div class="border border-black/15 rounded-full flex items-center w-full sm:w-auto">
                        <input type="search" name="" id=""
                            class="px-4 py-1 w-full sm:w-[8cm] outline-0"
                            placeholder="Nom d'utilisateur..">
                        <button class="px-4 border-s-3 text-rouge border-black/10 shrink-0">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                    <div class="flex items-center gap-3 sm:justify-end">
                        <div class="text-right min-w-0">
                            <p class="truncate">{{ auth()->user()->name }}</p>
                            <p class="text-sm text-vert">{{ ucfirst(auth()->user()->role) }}</p>
                        </div>
                        <div class="w-10 h-10 shrink-0 rounded-full overflow-hidden bg-black/5">
                            <img src="{{ auth()->user()->image ? asset('images/' . auth()->user()->image) : asset('images/default-avatar.png') }}"
                                alt="{{ auth()->user()->name }}"
                                class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>
                @yield('contenue-admin')
            </div>
        </div>
    </div>
</div>