<?php
session_start();
include("../../dataBaseConnection.php");

if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'hmis') {
    header("Location: ../../index.html");
    exit();
}

$fullName = $_SESSION['full_name'] ?? 'HMIS Officer';

// Fetch Pending Packages
$pendingPackages = [];
$res = $dataBaseConnection->query("SELECT * FROM statistical_packages WHERE status = 'Pending' ORDER BY received_at DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $pendingPackages[] = $row;
    }
}

// Fetch Unread Notifications Count
$notifCount = 0;
$notifRes = $dataBaseConnection->query("SELECT COUNT(*) as cnt FROM activity_notifications WHERE role='hmis' AND is_read=0");
if ($notifRes) {
    $notifCount = $notifRes->fetch_assoc()['cnt'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Reports | HMIS Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../../css/logout.css">
    <link rel="stylesheet" href="../../css/table-responsive.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            <a href="hmis_dashboard.php" class="nav-item">
                <i class="fa-solid fa-house"></i>
                <span>Dashboard</span>
            </a>
            <a href="generate_reports.php" class="nav-item active">
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
            <a href="hmis_notifications.php" class="nav-item">
                <i class="fa-solid fa-bell"></i>
                <span>System Notifications</span>
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
                <button class="icon-btn" onclick="location.href='hmis_notifications.php'">
                    <i class="fa-solid fa-bell"></i>
                    <?php if ($notifCount > 0): ?>
                        <span class="badge-count"><?php echo $notifCount; ?></span>
                    <?php else: ?>
                        <span class="badge-dot"></span>
                    <?php endif; ?>
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
                <h1>Generate Official Reports</h1>
                <p>Select a validated statistical package to generate a professional health report.</p>
            </div>

            <section class="content-card">
                <div class="card-header">
                    <h2>Pending Statistical Packages</h2>
                    <span style="font-size: 0.85rem; color: var(--text-muted);"><?php echo count($pendingPackages); ?> Packages Ready</span>
                </div>
                
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Package ID</th>
                                <th>Period</th>
                                <th>Received From</th>
                                <th>Status</th>
                                <th>Select Format</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pendingPackages)): ?>
                                <tr><td colspan="6" style="text-align:center; padding: 3rem;">No pending statistical packages found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($pendingPackages as $pkg): ?>
                                <tr>
                                    <td data-label="Package ID"><strong>#<?php echo htmlspecialchars($pkg['package_id']); ?></strong></td>
                                    <td data-label="Period"><?php echo htmlspecialchars($pkg['period']); ?></td>
                                    <td data-label="Received From"><?php echo htmlspecialchars($pkg['focal_person_name']); ?></td>
                                    <td data-label="Status"><span class="status-badge pending"><?php echo htmlspecialchars($pkg['status']); ?></span></td>
                                    <form action="process_generation.php" method="POST">
                                        <td data-label="Select Format">
                                            <select name="reportFormat" style="padding: 0.4rem; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 0.85rem;">
                                                <option value="PDF">Standard PDF</option>
                                                <option value="Excel">Data Excel</option>
                                            </select>
                                        </td>
                                        <td data-label="Action">
                                            <input type="hidden" name="package_id" value="<?php echo htmlspecialchars($pkg['package_id']); ?>">
                                            <button type="submit" class="btn-action" style="background: var(--primary); color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.85rem;">
                                                Generate
                                            </button>
                                        </td>
                                    </form>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <script src="../js/script.js"></script>
    <script src="../../js/logout.js"></script>
</body>
</html>
