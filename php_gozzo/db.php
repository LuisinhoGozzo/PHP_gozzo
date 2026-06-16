<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "nomina_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection Error: " . mysqli_connect_error());
}
?>