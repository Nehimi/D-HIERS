<?php
session_start();
include "../dataBaseConnection.php";

// Fetch Focal Person Specific Stats
// 1. Total Household Reports (Ready for Validation/HMIS)
$totalReportsQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total FROM health_data");
$totalReports = ($totalReportsQuery) ? mysqli_fetch_assoc($totalReportsQuery)['total'] : 0;

// 2. Pending HMIS Submissions (Ready for Validation)
$pendingValidationQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total FROM health_data WHERE status='Pending' OR status IS NULL");
$pendingValidation = ($pendingValidationQuery) ? mysqli_fetch_assoc($pendingValidationQuery)['total'] : 0;

// 3. Recently Submitted Data (for activity feed)
$recentReportsQuery = mysqli_query($dataBaseConnection, "SELECT * FROM health_data ORDER BY id DESC LIMIT 5");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Focal Person Dashboard | D-HEIRS</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="focal_dashboard.css" />
    <link rel="stylesheet" href="../css/logout.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <a href="focal_dashboard.php" class="nav-item active">
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
            <a href="hmis_data_submission.php" class="nav-item">
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
        <!-- Header -->
        <header class="dashboard-header">
            <div class="header-search">
                <i class="fa-solid fa-search"></i>
                <input type="text" placeholder="Search reports, data, validation status...">
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

        <!-- Dashboard Content -->
        <div class="content-wrapper">
            <div class="page-header">
                <h1>Focal Overview</h1>
                <div class="header-btns">
                    <button class="smart-back-btn" onclick="window.history.back()">
                        <span class="icon"><i class="fa-solid fa-arrow-left"></i></span>
                        Back
                    </button>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon color-1">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <div class="stat-details">
                        <h3>Total Reports</h3>
                        <p class="number"><?php echo $totalReports; ?></p>
                        <span class="trend positive"><i class="fa-solid fa-check-circle"></i> Received</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon color-2">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div class="stat-details">
                        <h3>Needs Validation</h3>
                        <p class="number"><?php echo $pendingValidation; ?></p>
                        <span class="trend neutral">Pending Review</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon color-3">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="stat-details">
                        <h3>System Integrity</h3>
                        <p class="number">98%</p>
                        <span class="trend positive">Operational</span>
                    </div>
                </div>
            </div>

            <!-- Recent Reports Table -->
            <div class="dashboard-grid" style="margin-top: 2rem;">
                <div class="card-panel table-section">
                    <div class="panel-header">
                        <h2>Recent Health Data</h2>
                        <a href="export_hmis_report.php" class="view-all">View All</a>
                    </div>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Report ID</th>
                                    <th>Patient</th>
                                    <th>Kebele</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($recentReportsQuery && mysqli_num_rows($recentReportsQuery) > 0) {
                                    while ($row = mysqli_fetch_assoc($recentReportsQuery)) {
                                        echo "<tr>";
                                        echo "<td><strong>#" . $row['id'] . "</strong></td>";
                                        echo "<td>" . htmlspecialchars($row['patient_name'] ?? 'N/A') . "</td>";
                                        echo "<td>" . htmlspecialchars($row['kebele'] ?? 'N/A') . "</td>";
                                        $status = $row['status'] ?? 'Pending';
                                        $statusClass = strtolower(str_replace(' ', '-', $status));
                                        echo "<td><span class='status-tag $statusClass'>" . $status . "</span></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' style='padding: 2rem; text-align: center; color: var(--text-light);'>No recent data available</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-panel side-panel">
                    <div class="panel-header">
                        <h2>Quick Actions</h2>
                    </div>
                    <div class="action-list">
                        <a href="statistical_report.php" class="action-item" style="text-decoration: none; color: inherit;">
                            <div class="action-icon"><i class="fa-solid fa-chart-pie"></i></div>
                            <div class="action-text">
                                <strong style="display: block;">Generate Stats</strong>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">Aggregation for reporting</span>
                            </div>
                        </a>
                        <a href="hmis_data_submission.php" class="action-item" style="text-decoration: none; color: inherit;">
                            <div class="action-icon"><i class="fa-solid fa-paper-plane"></i></div>
                            <div class="action-text">
                                <strong style="display: block;">Submit to HMIS</strong>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">Send to national DHIS2</span>
                            </div>
                        </a>
                        <a href="validate_data.php" class="action-item" style="text-decoration: none; color: inherit;">
                            <div class="action-icon"><i class="fa-solid fa-clipboard-check"></i></div>
                            <div class="action-text">
                                <strong style="display: block;">Validate Batch</strong>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">Verify pending field data</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../js/logout.js"></script>
</body>

</html>
