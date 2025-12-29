<?php
session_start();
include("../dataBaseConnection.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Fetch all unique months from health_data and statistical_packages
$periods = [];

// From health_data - Use a more robust date extraction
$sqlHealth = "SELECT DISTINCT SUBSTRING(created_at, 1, 7) as period FROM health_data WHERE created_at IS NOT NULL AND created_at != '0000-00-00 00:00:00'";
$resHealth = $dataBaseConnection->query($sqlHealth);
if ($resHealth) {
    while($row = $resHealth->fetch_assoc()) {
        if ($row['period'] && preg_match('/^\d{4}-\d{2}$/', $row['period'])) {
            $periods[$row['period']] = true;
        }
    }
} else {
    error_log("Health Data Query Error: " . $dataBaseConnection->error);
}

// From statistical_packages
$sqlPkg = "SELECT DISTINCT period FROM statistical_packages WHERE period IS NOT NULL";
$resPkg = $dataBaseConnection->query($sqlPkg);
if ($resPkg) {
    while($row = $resPkg->fetch_assoc()) {
        if ($row['period']) $periods[$row['period']] = true;
    }
}


// Fallback: If no periods found in DB, add current month as a default option
if (empty($periods)) {
    $periods[date('Y-m')] = true;
}

krsort($periods); // Sort months descending (Newest first)
?>
<!-- 
     DEBUG: Found <?php echo count($periods); ?> periods.
     PHP Date: <?php echo date('Y-m'); ?>
-->
<?php


// 2. Helper to determine status for each month
function getPeriodStatus($conn, $period) {
    // Check if any 'Processed' exist vs 'Pending'/'Forwarded'/'Focal-Validated'
    $statusSql = "SELECT status, COUNT(*) as count FROM health_data WHERE DATE_FORMAT(created_at, '%Y-%m') = '$period' GROUP BY status";
    $res = $conn->query($statusSql);
    
    $counts = [];
    while($r = $res->fetch_assoc()) {
        $counts[$r['status']] = $r['count'];
    }

    // Package status check
    $pkgStatusSql = "SELECT status FROM statistical_packages WHERE period = '$period' LIMIT 1";
    $pkgRes = $conn->query($pkgStatusSql);
    $pkgStatus = $pkgRes->fetch_assoc()['status'] ?? '';

    if (isset($counts['Pending']) || isset($counts['Forwarded'])) {
        return "Pending Validation";
    }
    if ($pkgStatus === 'Processed' || $pkgStatus === 'Submitted' || (isset($counts['Processed']) && !isset($counts['Focal-Validated']))) {
        return "Processed";
    }
    if (isset($counts['Focal-Validated']) || $pkgStatus === 'Validated') {
        return "Ready";
    }
    return "Data Missing";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layout/head.php'; ?>
    <link rel="stylesheet" href="css/statistical_report.css">
    <title>Generate Statistical Report | D-HEIRS</title>
</head>

<body class="dashboard-body">

    <!-- Sidebar -->
    <?php include 'layout/sidebar.php'; ?>

    <main class="main-content">
        
        <header class="dashboard-header">
            <div>
                <h2><i class="fa-solid fa-chart-pie"></i> Reporting & Summarization</h2>
                <p class="actor-role">Role: Linkage Focal Person</p>
            </div>
        </header>

        <section class="report-selection-section" style="margin-top: 2rem;">
            <div class="selection-card">
                <h3>Generate Woreda Statistical Report</h3>
                <p class="description">Select the reporting period for which all Kebele data has been validated and
                    summarized.</p>

                <form method="POST" action="hmis_data_submission.php" class="report-form">

                    <div class="form-group">
                        <label for="reportMonth"><i class="fa-solid fa-calendar-alt"></i> Select Reporting Month</label>
                        <select id="reportMonth" name="reportMonth" required>
                            <option value="">-- Choose Month --</option>
                            <?php foreach($periods as $period => $val): 
                                $status = getPeriodStatus($dataBaseConnection, $period);
                                $dateObj = DateTime::createFromFormat('Y-m', $period);
                                $label = ($dateObj ? $dateObj->format('F Y') : $period) . " ($status)";
                                $disabled = ($status === 'Processed') ? 'disabled' : '';
                            ?>
                                <option value="<?php echo $period; ?>" <?php echo $disabled; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="help-text">Only periods with **Focal-Validated** data can be
                            selected.</small>
                    </div>

                    <div class="form-group">
                        <label for="kebeleFilter"><i class="fa-solid fa-location-dot"></i> Filter by Kebele
                            (Optional)</label>
                        <select id="kebeleFilter" name="kebeleFilter">
                            <option value="all">-- All Kebeles (Woreda Summary) --</option>
                            <option value="Lich-Amba">Lich-Amba</option>
                            <option value="Arada">Arada</option>
                            <option value="Lereba">Lereba</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button name="GenerateReport" type="submit" class="btn-primary">
                            <i class="fa-solid fa-file-export"></i> Generate Statistical Report
                        </button>
                    </div>
                </form>

            </div>



        </section>

    </main>
    <script src="../js/logout.js"></script>
</body>
</html>
