<?php
session_start();
include("../../dataBaseConnection.php");

if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'hmis') {
    header("Location: ../../index.html");
    exit();
}

$fullName = $_SESSION['full_name'] ?? 'HMIS Officer';

// Fetch Generated Reports
$reports = [];
$res = $dataBaseConnection->query("SELECT * FROM hmis_reports ORDER BY generated_at DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $reports[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Reports | HMIS Dashboard</title>
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
            <a href="export_reports.php" class="nav-item active">
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
                <input type="text" placeholder="Search report library...">
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
                <h1>Report Export Gallery</h1>
                <p>Browse and download generated health reports in PDF or Excel formats.</p>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div style="background: #f0fdf4; border: 1px solid #15803d; color: #15803d; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fa-solid fa-circle-check"></i>
                    <span><strong>Success!</strong> Report generated successfully. You can download it below.</span>
                </div>
            <?php endif; ?>

            <section class="content-card">
                <div class="card-header">
                    <h2>Generated Health Reports</h2>
                    <div class="gallery-filters" style="display: flex; gap: 0.5rem;">
                        <span style="font-size: 0.85rem; color: var(--text-muted);"><?php echo count($reports); ?> Reports Total</span>
                    </div>
                </div>
                
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Report Name</th>
                                <th>Format</th>
                                <th>Date Generated</th>
                                <th>File Size</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reports)): ?>
                                <tr><td colspan="5" style="text-align:center; padding: 3rem;">No generated reports found. Try generating one first.</td></tr>
                            <?php else: ?>
                                <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td data-label="Report Name">
                                        <div style="display:flex; align-items:center; gap:0.75rem;">
                                            <div style="width:36px; height:36px; background:#f1f5f9; border-radius:8px; display:flex; align-items:center; justify-content:center; color:var(--primary);">
                                                <i class="fa-solid <?php echo ($report['format'] == 'PDF' ? 'fa-file-pdf' : 'fa-file-excel'); ?>"></i>
                                            </div>
                                            <div>
                                                <span style="display:block; font-weight:700;"><?php echo htmlspecialchars($report['report_name']); ?></span>
                                                <span style="display:block; font-size:0.75rem; color:var(--text-muted);">ID: #<?php echo htmlspecialchars($report['report_id']); ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Format">
                                        <span class="status-badge generated"><?php echo htmlspecialchars($report['format']); ?></span>
                                    </td>
                                    <td data-label="Date Generated"><?php echo date('M j, Y H:i', strtotime($report['generated_at'])); ?></td>
                                    <td data-label="File Size">
                                        <span style="color:var(--text-muted);">
                                            <?php 
                                            $rawSize = $report['file_size'] ?? 0;
                                            if (is_numeric($rawSize)) {
                                                echo number_format((float)$rawSize / 1024, 1) . ' KB';
                                            } else {
                                                echo htmlspecialchars((string)$rawSize);
                                            }
                                            ?>
                                        </span>
                                    </td>
                                    <td data-label="Actions">
                                        <a href="<?php echo htmlspecialchars((string)($report['file_path'] ?? '#')); ?>" class="btn-action" target="_blank"
                                           style="background: var(--bg-body); color: var(--primary); border: 1px solid #e2e8f0; padding: 0.4rem 0.8rem; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.4rem;">
                                            <i class="fa-solid fa-download"></i> Download
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
        </section>
    </main>

    <script src="../js/script.js"></script>
    <script src="../../js/logout.js"></script>
</body>
</html>
