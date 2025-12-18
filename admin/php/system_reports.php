<?php
session_start();
include("../../dataBaseConnection.php");

// Ensure Generated Reports Table Exists (Auto-setup for "match project" requirement)
$setupSql = "CREATE TABLE IF NOT EXISTS generated_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_name VARCHAR(255) NOT NULL,
    report_type VARCHAR(100) NOT NULL,
    generated_by VARCHAR(255) NOT NULL,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    file_size VARCHAR(50) DEFAULT '0 KB',
    status VARCHAR(50) DEFAULT 'Ready',
    format VARCHAR(20) NOT NULL,
    details TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($dataBaseConnection, $setupSql);

// =======================
// AJAX HANDLER: GENERATE REPORT
// =======================
if (isset($_POST['ajax_generate'])) {
    header('Content-Type: application/json');
    
    $reportType = $_POST['report_type'];
    $dateRange = $_POST['date_range'];
    $format = $_POST['format'];
    $adminName = "Dr. Admin"; // Should come from Session

    // Mock Data Generation Logic
    $fileSize = "0 KB";
    $status = "Ready";
    
    // Simulate query & processing time
    sleep(1);

    // Calculate 'Size' based on real data counts (Simulated)
    if ($reportType == 'User Activity Summary') {
        // Count audit logs
        $cRes = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as c FROM audit_logs");
        $count = mysqli_fetch_assoc($cRes)['c'];
        $fileSize = round(($count * 0.5), 1) . " KB"; 
    } elseif ($reportType == 'Health Post Performance') {
         // Count health data
        $cRes = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as c FROM health_data");
        $count = mysqli_fetch_assoc($cRes)['c'];
        $fileSize = round(($count * 1.2), 1) . " KB";
    } else {
        $fileSize = "15 KB";
    }

    $reportName = str_replace(' ', '_', $reportType) . "_" . date('M_d');
    if ($format == 'PDF') $reportName .= ".pdf";
    elseif ($format == 'CSV') $reportName .= ".csv";
    else $reportName .= ".xlsx";

    // Insert into DB
    $insertSql = "INSERT INTO generated_reports (report_name, report_type, generated_by, file_size, status, format) 
                  VALUES ('$reportName', '$reportType', '$adminName', '$fileSize', '$status', '$format')";
    
    if (mysqli_query($dataBaseConnection, $insertSql)) {
        echo json_encode(['status' => 'success', 'message' => 'Report generated successfully!', 'file' => $reportName]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($dataBaseConnection)]);
    }
    exit;
}

