<?php
session_start();
include("dataBaseConnection.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  die("Invalid Request!");
}

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

$stmt = $dataBaseConnection->prepare("SELECT * FROM users WHERE userId = ?");
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
  die("User not found!");
}

$row = $result->fetch_assoc();

/* Check Role */
if ($row['role'] !== $detected_role) {
  die("Role mismatch! Please check your User ID.");
}

/* Verify Password */
if (!password_verify($password, $row['password'])) {
  die("Incorrect Password!");
}

/* Store Session */
$_SESSION['userId'] = $row['userId'];
$_SESSION['role'] = $row['role'];

/* Redirect According to Role */
switch ($row['role']) {

  case "hew":
    header("Location: HEW/HEW html/hew_dashboard.html");
    exit();

  case "coordinator":
    header("Location: HEW-COORDNATOR/Review_HEW_Report.html");
    exit();

  case "hmis":
    header("Location: dashboard_hmis.php");
    exit();

  case "linkage":
    header("Location: dashboard_linkage.php");
    exit();

  case "supervisor":
    header("Location: dashboard_supervisor.php");
    exit();

  case "admin":
    header("Location: admin.html");
    exit();
}

?>