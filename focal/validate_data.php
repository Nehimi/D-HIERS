<?php
session_start();
include "../dataBaseConnection.php";

// Handle Validation Action
if (isset($_POST['validate_id'])) {
    $reportId = mysqli_real_escape_string($dataBaseConnection, $_POST['validate_id']);
    $updateQuery = mysqli_query($dataBaseConnection, "UPDATE health_data SET status='Validated' WHERE id='$reportId'");
    if ($updateQuery) {
        $successMsg = "Report #$reportId Validated successfully.";
    }
}

// Fetch Reports needing validation (Pending or Submitted by Coordinator)
$pendingQuery = mysqli_query($dataBaseConnection, "SELECT * FROM health_data WHERE status='Pending' OR status IS NULL ORDER BY id DESC");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validate Collected Data | D-HEIRS</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="focal_dashboard.css" />
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
            <a href="validate_data.php" class="nav-item active">
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
                <input type="text" placeholder="Search by report ID, kebele, or patient...">
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
                    <h1>Data Validation (UC-14)</h1>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">Review and validate community health data forwarded from HEW Coordinators.</p>
                </div>
                <div class="header-btns">
                    <button class="smart-back-btn" onclick="window.history.back()">
                        <span class="icon"><i class="fa-solid fa-arrow-left"></i></span>
                        Back
                    </button>
                </div>
            </div>

            <?php if (isset($successMsg)): ?>
                <div class="alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <?php echo $successMsg; ?>
                </div>
            <?php endif; ?>

            <div class="card-panel table-section">
                <div class="panel-header">
                    <h2>Pending Validations</h2>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Report ID</th>
                                <th>Kebele</th>
                                <th>Indicator/Service</th>
                                <th>Value</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($pendingQuery && mysqli_num_rows($pendingQuery) > 0) {
                                while ($row = mysqli_fetch_assoc($pendingQuery)) {
                                    echo "<tr>";
                                    echo "<td class='primary-cell'><strong>#" . $row['id'] . "</strong></td>";
                                    echo "<td>" . htmlspecialchars($row['kebele'] ?? 'N/A') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['service_type'] ?? 'Health Entry') . "</td>";
                                    echo "<td><strong>" . htmlspecialchars($row['count'] ?? '1') . "</strong></td>";
                                    $status = $row['status'] ?? 'Pending';
                                    $statusClass = strtolower(str_replace(' ', '-', $status));
                                    echo "<td><span class='status-tag $statusClass'>" . $status . "</span></td>";
                                    echo "<td>
                                            <div style='display: flex; gap: 8px;'>
                                                <form method='POST' style='display:inline;'>
                                                    <input type='hidden' name='validate_id' value='".$row['id']."'>
                                                    <button type='submit' class='btn-validate'>
                                                        <i class='fa-solid fa-check'></i> Validate
                                                    </button>
                                                </form>
                                                <button class='btn-return'>
                                                    <i class='fa-solid fa-rotate-left'></i> Return
                                                </button>
                                            </div>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' style='padding: 3rem; text-align: center; color: var(--text-muted);'>No pending data for validation.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="../js/logout.js"></script>
</body>

</html>