// =======================
// AJAX HANDLER: DOWNLOAD REPORT (Mock)
// =======================
if (isset($_GET['download_id'])) {
    $id = intval($_GET['download_id']);
    $res = mysqli_query($dataBaseConnection, "SELECT * FROM generated_reports WHERE id=$id");
    if ($row = mysqli_fetch_assoc($res)) {
        // Simply force download of a text file with mock content
        $filename = $row['report_name'];
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"$filename\""); 
        echo "D-HEIRS System Report\n";
        echo "Type: " . $row['report_type'] . "\n";
        echo "Date: " . $row['generated_at'] . "\n";
        echo "End of Report";
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Reports | D-HEIRS</title>
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/system_reports.css">
</head>

<body class="dashboard-body">

   <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="brand-icon">
                <img src="../../images/logo.png" alt="">
            </div>
            <div class="brand-text">
                D-HEIRS
                <span>Admin Portal</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item">
                <i class="fa-solid fa-grid-2"></i>
                <span>Dashboard</span>
            </a>
            <a href="user_management.php" class="nav-item">
                <i class="fa-solid fa-users-gear"></i>
                <span>User Management</span>
            </a>
            <a href="kebele_config.php" class="nav-item ">
                <i class="fa-solid fa-map-location-dot"></i>
                <span>Kebele Config</span>
            </a>
            <a href="audit_logs.php" class="nav-item">
                <i class="fa-solid fa-file-shield"></i>
                <span>Audit Logs</span>
            </a>
            <a href="system_reports.php" class="nav-item active">
                <i class="fa-solid fa-chart-pie"></i>
                <span>System Reports</span>
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <a href="../../index.html" class="nav-item logout">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="dashboard-header">
            <div class="header-search">
                <i class="fa-solid fa-search"></i>
                <input type="text" id="globalSearch" placeholder="Search reports...">
            </div>

            <div class="header-actions">
                <a href="messages.html" class="icon-btn">
                    <i class="fa-solid fa-bell"></i>
                    <span class="badge-dot"></span>
                </a>
                <a href="admin_profile.html" class="user-profile" style="cursor: pointer; text-decoration: none;">
                    <img src="../../images/avatar.png" alt="Admin" class="avatar-sm">
                    <div class="user-info">
                        <span class="name">Dr. Admin</span>
                        <span class="role">System Administrator</span>
                    </div>
                </a>
            </div>
        </header>
          <!-- Dashboard Content -->
        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h1>System Reports</h1>
                    <p class="page-subtitle">Generate and view system performance and health data reports</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="document.querySelector('.report-gen-card').scrollIntoView({behavior: 'smooth'})">
                        <i class="fa-solid fa-file-export"></i> New Report
                    </button>
                </div>
            </div>

            <!-- Report Generation Section -->
            <div class="config-card mb-4 report-gen-card">
                <div class="card-header">
                    <h2><i class="fa-solid fa-filter"></i> Generate Custom Report</h2>
                </div>
                <div class="card-body">
                    <form id="generateReportForm" class="report-form form-grid">
                        <div id="genMessage" class="message-container" style="grid-column: 1 / -1;"></div>
                        
                        <div class="form-row footer-links-like">
                            <div class="form-group">
                                <label>Report Type</label>
                                <select name="report_type" class="form-select">
                                    <option value="User Activity Summary">User Activity Summary</option>
                                    <option value="Health Post Performance">Health Post Performance</option>
                                    <option value="Disease Surveillance">Disease Surveillance</option>
                                    <option value="System Usage Stats">System Usage Stats</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Date Range</label>
                                <select name="date_range" class="form-select">
                                    <option value="This Week">This Week</option>
                                    <option value="This Month">This Month</option>
                                    <option value="Last 3 Months">Last 3 Months</option>
                                    <option value="Custom Range">Custom Range</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Format</label>
                                <div class="radio-group">
                                    <label class="radio-item">
                                        <input type="radio" name="format" value="PDF" checked> PDF
                                    </label>
                                    <label class="radio-item">
                                        <input type="radio" name="format" value="CSV"> CSV
                                    </label>
                                    <label class="radio-item">
                                        <input type="radio" name="format" value="Excel"> Excel
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fa-solid fa-bolt"></i> Generate Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Recent Generated Reports Table -->
            <div class="config-card mt-4">
                <div class="card-header">
                    <h2><i class="fa-solid fa-clock-rotate-left"></i> Recent Reports</h2>
                    <a href="system_reports_history.php" class="btn-text">View All History</a>
                </div>
                <div class="card-body">
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Report Name</th>
                                    <th>Type</th>
                                    <th>Generated By</th>
                                    <th>Date</th>
                                    <th>Size</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="reportsTableBody">
                                <?php
                                $histQuery = mysqli_query($dataBaseConnection, "SELECT * FROM generated_reports ORDER BY generated_at DESC LIMIT 5");
                                if (mysqli_num_rows($histQuery) > 0) {
                                    while ($row = mysqli_fetch_assoc($histQuery)) {
                                        $iconClass = 'pdf';
                                        $iconType = 'fa-file-pdf';
                                        if ($row['format'] == 'CSV') { $iconClass = 'csv'; $iconType = 'fa-file-csv'; }
                                        elseif ($row['format'] == 'Excel') { $iconClass = 'excel'; $iconType = 'fa-file-excel'; }
                                        
                                        $dateDisplay = date('M d, Y', strtotime($row['generated_at']));
                                        
                                        echo "<tr>
                                            <td class='primary-cell'>
                                                <div class='file-cell'>
                                                    <i class='fa-solid $iconType file-icon $iconClass'></i>
                                                    <span>{$row['report_name']}</span>
                                                </div>
                                            </td>
                                            <td>{$row['report_type']}</td>
                                            <td>{$row['generated_by']}</td>
                                            <td>{$dateDisplay}</td>
                                            <td>{$row['file_size']}</td>
                                            <td><span class='status-badge status-success'>{$row['status']}</span></td>
                                            <td>
                                                <a href='system_reports.php?download_id={$row['id']}' class='btn-icon' target='_blank'><i class='fa-solid fa-download'></i></a>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' style='text-align:center'>No reports generated yet.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script src="../js/script.js?v=<?php echo time(); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('generateReportForm');
            const messageContainer = document.getElementById('genMessage');
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                // Loading State
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Generating...';
                submitBtn.disabled = true;
                
                const formData = new FormData(this);
                formData.append('ajax_generate', '1');
                
                fetch('system_reports.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success') {
                         messageContainer.innerHTML = `<div class="success-message" style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center;">${data.message}</div>`;
                         
                         // Refresh table after 1s
                         setTimeout(() => {
                             location.reload();
                         }, 1000);
                    } else {
                        messageContainer.innerHTML = `<div class="error-message" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center;">${data.message}</div>`;
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                })
                .catch(err => {
                    console.error(err);
                    messageContainer.innerHTML = `<div class="error-message" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center;">An unexpected error occurred.</div>`;
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            });
        });
    </script>
</body>

</html>
