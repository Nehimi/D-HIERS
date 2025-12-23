<?php
session_start();
include "../dataBaseConnection.php";

// Fetch submitted data packages (simulated/placeholder logic for now)
// In a real system, this would come from a 'submitted_reports' table
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMIS Reports | D-HEIRS</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="focal_dashboard.css" />
    <link rel="stylesheet" href="generate_hmis_report.css">
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
            <a href="generate_hmis_report.php" class="nav-item active">
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
                <input type="text" placeholder="Search data packages...">
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
                    <h1>HMIS Report Generation</h1>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">Select statistical packages to generate final HMIS templates.</p>
                </div>
                <div class="header-btns">
                    <button class="smart-back-btn" onclick="window.history.back()">
                        <span class="icon"><i class="fa-solid fa-arrow-left"></i></span>
                        Back
                    </button>
                </div>
            </div>

            <section class="hmis-report-section">
                <div class="selection-card data-package">
                    <h3>1. Data Package Selection</h3>
                    <p class="description">Choose the finalized statistical data package submitted for national reporting.</p>

                    <form method="POST" action="hmis_report_process.php" class="hmis-form">
                        <div class="form-group">
                            <label for="dataPackage"><i class="fa-solid fa-database"></i> Data Package</label>
                            <select id="dataPackage" name="dataPackage" required>
                                <option value="">-- Choose Submitted Package --</option>
                                <option value="ID-2025-11-20" data-status="Ready">November 2025 (Woreda Summary) - Ready</option>
                                <option value="ID-2025-10-15" data-status="Processed" disabled>October 2025 (Processed)</option>
                            </select>
                        </div>

                        <div class="report-format-group">
                            <h3>2. Final Template</h3>
                            <div class="form-group">
                                <label for="reportFormat"><i class="fa-solid fa-file-lines"></i> Output Format</label>
                                <select id="reportFormat" name="reportFormat" required>
                                    <option value="">-- Select Report Format --</option>
                                    <option value="monthly-summary">Monthly Health Summary (Standard)</option>
                                    <option value="kpi-report">KPI Performance Report</option>
                                    <option value="annual-review">Annual Snapshots</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button name="GenerateHMISReport" type="submit" class="btn-primary-generate">
                                <i class="fa-solid fa-cogs"></i> Generate Final HMIS Report
                            </button>
                        </div>
                    </form>
                </div>

                <div class="status-card log-display">
                    <h3>Submission Log</h3>
                    <div class="log-entry ready">
                        <span class="log-icon"><i class="fa-solid fa-check-circle"></i></span>
                        <div class="log-details" style="display: flex; flex-direction: column;">
                            <span class="log-text">Nov 2025: **Validated**</span>
                            <span class="log-date" style="font-size: 0.75rem; color: var(--text-light);">2025-12-15</span>
                        </div>
                    </div>
                    <div class="log-entry processed">
                        <span class="log-icon"><i class="fa-solid fa-file-alt"></i></span>
                        <div class="log-details" style="display: flex; flex-direction: column;">
                            <span class="log-text">Oct 2025: **Generated**</span>
                            <span class="log-date" style="font-size: 0.75rem; color: var(--text-light);">2025-11-01</span>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script src="../js/logout.js"></script>
</body>

</html>
