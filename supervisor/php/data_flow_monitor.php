<?php
session_start();
include "../../dataBaseConnection.php";

// Fetch flow data with exception handling for "The Magic" columns
try {
    $query = mysqli_query($dataBaseConnection, "SELECT id, kebele, service_type, status, created_at FROM health_data ORDER BY created_at DESC LIMIT 20");
} catch (mysqli_sql_exception $e) {
    if (strpos($e->getMessage(), 'created_at') !== false) {
        $errorMsg = "Database alignment required: 'created_at' column is missing.";
        // Fallback query for temporary visibility if the user hasn't run the SQL patch yet
        $query = mysqli_query($dataBaseConnection, "SELECT id, kebele, service_type, status, id as created_at FROM health_data LIMIT 20");
    } else {
        die("Fatal Database Error: " . $e->getMessage());
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Flow Monitor | D-HEIRS</title>
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
        .status-pill { padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .status-pending { background: #fff8e1; color: #f59e0b; }
        .status-validated { background: #e0f2fe; color: #0284c7; }
        .status-summarized { background: #e0f2f1; color: #00897b; }
        .status-submitted { background: #f0fdf4; color: #16a34a; }
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
            <a href="data_flow_monitor.php" class="nav-item active">
                <i class="fa-solid fa-diagram-project"></i>
                <span>Data Flow Monitor</span>
            </a>
            <a href="performance_reports.php" class="nav-item">
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
            <h1>Data Flow Monitor (UC-20)</h1>
            <div class="user-profile">
                <span class="role">Supervisor</span>
            </div>
        </header>

        <div class="content-wrapper">
            <div class="page-intro">
                <p style="color: var(--text-muted);">Tracking real-time status transitions of health records from field entry to national submission.</p>
            </div>

            <?php if (isset($errorMsg)): ?>
                <div style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; border: 1px solid rgba(239, 68, 68, 0.2);">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div>
                        <strong>Action Required:</strong> <?php echo $errorMsg; ?>
                        <br><small>Please run the updated `MASTER_DATABASE_ALIGNMENT.sql` to fix this.</small>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card-panel" style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: var(--shadow-md);">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #f1f5f9; text-align: left;">
                            <th style="padding: 1rem;">Source (Kebele)</th>
                            <th style="padding: 1rem;">Indicator</th>
                            <th style="padding: 1rem;">Status Timeline</th>
                            <th style="padding: 1rem;">Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($query)): ?>
                        <tr style="border-bottom: 1px solid #f8fafc;">
                            <td style="padding: 1rem;"><strong><?php echo htmlspecialchars($row['kebele'] ?? 'Unknown Kebele'); ?></strong></td>
                            <td style="padding: 1rem;"><?php echo htmlspecialchars($row['service_type'] ?? 'Health Data'); ?></td>
                            <td style="padding: 1rem;">
                                <?php 
                                    $status = $row['status'] ?? 'Pending';
                                    $class = 'status-pending';
                                    if($status == 'Validated') $class = 'status-validated';
                                    if($status == 'Summarized') $class = 'status-summarized';
                                    if($status == 'Submitted to DHIS2') $class = 'status-submitted';
                                ?>
                                <span class="status-pill <?php echo $class; ?>"><?php echo $status; ?></span>
                            </td>
                            <td style="padding: 1rem; color: var(--text-muted); font-size: 0.85rem;"><?php echo $row['created_at']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="../../js/logout.js"></script>
</body>

</html>
