<?php
session_start(); // Démarrer la session dès le début
include 'php/connection.php';

// Vérifier la connexion à la base de données
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

// Vérifier si l'utilisateur est connecté
$is_logged_in = isset($_SESSION['user_id']);

// Récupérer les produits
$sql = "SELECT * FROM produits";
$result = $conn->query($sql);

// Vérification de la requête SQL
if (!$result) {
    die("Erreur SQL : " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">

    <title>Produits disponibles</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #007BFF;
            color: white;
        }
        .header a, .logout-button, .account-button {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
        }
        .logout-button, .account-button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
            color: white;
        }
        .search-container {
            margin: 20px 0;
        }
        .search-bar {
            padding: 8px;
            width: 250px;
        }
        .reset-button {
            padding: 8px 10px;
            background-color: #FF4C4C;
            color: white;
            border: none;
            cursor: pointer;
            margin-left: 10px;
        }
        .product {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px;
            display: inline-block;
            width: 200px;
            background: white;
            border-radius: 8px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .product img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .manage-products {
            margin-top: 20px;
        }
        .manage-products a {
            padding: 8px 15px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div>
            <?php if ($is_logged_in) { ?>
                <form action="logout.php" method="POST" style="display:inline;">
                    <button type="submit" class="logout-button">Déconnexion</button>
                </form>
                <a href="../php/my_products.php" class="account-button">Mon compte</a>
            <?php } else { ?>
                <a href="login.php">Connexion</a>
                <a href="register.php">Inscription</a>
            <?php } ?>
        </div>
        <a href="../php/upload_product.php">Vendre un produit</a>
    </div>

    <h1>Produits disponibles</h1>

    <div class="search-container">
        <input type="text" id="search" class="search-bar" placeholder="Rechercher un produit..." onkeyup="searchProduct()">
        <button class="reset-button" onclick="resetSearch()">Effacer le filtre</button>
    </div>

    <div id="product-list"> 
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="product">
                <h2><?= htmlspecialchars($row['nom']) ?></h2>
                <?php if (!empty($row['image'])) { ?>
                    <img src="../images/produits/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['nom']) ?>">
                <?php } else { ?>
                    <img src="../images/default-product.jpg" alt="Image non disponible">
                <?php } ?>
                <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                <p><strong><?= number_format($row['prix'], 2) ?> €</strong></p>
                <a href="../php/product.php?id=<?= htmlspecialchars($row['id']) ?>">Voir le produit</a>

            </div>
        <?php } ?>
    </div>

    <?php if ($is_logged_in) { ?>
        <div class="manage-products">
            <a href="../php/my_products.php">Mes produits</a>
        </div>
    <?php } ?>

    <script>
        function searchProduct() {
            let input = document.getElementById('search').value.toLowerCase();
            let products = document.querySelectorAll('.product');

            products.forEach(product => {
                let title = product.querySelector('h2').textContent.toLowerCase();
                product.style.display = title.includes(input) ? 'inline-block' : 'none';
            });
        }

        function resetSearch() {
            document.getElementById('search').value = '';
            let products = document.querySelectorAll('.product');
            products.forEach(product => {
                product.style.display = 'inline-block';
            });
        }
    </script>

</body>
</html>
