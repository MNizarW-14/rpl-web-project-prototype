<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "jurnalis_kampus";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>