<?php
/**
 * Unified Login API
 * Supports login via User ID or Email
 */
session_start();
header('Content-Type: application/json');
include("dataBaseConnection.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  echo json_encode(['status' => 'error', 'message' => 'Invalid Request!']);
  exit();
}

// Get and trim inputs
$loginInput = trim($_POST['userId'] ?? ''); // This field is labeled "User ID / Email" in HTML
$password = trim($_POST['password'] ?? '');

if (empty($loginInput) || empty($password)) {
  echo json_encode(['status' => 'error', 'message' => 'Both fields are required!']);
  exit();
}

try {
  // 1. Determine if input is email or User ID
  $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);

  // Check column names in users table (handling the 'emali' typo)
  $colRes = mysqli_query($dataBaseConnection, "SHOW COLUMNS FROM users");
  $emailCol = 'email';
  while ($col = mysqli_fetch_assoc($colRes)) {
    if ($col['Field'] === 'email') {
      $emailCol = 'email';
      break;
    }
    if ($col['Field'] === 'emali') {
      $emailCol = 'emali';
    }
  }

  // 2. Fetch user from database
  if ($isEmail) {
    $stmt = $dataBaseConnection->prepare("SELECT * FROM users WHERE $emailCol = ?");
  } else {
    $stmt = $dataBaseConnection->prepare("SELECT * FROM users WHERE userId = ?");
  }

  $stmt->bind_param("s", $loginInput);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows !== 1) {
    echo json_encode(['status' => 'error', 'message' => 'User not found!']);
    exit();
  }

  $row = $result->fetch_assoc();

  // 3. Verify Password
  if (!password_verify($password, $row['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Incorrect Password!']);
    exit();
  }

  // 4. Check Status (if active)
  if (isset($row['status']) && strtolower($row['status']) !== 'active') {
    echo json_encode(['status' => 'error', 'message' => 'Your account is currently ' . $row['status'] . '. Please contact your administrator.']);
    exit();
  }

  // 5. Detect Role Prefix Enforcement (Optional, keeping for legacy logic)
  // If they logged in via ID, we can still verify the prefix matches their role
  if (!$isEmail) {
    $detected_role = "";
    if (strpos($loginInput, "HEW") === 0)
      $detected_role = "hew";
    elseif (strpos($loginInput, "COORD") === 0)
      $detected_role = "coordinator";
    elseif (strpos($loginInput, "HMIS") === 0)
      $detected_role = "hmis";
    elseif (strpos($loginInput, "LINK") === 0)
      $detected_role = "linkage";
    elseif (strpos($loginInput, "SUP") === 0)
      $detected_role = "supervisor";
    elseif (strpos($loginInput, "ADMIN") === 0)
      $detected_role = "admin";

    if (!empty($detected_role) && $row['role'] !== $detected_role) {
      echo json_encode(['status' => 'error', 'message' => 'ID prefix does not match your assigned role!']);
      exit();
    }
  }

  // 6. Store Session
  $_SESSION['userId'] = $row['userId'];
  $_SESSION['role'] = $row['role'];
  $_SESSION['user_db_id'] = $row['id'];
  $_SESSION['full_name'] = ($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '');

  // 7. Redirect According to Role
  $redirectUrl = "";
  switch (strtolower($row['role'])) {
    case "hew":
      $redirectUrl = "HEW/php/hew_dashboard.php";
      break;
    case "coordinator":
      $redirectUrl = "HEW-COORDNATOR/Review_HEW_Report.php"; // Verify this later
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
      $redirectUrl = "admin/php/dashboard.php";
      break;
    default:
      $redirectUrl = "index.html"; // Fallback
  }

  echo json_encode(['status' => 'success', 'message' => 'Login successful', 'redirect' => $redirectUrl]);

} catch (Exception $e) {
  error_log("Login Error: " . $e->getMessage());
  echo json_encode(['status' => 'error', 'message' => 'A system error occurred. Please try again later.']);
}
?>