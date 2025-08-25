<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation de Scolarité</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            margin: 50px;
            color: #000;
            background: #fff;
        }

        .container {
            border: 1px solid #000;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header img {
            width: 100px;
            height: auto;
            margin-bottom: 10px;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 22px;
            text-transform: uppercase;
        }

        .header p {
            margin: 0;
            font-size: 14px;
        }

        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            text-decoration: underline;
            margin: 40px 0 30px;
        }

        .content p {
            font-size: 16px;
            text-align: justify;
            margin: 15px 0;
        }

        .footer {
            margin-top: 60px;
            font-size: 16px;
        }

        .footer .date {
            margin-bottom: 60px;
        }

        .signature-block {
            display: flex;
            justify-content: flex-end;
            flex-direction: column;
            text-align: right;
        }

        .signature-block img {
            max-width: 120px;
            margin-top: 5px;
        }

        .signature-label {
            font-weight: bold;
            margin-bottom: 40px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .container {
                border: none;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            @if($config->logo_path)
                <img src="{{ storage_path('app/public/' . $config->logo_path) }}" alt="Logo">
            @endif
            <h2>{{ $config->nom_ecole }}</h2>
            <p>{{ $config->adresse }}, {{ $config->ville }}</p>
            <p>Tél : {{ $config->telephone }} | Fax : {{ $config->fax }}</p>
        </div>

        <!-- Titre -->
        <div class="title">Attestation de Scolarité</div>

        <!-- Contenu -->
        <div class="content">
            <p>Le Doyen de <strong>{{ $config->nom_ecole }}</strong>, soussigné, atteste que :</p>

            <p><strong>Nom et Prénom :</strong> {{ $etudiant->nom }} {{ $etudiant->prenom }}</p>
            <p><strong>Né(e) le :</strong> {{ date('d/m/Y', strtotime($etudiant->date_naissance)) }}</p>
            @if($etudiant->cin)
                <p><strong>Titulaire de la CIN N° :</strong> {{ $etudiant->cin }}</p>
            @endif
            <p>Poursuit ses études au diplôme de la : <strong>{{ $etudiant->classroom->name ?? 'Niveau non spécifié' }}</strong></p>
            <p>Au cours de l'année universitaire : <strong>{{ $attestation->annee_universitaire }}</strong></p>
            <p>La présente attestation est délivrée à l'intéressé(e) pour servir et valoir ce que de droit.</p>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            <p class="date">Fait à {{ $config->ville }}, le {{ $attestation->date_emission }}</p>

            <div class="signature-block">
                <p class="signature-label">Cachet et signature</p>
                @if($config->cachet_path)
                    <img src="{{ storage_path('app/public/' . $config->cachet_path) }}" alt="Cachet">
                @endif
                @if($config->signature_path)
                    <img src="{{ storage_path('app/public/' . $config->signature_path) }}" alt="Signature">
                @endif
            </div>
        </div>
    </div>
</body>
</html>
