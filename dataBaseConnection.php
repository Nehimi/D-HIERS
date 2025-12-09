<?php
$server = "localhost";
$user = "root";
$password = "";
$database = "LichAmba_database";

$dataBaseConnection = mysqli_connect($server, $user, $password, $database) or die(mysqli_error($dataBaseConnection));
