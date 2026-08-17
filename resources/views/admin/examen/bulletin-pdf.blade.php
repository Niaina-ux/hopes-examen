<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #333; }
        .text-right { text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px 10px; }
        th { background: #f5f5f5; text-align: left; }
        .text-right-td { text-align: right; }
        .titre-principal { text-align: center; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="text-right">le, {{ now()->translatedFormat('d F Y') }}</div>

    <table style="border: none; margin-top: 10px;">
        <tr>
            <td style="border: none; width: 60px;">
                <img src="{{ public_path('images/logo.png') }}" style="width: 50px;">
            </td>
            <td style="border: none;">
                <strong>HOPES FORMATION</strong><br>
                Ecole de formation professionnelle<br>
                <em>Le raccourci Lorem ipsum dolor sit amet</em>
            </td>
            <td style="border: none; text-align: right;">
                <strong>{{ $student->name }}</strong><br>
                Matricule: {{ $etudiant->matricule ?? '—' }}<br>
                Domaine: {{ $examen->categorie->nom ?? '—' }}
            </td>
        </tr>
    </table>

    <div class="titre-principal">
        <h2 style="text-transform: uppercase;">Résumé des notes</h2>
        <p style="font-size: 16px;">{{ $examen->titre }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Exercice</th>
                <th class="text-right-td">Note</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resumeParType as $r)
                <tr>
                    <td>{{ $r['nom'] }}</td>
                    <td class="text-right-td">{{ $r['obtenus'] }} / {{ $r['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td><strong>Total général</strong></td>
                <td class="text-right-td"><strong>{{ $totalPointsGlobalObtenus }} / {{ $totalNoteGlobal }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <p class="text-right" style="margin-top: 15px;">
        Mention : <strong>{{ $mention }}</strong>
    </p>
</body>
</html>
