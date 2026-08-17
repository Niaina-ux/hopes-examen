<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px 10px; }
        th { background: #f5f5f5; }
        .text-center { text-align: center; }
        .flex{display: flex;}
        .justify-between{justify-content: space-between;}
    </style>
</head>
<body>
    <table style="border: none;">
        <tr>
            <td style="border: none; width: 60px;"><img src="{{ public_path('images/logo.png') }}" style="width: 50px;"></td>
            <td style="border: none;">
                <strong>HOPES FORMATION</strong><br>
                Centre de formation professionnel
            </td>
            <td style="border: none; text-align: right;">
                <strong>{{ $student->name }}</strong><br>
                Matricule: {{ $userStudent->matricule ?? '—' }}<br>
                Domaine: {{ $userStudent->categorie->nom ?? '—' }}
            </td>
        </tr>
    </table>

    <h2 class="text-center" style="text-transform: uppercase; margin-top: 20px;">Résultat final</h2>

    <table>
        <thead>
            <tr><th>N°</th><th>Titre</th><th>Note</th></tr>
        </thead>
        <tbody>
            @foreach($statistiques as $index => $s)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $s['titre'] }}</td>
                    <td class="text-center">{{ $s['pourcentage'] }} %</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <table style="border: none;">
        <tr>
            <td style="border: none;">
                <p><strong>Moyenne générale : {{ $moyenneGenerale ?? 0 }} %</strong> ({{ $statistiques->count() }} examens)</p>
            </td>
            <td style="border: none; text-align: right;">
                <p class="italic font-medium text-orange-500">Félicitations et courage pour la suite !</p>
            </td>
        </tr>
    </table>
</body>
</html>