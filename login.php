<?php
session_start();
include 'php/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération des données du formulaire
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Vérification dans la base de données avec une requête préparée pour éviter les injections SQL
    $sql = "SELECT * FROM utilisateurs WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);  // "s" pour string
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Vérifier si l'utilisateur existe et si le mot de passe est correct
    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nom'];
        $_SESSION['user_email'] = $user['email'];
        header('Location: index.php'); // Redirection vers la page d'accueil
        exit;
    } else {
        echo "Identifiants invalides.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Connexion</title>
</head>
<body>
<div class="form-box">
    <form class="form" method="POST">
        <span class="title">Se connecter</span>
        <span class="subtitle">Create a free account with your email.</span>
        <div class="form-container">
        <input type="email" class="input" name="email" required placeholder="Email">
                <input type="password" class="input" name="mot_de_passe" required placeholder="Password">
        </div>
        <button type="submit">Se connecter</button>
    </form>
<div class="form-section">
   <p>Pas encore de compte ? <a href="register.php">S'inscrire ici</a></p>
</body>
</html>



<!-- From Uiverse.io by alexruix --> 

