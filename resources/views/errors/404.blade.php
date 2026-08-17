<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page introuvable</title>

    @vite(['resources/css/app.css'])

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="min-h-screen  text-black/60  flex items-center justify-center p-5">
    <div class="w-full max-w-2xl text-center">
        {{-- 404 --}}
        <div class="relative">
            <h1 class="text-[8rem] sm:text-[11rem] leading-none
                       font-black text-[#092957]/10 select-none">
                404
            </h1>
            <div class="absolute inset-0  flex items-center justify-center">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full
                            bg-vert text-white bg-green-600/50
                            flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-magnifying-glass text-3xl sm:text-4xl"></i>
                </div>
            </div>
        </div>
        {{-- Message --}}
        <div class="mt-2">
            <h2 class="text-2xl sm:text-3xl font-bold">
                Page introuvable
            </h2>

            <div class="w-20 h-[3px] bg-[#c9a227] mx-auto my-4"></div>

            <p class=" text-sm sm:text-base max-w-md mx-auto">
                Désolé, la page que vous recherchez n'existe pas
                ou a été déplacée.
            </p>
        </div>

        {{-- Button --}}
        <div class="mt-8 flex flex-col sm:flex-row
                    justify-center items-center gap-3">

            <a href="{{ url('/') }}"
               class="w-full sm:w-auto px-6 py-3 rounded-full
                       text-white bg-orange-400
                      font-semibold transition
                      hover:bg-orange-300">
                <i class="fa-solid fa-house mr-2"></i>
                Retour à l'accueil
            </a>

            <button onclick="history.back()"
                    class="w-full sm:w-auto px-6 py-3 rounded-full
                           border border-green-600/50
                            font-semibold
                           transition hover:bg-[#092957]/5">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Page précédente
            </button>

        </div>
        {{-- Footer --}}
        <div class="mt-12 text-sm ">
            <p class="font-semibold ">
                Hopes Formation
            </p>
            <p>
                Centre de formation professionnel
            </p>
        </div>
    </div>
</body>
</html>