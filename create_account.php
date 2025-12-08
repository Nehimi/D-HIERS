<?php
session_start();
include "dataBaseConnection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $first_name = $_POST["first_name"];
  $last_name = $_POST["last_name"];
  $emali = $_POST["emali"];
  $phone_no = $_POST["phone_no"];
  $userId = $_POST["userId"];
  $role = $_POST["role"];
  $kebele = $_POST["kebele"];
  $status = $_POST["status"];
  $password = $_POST["password"];
  $confirmPassword = $_POST["confirmPassword"];

  $hashedpassword = password_hash($password, PASSWORD_DEFAULT);

  $sql = "INSERT INTO users (first_name, last_name, emali, phone_no, userId, role, kebele, status, password, confirmPassword) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
  $stmt = $dataBaseConnection->prepare($sql);
  $stmt->bind_param("sssissssss", $first_name, $last_name, $emali, $phone_no, $userId, $role, $kebele, $status, $hashedpassword, $hashedpassword);

  if ($stmt->execute()) {
    echo "User Created Successfully!";
  } else {
    echo "Error: " . $stmt->error;
  }
  $stmt->close();
}

$dataBaseConnection->close();
?>