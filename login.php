<?php
session_start();
include("dataBaseConnection.php");

$userId = $_POST['userId'];
$password = $_POST['password'];

if (strpos($userId, "HEW") === 0) {
  $detected_role = "hew";
} elseif (strpos($userId, "COORD") === 0) {
  $detected_role = "coordinator";
} elseif (strpos($userId, "HMIS") === 0) {
  $detected_role = "hmis";
} elseif (strpos($userId, "LINK") === 0) {
  $detected_role = "linkage";
} elseif (strpos($userId, "SUP") === 0) {
  $detected_role = "supervisor";
} elseif (strpos($userId, "ADMIN") === 0) {
  $detected_role = "admin";
} else {
  die("Invalid ID format!");
}


$stmt = $dataBaseConnetion->prepare("SELECT * FROM users WHERE userId = ?");
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();

// If user exists
if ($result->num_rows == 1) {

  $row = $result->fetch_assoc();

  if ($row['role'] !== $detected_role) {
    die("Role mismatch! Please check your User ID.");
  }

  if (password_verify($password, $row['password'])) {

    $_SESSION['userId'] = $row['userId'];
    $_SESSION['role'] = $row['role'];
    $_SESSION['fullname'] = $row['fullname'];

    switch ($row['role']) {

      case "hew":
        header("Location: dashboard_hew.php");
        break;

      case "coordinator":
        header("Location: dashboard_coordinator.php");
        break;

      case "hmis":
        header("Location: dashboard_hmis.php");
        break;

      case "linkage":
        header("Location: dashboard_linkage.php");
        break;

      case "supervisor":
        header("Location: dashboard_supervisor.php");
        break;

      case "admin":
        header("Location: admin.html");
        break;
    }

    exit();

  } else {
    echo "Incorrect Password!";
  }

} else {
  echo "User not found!";
}
?>