<?php
session_start();
include "../dataBaseConnection.php";

// Handle Final Submission to HMIS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['preparerName'])) {
    $preparer = mysqli_real_escape_string($dataBaseConnection, $_POST['preparerName']);
    $month = mysqli_real_escape_string($dataBaseConnection, $_POST['reportingMonth']);
    $packageId = "STAT-" . date('Ymd') . "-" . rand(100, 999);
    $focalId = $_SESSION['user_db_id'] ?? null;
    
    // 1. Create a statistical package for HMIS Officer (Including focal_person_id for FK constraint)
    if ($focalId) {
        $insertPackage = mysqli_query($dataBaseConnection, "INSERT INTO statistical_packages (package_id, period, focal_person_id, focal_person_name, status) VALUES ('$packageId', '$month', '$focalId', '$preparer', 'Pending')");
    } else {
        // Fallback if session is lost, but still try to insert with NULL if allowed or handle error
        $insertPackage = mysqli_query($dataBaseConnection, "INSERT INTO statistical_packages (package_id, period, focal_person_name, status) VALUES ('$packageId', '$month', '$preparer', 'Pending')");
    }
    
    if ($insertPackage) {
        // 2. MAGIC: Create a notification for HMIS Officers
        $notifTitle = "New Data Submission";
        $notifMessage = "Focal Person **$preparer** submitted health data for **$month**. Package ID: **$packageId**.";
        $notifUrl = "hmis_dashboard.php";
        mysqli_query($dataBaseConnection, "INSERT INTO activity_notifications (role, title, message, type, action_url) VALUES ('hmis', '$notifTitle', '$notifMessage', 'success', '$notifUrl')");

        // 3. Update status of records to prevent duplicate submission
        mysqli_query($dataBaseConnection, "UPDATE health_data SET status='Submitted to DHIS2' WHERE status='Summarized'");
        $successMsg = "Report #$packageId successfully submitted to the HMIS Office!";
    }
}

// Fetch Focal Specific Stats for confirmation
$totalReportsQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total FROM health_data WHERE status='Summarized'");
$totalReports = ($totalReportsQuery) ? mysqli_fetch_assoc($totalReportsQuery)['total'] : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Submit Validated Data | D-HEIRS</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="focal_dashboard.css" />
    <link rel="stylesheet" href="hmis_data_submission.css">
    <link rel="stylesheet" href="../css/logout.css">
</head>

<body class="dashboard-body">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="brand-icon">
                <img src="image.jpg" alt="Logo">
            </div>
            <div class="brand-text">
                D-HEIRS
                <span>Focal Person Portal</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="focal_dashboard.php" class="nav-item">
                <i class="fa-solid fa-grid-2"></i>
                <span>Dashboard</span>
            </a>
            <a href="validate_data.php" class="nav-item">
                <i class="fa-solid fa-clipboard-check"></i>
                <span>Validate Data</span>
            </a>
            <a href="statistical_report.php" class="nav-item">
                <i class="fa-solid fa-chart-line"></i>
                <span>Statistical Reports</span>
            </a>
            <a href="generate_hmis_report.php" class="nav-item">
                <i class="fa-solid fa-file-medical"></i>
                <span>HMIS Reports</span>
            </a>
            <a href="export_hmis_report.php" class="nav-item">
                <i class="fa-solid fa-file-export"></i>
                <span>Export Data</span>
            </a>
            <a href="hmis_data_submission.php" class="nav-item active">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Submit to HMIS</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="../index.html" class="nav-item logout">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="dashboard-header">
            <div class="header-search">
                <i class="fa-solid fa-search"></i>
                <input type="text" placeholder="Search facilities, indicators...">
            </div>
            <div class="header-actions">
                <button class="icon-btn">
                    <i class="fa-solid fa-bell"></i>
                    <span class="badge-dot"></span>
                </button>
                <div class="user-profile">
                    <img src="image.jpg" alt="Profile" class="avatar-sm">
                    <div class="user-info">
                        <span class="name">Focal Officer</span>
                        <span class="role">District Coordinator</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h1>Submit Proccesed Data (UC-16)</h1>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">Submit validated and summarized health indicators to the HMIS Office.</p>
                </div>
                <div class="header-btns">
                    <button class="smart-back-btn" onclick="window.history.back()">
                        <span class="icon"><i class="fa-solid fa-arrow-left"></i></span>
                        Back
                    </button>
                </div>
            <?php if (isset($successMsg)): ?>
                <div class="alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <?php echo $successMsg; ?>
                </div>
            <?php endif; ?>

            <div class="card-panel" style="max-width: 900px; margin: 0 auto;">
                <div class="panel-header">
                    <h2>Submit Official Report</h2>
                </div>
                
                <form action="hmis_data_submission.php" method="POST" id="hmis-report-form">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <fieldset class="report-metadata" style="border: 2px solid #f1f5f9; padding: 2rem; border-radius: 16px; margin-bottom: 2rem;">
                            <legend style="color: var(--primary); font-weight: 800; padding: 0 15px; font-family: var(--font-heading); font-size: 1.1rem;">1. Submission Details</legend>
                            <div class="form-group">
                                <label for="facility-id">Facility ID / Code</label>
                                <input type="text" id="facility-id" name="facilityId" value="WOREDA-HQ-01" required>
                            </div>
                            <div class="form-group">
                                <label for="reporting-month">Reporting Month</label>
                                <input type="month" id="reporting-month" name="reportingMonth" value="2025-11" required>
                            </div>
                            <div class="form-group">
                                <label for="preparer-name">Coordinator Name</label>
                                <input type="text" id="preparer-name" name="preparerName" placeholder="Enter full name" required>
                            </div>
                        </fieldset>

                        <fieldset class="data-section" style="border: 2px solid #f1f5f9; padding: 2rem; border-radius: 16px; margin-bottom: 2rem;">
                            <legend style="color: var(--secondary); font-weight: 800; padding: 0 15px; font-family: var(--font-heading); font-size: 1.1rem;">2. Aggregated Metrics</legend>
                            <div class="form-group">
                                <label for="total-reports">Total Validated Reports</label>
                                <input type="number" id="total-reports" name="totalValidated" value="<?php echo $totalReports; ?>" readonly style="background: #f8fafc;">
                                <small style="color: var(--text-muted); font-style: italic;">Counted from validated records.</small>
                            </div>
                            <div class="form-group">
                                <label for="deliveries">Confirmed Deliveries</label>
                                <input type="number" id="deliveries" name="deliveries" min="0" value="0" required>
                            </div>
                        </fieldset>
                    </div>

                    <div class="submit-area" style="margin-top: 1rem; padding-top: 2rem; border-top: 2px solid #f1f5f9;">
                        <button type="submit" class="btn-primary" style="width: 100%; height: 60px;">
                            <i class="fa-solid fa-paper-plane"></i>
                            Finalize and Submit to HMIS Office
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="../js/logout.js"></script>
</body>

</html>
