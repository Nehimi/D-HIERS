<?php
/**
 * Database Connection
 * D-HEIRS - Digital Health Extension Information Gathering & Reporting System
 * 
 * Database: lichamba_database
 * Server: localhost (XAMPP/WAMP)
 */

$servername = "localhost";
$username = "root";
$password = "";
$database = "lichamba_database";

// Create connection using mysqli
$dataBaseConnection = new mysqli($servername, $username, $password, $database);

// Check connection
if ($dataBaseConnection->connect_error) {
    error_log("Database connection failed: " . $dataBaseConnection->connect_error);
    die("Connection failed: " . $dataBaseConnection->connect_error);
}

// Set charset to UTF-8
$dataBaseConnection->set_charset("utf8mb4");
?>