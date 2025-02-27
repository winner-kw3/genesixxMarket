<?php
$servername = "db5017292825.hosting-data.io";
$username = "dbu827038";
$password = "Kodjowinner2005$";
$dbname = "dbs13870670";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
