<!-- resources/views/emails/credentials.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vos Informations de Connexion</title>
</head>
<body>
    <h1>Bonjour, {{ $utilisateur->name }}</h1>
    <p>Voici vos informations de connexion :</p>
    <ul>
        <li>Email: {{ $utilisateur->email }}</li>
        <li>Mot de passe: {{ $password }}</li>
    </ul>
    <p>Veuillez les garder en sécurité.</p>
</body>
</html>
