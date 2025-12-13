<?php
/**
 * Audit Logging Helper Function
 */

function logAction($connection, $action, $details, $status = 'success') {
    // Get user details from session if available
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    $userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'System';
    $userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'system';
    
    // Get IP Address
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    
    // Prepare SQL
    $sql = "INSERT INTO audit_logs (user_id, user_name, user_role, action, details, ip_address, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $connection->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("issssss", $userId, $userName, $userRole, $action, $details, $ipAddress, $status);
        $stmt->execute();
        $stmt->close();
    }
}
?>
