<div class=" w-full min-h-screen flex ">
    {{-- Overlay --}}
    <div id="sedbarOverlayy"
         class="fixed inset-0 z-[150] hidden opacity-0 transition-opacity duration-300">
    </div>
    {{-- Sidebar --}}
    <div id="sedbar"
         class="fixed lg:sticky top-0 left-0 z-[160]
                w-[230px] lg:w-[18%]
                bg-white border-e border-black/5 max-h-screen h-screen
                -translate-x-full lg:translate-x-0
                transition-transform duration-300 ease-in-out
                dark:bg-neutral-800 dark:border-white/6">
        @include('layouts.prof-layouts.proflayoutsnav')
    </div>
    <div class="flex-1 px-5 min-w-0">
        <div class="flex  justify-between items-center gap-4
                    border-b-5 border-black/5 py-2 sticky top-0
                    bg-white  z-[110]
                    dark:border-white/10 dark:bg-neutral-800">
            <div class="flex-1 flex gap-3 items-center">
                <button id="sedbarshow"
                        class="bg-black/3 rounded-md p-1 px-2
                               flex justify-center items-center
                               dark:bg-white/10">
                    <i class="fa-solid fa-bars-progress text-xl"></i>
                </button>
            </div>
            <div class="border border-black/15 rounded-full flex items-center w-auto
                        dark:border-white/25">
                <input type="search"
                       class="px-4 py-1 w-full sm:w-[8cm] outline-0"
                       placeholder="Nom d'utilisateur..">
                <button class="px-4 border-s-3 text-rouge border-black/10 shrink-0
                                dark:border-white/25">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
            <div 
                class="flex items-center gap-3 ">
                <button id="darkModeBouton"
                        type="button"
                        class="w-8 h-8 rounded-full bg-black/2  text-gray-800
                            dark:text-white dark:bg-gray-700 ">

                    <i class="fa-solid fa-moon"></i>
                </button>
                <div 
                    class="w-8 h-8 md:w-9 md:h-9 border border-black/3  shrink-0 rounded-full overflow-hidden bg-black/5
                    dark:bg-black/30 dark:border-white/20">
                    <img src="{{ auth()->user()->image
                        ? asset('images/' . auth()->user()->image)
                        : asset('images/default-avatar.png') }}"
                         alt="{{ auth()->user()->name }}"
                         class="w-full h-full object-cover m-1 ">
                </div>
            </div>
        </div>
            @yield('contenue-prof')
        </div>
    </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sedbar = document.getElementById('sedbar');
        const sedbarshow = document.getElementById('sedbarshow');
        const sedbarOverlay = document.getElementById('sedbarOverlayy');
        function openSedbar() {
            sedbar.classList.remove('-translate-x-full');
            sedbarOverlay.classList.remove('hidden');
            setTimeout(() => {
                sedbarOverlay.classList.remove('opacity-0');
            }, 10);
        }
        function closeSedbar() {
            sedbar.classList.add('-translate-x-full');
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