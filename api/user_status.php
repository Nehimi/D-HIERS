<?php
/**
 * User Status Management API
 * Professional-grade status toggling system
 * 
 * Handles: active, inactive, pending status changes
 * Supports: Single user and bulk operations
 */

session_start();
header('Content-Type: application/json');
include "../dataBaseConnection.php";

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', '../logs/status_api_errors.log');

try {
    // Verify database connection
    if (!$dataBaseConnection) {
        throw new Exception("Database connection failed");
    }

    // Get request data
    $action = $_POST['action'] ?? $_GET['action'] ?? null;
    $userId = $_POST['user_id'] ?? $_GET['user_id'] ?? null;
    $userIds = $_POST['user_ids'] ?? null; // For bulk operations
    $newStatus = $_POST['status'] ?? $_GET['status'] ?? null;

    // Validate action
    if (!$action) {
        throw new Exception("No action specified");
    }

    // Response array
    $response = [
        'success' => false,
        'message' => '',
        'data' => null
    ];

    switch($action) {
        case 'change_status':
            if (!$userId || !$newStatus) {
                throw new Exception("User ID and status are required");
            }

            // Validate status
            $validStatuses = ['active', 'inactive', 'pending'];
            if (!in_array($newStatus, $validStatuses)) {
                throw new Exception("Invalid status. Must be: active, inactive, or pending");
            }

            // Update user status
            $stmt = $dataBaseConnection->prepare("UPDATE users SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $newStatus, $userId);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "User status updated to " . ucfirst($newStatus);
                $response['data'] = [
                    'user_id' => $userId,
                    'new_status' => $newStatus
                ];
                
                // Log the change
                error_log("Status changed: User ID $userId -> $newStatus", 3, '../logs/status_changes.log');
            } else {
                throw new Exception("Failed to update status: " . $stmt->error);
            }
            break;

        case 'bulk_status_change':
            if (!$userIds || !$newStatus) {
                throw new Exception("User IDs and status are required for bulk operation");
            }

            // Parse user IDs
            $ids = is_array($userIds) ? $userIds : explode(',', $userIds);
            
            // Validate status
            $validStatuses = ['active', 'inactive', 'pending'];
            if (!in_array($newStatus, $validStatuses)) {
                throw new Exception("Invalid status");
            }

            // Build placeholders
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            $sql = "UPDATE users SET status = ? WHERE id IN ($placeholders)";
            
            $stmt = $dataBaseConnection->prepare($sql);
            
            // Bind parameters
            $types = str_repeat('i', count($ids));
            $params = array_merge([$newStatus], $ids);
            $stmt->bind_param("s" . $types, ...$params);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Updated " . count($ids) . " users to " . ucfirst($newStatus);
                $response['data'] = [
                    'count' => count($ids),
                    'new_status' => $newStatus
                ];
            } else {
                throw new Exception("Bulk update failed: " . $stmt->error);
            }
            break;

        case 'toggle_status':
            // Toggle between active and inactive
            if (!$userId) {
                throw new Exception("User ID is required");
            }

            // Get current status
            $stmt = $dataBaseConnection->prepare("SELECT status FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if (!$user) {
                throw new Exception("User not found");
            }

            // Toggle status
            $newStatus = ($user['status'] === 'active') ? 'inactive' : 'active';
            
            $stmt = $dataBaseConnection->prepare("UPDATE users SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $newStatus, $userId);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "User status toggled to " . ucfirst($newStatus);
                $response['data'] = [
                    'user_id' => $userId,
                    'old_status' => $user['status'],
                    'new_status' => $newStatus
                ];
            } else {
                throw new Exception("Failed to toggle status");
            }
            break;

        case 'get_status_counts':
            // Get counts for each status
            $countsQuery = "
                SELECT 
                    status,
                    COUNT(*) as count
                FROM users
                GROUP BY status
            ";
            
            $result = mysqli_query($dataBaseConnection, $countsQuery);
            $counts = [
                'active' => 0,
                'inactive' => 0,
                'pending' => 0,
                'total' => 0
            ];

            while ($row = mysqli_fetch_assoc($result)) {
                $counts[$row['status']] = (int)$row['count'];
                $counts['total'] += (int)$row['count'];
            }

            $response['success'] = true;
            $response['message'] = "Status counts retrieved";
            $response['data'] = $counts;
            break;

        default:
            throw new Exception("Unknown action: " . $action);
    }

    echo json_encode($response);

} catch (Exception $e) {
    // Log error
    error_log("Status API Error: " . $e->getMessage(), 3, '../logs/status_api_errors.log');
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'data' => null
    ]);
}
?>
