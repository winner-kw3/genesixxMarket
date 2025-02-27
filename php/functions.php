<?php
function getProduct($conn, $product_id) {
    $sql = "SELECT * FROM produits WHERE id = '$product_id'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}
?>
