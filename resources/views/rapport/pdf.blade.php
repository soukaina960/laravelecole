<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport Global</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th { border: 1px solid #000; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Rapport Financier Global</h1>
    <table>
        <tr><th>Nombre d'étudiants</th><td>{{ $totalEtudiants }}</td></tr>
        <tr><th>Nombre de professeurs</th><td>{{ $totalProfs }}</td></tr>
        <tr><th>Montants étudiants</th><td>{{ $montantEtudiants }} MAD</td></tr>
        <tr><th>Montants professeurs</th><td>{{ $montantProfs }} MAD</td></tr>
        <tr><th>Résultat final</th><td>{{ $resultatFinal }} MAD</td></tr>
    </table>
</body>
</html>
