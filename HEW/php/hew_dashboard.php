<?php
session_start();
include "../../dataBaseConnection.php";

// Fetch Stats
// 1. Total Households
$totalHouseholdQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total FROM household");
$totalHouseholds = ($totalHouseholdQuery) ? mysqli_fetch_assoc($totalHouseholdQuery)['total'] : 0;

// 2. Total Health Data (Dynamic Fetch)
$healthDataQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total FROM health_data");
$totalHealthData = ($healthDataQuery) ? mysqli_fetch_assoc($healthDataQuery)['total'] : 0;

// 3. Recent Registrations
// Use ID for sorting as a fallback since created_at might be missing in some DB versions
$recentQuery = mysqli_query($dataBaseConnection, "SELECT * FROM household ORDER BY id DESC LIMIT 5");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HEW Dashboard | D-HEIRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../admin/css/admin.css">
    <link rel="stylesheet" href="../css/hew.css">
    <link rel="stylesheet" href="../css/hew_style.css">
</head>

<body class="dashboard-body">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="brand-icon">
                <img src="../images/logo.png" alt="">
            </div>
            <div class="brand-text">
                D-HEIRS
                <span>HEW Portal</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="hew_dashboard.php" class="nav-item active">
                <i class="fa-solid fa-grid-2"></i>
                <span>Dashboard</span>
            </a>
            <a href="register_household.php" class="nav-item">
                <i class="fa-solid fa-users-gear"></i>
                <span>Register Household</span>
            </a>
            <a href="enter_health_data.php" class="nav-item">
                <i class="fa-solid fa-stethoscope"></i>
                <span>Enter Health Data</span>
            </a>
            <a href="edit_submitted_data.php" class="nav-item">
                <i class="fa-solid fa-file-pen"></i>
                <span>Edit Submitted Data</span>
            </a>
            <a href="submit_reports.php" class="nav-item">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Submit Reports</span>
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
        <!-- Header -->
        <header class="dashboard-header">
            <div class="header-search">
                <i class="fa-solid fa-search"></i>
                <input type="text" placeholder="Search households, reports...">
            </div>
            <div class="header-actions">
                <button class="icon-btn">
                    <i class="fa-solid fa-bell"></i>
                    <span class="badge-dot"></span>
                </button>
                <div class="user-profile">
                    <img src="../images/avatar.png" alt="HEW" class="avatar-sm">
                    <div class="user-info">
                        <span class="name">Semira Kedir</span>
                        <span class="role">Health Ext. Worker</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="content-wrapper">
            <div class="page-header">
                <h1>Overview</h1>
                <a href="register_household.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> New Household
                </a>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon color-1">
                        <i class="fa-solid fa-house-chimney-medical"></i>
                    </div>
                    <div class="stat-details">
                        <h3>Total Households</h3>
                        <p class="number"><?php echo $totalHouseholds; ?></p>
                        <span class="trend positive"><i class="fa-solid fa-arrow-up"></i> Registered</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon color-2">
                        <i class="fa-solid fa-notes-medical"></i>
                    </div>
                    <div class="stat-details">
                        <h3>Health Data</h3>
                        <p class="number"><?php echo $totalHealthData; ?></p>
                        <span class="trend neutral">Entries this month</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon color-3">
                        <i class="fa-solid fa-file-circle-check"></i>
                    </div>
                    <div class="stat-details">
                        <h3>Reports</h3>
                        <p class="number">12</p>
                        <span class="trend positive">All Submitted</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon color-4">
                        <i class="fa-solid fa-server"></i>
                    </div>
                    <div class="stat-details">
                        <h3>System Status</h3>
                        <p class="number">100%</p>
                        <span class="trend positive">Operational</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Quick Actions -->
            <div class="dashboard-grid">
                <!-- Recent Registrations Table -->
                <div class="card-panel table-section">
                    <div class="panel-header">
                        <h2>Recent Household Registrations</h2>
                        <a href="edit_submitted_data.php" class="view-all">View All</a>
                    </div>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Household ID</th>
                                    <th>Head of Family</th>
                                    <th>Kebele</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($recentQuery && mysqli_num_rows($recentQuery) > 0) {
                                    while ($row = mysqli_fetch_assoc($recentQuery)) {
                                        // Handle missing created_at elegantly
                                        $dateDisplay = isset($row['created_at']) ? date('M d, Y', strtotime($row['created_at'])) : "N/A";
                                        
                                        echo "<tr>";
                                        echo "<td class='primary-cell'><span class='user-name' style='color:var(--primary);'>" . htmlspecialchars($row['householdId']) . "</span></td>";
                                        echo "<td>" . htmlspecialchars($row['memberName']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['kebele']) . "</td>";
                                        echo "<td><span class='status-tag active'>Active</span></td>"; 
                                        echo "<td>" . $dateDisplay . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' style='padding: 2rem; text-align: center; color: #94a3b8;'>No households registered yet.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Actions Panel -->
                <div class="card-panel side-panel">
                    <div class="panel-header">
                        <h2>Quick Actions</h2>
                    </div>
                    <div class="action-list">
                        <a href="register_household.php" class="action-item" style="text-decoration:none;">
                            <div class="action-icon"><i class="fa-solid fa-user-plus"></i></div>
                            <div class="action-text">
                                <strong>Register Household</strong>
                                <span>Add new family details</span>
                            </div>
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                        <a href="enter_health_data.php" class="action-item" style="text-decoration:none;">
                            <div class="action-icon"><i class="fa-solid fa-stethoscope"></i></div>
                            <div class="action-text">
                                <strong>Enter Health Data</strong>
                                <span>Log patient visit info</span>
                            </div>
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                        <a href="submit_reports.php" class="action-item" style="text-decoration:none;">
                            <div class="action-icon"><i class="fa-solid fa-file-export"></i></div>
                            <div class="action-text">
                                <strong>Submit Report</strong>
                                <span>Weekly health summary</span>
                            </div>
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    </div>

                    <div class="system-health">
                        <h3>Storage Usage</h3>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 45%"></div>
                        </div>
                        <div class="progress-labels">
                            <span>45% Used</span>
                            <span>2GB Total</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
