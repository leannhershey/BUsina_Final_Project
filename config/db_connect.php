<?php
// config/db_connect.php
$conn = mysqli_connect("localhost", "root", "", "busina_db");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>