<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletin de Notes</title>
    <style>
        body { font-family: sans-serif; background: #f2f2f2; padding: 20px; }
        .bulletin { background: #fff; padding: 20px; margin-bottom: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px 12px; border: 1px solid #ccc; text-align: center; }
        th { background: #007BFF; color: white; }
    </style>
</head>
<body>

    <h1>Bulletin de Notes</h1>

    @foreach ($bulletins as $bulletin)
        <div class="bulletin">
            <h2>{{ $bulletin['etudiant']->nom }} {{ $bulletin['etudiant']->prenom }}</h2>
            <table>
                <thead>
                    <tr>
                        <th>Matière</th>
                        <th>Note 1</th>
                        <th>Note 2</th>
                        <th>Note 3</th>
                        <th>Note 4</th>
                        <th>Note finale</th>
                        <th>Remarque</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bulletin['evaluations'] as $eval)
                        <tr>
                            <td>{{ $eval->matiere->nom ?? 'N/A' }}</td>
                            <td>{{ $eval->note1 }}</td>
                            <td>{{ $eval->note2 }}</td>
                            <td>{{ $eval->note3 }}</td>
                            <td>{{ $eval->note4 }}</td>
                            <td><strong>{{ $eval->note_finale }}</strong></td>
                            <td>{{ $eval->remarque }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

</body>
</html>
