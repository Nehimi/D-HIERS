<?php
session_start();
include("../dataBaseConnection.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

$message = "";
$messageType = "";

if (isset($_POST['GenerateReport'])) {
    $reportMonth = $_POST['reportMonth'] ?? ''; // Format: YYYY-MM
    $kebeleFilter = $_POST['kebeleFilter'] ?? 'all';
    
    if (!$reportMonth) {
        $message = "Please select a reporting month.";
        $messageType = "error";
    } else {
        // Convert YYYY-MM to "Month YYYY" for display/DB (e.g. "November 2025")
        $dateObj = DateTime::createFromFormat('Y-m', $reportMonth);
        $periodName = $dateObj->format('F Y');
        
        // 1. CHECK FOR VALIDATED DATA
        // We aggregate data that has been validated by the Focal Person (Status='Focal-Validated')
        // Filter by date (approximate based on updated_at) and Kebele
        
        // Check health_data
        $sql = "SELECT service_type, COUNT(*) as count 
                FROM health_data 
                WHERE status = 'Focal-Validated' 
                AND DATE_FORMAT(created_at, '%Y-%m') = '$reportMonth'";

        if ($kebeleFilter !== 'all') {
            $sql .= " AND kebele = '" . $dataBaseConnection->real_escape_string($kebeleFilter) . "'";
        }
        
        $sql .= " GROUP BY service_type";
        
        $result = $dataBaseConnection->query($sql);
        $stats = [];
        $totalRecords = 0;
        
        while($row = $result->fetch_assoc()) {
            $stats[] = $row;
            $totalRecords += $row['count'];
        }

        // --- ADDED: Check statistical_packages for this period ---
        $pkgSql = "SELECT 'Household Data Package' as service_type, COUNT(*) as count 
                   FROM statistical_packages 
                   WHERE status = 'Validated' AND period = '$reportMonth'";
        $pkgRes = $dataBaseConnection->query($pkgSql);
        if ($pkgRow = $pkgRes->fetch_assoc()) {
             if ($pkgRow['count'] > 0) {
                 $stats[] = $pkgRow;
                 $totalRecords += $pkgRow['count'];
             }
        }
        
        if ($totalRecords > 0) {
            // 2. GENERATE REPORT (Update Statistical Package or Create New Report Record)
            // For simplicity, we create a new entry in 'hmis_reports' marking it ready for the HMIS officer
            // Or we update the 'statistical_packages' status.
            
            $reportId = "RPT-" . strtoupper(uniqid());
            $genBy = $_SESSION['user_db_id'] ?? 1; // Fallback ID
            $jsonStats = json_encode($stats);
            
            // Insert into generated_reports (History)
            $insertSql = "INSERT INTO generated_reports (report_name, report_type, generated_by, status, format, details) 
                          VALUES (?, 'Statistical Summary', ?, 'Ready', 'PDF', ?)";
            
            $reportName = "Woreda Statistical Report - $periodName";
            
            $stmt = $dataBaseConnection->prepare($insertSql);
            $stmt->bind_param("sis", $reportName, $genBy, $jsonStats);
            
            if ($stmt->execute()) {
                 // 3. LOCK DATA (Update status to 'Processed' so it's not counted again)
                 $updateSql = "UPDATE health_data 
                               SET status = 'Processed' 
                               WHERE status = 'Focal-Validated' 
                               AND DATE_FORMAT(created_at, '%Y-%m') = '$reportMonth'";
                 
                 if ($kebeleFilter !== 'all') {
                    $updateSql .= " AND kebele = '" . $dataBaseConnection->real_escape_string($kebeleFilter) . "'";
                 }
                 
                 $dataBaseConnection->query($updateSql);

                 // Also lock statistical packages (Set to 'Submitted' so HMIS picks it up)
                 $dataBaseConnection->query("UPDATE statistical_packages SET status = 'Submitted' WHERE status = 'Validated' AND period = '$reportMonth'");
                 
                 $message = "Report generated successfully! $totalRecords records processed for $periodName.";
                 $messageType = "success";
            } else {
                $message = "Database error: " . $stmt->error;
                $messageType = "error";
            }
            
        } else {
            $message = "No 'Focal-Validated' data found for $periodName. Please validate data in the 'Validate Incoming Data' page first.";
            $messageType = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Generation Status</title>
    <link rel="stylesheet" href="statistical_report.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .status-card {
            max-width: 600px;
            margin: 100px auto;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            font-family: 'Inter', sans-serif;
        }
        .success { background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .error { background-color: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .btn {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.75rem 1.5rem;
            background: #0284c7;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
        }
        .btn:hover { background: #0369a1; }
    </style>
</head>
<body>
    <div class="status-card <?php echo $messageType; ?>">
        <i class="fa-solid <?php echo ($messageType=='success') ? 'fa-check-circle' : 'fa-circle-exclamation'; ?> fa-3x" style="margin-bottom:1rem"></i>
        <h2><?php echo ($messageType=='success') ? 'Success' : 'Action Required'; ?></h2>
        <p><?php echo $message; ?></p>
        
        <a href="focal_dashboard.php" class="btn">Return to Dashboard</a>
    </div>
</body>
</html>
