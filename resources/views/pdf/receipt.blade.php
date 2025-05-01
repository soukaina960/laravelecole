<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Paiement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
        }

        .receipt-info {
            margin-top: 20px;
        }

        .receipt-info p {
            margin: 5px 0;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
        }

        .table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .table, .table th, .table td {
            border: 1px solid black;
        }

        .table th, .table td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Reçu de Paiement</h1>
    <div class="receipt-info">
        <p><strong>Nom de l'école:</strong> {{ $ecole }}</p>
        <p><strong>Nom de l'étudiant:</strong> {{ $etudiant->nom }} {{ $etudiant->prenom }}</p>
        <p><strong>Nom du parent:</strong> {{ $parent->nom }} {{ $parent->prenom }}</p>
        <p><strong>Moins de paiement:</strong> {{ $paiement->mois }}</p>
        <p><strong>Date de paiement:</strong> {{ $paiement->date_paiement }}</p>
        <p><strong>Status de paiement:</strong> {{ $paiement->est_paye ? 'Payé' : 'Non payé' }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID Paiement</th>
                <th>Étudiant ID</th>
                <th>Mois</th>
                <th>Date de paiement</th>
                <th>État du paiement</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $paiement->id }}</td>
                <td>{{ $paiement->etudiant_id }}</td>
                <td>{{ $paiement->mois }}</td>
                <td>{{ $paiement->date_paiement }}</td>
                <td>{{ $paiement->est_paye ? 'Payé' : 'Non payé' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Merci pour votre paiement!</p>
    </div>
</body>
</html>
