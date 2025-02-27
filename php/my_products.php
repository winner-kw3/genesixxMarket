<?php
include '../php/connection.php';

// Vérifier si l'utilisateur est connecté
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/login.php'); // Redirection si l'utilisateur n'est pas connecté
    exit();
}

$id_vendeur = intval($_SESSION['user_id']); // Sécurisation de l'ID du vendeur

// Récupérer les produits de l'utilisateur
$stmt = $conn->prepare("SELECT * FROM produits WHERE id_vendeur = ?");
$stmt->bind_param("i", $id_vendeur);
$stmt->execute();

// Vérifier si la requête a retourné des résultats
$result = $stmt->get_result();
if (!$result) {
    echo "<p>Erreur lors de la récupération des produits.</p>";
    exit();
}

// Traiter la modification ou la suppression d'un produit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['edit'])) {
        $id = intval($_POST['id']);
        $nom = htmlspecialchars($_POST['nom']);
        $description = htmlspecialchars($_POST['description']);
        $prix = floatval($_POST['prix']);
        $image = '';

        if (!empty($_FILES['image']['name'])) {
            $image = basename($_FILES['image']['name']);
            $target_file = "../images/produits/$image";

            // Vérifier si l'image est valide
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array(mime_content_type($_FILES['image']['tmp_name']), $allowed_types)) {
                move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
                $sql_update = "UPDATE produits SET nom = ?, description = ?, prix = ?, image = ? WHERE id = ? AND id_vendeur = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("ssdssi", $nom, $description, $prix, $image, $id, $id_vendeur);
            } else {
                echo "<p>Format d'image non autorisé.</p>";
                exit();
            }
        } else {
            $sql_update = "UPDATE produits SET nom = ?, description = ?, prix = ? WHERE id = ? AND id_vendeur = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ssdsi", $nom, $description, $prix, $id, $id_vendeur);
        }

        if ($stmt_update->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']); // Recharger la page après modification
            exit();
        } else {
            echo "<p>Erreur lors de la mise à jour.</p>";
        }
    }

    if (isset($_POST['delete'])) {
        $id = intval($_POST['delete']);
        $sql_delete = "DELETE FROM produits WHERE id = ? AND id_vendeur = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("ii", $id, $id_vendeur);

        if ($stmt_delete->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']); // Recharger la page après suppression
            exit();
        } else {
            echo "<p>Erreur lors de la suppression.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes produits</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .product {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px;
            display: inline-block;
            width: 250px;
            background: white;
            border-radius: 8px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .product img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }
        .product h2 {
            font-size: 1.2em;
            margin: 10px 0;
        }
        .product p {
            font-size: 1em;
            color: #555;
        }
        .form-container input, .form-container textarea {
            width: calc(100% - 20px);
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container button {
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
        }
        .delete-button {
            background-color: #FF4C4C;
            padding: 10px;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
            margin-top: 5px;
        }
        .form-container input[type="file"] {
            border: none;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .cancel {
            background-color: #ccc;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Mes produits</h1>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="product">
                <h2><?= htmlspecialchars($row['nom']) ?></h2>
                <img src="../images/produits/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['nom']) ?>">
                <p><?= htmlspecialchars($row['description']) ?></p>
                <p><strong><?= number_format($row['prix'], 2) ?> €</strong></p>

                <!-- Formulaire pour modifier un produit -->
                <form method="POST" enctype="multipart/form-data" class="form-container">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="text" name="nom" value="<?= htmlspecialchars($row['nom']) ?>" required>
                    <textarea name="description" required><?= htmlspecialchars($row['description']) ?></textarea>
                    <input type="number" name="prix" value="<?= htmlspecialchars($row['prix']) ?>" step="0.01" required>
                    <input type="file" name="image">
                    <button type="submit" name="edit">Modifier</button>
                </form>

                <!-- Bouton pour supprimer avec confirmation -->
                <button type="button" class="delete-button" onclick="showModal(<?= $row['id'] ?>)">Supprimer</button>
            </div>
        <?php } ?>
    </div>

    <!-- Modal de confirmation -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3>Êtes-vous sûr de vouloir supprimer ce produit ?</h3>
            <input type="hidden" id="deleteProductId">
            <form id="deleteForm" method="POST">
                <button type="button" class="cancel" onclick="closeModal()">Non</button>
                <button type="button" onclick="confirmDelete()">Oui</button>
            </form>
        </div>
    </div>

    <script>
        // Afficher le modal
        function showModal(productId) {
            document.getElementById('deleteProductId').value = productId;
            document.getElementById('confirmModal').style.display = 'flex';
        }

        // Fermer le modal
        function closeModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }

        // Confirmer la suppression
        function confirmDelete() {
            var productId = document.getElementById('deleteProductId').value;
            var form = document.getElementById('deleteForm');
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete';
            input.value = productId;
            form.appendChild(input);
            form.submit();
        }
    </script>

</body>
</html>
