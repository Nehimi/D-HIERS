<?php
/**
 * Verify Reset Token API
 * D-HEIRS - Digital Health Extension Information Gathering & Reporting System
 * 
 * This API verifies if a password reset token is valid:
 * 1. Checks if token exists
 * 2. Checks if token has not been used
 * 3. Checks if token has not expired
 */

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
include "../dataBaseConnection.php";

// Response array
$response = [
    'status' => 'invalid',
    'message' => ''
];

try {
    // Verify database connection
    if (!$dataBaseConnection) {
        throw new Exception("Database connection failed");
    }

    // Get token from request (support both POST and GET)
    $token = trim($_POST['token'] ?? $_GET['token'] ?? '');

    // Validate token
    if (empty($token)) {
        $response['message'] = 'No token provided';
        echo json_encode($response);
        exit();
    }

    // First check if password_resets table exists
    $tableCheck = mysqli_query($dataBaseConnection, "SHOW TABLES LIKE 'password_resets'");
    if (mysqli_num_rows($tableCheck) == 0) {
        $response['message'] = 'Invalid reset link';
        echo json_encode($response);
        exit();
    }

    // Check token directly in password_resets table (simpler query)
    $stmt = $dataBaseConnection->prepare("SELECT * FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $response['message'] = 'Invalid reset link';
        echo json_encode($response);
        exit();
    }

    $tokenData = $result->fetch_assoc();

    // Check if token has been used
    if ($tokenData['used'] == 1) {
        $response['message'] = 'This reset link has already been used';
        echo json_encode($response);
        exit();
    }

    // Check if token has expired
    if (strtotime($tokenData['expires_at']) < time()) {
        $response['message'] = 'This reset link has expired';
        echo json_encode($response);
        exit();
    }

    // Token is valid - get user info
    $userId = $tokenData['user_id'];
    $userStmt = $dataBaseConnection->prepare("SELECT first_name, last_name, userId FROM users WHERE id = ?");
    $userStmt->bind_param("i", $userId);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    
    $userData = $userResult->fetch_assoc();

    $response['status'] = 'valid';
    $response['message'] = 'Token is valid';
    $response['data'] = [
        'email' => $tokenData['email'],
        'name' => ($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''),
        'userId' => $userData['userId'] ?? '',
        'expires_at' => $tokenData['expires_at']
    ];

} catch (Exception $e) {
    error_log("Verify Token Error: " . $e->getMessage());
    $response['status'] = 'error';
    $response['message'] = 'An error occurred while verifying the token';
}

echo json_encode($response);
?>
