<?php
include 'php/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération des données du formulaire
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $numero_whatsapp = $_POST['numero_whatsapp'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT); // Sécurisation du mot de passe

    // Insertion dans la base de données
    $sql = "INSERT INTO utilisateurs (nom, email, numero_whatsapp, mot_de_passe) 
            VALUES ('$nom', '$email', '$numero_whatsapp', '$mot_de_passe')";

    if ($conn->query($sql) === TRUE) {
        echo "Inscription réussie. <a href='login.php'>Se connecter</a>";
    } else {
        echo "Erreur : " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>
    <title>Inscription</title>
</head>
<body>
<div class="form-box">
    <form class="form" method="POST">
        <span class="title">Créer un compte</span>
        <span class="subtitle">Create a free account with your email.</span>
        <div class="form-container">
            <input type="text" class="input" name="nom" required placeholder="Nom">
            <input type="email" class="input" name="email" required placeholder="Email">
            
            <!-- Champ pour le numéro WhatsApp avec l'intégration intl-tel-input -->
            <input id="phone" type="tel" class="input" name="numero_whatsapp" required placeholder="Numéro WhatsApp">
            
            <input type="password" class="input" name="mot_de_passe" required placeholder="Mot de passe">
        </div>
        <button type="submit">S'inscrire</button>
    </form>
    <div class="form-section">
        <p>Vous avez un compte ? <a href="login.php">Connectez-vous</a></p>
    </div>
</div>

<!-- Intégration des scripts JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script>
    var input = document.querySelector("#phone");
    var iti = window.intlTelInput(input, {
        initialCountry: "auto",
        geoIpLookup: function(callback) {
            fetch('https://ipapi.co/json')
                .then(response => response.json())
                .then(data => callback(data.country_code))
                .catch(() => callback('FR'));
        },
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
    });

    // Lorsque le formulaire est soumis, récupère le code pays et le numéro
    document.querySelector("form").addEventListener("submit", function(event) {
        var codePays = iti.getSelectedCountryData().dialCode; // Code pays (avec le +)
        var numero = iti.getNumber(); // Numéro complet (avec le code pays et +)

        // Supprimer le "+" et les espaces dans le numéro avant de l'envoyer
        var numeroSansPlus = numero.replace(/\+/g, ''); // Retirer le "+" du code pays
        var numeroSansEspaces = numeroSansPlus.replace(/\s+/g, ''); // Supprimer les espaces

        // Envoie le code pays au champ caché et garde le numéro formaté sans "+" et espaces
        document.querySelector("#code_pays").value = codePays; // Envoie le code pays dans le champ caché
        document.querySelector("input[name='numero_whatsapp']").value = numeroSansEspaces; // Envoie le numéro sans "+" et espaces
    });
</script>



</body>
</html>
