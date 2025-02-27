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
    <title>Genesixx - MarketPlace</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- Styles -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #212529;
        }

        .navbar {
            background: linear-gradient(135deg, #4e54c8, #8f94fb);
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.8rem;
        }

        .navbar .nav-link {
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .navbar .nav-link:hover {
            color: #ffdd57;
        }

        .hero-section {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: white;
            padding: 120px 20px;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .hero-section p {
            font-size: 1.5rem;
            margin-bottom: 30px;
        }

        .btn-primary {
            background-color: #6a11cb;
            border: none;
            padding: 12px 24px;
            font-size: 1.2rem;
            border-radius: 30px;
        }

        .btn-primary:hover {
            background-color: #2575fc;
        }

        .search-bar {
            max-width: 600px;
            margin: 40px auto;
            display: flex;
            gap: 10px;
        }

        .product-card {
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }

        .product-card img {
            height: 300px;
            object-fit: cover;
        }

        .product-card .card-body {
            padding: 20px;
        }

        .product-card h5 {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .product-card p {
            font-size: 1rem;
            color: #6c757d;
        }

        .product-card .btn {
            padding: 10px 20px;
            border-radius: 30px;
        }

        /* Style général du footer */
.footer {
  background-color: #1a1a1a; /* Fond sombre */
  color: #fff; /* Texte en blanc */
  padding: 20px 0;
  text-align: center;
  font-family: Arial, sans-serif;
}

/* Style du texte */
.footer p {
  margin: 0;
  font-size: 14px;
  color: #aaa;
}

/* Icônes de réseaux sociaux */
.social-icons {
  margin-top: 10px;
}

.social-icon {
  color: #fff; /* Icônes blanches */
  font-size: 20px;
  margin: 0 10px;
  text-decoration: none;
  transition: color 0.3s;
}

/* Effet au survol des icônes */
.social-icon:hover {
  color: #ff5a5f; /* Changer la couleur au survol */
}

/* Taille des icônes */
.social-icon i {
  font-size: 1.5em; /* Icônes plus grandes */
}


    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Genesixx - MarketPlace</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if ($is_logged_in) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="php/my_products.php">Mon Compte</a>
                        </li>
                        <li class="nav-item">
                            <form action="logout.php" method="POST" class="d-inline">
                                <button type="submit" class="btn btn-danger">Déconnexion</button>
                            </form>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Connexion</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.php">Inscription</a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <h1>Explorez l'Univers des Produits Uniques</h1>
        <p>Des offres exclusives, une qualité inégalée. Faites-vous plaisir aujourd'hui !</p>
        <a href="php/upload_product.php" class="btn btn-light btn-lg">Vendre un Produit</a>
    </div>

    <!-- Search Bar -->
    <div class="container">
        <div class="search-bar">
            <input type="text" id="search" class="form-control" placeholder="Rechercher un produit...">
            <button class="btn btn-primary" onclick="searchProduct()">Rechercher</button>
        </div>
    </div>

    <!-- Product Listing -->
    <div class="container my-5">
        <div id="product-list" class="row g-4">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="col-lg-4 col-md-6 product-item">
                    <div class="card product-card">
                        <img src="<?= !empty($row['image']) ? 'images/produits/' . htmlspecialchars($row['image']) : '../images/default-product.jpg' ?>" alt="<?= htmlspecialchars($row['nom']) ?>" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['nom']) ?></h5>
                            <p class="card-text"><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                            <p class="fw-bold">Prix : <?= number_format($row['prix'], 2) ?> €</p>
                            <a href="../php/product.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-primary">Voir le Produit</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <footer class="footer">
    <div class="footer-content">
        <p>© 2025 Genesixx MarketPlae. Tous droits réservés.</p>
        <div class="social-icons">
        <a href="https://www.facebook.com" target="_blank" class="social-icon"><i class="fab fa-facebook-f"></i></a>
        <a href="https://www.instagram.com" target="_blank" class="social-icon"><i class="fab fa-instagram"></i></a>
        <a href="https://www.twitter.com" target="_blank" class="social-icon"><i class="fab fa-twitter"></i></a>
        <a href="https://www.linkedin.com" target="_blank" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
        </div>
    </div>
    </footer>


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