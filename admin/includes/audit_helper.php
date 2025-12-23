<?php
/**
 * Log an audit event to the database
 * 
 * @param mysqli $conn Database connection
 * @param int|null $userId User ID (null for anonymous/system events)
 * @param string $userName User name
 * @param string $userRole User role
 * @param string $action Action performed
 * @param string $details Detailed description
 * @param string $status Status (success, failed, error)
 * @return bool Success status
 */
function logAuditEvent($conn, $userId, $userName, $userRole, $action, $details, $status = 'success') {
    // Get IP address
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    // Prepare SQL statement
    $sql = "INSERT INTO audit_logs (user_id, user_name, user_role, action, details, ip_address, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("Audit log preparation failed: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("issssss", $userId, $userName, $userRole, $action, $details, $ipAddress, $status);
    
    $result = $stmt->execute();
    
    if (!$result) {
        error_log("Audit log execution failed: " . $stmt->error);
    }
    
    $stmt->close();
    
    return $result;
}

/**
 * Get recent audit logs
 * 
 * @param mysqli $conn Database connection
 * @param int $limit Number of logs to retrieve
 * @param int $offset Offset for pagination
 * @param array $filters Filters (action_type, role, date_range)
 * @return array Audit logs
 */
function getAuditLogs($conn, $limit = 20, $offset = 0, $filters = []) {
    $sql = "SELECT * FROM audit_logs WHERE 1=1";
    $params = [];
    $types = "";
    
    // Apply filters
    if (!empty($filters['action_type']) && $filters['action_type'] !== 'all') {
        $sql .= " AND action LIKE ?";
        $params[] = '%' . $filters['action_type'] . '%';
        $types .= 's';
    }
    
    if (!empty($filters['role']) && $filters['role'] !== 'all') {
        $sql .= " AND user_role = ?";
        $params[] = $filters['role'];
        $types .= 's';
    }
    
    if (!empty($filters['date_range'])) {
        $dateFilter = $filters['date_range'];
        switch ($dateFilter) {
            case 'last_24_hours':
                $sql .= " AND created_at >= NOW() - INTERVAL 1 DAY";
                break;
            case 'last_7_days':
                $sql .= " AND created_at >= NOW() - INTERVAL 7 DAY";
                break;
            case 'last_30_days':
                $sql .= " AND created_at >= NOW() - INTERVAL 30 DAY";
                break;
        }
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("Get audit logs preparation failed: " . $conn->error);
        return [];
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $logs = [];
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
    
    $stmt->close();
    
    return $logs;
}

/**
 * Get total count of audit logs
 * 
 * @param mysqli $conn Database connection
 * @param array $filters Filters to apply
 * @return int Total count
 */
function getAuditLogsCount($conn, $filters = []) {
    $sql = "SELECT COUNT(*) as total FROM audit_logs WHERE 1=1";
    $params = [];
    $types = "";
    
    // Apply the same filters as getAuditLogs
    if (!empty($filters['action_type']) && $filters['action_type'] !== 'all') {
        $sql .= " AND action LIKE ?";
        $params[] = '%' . $filters['action_type'] . '%';
        $types .= 's';
    }
    
    if (!empty($filters['role']) && $filters['role'] !== 'all') {
        $sql .= " AND user_role = ?";
        $params[] = $filters['role'];
        $types .= 's';
    }
    
    if (!empty($filters['date_range'])) {
        $dateFilter = $filters['date_range'];
        switch ($dateFilter) {
            case 'last_24_hours':
                $sql .= " AND created_at >= NOW() - INTERVAL 1 DAY";
                break;
            case 'last_7_days':
                $sql .= " AND created_at >= NOW() - INTERVAL 7 DAY";
                break;
            case 'last_30_days':
                $sql .= " AND created_at >= NOW() - INTERVAL 30 DAY";
                break;
        }
    }
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return 0;
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return (int)$row['total'];
}
?>
