<?php
session_start();
include("../../dataBaseConnection.php");

// Check if user is logged in and has HMIS role
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'hmis') {
    header("Location: ../../index.html");
    exit();
}

$fullName = $_SESSION['full_name'] ?? 'HMIS Officer';
$userId = $_SESSION['user_db_id'] ?? 0;

// Fetch Summary Stats
$stats = [
    'received' => 0,
    'generated' => 0,
    'submitted' => 0
];

// Count Reports Received (Statistical Packages)
$res = $dataBaseConnection->query("SELECT COUNT(*) as cnt FROM statistical_packages");
if ($res) {
    $row = $res->fetch_assoc();
    $stats['received'] = $row['cnt'];
}

// Count Reports Generated
$res = $dataBaseConnection->query("SELECT COUNT(*) as cnt FROM hmis_reports");
if ($res) {
    $row = $res->fetch_assoc();
    $stats['generated'] = $row['cnt'];
}

// Count DHIS2 Submissions
$res = $dataBaseConnection->query("SELECT COUNT(*) as cnt FROM dhis2_submissions WHERE status = 'Success'");
if ($res) {
    $row = $res->fetch_assoc();
    $stats['submitted'] = $row['cnt'];
}

// Fetch Recent Statistical Submissions
$recentSubmissions = [];
$res = $dataBaseConnection->query("SELECT * FROM statistical_packages ORDER BY received_at DESC LIMIT 5");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $recentSubmissions[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMIS Dashboard | D-HEIRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../../css/logout.css">
    <link rel="stylesheet" href="../../css/table-responsive.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="brand-icon">
                <img src="../../images/logo.png" alt="D-HEIRS" onerror="this.src='../../focal/image.jpg'">
            </div>
            <div class="brand-text">
                D-HEIRS
                <span>HMIS Portal</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="hmis_dashboard.php" class="nav-item active">
                <i class="fa-solid fa-house"></i>
                <span>Dashboard</span>
            </a>
            <a href="generate_reports.php" class="nav-item">
                <i class="fa-solid fa-file-medical"></i>
                <span>Generate Reports</span>
            </a>
            <a href="export_reports.php" class="nav-item">
                <i class="fa-solid fa-download"></i>
                <span>Export Reports</span>
            </a>
            <a href="dhis2_submission.php" class="nav-item">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span>DHIS2 Submission</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="../../index.html" class="nav-item logout" id="logoutBtn">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="main-content">
        <!-- Premium Header -->
        <header class="dashboard-header">
            <div class="header-search">
                <i class="fa-solid fa-search"></i>
                <input type="text" placeholder="Search reports, packages, or logs...">
            </div>
            <div class="header-actions">
                <button class="icon-btn">
                    <i class="fa-solid fa-bell"></i>
                    <span class="badge-dot"></span>
                </button>
                <div class="user-profile">
                    <img src="../../images/avatar.png" alt="User" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($fullName); ?>&background=0f766e&color=fff'">
                    <div class="user-info">
                        <span class="name"><?php echo htmlspecialchars($fullName); ?></span>
                        <span class="role">HMIS Officer</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-wrapper">
            <div class="page-intro">
                <h1>HMIS Dashboard Overview</h1>
                <p>Welcome back! Here is a summary of the health data reporting status.</p>
            </div>

            <!-- Stats Grid -->
            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fa-solid fa-file-import"></i>
                    </div>
                    <div class="stat-info">
                        <span class="num"><?php echo $stats['received']; ?></span>
                        <span class="label">Received</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon teal">
                        <i class="fa-solid fa-file-circle-check"></i>
                    </div>
                    <div class="stat-info">
                        <span class="num"><?php echo $stats['generated']; ?></span>
                        <span class="label">Generated</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                    <div class="stat-info">
                        <span class="num"><?php echo $stats['submitted']; ?></span>
                        <span class="label">DHIS2 Ready</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div class="stat-info">
                        <span class="num">98%</span>
                        <span class="label">Timeliness</span>
                    </div>
                </div>
            </section>

            <div class="dashboard-grid">
                <!-- Main Activity Panel -->
                <div class="content-card">
                    <div class="card-header">
                        <h2>Recent Statistical Submissions</h2>
                        <a href="generate_reports.php" style="color: var(--primary); font-weight: 600; font-size: 0.9rem;">View All</a>
                    </div>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Package ID</th>
                                    <th>Period</th>
                                    <th>Source</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentSubmissions)): ?>
                                    <tr><td colspan="5" style="text-align:center; padding: 3rem;">No recent submissions found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($recentSubmissions as $sub): ?>
                                    <tr>
                                        <td data-label="Package ID"><strong>#<?php echo htmlspecialchars($sub['package_id']); ?></strong></td>
                                        <td data-label="Period"><?php echo htmlspecialchars($sub['period']); ?></td>
                                        <td data-label="Source"><?php echo htmlspecialchars($sub['focal_person_name']); ?></td>
                                        <td data-label="Status">
                                            <span class="status-badge <?php echo strtolower($sub['status']); ?>">
                                                <?php echo htmlspecialchars($sub['status']); ?>
                                            </span>
                                        </td>
                                        <td data-label="Action">
                                            <?php if ($sub['status'] === 'Pending'): ?>
                                                <a href="generate_reports.php?id=<?php echo $sub['package_id']; ?>" 
                                                   style="background: var(--primary); color: white; padding: 0.4rem 0.8rem; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600;">Process</a>
                                            <?php else: ?>
                                                <span style="color: var(--text-light); font-size: 0.85rem;">Completed</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Actions Panel -->
                <div class="content-card sidebar-panel" style="display: flex; flex-direction: column; gap: 1rem;">
                    <div class="card-header">
                        <h2>Quick Actions</h2>
                    </div>
                    <div style="display: grid; gap: 1rem;">
                        <a href="generate_reports.php" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9; text-decoration: none; color: inherit; transition: all 0.2s;">
                            <div style="width: 40px; height: 40px; background: rgba(15, 118, 110, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 1.2rem;"><i class="fa-solid fa-plus"></i></div>
                            <div style="flex: 1;">
                                <span style="font-weight: 700; display: block; font-size: 0.95rem;">Generate Report</span>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">Create new PDF/Excel</span>
                            </div>
                        </a>
                        <a href="dhis2_submission.php" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9; text-decoration: none; color: inherit; transition: all 0.2s;">
                            <div style="width: 40px; height: 40px; background: rgba(139, 92, 246, 0.1); color: #8b5cf6; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 1.2rem;"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                            <div style="flex: 1;">
                                <span style="font-weight: 700; display: block; font-size: 0.95rem;">DHIS2 Sync</span>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">External submission</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../js/script.js"></script>
    <script src="../../js/logout.js"></script>
</body>
</html>
