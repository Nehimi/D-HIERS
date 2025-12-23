<?php
session_start();
include "../dataBaseConnection.php";

// Fetch generated reports (placeholder logic for list display)
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export HMIS Reports | D-HEIRS</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="focal_dashboard.css" />
    <link rel="stylesheet" href="export_hmis_report.css">
    <link rel="stylesheet" href="../css/logout.css">

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
            position: relative;
        }
    </style>
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
            <a href="export_hmis_report.php" class="nav-item active">
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
        <header class="dashboard-header">
            <div class="header-search">
                <i class="fa-solid fa-search"></i>
                <input type="text" placeholder="Search generated files...">
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
                    <h1>Export Reports</h1>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">Download generated HMIS reports in multiple formats.</p>
                </div>
                <div class="header-btns">
                    <button class="smart-back-btn" onclick="window.history.back()">
                        <span class="icon"><i class="fa-solid fa-arrow-left"></i></span>
                        Back
                    </button>
                </div>
            </div>

            <section class="hmis-export-section">
                <div class="reports-list-card selection-card" style="width: 100%;">
                    <h3>Generated Templates</h3>
                    <ul class="report-list" style="list-style: none; padding: 0;">
                        <li style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid #f1f5f9; background: #f8fafc; border-radius: 12px; margin-bottom: 1rem;">
                            <div class="report-details">
                                <span class="report-title" style="font-weight: 600; display: block;">Monthly Health Summary (Nov 2025)</span>
                                <span class="report-meta" style="font-size: 0.8rem; color: var(--text-muted);">Format: Standard HMIS Template</span>
                            </div>
                            <div class="report-actions" style="display: flex; align-items: center; gap: 1rem;">
                                <span class="status-badge" style="background: rgba(40, 167, 69, 0.1); color: #28a745; padding: 4px 12px; border-radius: 50px; font-size: 0.8rem;">Ready</span>
                                <button class="btn-export" onclick="openExportModal('Monthly Health Summary (Nov 2025)')" style="background: var(--primary); color: white; border: none; padding: 8px 20px; border-radius: 8px; cursor: pointer;">
                                    <i class="fa-solid fa-download"></i> Export
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </main>

    <!-- Modal -->
    <div id="exportModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeExportModal()" style="position: absolute; right: 1.5rem; top: 1rem; font-size: 1.5rem; cursor: pointer;">&times;</span>
            <h3 style="margin-bottom: 0.5rem;"><i class="fa-solid fa-file-export" style="color: var(--primary);"></i> Choose Format</h3>
            <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1.5rem;">Report: <strong id="reportName" style="color: var(--text-main);"></strong></p>

            <div class="format-selection" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <label style="cursor: pointer;">
                    <input type="radio" name="exportFormat" value="pdf" checked style="display: none;">
                    <div class="format-option" style="border: 2px solid #f1f5f9; padding: 1rem; border-radius: 12px; text-align: center;">
                        <i class="fa-solid fa-file-pdf" style="font-size: 2rem; color: #ef4444; margin-bottom: 0.5rem;"></i>
                        <span style="display: block; font-weight: 600;">PDF</span>
                    </div>
                </label>
                <label style="cursor: pointer;">
                    <input type="radio" name="exportFormat" value="excel" style="display: none;">
                    <div class="format-option" style="border: 2px solid #f1f5f9; padding: 1rem; border-radius: 12px; text-align: center;">
                        <i class="fa-solid fa-file-excel" style="font-size: 2rem; color: #10b981; margin-bottom: 0.5rem;"></i>
                        <span style="display: block; font-weight: 600;">Excel</span>
                    </div>
                </label>
            </div>

            <button class="btn-primary-generate" onclick="initiateDownload()" style="width: 100%; border-radius: 12px; background: var(--primary);">
                Export & Download
            </button>

            <div id="downloadStatus" style="display: none; margin-top: 1rem; text-align: center; font-size: 0.9rem; color: var(--primary);">
                <i class="fa-solid fa-spinner fa-spin"></i> Processing...
            </div>
        </div>
    </div>

    <script src="../js/logout.js"></script>
    <script>
        function openExportModal(reportTitle) {
            document.getElementById('reportName').textContent = reportTitle;
            document.getElementById('exportModal').style.display = 'flex';
        }
        function closeExportModal() {
            document.getElementById('exportModal').style.display = 'none';
        }
        function initiateDownload() {
            const status = document.getElementById('downloadStatus');
            status.style.display = 'block';
            setTimeout(() => {
                status.innerHTML = '<i class="fa-solid fa-check-circle"></i> Download Started!';
                setTimeout(closeExportModal, 2000);
            }, 1500);
        }
    </script>
</body>

</html>
