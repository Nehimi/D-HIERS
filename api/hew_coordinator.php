<?php
header("Content-Type: application/json");
include_once "../dataBaseConnection.php";

// Basic error handling
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
        case 'dashboard_stats':
            // 1. Active HEWs
            $activeHewsSql = "SELECT COUNT(*) as count FROM users WHERE role = 'hew' AND status = 'active'";
            $activeHewsRes = $dataBaseConnection->query($activeHewsSql);
            $activeHews = $activeHewsRes->fetch_assoc()['count'] ?? 0;

            // 2. Pending Reports
            $pendingReportsSql = "SELECT COUNT(*) as count FROM health_data WHERE status = 'Pending'";
            $pendingReportsRes = $dataBaseConnection->query($pendingReportsSql);
            $pendingReports = $pendingReportsRes->fetch_assoc()['count'] ?? 0;

            // 3. Validated Today
            $validatedTodaySql = "SELECT COUNT(*) as count FROM health_data WHERE status = 'Validated' AND DATE(updated_at) = CURDATE()";
            $validatedTodayRes = $dataBaseConnection->query($validatedTodaySql);
            $validatedToday = $validatedTodayRes->fetch_assoc()['count'] ?? 0;

            // 4. Total Packages Forwarded
            $packagesSql = "SELECT COUNT(*) as count FROM statistical_packages";
            $packagesRes = $dataBaseConnection->query($packagesSql);
            $packages = $packagesRes->fetch_assoc()['count'] ?? 0;

            $response = [
                'success' => true,
                'data' => [
                    'active_hews' => $activeHews,
                    'pending_reports' => $pendingReports,
                    'validated_today' => $validatedToday,
                    'packages_forwarded' => $packages
                ]
            ];
            break;

        case 'monitor':
            // 1. Fetch all HEW users
            $hewSql = "SELECT id, first_name, last_name, kebele, status FROM users WHERE role = 'hew'";
            $hewResult = $dataBaseConnection->query($hewSql);
            
            $data = [];
            
            if ($hewResult) {
                while($hew = $hewResult->fetch_assoc()) {
                    // Unique key for frontend mapping
                    $key = $hew['first_name'] . "_" . $hew['last_name'] . "_" . $hew['id'];
                    
                    // 2. Get Report Metrics for this HEW's Kebele
                    // (Assuming 1 Kebele per HEW for simplicity, or we filter health_data by hew_id if we had it)
                    $kebele = $dataBaseConnection->real_escape_string($hew['kebele']);
                    
                    $statsSql = "SELECT COUNT(*) as total, 
                                 SUM(CASE WHEN status='Pending' THEN 1 ELSE 0 END) as pending,
                                 SUM(CASE WHEN service_type='ANC Visit' THEN 1 ELSE 0 END) as anc
                                 FROM health_data 
                                 WHERE kebele = '$kebele'";
                    
                    $statsRes = $dataBaseConnection->query($statsSql);
                    $stats = $statsRes->fetch_assoc();
                    
                    $data[$key] = [
                        "name" => $hew['first_name'] . " " . $hew['last_name'],
                        "kebele" => $hew['kebele'],
                        "status" => $hew['status'], // User status
                        "visits" => $stats['total'] ?? 0,
                        "pending_reports" => $stats['pending'] ?? 0,
                        "anc_cases" => $stats['anc'] ?? 0
                    ];
                }
            }
            
            $response = ['success' => true, 'data' => $data];
            break;

        case 'review':
            // ... (Existing review logic is fine, it fetches real rows)
            $kebele = isset($_GET['kebele']) ? $_GET['kebele'] : '';
            $dataType = isset($_GET['dataType']) ? $_GET['dataType'] : '';
            $status = isset($_GET['status']) ? $_GET['status'] : '';

            if(!$kebele && $kebele !== 'all') {
                 throw new Exception("Kebele is required");
            }
            
            $queryData = [];
            
            if ($dataType == 'household data') {
                $sql = "SELECT householdId, memberName, age, sex FROM household WHERE kebele = ?";
                $stmt = $dataBaseConnection->prepare($sql);
                $stmt->bind_param("s", $kebele);
            } else {
                // Fetch health data with HEW name
                $sql = "SELECT h.*, CONCAT(u.first_name, ' ', u.last_name) as hew_name 
                        FROM health_data h 
                        LEFT JOIN users u ON h.submitted_by_id = u.id 
                        WHERE (h.kebele = ? OR ? = 'all')";
                
                if ($status) {
                    $sql .= " AND h.status = ?";
                    $stmt = $dataBaseConnection->prepare($sql);
                    $stmt->bind_param("sss", $kebele, $kebele, $status);
                } else {
                    $stmt = $dataBaseConnection->prepare($sql);
                    $stmt->bind_param("ss", $kebele, $kebele);
                }
            }
            
            if ($stmt && $stmt->execute()) {
                $res = $stmt->get_result();
                while($row = $res->fetch_assoc()) {
                    $queryData[] = $row;
                }
            }

            $response = ['success' => true, 'data' => $queryData];
            break;

        case 'mark_reviewed':
            $input = json_decode(file_get_contents('php://input'), true);
            $kebele = $input['kebele'] ?? '';
            $dataType = $input['dataType'] ?? '';

            if (!$kebele || !$dataType) throw new Exception("Kebele and Data Type are required");

            $sql = "UPDATE health_data SET status = 'Reviewed', updated_at = NOW() 
                    WHERE kebele = ? AND (service_type = ? OR 'household data' = ?) AND status = 'Pending'";
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bind_param("sss", $kebele, $dataType, $dataType);
            
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => "Successfully marked records as Reviewed", 'affected_rows' => $stmt->affected_rows];
            } else {
                throw new Exception("Review action failed: " . $stmt->error);
            }
            break;

        case 'validate':
            // ... (Existing validate logic is fine)
            $input = json_decode(file_get_contents('php://input'), true);
            $type = $input['dataType'] ?? '';
            
            if (!$type) throw new Exception("Data type is required");

            $stmt = $dataBaseConnection->prepare("UPDATE health_data SET status = 'Validated', updated_at = NOW() WHERE service_type = ? AND status IN ('Pending', 'Reviewed')");
            $stmt->bind_param("s", $type);
            
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => "Records validated successfully", 'affected_rows' => $stmt->affected_rows];
            } else {
                throw new Exception("Validation failed: " . $stmt->error);
            }
            break;

        case 'notify_submission':
            $input = json_decode(file_get_contents('php://input'), true);
            $hewId = $input['hewId'] ?? '';
            $kebele = $input['kebele'] ?? '';
            $hewName = $input['hewName'] ?? 'HEW';

            if (!$hewId || !$kebele) throw new Exception("HEW ID and Kebele are required");

            // Create notification for the Coordinator
            $title = "New Report from $hewName";
            $message = "A new health activity report has been submitted from Kebele: $kebele.";
            $actionUrl = "Review_HEW_Report.php"; // Coordinator's review page

            $stmt = $dataBaseConnection->prepare("INSERT INTO activity_notifications (title, message, type, action_url) VALUES (?, ?, 'info', ?)");
            $stmt->bind_param("sss", $title, $message, $actionUrl);
            
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => "Coordinator notified successfully"];
            } else {
                throw new Exception("Notification failed: " . $stmt->error);
            }
            break;

        case 'get_notifications':
            $stmt = $dataBaseConnection->prepare("SELECT * FROM activity_notifications WHERE is_read = 0 ORDER BY created_at DESC LIMIT 5");
            $stmt->execute();
            $result = $stmt->get_result();
            $notifications = [];
            while ($row = $result->fetch_assoc()) {
                $notifications[] = $row;
            }
            $response = ['success' => true, 'data' => $notifications];
            break;

        case 'forward':
            $input = json_decode(file_get_contents('php://input'), true);
            $dataType = $input['dataType'] ?? 'General';
            $notes = $input['notes'] ?? '';

            // 1. GENERATE SUMMARY from Validated Data (The "Generator" step)
            // We only forward 'Validated' data
            $summarySql = "SELECT service_type, COUNT(*) as count, kebele 
                           FROM health_data 
                           WHERE status = 'Validated'
                           GROUP BY service_type, kebele";
            
            $summaryRes = $dataBaseConnection->query($summarySql);
            $summaryData = [];
            $totalForwarded = 0;
            
            while($row = $summaryRes->fetch_assoc()) {
                $summaryData[] = $row;
                $totalForwarded += $row['count'];
            }
            
            if ($totalForwarded == 0) {
                 throw new Exception("No 'Validated' data found to forward. Please Validate data first.");
            }

            // 2. Create the Package
            $packageId = 'PKG-' . strtoupper(uniqid());
            $period = date('Y-m');
            $focalId = 1; 
            
            // Encode the real summary
            $jsonSummary = json_encode([
                "generated_by" => "HEW Coordinator",
                "forwarded_at" => date('Y-m-d H:i:s'),
                "notes" => $notes,
                "metrics" => $summaryData
            ]);

            $stmt = $dataBaseConnection->prepare("INSERT INTO statistical_packages (package_id, period, focal_person_id, status, data_summary) VALUES (?, ?, ?, 'Pending', ?)");
            $stmt->bind_param("ssis", $packageId, $period, $focalId, $jsonSummary);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to create package: " . $stmt->error);
            }

            // 3. Create Notification for Focal Person
            $notifTitle = "New Data Package from HEW Coordinator";
            $notifMsg = "Period: $period. Data Type: $dataType. $totalForwarded records. Notes: " . substr($notes, 0, 50) . "...";
            $notifSql = "INSERT INTO activity_notifications (role, title, message, action_url) VALUES ('linkage', ?, ?, 'statistical_report.html')";
            $notifStmt = $dataBaseConnection->prepare($notifSql);
            $notifStmt->bind_param("ss", $notifTitle, $notifMsg);
            $notifStmt->execute();

            // 4. Update records to 'Forwarded'
            $updateSql = "UPDATE health_data SET status = 'Forwarded' WHERE status = 'Validated'"; 
            $dataBaseConnection->query($updateSql);

            $response = [
                'success' => true, 
                'message' => "Aggregated $totalForwarded records and forwarded Package $packageId to Linkage Focal Person."
            ];
            break;

        default:
            $response = ['success' => false, 'message' => 'Valid actions: monitor, review, validate, forward'];
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
?>
