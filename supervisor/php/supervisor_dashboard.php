<?php
session_start();
include "../../dataBaseConnection.php";

// Fetch Oversight Stats
$totalKebeles = 3; // Lich-Amba, Arada, Lereba
$systemUptime = "99.9%";

// 1. Total Active HEWs
$hewQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total FROM users WHERE role='hew' AND status='Active'");
$activeHEWs = ($hewQuery) ? mysqli_fetch_assoc($hewQuery)['total'] : 0;

// 2. Pending Data flows (Total entries across system that aren't yet at DHIS2)
$pendingFlowsQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total FROM health_data WHERE status != 'Submitted to DHIS2'");
$pendingFlows = ($pendingFlowsQuery) ? mysqli_fetch_assoc($pendingFlowsQuery)['total'] : 0;

// 3. Reports ready for HMIS (processed by Linkage Focal)
$readyHMISQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total FROM health_data WHERE status = 'Summarized'");
$readyHMIS = ($readyHMISQuery) ? mysqli_fetch_assoc($readyHMISQuery)['total'] : 0;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Dashboard | D-HEIRS</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="../../focal/focal_dashboard.css" />
    <link rel="stylesheet" href="../../css/logout.css">
    
    <style>
        :root {
            --supervisor-primary: #0f766e; /* Teal Pro */
            --supervisor-gradient: linear-gradient(180deg, #0d9488 0%, #0f766e 40%, #115e59 70%, #134e4a 100%);
        }
        .sidebar { background: var(--supervisor-gradient) !important; }
        .stat-card:hover { border-color: var(--supervisor-primary); }
        .num { color: var(--supervisor-primary) !important; }
        .nav-item.active { background: rgba(255,255,255,0.2); }
    </style>
</head>

<body class="dashboard-body">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="brand-icon">
                <img src="../../focal/image.jpg" alt="Logo">
            </div>
            <div class="brand-text">
                D-HEIRS
                <span>Supervisor Portal</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="supervisor_dashboard.php" class="nav-item active">
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

    <!-- Main Content -->
    <main class="main-content">
        <header class="dashboard-header">
            <div class="header-search">
                <i class="fa-solid fa-search"></i>
                <input type="text" placeholder="Search system logs, staff, or flows...">
            </div>
            <div class="header-actions">
                <button class="icon-btn">
                    <i class="fa-solid fa-bell"></i>
                    <span class="badge-dot"></span>
                </button>
                <div class="user-profile">
                    <img src="../../images/avatar.png" alt="Profile" class="avatar-sm" onerror="this.src='https://ui-avatars.com/api/?name=Supervisor&background=6366f1&color=fff'">
                    <div class="user-info">
                        <span class="name">System Supervisor</span>
                        <span class="role">Oversight Officer</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-wrapper">
            <div class="page-intro">
                <h1>Oversight Overview</h1>
                <p>Monitoring the integrity and timeliness of the LICH-AMBA health data flow.</p>
            </div>

            <!-- Stats Grid -->
            <section class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 16px; display: flex; align-items: center; gap: 1rem; box-shadow: var(--shadow-sm);">
                    <div class="stat-icon" style="background: rgba(15, 118, 110, 0.1); color: #0f766e; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 1.5rem;"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-info">
                        <span class="num" style="display: block; font-size: 1.8rem; font-weight: 800; color: #0f766e;"><?php echo $activeHEWs; ?></span>
                        <span class="label" style="font-size: 0.85rem; color: var(--text-muted);">Active HEWs</span>
                    </div>
                </div>
                <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 16px; display: flex; align-items: center; gap: 1rem; box-shadow: var(--shadow-sm);">
                    <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 1.5rem;"><i class="fa-solid fa-spinner"></i></div>
                    <div class="stat-info">
                        <span class="num" style="display: block; font-size: 1.8rem; font-weight: 800; color: #f59e0b;"><?php echo $pendingFlows; ?></span>
                        <span class="label" style="font-size: 0.85rem; color: var(--text-muted);">In-Process Records</span>
                    </div>
                </div>
                <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 16px; display: flex; align-items: center; gap: 1rem; box-shadow: var(--shadow-sm);">
                    <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 1.5rem;"><i class="fa-solid fa-check-double"></i></div>
                    <div class="stat-info">
                        <span class="num" style="display: block; font-size: 1.8rem; font-weight: 800; color: #10b981;"><?php echo $readyHMIS; ?></span>
                        <span class="label" style="font-size: 0.85rem; color: var(--text-muted);">Ready for DHIS2</span>
                    </div>
                </div>
                <div class="stat-card" style="background: white; padding: 1.5rem; border-radius: 16px; display: flex; align-items: center; gap: 1rem; box-shadow: var(--shadow-sm);">
                    <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 1.5rem;"><i class="fa-solid fa-bolt"></i></div>
                    <div class="stat-info">
                        <span class="num" style="display: block; font-size: 1.8rem; font-weight: 800; color: #ef4444;"><?php echo $systemUptime; ?></span>
                        <span class="label" style="font-size: 0.85rem; color: var(--text-muted);">System Health</span>
                    </div>
                </div>
            </section>

            <div class="dashboard-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                <div class="content-card" style="background: white; border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow-md);">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h2>System Data Flow Status</h2>
                        <a href="data_flow_monitor.php" style="color: #0f766e; font-weight: 600;">View Full Map</a>
                    </div>
                    <!-- Data Flow Visualization Placeholder -->
                    <div class="flow-chart" style="padding: 2rem; background: #f8fafc; border-radius: 15px; border: 2px dashed #e2e8f0; text-align: center;">
                         <i class="fa-solid fa-diagram-next" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 1rem; display: block;"></i>
                         <p style="color: var(--text-muted);">Dynamic Data Flow Visualization (HEW → Coordinator → Focal → HMIS)</p>
                    </div>
                </div>

                <div class="content-card" style="background: white; border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow-md);">
                    <h3>Workflow Health</h3>
                    <div class="health-metrics" style="margin-top: 1.5rem; display: grid; gap: 1rem;">
                        <div class="metric">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <span style="font-size: 0.9rem; font-weight: 600;">Data Validation Speed</span>
                                <span style="font-size: 0.9rem; color: #10b981;">Fast</span>
                            </div>
                            <div style="height: 8px; background: #f1f5f9; border-radius: 5px;"><div style="width: 85%; height: 100%; background: #10b981; border-radius: 5px;"></div></div>
                        </div>
                        <div class="metric">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <span style="font-size: 0.9rem; font-weight: 600;">Reporting Compliance</span>
                                <span style="font-size: 0.9rem; color: #f59e0b;">92%</span>
                            </div>
                            <div style="height: 8px; background: #f1f5f9; border-radius: 5px;"><div style="width: 92%; height: 100%; background: #f59e0b; border-radius: 5px;"></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../../js/logout.js"></script>
</body>

</html>
