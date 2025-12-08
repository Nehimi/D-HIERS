<?php
$server = "localhost";
$user = "root";
$password = "";
$database = "LichAmba_database";

$dataBaseConnection = mysqli_connect($server, $user, $password, $database) or die(mysqli_error($dataBaseConnection));


if ($dataBaseConnection->connect_error) {
  die("Connection failed: " . $dataBaseConnection->connect_error);
}

echo "Database connected successfully!";
?>