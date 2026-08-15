<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/sass/app.scss','resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="text-black/60">
    @include('layouts.student-layouts.header')
    @yield('contenue-student')
    @include('layouts.student-layouts.footer')
    <script>
    function openModal(id) {
        const modal = document.getElementById(id);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('fixed') && e.target.id) {
            closeModal(e.target.id);
        }
    });
    </script>

   
    @stack('scripts')
</body>
</html>