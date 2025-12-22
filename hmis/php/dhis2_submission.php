<?php
session_start();
include("../../dataBaseConnection.php");

if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'hmis') {
    header("Location: ../../index.html");
    exit();
}

$fullName = $_SESSION['full_name'] ?? 'HMIS Officer';

// Fetch Reports ready for DHIS2 (Generated but not yet submitted or failed)
$pendingSubmissions = [];
$res = $dataBaseConnection->query("SELECT * FROM hmis_reports WHERE status = 'Generated' ORDER BY generated_at DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $pendingSubmissions[] = $row;
    }
}

// Fetch Submission History
$history = [];
$res = $dataBaseConnection->query("
    SELECT s.*, r.report_name 
    FROM dhis2_submissions s 
    JOIN hmis_reports r ON s.report_id = r.report_id 
    ORDER BY s.submitted_at DESC LIMIT 5
");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $history[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DHIS2 Submission | HMIS Dashboard</title>
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
            <a href="generate_reports.php" class="nav-item">
                <i class="fa-solid fa-file-medical"></i>
                <span>Generate Reports</span>
            </a>
            <a href="export_reports.php" class="nav-item">
                <i class="fa-solid fa-download"></i>
                <span>Export Reports</span>
            </a>
            <a href="dhis2_submission.php" class="nav-item active">
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
                <input type="text" placeholder="Search submissions or status...">
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
                <h1>DHIS2 Connectivity</h1>
                <p>Transfer approved community health data to the national DHIS2 system.</p>
            </div>

            <section class="content-card">
                <div class="dhis2-status" style="display: flex; gap: 1rem; align-items: center; padding: 1.25rem; background: #f0f9ff; border: 1px solid #7dd3fc; border-radius: 12px; margin-bottom: 2rem;">
                    <div style="width: 12px; height: 12px; background: #22c55e; border-radius: 50%; box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);"></div>
                    <span style="font-weight: 700; color: #0369a1;">DHIS2 Integration Status: Online & Connected</span>
                </div>

                <div class="card-header">
                    <h2>Pending DHIS2 Entries</h2>
                    <span style="font-size: 0.85rem; color: var(--text-muted);"><?php echo count($pendingSubmissions); ?> Ready to Sync</span>
                </div>
                
                <div class="submission-list">
                    <?php if (empty($pendingSubmissions)): ?>
                        <p style="color: var(--text-muted); text-align: center; padding: 3rem; background: #f8fafc; border-radius: 12px; border: 1px dashed #e2e8f0;">No pending reports for DHIS2 submission.</p>
                    <?php else: ?>
                        <?php foreach ($pendingSubmissions as $report): ?>
                        <div class="submission-item" style="border: 1px solid #f1f5f9; padding: 1.5rem; border-radius: 12px; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; transition: all 0.2s;">
                            <div>
                                <h3 style="font-size: 1.1rem; margin-bottom: 0.25rem; color: var(--text-main);"><?php echo htmlspecialchars($report['report_name']); ?></h3>
                                <span style="font-size: 0.85rem; color: var(--text-muted);">Generated: <?php echo date('M j, Y', strtotime($report['generated_at'])); ?> | Report ID: #<?php echo htmlspecialchars($report['report_id']); ?></span>
                            </div>
                            <form action="process_dhis2.php" method="POST">
                                <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($report['report_id']); ?>">
                                <button type="submit" class="btn-action" style="background: var(--primary); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem;">
                                    <i class="fa-solid fa-cloud-arrow-up"></i> Sync to DHIS2
                                </button>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="card-header" style="margin-top: 3.5rem;">
                    <h2>Recent Submission History</h2>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Submission ID</th>
                                <th>Report Name</th>
                                <th>Date</th>
                                <th>DHIS2 Reference</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($history)): ?>
                                <tr><td colspan="5" style="text-align:center; padding: 2rem;">No submission history found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($history as $log): ?>
                                <tr>
                                    <td data-label="Submission ID"><strong>#<?php echo htmlspecialchars($log['submission_id']); ?></strong></td>
                                    <td data-label="Report Name"><?php echo htmlspecialchars($log['report_name']); ?></td>
                                    <td data-label="Date"><?php echo date('M j, Y', strtotime($log['submitted_at'])); ?></td>
                                    <td data-label="DHIS2 Reference"><code style="background: #f1f5f9; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.85rem;"><?php echo htmlspecialchars($log['dhis2_reference']); ?></code></td>
                                    <td data-label="Status"><span class="status-badge completed"><?php echo htmlspecialchars($log['status']); ?></span></td>
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
