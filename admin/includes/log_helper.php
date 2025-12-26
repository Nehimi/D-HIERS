<?php
/**
 * Audit Logging Helper Function
 */

function logAction($connection, $action, $details, $status = 'success') {
    try {
        // Get user details from session if available
        $userId = $_SESSION['user_id'] ?? 0;
        $userName = $_SESSION['user_name'] ?? 'System';
        $userRole = $_SESSION['role'] ?? 'system';
        
        // Get IP Address
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        
        // Prepare SQL - Using the modern schema with user_role and status
        $sql = "INSERT INTO audit_logs (user_id, user_name, user_role, action, details, ip_address, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $connection->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("issssss", $userId, $userName, $userRole, $action, $details, $ipAddress, $status);
            $stmt->execute();
            $stmt->close();
        } else {
            error_log("Audit Logging Prepare Failed: " . $connection->error);
        }
    } catch (Exception $e) {
        // Log to PHP error log but don't kill the main process
        error_log("Audit Logging Failed: " . $e->getMessage());
    }
}
?>
