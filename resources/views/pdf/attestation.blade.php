<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Attestation de Scolarité</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .header, .footer { display: flex; justify-content: space-between; align-items: center; }
        .logo img { width: 180px}
        .school-info {
            font-size: 14px;
            margin-top:25px
            
            
        }
        .school-info h2 {  color: #2d2d7f; font-size: 22px;margin-top:-120px;margin-left: 300px; }
        .school-info p { margin: 2px 0;margin-left: 450px; }
        .title { text-align: center; font-size: 18px; font-weight: bold; text-decoration: underline; margin: 30px 0; }
        .content { margin: 20px 0; }
        .footer { margin-top: 80px; text-align: right; flex-direction: column; align-items: flex-end; font-size: 14px; }
        .signature { margin-top: 80px; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header" >
        @if($config->logo_path)
            <div class="logo" style="display: flex;; align-items: center;">
                <img src="{{ storage_path('app/public/' . $config->logo_path) }}" alt="Logo">
            </div>
        @endif
        <div class="school-info" >
        <h2>{{ $config->nom_ecole }}</h2>
            <p>Tél : {{ $config->telephone }} - Fax : {{ $config->fax }}</p>
        </div>
    </div>

    <!-- Title -->
    <div class="title">ATTESTATION DE SCOLARITÉ</div>

    <!-- Content -->
    <div class="content">
        <p>Le Doyen de la {{ $config->nom_ecole }}, soussigné, atteste que :</p>
        
        <p><strong>Nom et Prénom :</strong> {{ $etudiant->nom }} {{ $etudiant->prenom }}</p>
        <p><strong>Né(e) le :</strong> {{ date('d/m/Y', strtotime($etudiant->date_naissance)) }}</p>
        @if($etudiant->cin)
            <p><strong>Titulaire de la CIN N° :</strong> {{ $etudiant->cin }}</p>
        @endif
        
        <p>Poursuit ses études au diplôme de la :
       <strong>{{ $etudiant->classroom->nom ?? 'Niveau non spécifié' }}</strong></p>
        
        <p>Au cours de l'année universitaire : {{ $attestation->annee_universitaire }}</p>
        
        <p>La présente attestation est délivrée à l'intéressé(e) pour servir et valoir ce que de droit.</p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>{{ $config->ville }}, le {{ $attestation->date_emission }}</p>
        <div class="signature">
            <p>Visa de l'administration</p>
        </div>
    </div>
   
    </div>
</body>
</html>
