<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 40px;
            color: #333;
        }

        /* Couleurs principales */
        :root {
            --blue-dark: #27548A;
            --blue-light: #9EC6F3;
            --red-soft: #e63946;
        }

        .header-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .header-table td {
            vertical-align: top;
            border: none;
        }

        .logo {
            height: 90px;
        }

        h2 {
            margin: 0;
            font-size: 20px;
            color: var(--blue-dark);
        }

        h3 {
            text-align: center;
            color: var(--blue-dark);
            margin-top: 30px;
        }

        .center {
            text-align: center;
        }

        p {
            margin: 4px 0;
        }

        .student-info-box {
            border: 1px solid var(--blue-light);
            background-color: #f7fbff;
            padding: 10px 15px;
            margin: 20px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .info-item {
            flex: 1 1 30%;
            min-width: 150px;
        }

        .info-item strong {
            color: var(--blue-dark);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background-color: var(--blue-light);
            color: #000;
        }

        tfoot td {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .footer {
            margin-top: 60px;
        }

        .footer p {
            text-align: right;
        }
    </style>
</head>
<body>

    <!-- En-tête établissement -->
    <table class="header-table">
        <tr>
            <td style="width: 20%;">
                <img src="{{ public_path('image.png') }}" class="logo">
            </td>
            <td class="center">
                <h2>Établissement Skolyx</h2>
                <p>Adresse : Rue Exemple, Casablanca</p>
                <p>Tél : 05 22 00 00 00 - Email : contact@skolyx.ma</p>
            </td>
        </tr>
    </table>

    <!-- Titre -->
    <h3>Bulletin Scolaire</h3>

    <!-- Informations de l’étudiant -->
    <div class="student-info-box">
        <div class="info-item"><strong>Nom :</strong> {{ $etudiant->nom }} {{ $etudiant->prenom }}</div>
        <div class="info-item"><strong>Date de naissance :</strong>{{ $etudiant->date_naissance ?? '-' }}</div>
        <div class="info-item"><strong>Classe :</strong> {{ $etudiant->classroom->name ?? '-' }}</div>
        <div class="info-item"><strong>Année scolaire :</strong>{{ $annee_scolaire->annee ?? '-' }}</div>
        <div class="info-item"><strong>Nombre total :</strong> {{ $nombreEtudiantsDansClasse ?? '-' }}</div>
        <div class="info-item"><strong>Semestre :</strong> {{ $semestre->nom ?? '-' }}</div>
    </div>

    <!-- Tableau des évaluations -->
    <table>
        <thead>
            <tr>
                <th>Matière</th>
                <th>Note Finale</th>
                <th>Remarque</th>
            </tr>
        </thead>
        <tbody>
            @foreach($evaluations as $eval)
            <tr>
                <td>{{ $eval->matiere->nom ?? $eval->professeur->specialite ?? 'N/A' }}</td>
                <td>{{ $eval->note_finale }}</td>
                <td>{{ $eval->remarque }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Moyenne Générale</td>
                <td>{{ $moyenneGenerale }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Pied de page -->
    <div class="footer">
        <p>Fait à Casablanca, le {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        <p>Signature du Directeur</p>
    </div>

</body>
</html>