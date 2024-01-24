<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "final_db";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}
?>
