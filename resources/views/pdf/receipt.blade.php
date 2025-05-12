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

        :root {
            --green-dark: #2A9D8F;
            --green-light: #CFF5EE;
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
            color: var(--green-dark);
        }

        h3 {
            text-align: center;
            color: var(--green-dark);
            margin-top: 30px;
        }

        p {
            margin: 4px 0;
        }

        .receipt-info-box {
            border: 1px solid var(--green-light);
            background-color: #f7fdfa;
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
            color: var(--green-dark);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: var(--green-light);
            color: #000;
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

    <!-- En-tête de l’établissement -->
    <table class="header-table">
        <tr>
            <td style="width: 20%;">
                <img src="{{ public_path('image.png') }}" class="logo">
            </td>
            <td style="text-align: center;">
                <h2>Établissement Skolyx</h2>
                <p>Adresse : Rue Exemple, Casablanca</p>
                <p>Tél : 05 22 00 00 00 - Email : contact@skolyx.ma</p>
            </td>
        </tr>
    </table>

    <!-- Titre -->
    <h3>Reçu de Paiement</h3>

    <!-- Informations de paiement -->
    <div class="receipt-info-box">
        <div class="info-item"><strong>Nom de l'école :</strong> {{ $ecole }}</div>
        <div class="info-item"><strong>Nom de l’étudiant :</strong> {{ $etudiant->nom }} {{ $etudiant->prenom }}</div>
        <div class="info-item"><strong>Nom du parent :</strong> {{ $parent->nom }} {{ $parent->prenom }}</div>
        <div class="info-item"><strong>Mois de paiement :</strong> {{ $paiement->mois }}</div>
        <div class="info-item"><strong>Date de paiement :</strong> {{ $paiement->date_paiement }}</div>
        <div class="info-item"><strong>Statut de paiement :</strong> {{ $paiement->est_paye ? 'Payé' : 'Non payé' }}</div>
    </div>

    <!-- Tableau -->
    <table>
        <thead>
            <tr>
                <th>Mois</th>
                <th>Date de paiement</th>
                <th>État du paiement</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $paiement->mois }}</td>
                <td>{{ $paiement->date_paiement }}</td>
                <td>{{ $paiement->est_paye ? 'Payé' : 'Non payé' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Pied de page -->
    <div class="footer">
        <p>Fait à Casablanca, le {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        <p>Signature du Directeur</p>
    </div>

</body>
</html>
