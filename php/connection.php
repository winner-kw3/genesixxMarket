<?php
$servername = "localhost"; // Serveur MySQL local
$username = "root"; // Par défaut sous WAMP
$password = ""; // Aucun mot de passe par défaut sous WAMP
$dbname = "genesixx"; // Remplace par le nom exact de ta base de données

// Connexion à MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
?>

