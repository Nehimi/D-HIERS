<?php
/**
 * Forgot Password API - Robust Version
 * D-HEIRS System
 */

session_start();
header('Content-Type: application/json');

include_once "../../dataBaseConnection.php";

error_reporting(E_ALL);
ini_set('display_errors', 0);

$response = [
    'status' => 'error',
    'message' => 'An unknown error occurred'
];

try {
    if (!$dataBaseConnection) {
        throw new Exception("Database connection failed");
    }

    $email = trim($_POST['email'] ?? '');
    if (empty($email)) {
        throw new Exception("Email address is required");
    }

    // 1. Find user - Check if column is 'email' or 'emali'
    $colRes = mysqli_query($dataBaseConnection, "SHOW COLUMNS FROM users");
    $emailCol = 'email';
    $hasEmail = false;
    while($col = mysqli_fetch_assoc($colRes)) {
        if ($col['Field'] === 'email') { $emailCol = 'email'; $hasEmail = true; break; }
        if ($col['Field'] === 'emali') { $emailCol = 'emali'; $hasEmail = true; }
    }

    $stmt = $dataBaseConnection->prepare("SELECT id, first_name, phone_no FROM users WHERE $emailCol = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Obfuscate success for security
        echo json_encode(['status' => 'success', 'message' => 'If this email is registered, you will receive a reset link shortly.']);
        exit();
    }

    $user = $result->fetch_assoc();
    $userId = $user['id'];
    $firstName = $user['first_name'];
    $phoneNumber = $user['phone_no'] ?? '';

    // 2. Generate and store token
    $token = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    // Ensure table and columns exist
    mysqli_query($dataBaseConnection, "CREATE TABLE IF NOT EXISTS `password_resets` (
        `id` int NOT NULL AUTO_INCREMENT,
        `user_id` int NOT NULL,
        `email` varchar(100) NOT NULL,
        `token` varchar(64) NOT NULL,
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `expires_at` datetime NOT NULL,
        `used` tinyint(1) NOT NULL DEFAULT 0,
        `details` text DEFAULT NULL,
        PRIMARY KEY (`id`)
    )");
    
    // Add details column if it missing (legacy support)
    $colCheck = mysqli_query($dataBaseConnection, "SHOW COLUMNS FROM password_resets LIKE 'details'");
    if (mysqli_num_rows($colCheck) == 0) {
        mysqli_query($dataBaseConnection, "ALTER TABLE password_resets ADD COLUMN details text DEFAULT NULL");
    }

    $insertStmt = $dataBaseConnection->prepare("INSERT INTO password_resets (user_id, email, token, expires_at, details) VALUES (?, ?, ?, ?, ?)");
    $details = json_encode(['ip' => $_SERVER['REMOTE_ADDR']]);
    $insertStmt->bind_param("issss", $userId, $email, $token, $expiresAt, $details);
    $insertStmt->execute();

    // 3. Send Notification
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $currentScriptPath = dirname($_SERVER['REQUEST_URI']); // e.g., /D-HEIRS/authentication/php
    // Construct path to login.html relative to this script: ../html/login.html
    // If $currentScriptPath is /project/authentication/php, we want /project/authentication/html/login.html
    $basePath = str_replace('/php', '/html', $currentScriptPath); 
    
    $resetLink = "$protocol://$host$basePath/login.html?token=$token";

    $notify = sendPasswordResetNotification($email, $phoneNumber, $firstName, $resetLink, $token);

    $response = [
        'status' => 'success',
        // Appending link for convenient testing
        'message' => 'A password reset link has been generated. <br><br>
        <div style="display:flex; gap:10px; align-items:center; background:rgba(15, 118, 110, 0.1); padding:10px; border-radius:8px;">
            <input type="text" value="' . $resetLink . '" id="resetLinkInput" readonly style="width:100%; border:none; background:transparent; font-size:12px; font-family:monospace; color:#0f766e; outline:none;">
            <button onclick="copyLink()" type="button" style="background:#0f766e; color:white; border:none; padding:6px 12px; border-radius:6px; cursor:pointer; font-size:12px; font-weight:600; white-space:nowrap;">
                <i class="fa-solid fa-copy"></i> Copy
            </button>
        </div>
        <br>
        <a href="' . $resetLink . '" style="color:#0f766e;text-decoration:underline;font-size:13px;font-weight:600;">Open Link Directly &rarr;</a>',
        'email_sent' => $notify['email_sent'],
        'sms_sent' => $notify['sms_sent']
    ];
    
    // In dev mode, return the link
    $response['reset_link'] = $resetLink;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

/**
 * Mock Notification Function
 * Replace this with actual PHPMailer/SMS logic in production
 */
function sendPasswordResetNotification($email, $phone, $name, $link, $token) {
    // Log the link for debugging
    error_log("Password Reset Link for $email: $link");
    
    return [
        'email_sent' => true,
        'sms_sent' => false
    ];
}

echo json_encode($response);
?>
