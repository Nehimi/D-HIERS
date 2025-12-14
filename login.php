<?php
session_start();
header('Content-Type: application/json');
include("dataBaseConnection.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request!']);
    exit();
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
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID ID format!']);
    exit();
}

$stmt = $dataBaseConnection->prepare("SELECT * FROM users WHERE userId = ?");
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo json_encode(['status' => 'error', 'message' => 'User not found!']);
    exit();
}

$row = $result->fetch_assoc();

/* Check Role */
if ($row['role'] !== $detected_role) {
    echo json_encode(['status' => 'error', 'message' => 'Role mismatch! Please check your User ID.']);
    exit();
}

/* Verify Password */
if (!password_verify($password, $row['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Incorrect Password!']);
    exit();
}

/* Store Session */
$_SESSION['userId'] = $row['userId'];
$_SESSION['role'] = $row['role'];

/* Redirect According to Role */
$redirectUrl = "";
switch ($row['role']) {

  case "hew":
    $redirectUrl = "HEW/HEW_html/hew_dashboard.php";
    break;

  case "coordinator":
    $redirectUrl = "HEW-COORDNATOR/Review_HEW_Report.php";
    break;

  case "hmis":
    $redirectUrl = "dashboard_hmis.php";
    break;

  case "linkage":
    $redirectUrl = "dashboard_linkage.php";
    break;

  case "supervisor":
    $redirectUrl = "dashboard_supervisor.php";
    break;

  case "admin":
    $redirectUrl = "admin.html";
    break;
}

echo json_encode(['status' => 'success', 'redirect' => $redirectUrl]);
exit();

?>