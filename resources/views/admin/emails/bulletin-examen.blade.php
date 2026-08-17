<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <div style="max-width: 600px; margin: auto; padding: 20px;">
        <h2 style="color: rgb(104, 167, 2);">Votre bulletin de notes</h2>
        <p>Bonjour {{ $student->name }},</p>
        <p>Veuillez trouver ci-joint votre bulletin de notes pour l'examen <strong>{{ $examen->titre }}</strong>.</p>
        <p style="font-size: 12px; color: #888;">Ce document est également téléchargeable depuis votre espace élève.</p>
    </div>
</body>
</html>