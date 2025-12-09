<?php
include 'dataBaseConnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $firstName = $_POST['first_name'];
  $lastName = $_POST['last_name'];
  $email = $_POST['emali'];
  $phone = $_POST['phone_no'];
  $userId = $_POST['userId'];
  $role = $_POST['role'];
  $kebele = $_POST['kebele'];
  $status = $_POST['status'];
  $password = $_POST['password'];
  $confirm = $_POST['confirmPassword'];

  if ($password !== $confirm) {
    die("Passwords do not match!");
  }

  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  $sql = "INSERT INTO users 
            (first_name, last_name, emali, phone_no, userId, role, kebele, status, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

  $stmt = $dataBaseConnection->prepare($sql);

  if (!$stmt) {
    die("Prepare failed: " . $dataBaseConnection->error);
  }

  $stmt->bind_param(
    "sssssssss",
    $firstName,
    $lastName,
    $email,
    $phone,
    $userId,
    $role,
    $kebele,
    $status,
    $hashedPassword
  );

  if ($stmt->execute()) {
    echo "<script>alert('User Created Successfully!'); window.location='create_account.php';</script>";
  } else {
    echo "Error: " . $stmt->error;
  }
}
?>