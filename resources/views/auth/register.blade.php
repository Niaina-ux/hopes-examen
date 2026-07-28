<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Admin</title>
    @vite(['resources/sass/app.scss','resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="py-15 text-black/60">
        <div class="w-[11cm] m-auto rounded-md text-center px-4">
            <div class="mb-4">
                <img src="/images/logo.png" alt="" class="w-[2cm] m-auto">
            </div>
            <h3 class="text-2xl font-semibold mb-4 text-vert">Inscription Admin</h3>

            <form action="{{ route('admin.register.store') }}" method="POST">
                @csrf

                <input type="text" name="name" value="{{ old('name') }}" class="py-1 border-b-2 border-black/10 w-full outline-0 focus:border-[rgb(104,167,2)]"
                placeholder="Anarana..">
                @error('name')
                    <div class="text-left text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror

                <input type="email" name="email" value="{{ old('email') }}" class="py-1 border-b-2 border-black/10 w-full outline-0 focus:border-[rgb(104,167,2)] mt-5"
                placeholder="Email..">
                @error('email')
                    <div class="text-left text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror

                <input type="password" name="password" id="password" class="py-1 border-b-2 border-black/10 w-full outline-0 focus:border-[rgb(104,167,2)] mt-5"
                placeholder="Mot de passe">
                @error('password')
                    <div class="text-left text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror

                <input type="password" name="password_confirmation" id="password_confirmation" class="py-1 border-b-2 border-black/10 w-full outline-0 focus:border-[rgb(104,167,2)] mt-5"
                placeholder="Confirmer le mot de passe">

                <div class="mt-2 text-left">
                    <input type="checkbox" name="" id="afficherPassword">
                    <label for="afficherPassword">Afficher mot de passe</label>
                </div>

                <button type="submit" class="p-1 rounded-md w-full mt-5 bg-rouge">
                    S'inscrire
                </button>
            </form>

            <div class="text-center mt-2">
                Efa manana compte ?
                <a href="{{ route('login') }}" class="text-vert hover:underline">Connexion</a>
            </div>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const checkbox = document.getElementById('afficherPassword');

        checkbox.addEventListener('change', function () {
            const type = this.checked ? 'text' : 'password';
            passwordInput.type = type;
            confirmInput.type = type;
        });
    </script>
</body>
</html>