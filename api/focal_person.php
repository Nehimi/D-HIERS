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
            // Fetch detailed records forwarded by Coordinator (Status = 'Forwarded')
            $sql = "SELECT h.*, CONCAT(u.first_name, ' ', u.last_name) as hew_name 
                    FROM health_data h 
                    LEFT JOIN users u ON h.submitted_by_id = u.id 
                    WHERE h.status = 'Forwarded'"; // Only Forwarded items
            
            $result = $dataBaseConnection->query($sql);
            $data = [];
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $response = ['success' => true, 'data' => $data];
            break;

        case 'validate_row':
            // Focal Person accepts a row -> Status becomes 'Focal-Validated'
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;
            
            if (!$id) throw new Exception("Record ID required");

            $stmt = $dataBaseConnection->prepare("UPDATE health_data SET status = 'Focal-Validated', updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Record validated successfully'];
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
            
        default:
            throw new Exception("Unknown action: $action");
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
?>
