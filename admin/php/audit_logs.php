<?php
session_start();
include("../../dataBaseConnection.php");
include "../includes/pagination_helper.php";
$whereClauses = ["1=1"];

// Date Filter
$dateFilter = $_GET['date'] ?? 'all';
if ($dateFilter == 'last_24h') {
    $whereClauses[] = "created_at >= NOW() - INTERVAL 1 DAY";
} elseif ($dateFilter == 'last_7d') {
    $whereClauses[] = "created_at >= NOW() - INTERVAL 7 DAY";
} elseif ($dateFilter == 'last_30d') {
    $whereClauses[] = "created_at >= NOW() - INTERVAL 30 DAY";
}

// Action Filter
$actionFilter = $_GET['action'] ?? 'all';
if ($actionFilter != 'all') {
    // Map simplified values to potential DB values if needed, or assume exact match
    // Filter options: login, create, update, delete, access
    // DB values might be "Create User", "Update User", etc.
    // Use LIKE for flexibility
    $safeAction = mysqli_real_escape_string($dataBaseConnection, $actionFilter);
    $whereClauses[] = "action LIKE '%$safeAction%'";
}

// Role Filter
$roleFilter = $_GET['role'] ?? 'all';
if ($roleFilter != 'all') {
    $safeRole = mysqli_real_escape_string($dataBaseConnection, $roleFilter);
    $whereClauses[] = "user_role = '$safeRole'";
}

// Search Filter
$searchQuery = $_GET['search'] ?? '';
if (!empty($searchQuery)) {
    $safeSearch = mysqli_real_escape_string($dataBaseConnection, $searchQuery);
    $whereClauses[] = "(user_name LIKE '%$safeSearch%' OR details LIKE '%$safeSearch%' OR action LIKE '%$safeSearch%')";
}

$whereSql = implode(" AND ", $whereClauses);

// =======================
// PAGINATION & QUERY
// =======================
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$itemsPerPage = 10;

// Get total count
$countSql = "SELECT COUNT(*) as total FROM audit_logs WHERE $whereSql";
$countResult = mysqli_query($dataBaseConnection, $countSql);
$totalLogs = mysqli_fetch_assoc($countResult)['total'];

// Pagination
$paginationData = getPaginationData($page, $totalLogs, $itemsPerPage);
$offset = $paginationData['offset'];

