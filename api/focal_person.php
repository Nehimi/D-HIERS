<?php
header("Content-Type: application/json");
include_once "../dataBaseConnection.php";

error_reporting(E_ALL);
ini_set('display_errors', 0);

$action = isset($_GET['action']) ? $_GET['action'] : '';
$response = ['success' => false, 'message' => 'Invalid action'];

if (!$dataBaseConnection) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    switch ($action) {
        case 'fetch_forwarded':
            // 1. Fetch detailed health records
            $sqlH = "SELECT h.id, h.updated_at, h.kebele, h.service_type, 
                           h.patient_name as details, h.status,
                           CONCAT(u.first_name, ' ', u.last_name) as hew_name 
                    FROM health_data h 
                    LEFT JOIN users u ON h.submitted_by_id = u.id 
                    WHERE h.status = 'Forwarded'"; 
            
            $resH = $dataBaseConnection->query($sqlH);
            $data = [];
            while($row = $resH->fetch_assoc()) {
                $data[] = $row;
            }

            // 2. Fetch aggregated statistical packages (e.g. Household Data)
            $sqlP = "SELECT package_id as id, updated_at, 'All' as kebele, 
                           'Household Data Package' as service_type,
                           'Aggregated Demographic Data' as details, status,
                           'Coordinator' as hew_name 
                    FROM statistical_packages 
                    WHERE status = 'Pending'";
            
            $resP = $dataBaseConnection->query($sqlP);
            while($row = $resP->fetch_assoc()) {
                $data[] = $row;
            }

            $response = ['success' => true, 'data' => $data];
            break;

        case 'validate_row':
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;
            
            if (!$id) throw new Exception("Record ID required");

            if (strpos($id, 'PKG-') === 0) {
                // It's a package
                $stmt = $dataBaseConnection->prepare("UPDATE statistical_packages SET status = 'Validated', updated_at = NOW() WHERE package_id = ?");
                $stmt->bind_param("s", $id);
            } else {
                // It's a health_data row
                $stmt = $dataBaseConnection->prepare("UPDATE health_data SET status = 'Focal-Validated', updated_at = NOW() WHERE id = ?");
                $stmt->bind_param("i", $id);
            }
            
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Validation recorded successfully'];
            } else {
                throw new Exception("Update failed: " . $stmt->error);
            }
            break;

        case 'return_row':
            // Focal Person rejects a row -> Status sends back to 'Returned'
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;
            $details = $input['reason'] ?? 'Returned by Foacl Person';
            
            if (!$id) throw new Exception("Record ID required");

            // Append return reason to details
            $stmt = $dataBaseConnection->prepare("UPDATE health_data SET status = 'Returned', details = CONCAT(details, ' [RETURN: ', ?, ']'), updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("si", $details, $id);
            
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Record returned to HEW'];
            } else {
                throw new Exception("Update failed: " . $stmt->error);
            }
            break;

        case 'get_stats_summary':
            // Used for the report generation screen, counts 'Focal-Validated' items
            $sql = "SELECT service_type, COUNT(*) as count, kebele 
                    FROM health_data 
                    WHERE status = 'Focal-Validated'
                    GROUP BY service_type, kebele";
            
            $result = $dataBaseConnection->query($sql);
            $data = [];
            $hasData = false;
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
                $hasData = true;
            }
            
            // Allow generation even if empty? Ideally not.
            $response = ['success' => true, 'data' => $data, 'can_generate' => $hasData];
            break;

        case 'get_notifications':
            // Fetch notifications for Focal Person (role='linkage' or role='focal')
            $role = $_SESSION['role'] ?? 'linkage';
            $stmt = $dataBaseConnection->prepare("SELECT * FROM activity_notifications WHERE (role = ? OR role = 'focal') AND is_read = 0 ORDER BY created_at DESC LIMIT 5");
            $stmt->bind_param("s", $role);
            $stmt->execute();
            $result = $stmt->get_result();
            $notifications = [];
            while ($row = $result->fetch_assoc()) {
                $notifications[] = $row;
            }
            $response = ['success' => true, 'data' => $notifications];
            break;

        case 'mark_notifications_seen':
            $role = $_SESSION['role'] ?? 'linkage';
            $updateSql = "UPDATE activity_notifications SET is_read = 1 WHERE (role = ? OR role = 'focal') AND is_read = 0";
            $stmt = $dataBaseConnection->prepare($updateSql);
            $stmt->bind_param("s", $role);
            $updateResult = $stmt->execute();

            if ($updateResult) {
                $response = ['success' => true, 'message' => 'Notifications marked as seen'];
            } else {
                $response = ['success' => false, 'message' => 'Failed to update notifications'];
            }
            break;

        case 'mark_notification_read':
            $notifId = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if ($notifId <= 0) {
                $response = ['success' => false, 'message' => 'Invalid notification ID'];
                break;
            }

            $stmt = $dataBaseConnection->prepare("UPDATE activity_notifications SET is_read = 1 WHERE id = ?");
            $stmt->bind_param("i", $notifId);
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Notification marked as read'];
            } else {
                $response = ['success' => false, 'message' => 'Failed to update notification'];
            }
            break;
            
        default:
            throw new Exception("Unknown action: $action");
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
?>
