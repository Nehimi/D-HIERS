<?php
header("Content-Type: application/json");
include_once "../dataBaseConnection.php";

error_reporting(E_ALL);
ini_set('display_errors', 0);

$role = isset($_GET['role']) ? $_GET['role'] : '';

if (!$role) {
    echo json_encode(['success' => false, 'message' => 'Role required']);
    exit;
}

// Fetch unread notifications for this role
$sql = "SELECT * FROM activity_notifications WHERE role = ? AND is_read = 0 ORDER BY created_at DESC LIMIT 5";
$stmt = $dataBaseConnection->prepare($sql);
$stmt->bind_param("s", $role);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode(['success' => true, 'count' => count($notifications), 'notifications' => $notifications]);
?>
