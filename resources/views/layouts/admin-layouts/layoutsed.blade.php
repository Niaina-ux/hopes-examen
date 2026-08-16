<div class="bg-black/2 w-full min-h-screen flex text-black/60">
    {{-- Overlay --}}
    <div id="sedbarOverlay"
         class="fixed inset-0 bg-black/3 z-[150] hidden opacity-0 transition-opacity duration-300">
    </div>
    {{-- Sidebar --}}
    <div id="sedbar"
         class="fixed lg:sticky top-0 left-0 z-[160]
                w-[230px] lg:w-[18%]
                bg-black/2 border-e border-black/3 max-h-screen h-screen
                -translate-x-full lg:translate-x-0
                transition-transform duration-300 ease-in-out">
        @include('layouts.admin-layouts.layoutnav')
    </div>
    {{-- Contenu --}}
    <div class="flex-1 bg-white px-5 min-w-0">
        <div class="flex  justify-between items-center gap-4
                    border-b-5 border-black/5 py-2 sticky top-0
                    bg-white z-[110]">
            <div class="flex-1 flex gap-3 items-center">
                <button id="sedbarshow"
                        class="bg-black/3 rounded-md p-1 px-2
                               flex justify-center items-center">
                    <i class="fa-solid fa-bars-progress text-xl"></i>
                </button>
            </div>
            <div class="border border-black/15 rounded-full flex items-center w-auto">
                <input type="search"
                       class="px-4 py-1 w-full sm:w-[8cm] outline-0"
                       placeholder="Nom d'utilisateur..">
                <button class="px-4 border-s-3 text-rouge border-black/10 shrink-0">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
            <div class="flex items-center gap-3 shadow p-1 rounded-full sm:justify-end ">
                {{-- <div class="hidden text-right min-w-0 lg:block">
                    <p class="truncate">{{ auth()->user()->name }}</p>
                    <p class="text-sm text-vert">
                        {{ ucfirst(auth()->user()->role) }}
                    </p>
                </div> --}}
                <div class="w-7 h-7 md:w-8 md:h-8  shrink-0 rounded-full overflow-hidden bg-black/5 ">
                    <img src="{{ auth()->user()->image
                        ? asset('images/' . auth()->user()->image)
                        : asset('images/default-avatar.png') }}"
                         alt="{{ auth()->user()->name }}"
                         class="w-full h-full object-cover m-1 ">
                </div>
            </div>
        </div>
        @yield('contenue-admin')
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sedbar = document.getElementById('sedbar');
        const sedbarshow = document.getElementById('sedbarshow');
        const sedbarOverlay = document.getElementById('sedbarOverlay');
        function openSedbar() {
            sedbar.classList.remove('-translate-x-full');
            sedbar.classList.remove('bg-black/2');
            sedbar.classList.add('bg-white');
            sedbarOverlay.classList.remove('hidden');
            setTimeout(() => {
                sedbarOverlay.classList.remove('opacity-0');
            }, 10);
        }
        function closeSedbar() {
            sedbar.classList.add('-translate-x-full');
            sedbar.classList.remove('bg-white');
            sedbar.classList.add('bg-black/2');
            sedbarOverlay.classList.add('opacity-0');
            setTimeout(() => {
                sedbarOverlay.classList.add('hidden');
            }, 300);
        }
        sedbarshow.addEventListener('click', function () {
            if (sedbar.classList.contains('-translate-x-full')) {
                openSedbar();
            } else {
                closeSedbar();
            }
        });
        sedbarOverlay.addEventListener('click', closeSedbar);
    });
</script>