// Fetch Logs
$logsSql = "SELECT * FROM audit_logs WHERE $whereSql ORDER BY created_at DESC LIMIT $itemsPerPage OFFSET $offset";
$logsResult = mysqli_query($dataBaseConnection, $logsSql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Icons -->
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../../css/logout.css">
    <link rel="stylesheet" href="../css/audit_logs.css">
    <link rel="stylesheet" href="../../css/table-responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Audit Logs | D-HEIRS</title>
</head>

<body class="dashboard-body">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="brand-icon">
                <i class="fa-solid fa-heart-pulse"></i>
            </div>
            <div class="brand-text">
                D-HEIRS
                <span>Admin Portal</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item">
                <i class="fa-solid fa-grid-2"></i>
                <span>Dashboard</span>
            </a>
            <a href="user_management.php" class="nav-item">
                <i class="fa-solid fa-users-gear"></i>
                <span>User Management</span>
            </a>
            <a href="kebele_config.php" class="nav-item">
                <i class="fa-solid fa-map-location-dot"></i>
                <span>Kebele Config</span>
            </a>
            <a href="audit_logs.php" class="nav-item active">
                <i class="fa-solid fa-file-shield"></i>
                <span>Audit Logs</span>
            </a>
            <a href="system_reports.php" class="nav-item">
                <i class="fa-solid fa-chart-pie"></i>
                <span>System Reports</span>
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
        <!-- Top Header -->
        <header class="dashboard-header">
            <div class="header-search">
                <i class="fa-solid fa-search"></i>
                <form action="" method="GET" style="display:inline;">
                    <input type="text" name="search" id="globalSearch" placeholder="Search logs..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                </form>
            </div>

            <div class="header-actions">
                <a href="messages.html" class="icon-btn">
                    <i class="fa-solid fa-bell"></i>
                    <span class="badge-dot"></span>
                </a>
                <a href="admin_profile.html" class="user-profile" style="cursor: pointer; text-decoration: none;">
                    <img src="../../images/avatar.png" alt="Admin" class="avatar-sm">
                    <div class="user-info">
                        <span class="name">Dr. Admin</span>
                        <span class="role">System Administrator</span>
                    </div>
                </a>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h1>Audit Logs</h1>
                    <p class="page-subtitle">Track system activities, security events, and user actions</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.print()">
                        <i class="fa-solid fa-download"></i> Export Logs
                    </button>
                </div>
            </div>

            <!-- Filter Bar -->
            <form action="" method="GET" class="filter-bar">
                <!-- Preserve existing search/page parameters if needed, though simple form submit resets page usually -->
                
                <div class="filter-group">
                    <label>Date Range</label>
                    <select name="date" class="filter-select" onchange="this.form.submit()">
                        <option value="all" <?php echo $dateFilter == 'all' ? 'selected' : ''; ?>>All Time</option>
                        <option value="last_24h" <?php echo $dateFilter == 'last_24h' ? 'selected' : ''; ?>>Last 24 Hours</option>
                        <option value="last_7d" <?php echo $dateFilter == 'last_7d' ? 'selected' : ''; ?>>Last 7 Days</option>
                        <option value="last_30d" <?php echo $dateFilter == 'last_30d' ? 'selected' : ''; ?>>Last 30 Days</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Action Type</label>
                    <select name="action" class="filter-select" onchange="this.form.submit()">
                        <option value="all" <?php echo $actionFilter == 'all' ? 'selected' : ''; ?>>All Actions</option>
                        <option value="login" <?php echo $actionFilter == 'login' ? 'selected' : ''; ?>>Login/Logout</option>
                        <option value="create" <?php echo $actionFilter == 'create' ? 'selected' : ''; ?>>Create</option>
                        <option value="update" <?php echo $actionFilter == 'update' ? 'selected' : ''; ?>>Update</option>
                        <option value="delete" <?php echo $actionFilter == 'delete' ? 'selected' : ''; ?>>Delete</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>User Role</label>
                    <select name="role" class="filter-select" onchange="this.form.submit()">
                        <option value="all" <?php echo $roleFilter == 'all' ? 'selected' : ''; ?>>All Roles</option>
                        <option value="admin" <?php echo $roleFilter == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="hew" <?php echo $roleFilter == 'hew' ? 'selected' : ''; ?>>HEW</option>
                        <option value="coordinator" <?php echo $roleFilter == 'coordinator' ? 'selected' : ''; ?>>Coordinator</option>
                    </select>
                </div>
            </form>

            <!-- Logs Table -->
            <div class="config-card">
                <div class="card-body">
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>IP Address</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($logsResult) > 0) {
                                    while ($row = mysqli_fetch_assoc($logsResult)) {
                                        // Styling classes
                                        $statusClass = $row['status'] == 'success' ? 'status-success' : 'status-error';
                                        $statusLabel = ucfirst($row['status']);
                                        $roleClass = '';
                                        switch(strtolower($row['user_role'])) {
                                            case 'admin': $roleClass = 'admin'; break;
                                            case 'coordinator': $roleClass = 'coord'; break;
                                            case 'hew': $roleClass = 'hew'; break;
                                            default: $roleClass = 'system'; break;
                                        }
                                        $dateDisplay = date('M d, Y H:i:s', strtotime($row['created_at']));
                                        
                                        echo "<tr>";
                                        echo "<td class='time-cell'>{$dateDisplay}</td>";
                                        echo "<td class='primary-cell'>" . htmlspecialchars($row['user_name']) . "</td>";
                                        echo "<td><span class='role-tag {$roleClass}'>" . ucfirst($row['user_role']) . "</span></td>";
                                        echo "<td>" . htmlspecialchars($row['action']) . "</td>";
                                        echo "<td><div class='log-details'>" . htmlspecialchars($row['details']) . "</div></td>";
                                        echo "<td>" . htmlspecialchars($row['ip_address']) . "</td>";
                                        echo "<td><span class='status-badge {$statusClass}'>{$statusLabel}</span></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' style='text-align:center;'>No logs found matching your criteria.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php echo renderPagination($paginationData); ?>
                    <script>
                    function navigateToPage(page) {
                      const url = new URL(window.location);
                      url.searchParams.set('page', page);
                      window.location.href = url.toString();
                    }
                    </script>
                </div>
            </div>
        </div>
    </main>

    <script src="../js/script.js?v=<?php echo time(); ?>"></script>
    <script src="../../js/logout.js"></script>
</body>

</html>
