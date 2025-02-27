<?php
include 'connection.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validation des données envoyées
    $nom = mysqli_real_escape_string($conn, $_POST['nom']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $prix = mysqli_real_escape_string($conn, $_POST['prix']);
    
    // Vérification de l'image téléchargée
    $image = $_FILES['image']['name'];
    $target_dir = '../images/produits/';
    $target_file = $target_dir . basename($image);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Vérifier si le fichier est une image réelle ou une image falsifiée
    $check = getimagesize($_FILES['image']['tmp_name']);
    if ($check === false) {
        echo "Ce fichier n'est pas une image.";
        exit();
    }

    // Vérifier la taille de l'image (par exemple, pas plus de 5 Mo)
    if ($_FILES['image']['size'] > 5000000) {
        echo "L'image est trop grande. La taille maximale autorisée est de 5 Mo.";
        exit();
    }

    // Vérifier si le fichier a une extension autorisée (jpg, jpeg, png, gif)
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo "Seules les images au format JPG, JPEG, PNG et GIF sont autorisées.";
        exit();
    }

    // Déplacer l'image vers le dossier cible
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        echo "Désolé, une erreur est survenue lors du téléchargement de l'image.";
        exit();
    }

    // Ajouter le produit à la base de données
    $user_id = $_SESSION['user_id']; // Assurez-vous que $_SESSION['user_id'] est bien défini

    // Préparer la requête SQL avec une requête préparée pour éviter les injections SQL
    $stmt = $conn->prepare("INSERT INTO produits (id_vendeur, nom, description, prix, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issds", $user_id, $nom, $description, $prix, $image); // "issds" pour : int, string, string, decimal, string

    // Vérifier si l'exécution de la requête réussit
    if ($stmt->execute()) {
        echo "Produit ajouté avec succès !";
    } else {
        echo "Erreur lors de l'ajout du produit : " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <h1>Ajouter un produit</h1>

    <form action="upload_product.php" method="POST" enctype="multipart/form-data">
        <label for="nom">Nom du produit</label>
        <input type="text" id="nom" name="nom" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" required></textarea>

        <label for="prix">Prix</label>
        <input type="number" id="prix" name="prix" step="0.01" required>

        <label for="image">Image du produit</label>
        <input type="file" id="image" name="image" accept="image/*" required>

        <button type="submit">Ajouter le produit</button>
    </form>

    <a href="index.php">Retour à la liste des produits</a>

</body>
</html>
