<?php
include 'connection.php';
include 'functions.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $product = getProduct($conn, $product_id);

    // Récupérer le numéro WhatsApp du vendeur
    $user_id = $product['id_vendeur'];
    $query = "SELECT numero_whatsapp FROM utilisateurs WHERE id = $user_id";
    $result = $conn->query($query);
    $user = $result->fetch_assoc();
    $numero_whatsapp = $user['numero_whatsapp'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['nom']; ?></title>
</head>
<body>
    <h2><?php echo $product['nom']; ?></h2>
    <img src="../images/produits/<?php echo $product['image']; ?>" alt="<?php echo $product['nom']; ?>">
    <p><?php echo $product['description']; ?></p>
    <p>Prix : <?php echo $product['prix']; ?>€</p>
    <a href="https://wa.me/<?php echo $numero_whatsapp; ?>?text=Je%20suis%20intéressé%20par%20le%20produit%20<?php echo $product['nom']; ?>">Contacter le vendeur sur WhatsApp</a>
</body>
</html>
