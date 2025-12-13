<?php
/**
 * Database Connection - Simple & Clean
 */

$server = "localhost";
$user = "root";
$password = "";
$database = "LichAmba_database";

// Create connection
$dataBaseConnection = mysqli_connect($server, $user, $password, $database);

// Check connection
if (!$dataBaseConnection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($dataBaseConnection, "utf8mb4");
?>
