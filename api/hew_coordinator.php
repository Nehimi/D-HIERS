<?php
session_start();
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
                    'packages_forwarded' => $packages,
                    'user_name' => $_SESSION['full_name'] ?? 'Coordinator' // Dynamic Name
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
                                 SUM(CASE WHEN service_type='maternal_health' THEN 1 ELSE 0 END) as anc
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
            $countOnly = isset($_GET['count_only']) && $_GET['count_only'] == '1';

            if(!$kebele && $kebele !== 'all') {
                 throw new Exception("Kebele is required");
            }
            
            $queryData = [];
            
            if ($dataType == 'household data') {
                if ($countOnly) {
                    $sql = "SELECT COUNT(*) as total FROM household WHERE (kebele = ? OR ? = 'all')";
                } else {
                    $sql = "SELECT householdId, memberName, age, sex FROM household WHERE (kebele = ? OR ? = 'all')";
                }
                $stmt = $dataBaseConnection->prepare($sql);
                $stmt->bind_param("ss", $kebele, $kebele);
            } else {
                // Fetch health data with HEW name
                if ($countOnly) {
                    $sql = "SELECT COUNT(*) as total FROM health_data h WHERE (h.kebele = ? OR ? = 'all')";
                } else {
                    $sql = "SELECT h.*, CONCAT(u.first_name, ' ', u.last_name) as hew_name 
                            FROM health_data h 
                            LEFT JOIN users u ON h.submitted_by_id = u.id 
                            WHERE (h.kebele = ? OR ? = 'all')";
                }
                
                // Add specific service type filter if not 'health_data' or 'General'
                $lowerDataType = strtolower($dataType);
                if ($lowerDataType != 'health_data' && $lowerDataType != 'general') {
                    $sql .= " AND h.service_type = '" . $dataBaseConnection->real_escape_string($dataType) . "'";
                }
                
                if ($status) {
                    $sql .= " AND h.status = ?";
                    $stmt = $dataBaseConnection->prepare($sql);
                    if ($countOnly) {
                        $stmt->bind_param("sss", $kebele, $kebele, $status);
                    } else {
                        $stmt->bind_param("sss", $kebele, $kebele, $status);
                    }
                } else {
                    $stmt = $dataBaseConnection->prepare($sql);
                    $stmt->bind_param("ss", $kebele, $kebele);
                }
            }
            
            if ($stmt && $stmt->execute()) {
                $res = $stmt->get_result();
                if ($countOnly) {
                    $rowCount = $res->fetch_assoc()['total'] ?? 0;
                    $queryData = ['total' => $rowCount];
                } else {
                    while($row = $res->fetch_assoc()) {
                        $queryData[] = $row;
                    }
                }
            }

            $debugInfo = [
                'dataType' => $dataType,
                'kebele' => $kebele,
                'status' => $status
            ];
            
            if ($dataType == 'household data') {
                $countRes = $dataBaseConnection->query("SELECT COUNT(*) as count FROM household");
                $debugInfo['total_households'] = $countRes->fetch_assoc()['count'];
            }

            $response = ['success' => true, 'data' => $queryData, 'debug' => $debugInfo];
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
            
             // SPECIAL CASE: Household Data doesn't have status, so just say OK.
            if ($type == 'household data') {
                $response = ['success' => true, 'message' => "Household data verified for forwarding.", 'affected_rows' => 0];
                break;
            }

            // STRICTER VALIDATION: Only allow validating 'Reviewed' items. 
            // Coordinator MUST review first.
            $stmt = $dataBaseConnection->prepare("UPDATE health_data SET status = 'Validated', updated_at = NOW() WHERE service_type = ? AND status = 'Reviewed'");
            $stmt->bind_param("s", $type);
            
            if ($stmt->execute()) {
                $affected = $stmt->affected_rows;
                if($affected == 0) {
                     $response = ['success' => false, 'message' => "No 'Reviewed' data found for this category. Please Review reports first."];
                } else {
                     $response = ['success' => true, 'message' => "Records validated successfully", 'affected_rows' => $affected];
                }
            } else {
                throw new Exception("Validation failed: " . $stmt->error);
            }
            break;


        case 'notify_submission':
            // ... (Existing implementation)
            $input = json_decode(file_get_contents('php://input'), true);
            $hewId = $input['hewId'] ?? '';
            $kebele = $input['kebele'] ?? '';
            $hewName = $input['hewName'] ?? 'HEW';

            if (!$hewId || !$kebele) throw new Exception("HEW ID and Kebele are required");

            $updateStatusSql = "UPDATE health_data SET status = 'Pending' WHERE kebele = ? AND (status IS NULL OR status = 'Draft' OR status = 'New' OR status = '')";
            $statusStmt = $dataBaseConnection->prepare($updateStatusSql);
            $statusStmt->bind_param("s", $kebele);
            $statusStmt->execute();

            $title = "New Report from $hewName";
            $message = "A new health activity report has been submitted from Kebele: $kebele.";
            $actionUrl = "Review_HEW_Report.php";

            $stmt = $dataBaseConnection->prepare("INSERT INTO activity_notifications (title, message, type, action_url) VALUES (?, ?, 'info', ?)");
            $stmt->bind_param("sss", $title, $message, $actionUrl);
            
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => "Coordinator notified successfully"];
            } else {
                throw new Exception("Notification failed: " . $stmt->error);
            }
            break;

        case 'get_notifications':
            // Filter by role to ensure coordinator only sees their notifications
            $role = $_SESSION['role'] ?? 'coordinator';
            $stmt = $dataBaseConnection->prepare("SELECT * FROM activity_notifications WHERE (role = ? OR role IS NULL OR role = '') AND is_read = 0 ORDER BY created_at DESC LIMIT 5");
            $stmt->bind_param("s", $role);
            $stmt->execute();
            $result = $stmt->get_result();
            $notifications = [];
            while ($row = $result->fetch_assoc()) {
                $notifications[] = $row;
            }
            $response = ['success' => true, 'data' => $notifications];
            break;

        case 'forward':
            // 33 Years Exp Dev Tip: Always use Transactions for complex state changes
            $dataBaseConnection->begin_transaction();
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                $dataType = $input['dataType'] ?? 'General';
                $notes = $input['notes'] ?? '';

                $summaryData = [];
                $totalForwarded = 0;

                // 2. COLLECT SUMMARY DATA
                if ($dataType == 'household data') {
                    $summarySql = "SELECT 'household data' as service_type, COUNT(*) as count, kebele, sex 
                                   FROM household 
                                   GROUP BY kebele, sex";
                    $summaryRes = $dataBaseConnection->query($summarySql);
                    while($row = $summaryRes->fetch_assoc()) {
                        $summaryData[] = $row;
                        $totalForwarded += $row['count'];
                    }
                    if ($totalForwarded == 0) throw new Exception("No Household data found to forward.");

                } else {
                    $summarySql = "SELECT service_type, COUNT(*) as count, kebele 
                                   FROM health_data 
                                   WHERE status = 'Validated'";
                    
                    if ($dataType != 'General') {
                        $summarySql .= " AND service_type = '" . $dataBaseConnection->real_escape_string($dataType) . "'";
                    }
                    $summarySql .= " GROUP BY service_type, kebele";
                    $summaryRes = $dataBaseConnection->query($summarySql);
                    while($row = $summaryRes->fetch_assoc()) {
                        $summaryData[] = $row;
                        $totalForwarded += $row['count'];
                    }
                    
                    if ($totalForwarded == 0) throw new Exception("No 'Validated' data found to forward.");
                }

                // 3. CREATE PACKAGE
                $packageId = 'PKG-' . strtoupper(uniqid());
                $period = date('Y-m');
                $focalRes = $dataBaseConnection->query("SELECT id FROM users WHERE role IN ('focal', 'linkage') LIMIT 1");
                $focalId = $focalRes->fetch_assoc()['id'] ?? 1;
                
                $jsonSummary = json_encode([
                    "generated_by" => $_SESSION['full_name'] ?? "HEW Coordinator",
                    "forwarded_at" => date('Y-m-d H:i:s'),
                    "notes" => $notes,
                    "metrics" => $summaryData
                ]);

                $coordName = $_SESSION['full_name'] ?? 'Woreda Coordinator';
                $stmt = $dataBaseConnection->prepare("INSERT INTO statistical_packages (package_id, period, focal_person_id, status, data_summary, coordinator_name) VALUES (?, ?, ?, 'Pending', ?, ?)");
                $stmt->bind_param("ssiss", $packageId, $period, $focalId, $jsonSummary, $coordName);
                if (!$stmt->execute()) throw new Exception("Package creation failed");

                // 4. BULK UPDATE STATUS
                if ($dataType != 'household data') {
                    $updateSql = "UPDATE health_data SET status = 'Forwarded' WHERE status = 'Validated'";
                    if ($dataType != 'General') {
                        $updateSql .= " AND service_type = '" . $dataBaseConnection->real_escape_string($dataType) . "'";
                    }
                    $dataBaseConnection->query($updateSql);
                }

                // 5. NOTIFY FOCAL PERSON
                $notifTitle = "New Data Package: $dataType";
                $notifMsg = "Period $period. $totalForwarded records forwarded.";
                $notifSql = "INSERT INTO activity_notifications (role, title, message, action_url) VALUES ('linkage', ?, ?, 'validate_incoming_data.php')";
                $notifStmt = $dataBaseConnection->prepare($notifSql);
                $notifStmt->bind_param("ss", $notifTitle, $notifMsg);
                $notifStmt->execute();

                $dataBaseConnection->commit();
                $response = ['success' => true, 'message' => "Successfully forwarded $totalForwarded records."];

            } catch (Exception $e) {
                $dataBaseConnection->rollback();
                throw $e;
            }
            break;

        case 'mark_notifications_seen':
            // Mark all notifications for this coordinator as read
            $coordinatorId = $_SESSION['user_id'] ?? null;
            if (!$coordinatorId) {
                $response = ['success' => false, 'message' => 'User not logged in'];
                break;
            }

            $updateSql = "UPDATE activity_notifications SET is_read = 1 WHERE role = 'coordinator' AND is_read = 0";
            $updateResult = $dataBaseConnection->query($updateSql);

            if ($updateResult) {
                $response = [
                    'success' => true,
                    'message' => 'Notifications marked as seen'
                ];
            } else {
                $response = ['success' => false, 'message' => 'Failed to update notifications'];
            }
            break;

        case 'mark_notification_read':
            // Mark a specific notification as read
            $notifId = isset($_GET['id']) ? intval($_GET['id']) : 0;
            
            if ($notifId <= 0) {
                $response = ['success' => false, 'message' => 'Invalid notification ID'];
                break;
            }

            $updateSql = "UPDATE activity_notifications SET is_read = 1 WHERE id = ?";
            $stmt = $dataBaseConnection->prepare($updateSql);
            $stmt->bind_param("i", $notifId);
            $updateResult = $stmt->execute();

            if ($updateResult) {
                $response = [
                    'success' => true,
                    'message' => 'Notification marked as read'
                ];
            } else {
                $response = ['success' => false, 'message' => 'Failed to update notification'];
            }
            break;

        default:
            $response = ['success' => false, 'message' => 'Valid actions: monitor, review, validate, forward'];
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
?>
