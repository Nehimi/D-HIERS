<?php
session_start();
include "../../dataBaseConnection.php";

// Fetch Performance Stats per Role
$rolePerformance = [
    'HEW' => ['label' => 'Record Entry', 'percent' => 95, 'color' => '#0f766e'],
    'Coordinator' => ['label' => 'Validation Speed', 'percent' => 88, 'color' => '#10b981'],
    'Focal' => ['label' => 'Summarization', 'percent' => 75, 'color' => '#f59e0b'],
    'HMIS' => ['label' => 'DHIS2 Sync', 'percent' => 100, 'color' => '#ef4444']
];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Reports | D-HEIRS</title>
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
        .perf-card { background: white; padding: 2rem; border-radius: 20px; box-shadow: var(--shadow-md); text-align: center; }
        .circle-chart { width: 100px; height: 100px; margin: 0 auto 1rem; }
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
            <a href="performance_reports.php" class="nav-item active">
                <i class="fa-solid fa-chart-simple"></i>
                <span>Performance Stats</span>
            </a>
            <a href="oversight_reports.php" class="nav-item">
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
            <h1>Performance Reports (UC-21)</h1>
        </header>

        <div class="content-wrapper">
            <div class="page-intro">
                <p style="color: var(--text-muted);">Evaluating staff efficiency and data handling performance across all kebeles.</p>
            </div>

            <div class="performance-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                <?php foreach($rolePerformance as $role => $data): ?>
                <div class="perf-card">
                    <h3 style="margin-bottom: 0.5rem;"><?php echo $role; ?></h3>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;"><?php echo $data['label']; ?></p>
                    <div style="font-size: 2rem; font-weight: 800; color: <?php echo $data['color']; ?>; margin-bottom: 0.5rem;"><?php echo $data['percent']; ?>%</div>
                    <div style="height: 6px; background: #f1f5f9; border-radius: 5px; overflow: hidden;">
                        <div style="width: <?php echo $data['percent']; ?>%; height: 100%; background: <?php echo $data['color']; ?>;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="card-panel" style="margin-top: 2rem; background: white; padding: 2rem; border-radius: 20px; box-shadow: var(--shadow-md);">
                <h3>Submission Timeliness by Kebele</h3>
                <!-- Placeholder for Chart.js -->
                <div style="height: 250px; background: #f8fafc; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--text-muted); border: 2px dashed #ddd;">
                    Kebele Compliance Analytics Chart
                </div>
            </div>
        </div>
    </main>

    <script src="../../js/logout.js"></script>
</body>

</html>
