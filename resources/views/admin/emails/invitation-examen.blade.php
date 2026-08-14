<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <div style="max-width: 600px; margin: auto; padding: 20px;">
        @if($enRetard)
            <h2 style="color: rgb(220, 38, 38);">Votre examen est en retard</h2>

            <p>Bonjour {{ $student->name }},</p>

            <p>
                Votre examen <strong>{{ $examen->titre }}</strong> était prévu
                @if($dateExamen)
                    le <strong>{{ \Carbon\Carbon::parse($dateExamen)->translatedFormat('d F Y') }}</strong>
                @endif
                et n'a pas encore été passé. Merci de vous connecter dès que possible pour le réaliser.
            </p>
        @else
            <h2 style="color: rgb(104, 167, 2);">Invitation à l'examen</h2>

            <p>Bonjour {{ $student->name }},</p>

            <p>
                Vous êtes invité(e) à passer l'examen <strong>{{ $examen->titre }}</strong>
                @if($dateExamen)
                    le <strong>{{ \Carbon\Carbon::parse($dateExamen)->translatedFormat('d F Y') }}</strong>.
                @else
                    prochainement.
                @endif
            </p>
        @endif

        <p>{{ $examen->description }}</p>

        <div style="background: #f4f4f4; border-radius: 8px; padding: 16px; margin: 20px 0;">
            <p style="margin: 0;"><strong>Vos identifiants de connexion :</strong></p>
            <p style="margin: 4px 0;">Email : {{ $student->email }}</p>
            <p style="margin: 4px 0;">Mot de passe : <strong>{{ $motDePasse ?? 'Veuillez réinitialiser votre mot de passe' }}</strong></p>
        </div>

        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/login') }}" style="background: {{ $enRetard ? 'rgb(220, 38, 38)' : 'rgb(104, 167, 2)' }}; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none;">
                {{ $enRetard ? 'Passer l\'examen maintenant' : 'Se connecter à la plateforme' }}
            </a>
        </p>

        <p style="font-size: 12px; color: #888;">
            Si vous n'êtes pas concerné(e) par cet email, veuillez l'ignorer.
        </p>
    </div>
</body>
</html>