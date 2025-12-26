<?php
/**
 * Reset Password API
 * D-HEIRS System
 */

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
include_once "../../dataBaseConnection.php";

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to avoid breaking JSON
ini_set('log_errors', 1);
ini_set('error_log', '../logs/password_reset_errors.log');

$response = [
    'status' => 'error',
    'message' => 'An unknown error occurred'
];

try {
    if (!$dataBaseConnection) {
        throw new Exception("Database connection failed");
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method");
    }

    $token = trim($_POST['token'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($token)) {
        throw new Exception("Reset token is missing");
    }

    if (empty($password)) {
        throw new Exception("New password is required");
    }

    // 1. Verify token exists and is valid
    $stmt = $dataBaseConnection->prepare("SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Invalid or expired reset token. Please request a new link.");
    }

    $tokenData = $result->fetch_assoc();
    $userId = $tokenData['user_id'];
    $email = $tokenData['email'];

    // Get exact userId (like HEW01) from users table to log it
    $infoStmt = $dataBaseConnection->prepare("SELECT userId FROM users WHERE id = ?");
    $infoStmt->bind_param("i", $userId);
    $infoStmt->execute();
    $infoData = $infoStmt->get_result()->fetch_assoc();
    $actualUserId = $infoData['userId'] ?? 'Unknown';

    error_log("Resetting password for User ID: $userId (Login ID: $actualUserId)");

    // 2. Update user's password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    // Check which columns exist in users table to build the correct query
    $columns = [];
    $colRes = mysqli_query($dataBaseConnection, "SHOW COLUMNS FROM users");
    while($col = mysqli_fetch_assoc($colRes)) {
        $columns[] = $col['Field'];
    }
    
    $query = "UPDATE users SET password = ?";
    if (in_array('confirmPassword', $columns)) {
        $query .= ", confirmPassword = ''";
    }
    $query .= " WHERE id = ?";
    
    $updateStmt = $dataBaseConnection->prepare($query);
    $updateStmt->bind_param("si", $hashedPassword, $userId);
    
    if (!$updateStmt->execute()) {
        throw new Exception("Failed to update password in database");
    }

    // 3. Mark token as used
    $markUsed = $dataBaseConnection->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
    $markUsed->bind_param("i", $tokenData['id']);
    $markUsed->execute();

    // 4. Log the success
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $logQuery = "INSERT INTO password_reset_logs (user_id, email, action, ip_address, user_agent, details) VALUES (?, ?, 'reset_success', ?, ?, 'Password updated successfully')";
    $logStmt = $dataBaseConnection->prepare($logQuery);
    $logStmt->bind_param("isss", $userId, $email, $ip, $ua);
    $logStmt->execute();

    $response = [
        'status' => 'success',
        'message' => 'Password reset successful!'
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
