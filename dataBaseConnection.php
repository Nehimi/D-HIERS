<?php
$server = "localhost";
$user = "root";
$password = "";
$database = "LichAmba_database";

$dataBaseConnetion = mysqli_connect($server, $user, $password, $database) or die(mysqli_error($dataBaseConnetion));


if ($dataBaseConnetion->connect_error) {
  die("Connection failed: " . $dataBaseConnetion->connect_error);
}

echo "Database connected successfully!";
?>