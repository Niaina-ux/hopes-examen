<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examen terminé</title>
    @vite(['resources/sass/app.scss','resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<section class="h-screen flex justify-center items-center">
    <div class="container text-center text-black/70">
        <i class="fa-solid fa-circle-check text-6xl text-vert mb-4"></i>
        <h2 class="text-3xl font-bold mb-2">Examen terminé !</h2>
        <p class="text-gray-500 mb-6">{{ $examen->titre }}</p>
        <div class="mt-8 flex gap-3 items-center justify-center">
            <a href="{{ route('home') }}" class="bg-black/2 border hover-white border-black/40 px-6 py-3 rounded-full">
                Retour à l'accuiel
            </a>
            <a href="{{ route('student.examen.historique.show', $attempt->id)}}" class="bg-rouge text-white border border-transparent hover-rouge px-6 py-3 rounded-full">
                Voir l'examen
            </a>
        </div>
    </div>
</section>
</body>
</html>