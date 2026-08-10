<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    @vite(['resources/sass/app.scss','resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="text-black/60 h-screen flex justify-center items-center relative">
        <a href=" {{route('home')}} " class="absolute top-5 left-10 text-xl"><i class="fa-solid fa-xmark"></i></a>
        <div class="w-[11cm] m-auto rounded-md p-4 shadow border border-black/10">
            <h3 class="text-2xl font-semibold mb-4 text-vert text-center">Connexion</h3>
            @if (session('error'))
                <div class="text-left text-red-600 text-sm mb-3">
                    {{ session('error') }}
                </div>
            @endif
            <form action="{{ route('login.store') }}" method="POST">
                @csrf
                <label for="">Votre email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="p-2 border rounded-md border-black/20 w-full outline-0 focus:border-[rgb(104,167,2)] mb-3"
                placeholder="Exam@example.com">
                @error('email')
                    <div class="text-left text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
                <label for="">Mot de passe</label>
                <input type="password" name="password" id="password" class="p-2 border rounded-md border-black/20 w-full outline-0 focus:border-[rgb(104,167,2)]"
                placeholder="********">
                @error('password')
                    <div class="text-left text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror

                <div class="mt-2 text-left">
                    <input type="checkbox" name="" id="afficherPassword">
                    <label for="afficherPassword">Afficher mot de passe</label>
                </div>

                <button type="submit" class="p-1 rounded-md w-full my-5 bg-rouge">
                    Connexion
                </button>
            </form>

            @if(!$adminExiste)
                <div class="text-center mt-2">
                    S'inscrire en tant que
                    <a href="{{ route('admin.register') }}" class="text-vert hover:underline">admin ?</a>
                </div>
            @endif
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const checkbox = document.getElementById('afficherPassword');

        checkbox.addEventListener('change', function () {
            passwordInput.type = this.checked ? 'text' : 'password';
        });
    </script>
</body>
</html>