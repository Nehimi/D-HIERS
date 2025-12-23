<?php
session_start();
include "../../dataBaseConnection.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oversight Reports | D-HEIRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../focal/focal_dashboard.css" />
    <link rel="stylesheet" href="../../css/logout.css">
    <style>
        :root {
            --supervisor-primary: #0f766e;
            --supervisor-gradient: linear-gradient(180deg, #0d9488 0%, #0f766e 40%, #115e59 70%, #134e4a 100%);
        }
        .sidebar { background: var(--supervisor-gradient) !important; }
        .nav-item.active { background: rgba(255,255,255,0.2); }
    </style>
</head>

<body class="dashboard-body">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="brand-item" style="display: flex; align-items: center; gap: 10px; color: white;">
                <img src="../../focal/image.jpg" alt="Logo" style="width: 40px; border-radius: 8px;">
                <div style="font-weight: 800; font-family: 'Outfit';">D-HEIRS</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="supervisor_dashboard.php" class="nav-item">
                <i class="fa-solid fa-gauge-high"></i>
                <span>Oversight Dashboard</span>
            </a>
            <a href="data_flow_monitor.php" class="nav-item">
                <i class="fa-solid fa-diagram-project"></i>
                <span>Data Flow Monitor</span>
            </a>
            <a href="performance_reports.php" class="nav-item">
                <i class="fa-solid fa-chart-simple"></i>
                <span>Performance Stats</span>
            </a>
            <a href="oversight_reports.php" class="nav-item active">
                <i class="fa-solid fa-file-shield"></i>
                <span>Oversight Reports</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="../../index.html" class="nav-item logout">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="main-content">
        <header class="dashboard-header">
            <h1>Oversight Reports (UC-22)</h1>
        </header>

        <div class="content-wrapper">
            <div class="page-intro">
                <p style="color: var(--text-muted);">Generate formal oversight summaries for system review and institutional accountability.</p>
            </div>

            <div class="selection-card" style="background: white; padding: 2rem; border-radius: 20px; box-shadow: var(--shadow-md); max-width: 600px; margin: 0 auto;">
                <h3>Generate Oversight Review</h3>
                <form action="#" method="POST" style="margin-top: 1.5rem; display: grid; gap: 1.2rem;">
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Audit Period</label>
                        <input type="month" name="audit_period" required style="width: 100%; border: 2px solid #f1f5f9; border-radius: 10px; padding: 12px;">
                    </div>
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Oversight Focus</label>
                        <select name="focus" style="width: 100%; border: 2px solid #f1f5f9; border-radius: 10px; padding: 12px;">
                            <option>Workflow Compliance</option>
                            <option>Data Integrity Audit</option>
                            <option>Staff Performance Review</option>
                        </select>
                    </div>
                    <button type="button" onclick="alert('Oversight report generation initiated.')" style="background: var(--supervisor-primary); color: white; border: none; padding: 15px; border-radius: 12px; font-weight: 700; cursor: pointer;">
                        <i class="fa-solid fa-file-export" style="margin-right: 10px;"></i>
                        Generate & Download PDF
                    </button>
                </form>
            </div>
            
            <div style="margin-top: 3rem; text-align: center;">
                 <h4 style="color: var(--text-muted);">Archive of Previous Oversight Reports</h4>
                 <div style="margin-top: 1rem; color: var(--text-light); font-style: italic;">No historical oversight reports found.</div>
            </div>
        </div>
    </main>

    <script src="../../js/logout.js"></script>
</body>

</html>
