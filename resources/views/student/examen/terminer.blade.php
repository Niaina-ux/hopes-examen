<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examen terminé</title>
    @vite(['resources/sass/app.scss','resources/css/app.css', 'resources/js/app.js'])
</head>
<section class="h-screen flex justify-center items-center">
    <div class="container text-center">
        <i class="fa-solid fa-circle-check text-6xl text-vert mb-4"></i>
        <h2 class="text-3xl font-bold mb-2">Examen terminé !</h2>
        <p class="text-gray-500 mb-6">{{ $examen->titre }}</p>
        <div class="mt-8">
            <a href="{{ route('home') }}" class="bg-vert text-white px-6 py-3 rounded-md uppercase">
                Retour
            </a>
        </div>
    </div>
</section>
</body>
</html>