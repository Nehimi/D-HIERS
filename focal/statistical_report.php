<?php
session_start();
include "../dataBaseConnection.php";

// Fetch Kebele list for filter
$kebeleQuery = mysqli_query($dataBaseConnection, "SELECT DISTINCT kebeleName FROM kebele");
$kebeles = [];
if ($kebeleQuery) {
    while ($row = mysqli_fetch_assoc($kebeleQuery)) {
        $kebeles[] = $row['kebeleName'];
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistical Report | D-HEIRS</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="focal_dashboard.css" />
    <link rel="stylesheet" href="statistical_report.css">
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
            <a href="statistical_report.php" class="nav-item active">
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
                <input type="text" placeholder="Search report history...">
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
                    <h1>Statistical Reporting (UC-15)</h1>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">Convert validated community data into woreda-level statistical summaries.</p>
                </div>
                <div class="header-btns">
                    <button class="smart-back-btn" onclick="window.history.back()">
                        <span class="icon"><i class="fa-solid fa-arrow-left"></i></span>
                        Back
                    </button>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div style="background: rgba(40, 167, 69, 0.1); color: #28a745; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-check"></i>
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div style="background: rgba(220, 53, 69, 0.1); color: #dc3545; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <section class="report-selection-section">
                <div class="selection-card">
                    <h3>Generate Woreda Summary</h3>
                    <p class="description">Select the reporting period for which all Kebele data has been validated.</p>

                    <form method="POST" action="generate_report_processor.php" class="report-form">
                        <div class="form-group">
                            <label for="reportMonth"><i class="fa-solid fa-calendar-alt"></i> Reporting Month</label>
                            <select id="reportMonth" name="reportMonth" required>
                                <option value="">-- Choose Month --</option>
                                <option value="2025-11">November 2025 (Ready)</option>
                                <option value="2025-12">December 2025 (Pending)</option>
                                <option value="2025-10" disabled>October 2025 (Finalized)</option>
                            </select>
                            <small class="help-text">Only periods with **Validated** detailed reports can be selected.</small>
                        </div>

                        <div class="form-group">
                            <label for="kebeleFilter"><i class="fa-solid fa-location-dot"></i> Kebele Filter</label>
                            <select id="kebeleFilter" name="kebeleFilter">
                                <option value="all">-- All Kebeles (Woreda Summary) --</option>
                                <?php foreach ($kebeles as $kebele): ?>
                                    <option value="<?php echo htmlspecialchars($kebele); ?>"><?php echo htmlspecialchars($kebele); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-actions">
                            <button name="GenerateReport" type="submit" class="btn-primary">
                                <i class="fa-solid fa-file-export"></i> Generate Statistical Report
                            </button>
                        </div>
                    </form>
                </div>

                <div class="status-summary-card">
                    <h3>Report Timeline</h3>
                    <ul class="status-list">
                        <li class="status-item ready">
                            <span class="period">November 2025</span>
                            <span class="badge ready-badge">Ready</span>
                        </li>
                        <li class="status-item pending">
                            <span class="period">December 2025</span>
                            <span class="badge pending-badge">Pending</span>
                        </li>
                        <li class="status-item processed">
                            <span class="period">October 2025</span>
                            <span class="badge processed-badge">Finalized</span>
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </main>

    <script src="../js/logout.js"></script>
</body>

</html>
