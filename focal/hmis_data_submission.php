<?php
session_start();
include("../dataBaseConnection.php");

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);

$reportMonth = $_POST['reportMonth'] ?? '';
$kebeleFilter = $_POST['kebeleFilter'] ?? 'all';

// Initialize stats
$newPatients = 0;
$deliveries = 0;
$facilityId = "FAC-LICH-01"; // Example default
$periodDisplay = "";

if ($reportMonth) {
    // 1. Calculate Stats from Focal-Validated Data
    $sql = "SELECT service_type, COUNT(*) as count 
            FROM health_data 
            WHERE status = 'Focal-Validated' 
            AND DATE_FORMAT(updated_at, '%Y-%m') = '" . $dataBaseConnection->real_escape_string($reportMonth) . "'";

    if ($kebeleFilter !== 'all') {
        $sql .= " AND kebele = '" . $dataBaseConnection->real_escape_string($kebeleFilter) . "'";
    }
    
    $sql .= " GROUP BY service_type";
    
    $result = $dataBaseConnection->query($sql);
    while($row = $result->fetch_assoc()) {
        if ($row['service_type'] == 'ANC Visit') $newPatients += $row['count']; 
        if ($row['service_type'] == 'Delivery') $deliveries += $row['count'];
    }
    
    $dateObj = DateTime::createFromFormat('Y-m', $reportMonth);
    $periodDisplay = $dateObj ? $dateObj->format('F Y') : $reportMonth;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layout/head.php'; ?>
    <title>Review & Submit HMIS Report | Focal Person</title>
</head>

<body class="dashboard-body">

    <!-- Sidebar -->
    <?php include 'layout/sidebar.php'; ?>

    <main class="main-content">
        
        <header class="dashboard-header">
            <div>
                <h2><i class="fa-solid fa-file-signature"></i> Final Review & Submission</h2>
                <p class="actor-role">Role: Linkage Focal Person</p>
            </div>
        </header>

        <section class="container" style="margin-top: 2rem; padding: 0;">
            
            <div class="selection-card">
                <h3><i class="fa-solid fa-list-check"></i> Review Statistical Report</h3>
                <p class="description">
                    Review the aggregated data for <strong><?php echo $periodDisplay ?: 'Selected Period'; ?></strong> before official submission.
                </p>

                <form action="generate_report_processor.php" method="POST" id="hmis-report-form">
                    
                    <input type="hidden" name="reportMonth" value="<?php echo htmlspecialchars($reportMonth); ?>">
                    <input type="hidden" name="kebeleFilter" value="<?php echo htmlspecialchars($kebeleFilter); ?>">
                    <input type="hidden" name="GenerateReport" value="true">

                    <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label>Facility ID</label>
                            <input type="text" name="facilityId" value="<?php echo $facilityId; ?>" readonly class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Reporting Period</label>
                            <input type="text" value="<?php echo $periodDisplay; ?>" readonly class="form-control">
                        </div>
                    </div>

                     <div class="form-group">
                        <label>Prepared By</label>
                        <input type="text" name="preparerName" value="<?php echo $_SESSION['full_name'] ?? 'Focal Person'; ?>" readonly class="form-control">
                    </div>

                    <div style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <h4 style="margin-bottom: 1rem; color: var(--primary);">Aggregated Service Indicators</h4>
                        
                        <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div class="form-group">
                                <label>Total ANC Visits / New Patients</label>
                                <input type="number" name="newPatients" value="<?php echo $newPatients; ?>" readonly class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Total Deliveries</label>
                                <input type="number" name="deliveries" value="<?php echo $deliveries; ?>" readonly class="form-control">
                            </div>
                        </div>

                        <div class="form-actions" style="margin-top: 2rem; display: flex; gap: 1rem;">
                            <button type="submit" class="btn-primary" style="flex: 1; justify-content: center;">
                                <i class="fa-solid fa-paper-plane"></i> Confirm & Submit to HMIS
                            </button>
                            <a href="statistical_report.php" class="btn-export" style="text-decoration: none;">
                                Cancel
                            </a>
                        </div>
                    </div>

                </form>
            </div>

        </section>

    </main>
    <script src="../js/logout.js"></script>
</body>
</html>
