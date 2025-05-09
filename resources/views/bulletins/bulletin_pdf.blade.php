<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
    </style>
</head>
<body>
    <h2>Bulletin Scolaire</h2>
    <p><strong>Nom de l'étudiant :</strong> {{ $etudiant->nom }}</p>
    <p><strong>Semestre :</strong> {{ $semestre }}</p>
    <p><strong>Année Scolaire :</strong> {{ $annee_scolaire }}</p>

    <table>
        <thead>
            <tr>
                <th>Matière</th>
                <th>Note 1</th>
                <th>Note 2</th>
                <th>Note 3</th>
                <th>Note 4</th>
                <th>Note Finale</th>
                <th>Remarque</th>
            </tr>
        </thead>
        <tbody>
            @foreach($evaluations as $eval)
                <tr>
                    <td>{{ $eval->matiere->nom ?? $eval->professeur->specialite ?? 'N/A' }}</td>
                    <td>{{ $eval->note1 ?? '-' }}</td>
                    <td>{{ $eval->note2 ?? '-' }}</td>
                    <td>{{ $eval->note3 ?? '-' }}</td>
                    <td>{{ $eval->note4 ?? '-' }}</td>
                    <td>{{ $eval->note_finale }}</td>
                    <td>{{ $eval->remarque }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5"><strong>Moyenne Générale</strong></td>
                <td colspan="2">{{ $moyenneGenerale }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
