<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Admin</title>
    @vite(['resources/sass/app.scss','resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class=" text-black/60 flex justify-center items-center h-screen">
        <div class="w-[11cm]  rounded-xl p-4 border border-black/10 shadow ">
            <h3 class="text-2xl font-semibold text-center mb-4 text-vert">Inscription Admin</h3>

            <form action="{{ route('admin.register.store') }}" method="POST">
                @csrf
                <label for="" class="">Votre nom</label>
                <input type="text" name="name" value="{{ old('name') }}" class="p-2 border rounded-md border-black/20 w-full outline-0 focus:border-[rgb(104,167,2)] "
                placeholder="Anarana..">
                @error('name')
                    <div class="text-left text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
                <label for="" class="mt-2 inline-block">Votre email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="p-2 border rounded-md border-black/20 w-full outline-0 focus:border-[rgb(104,167,2)]  "
                placeholder="Email..">
                @error('email')
                    <div class="text-left text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
                <label for="" class="mt-2 inline-block">Mot de passe</label>
                <input type="password" name="password" id="password" class="p-2 border rounded-md border-black/20 w-full outline-0 focus:border-[rgb(104,167,2)]  "
                placeholder="Mot de passe">
                @error('password')
                    <div class="text-left text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
                <label for="" class="mt-2 inline-block">Confirme mot de passe</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="p-2 border rounded-md border-black/20 w-full outline-0 focus:border-[rgb(104,167,2)]"
                placeholder="Confirmer le mot de passe">

                <div class="text-left my-2">
                    <input type="checkbox" name="" id="afficherPassword">
                    <label for="afficherPassword">Afficher mot de passe</label>
                </div>

                <button type="submit" class="p-2 hover-rouge text-white rounded-xl  w-full mt-2 bg-rouge">
                    S'inscrire
                </button>
            </form>

            <div class="text-center mt-2">
                Si vous avez de compte!
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