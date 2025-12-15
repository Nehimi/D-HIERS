<?php
include 'dataBaseConnection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/table-responsive.css">
    <title>Admin | D-HEIRS</title>
</head>

<body class="dashboard-body">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="brand-icon">
                <img src="images/logo.png" alt="">
            </div>
            <div class="brand-text">
                D-HEIRS
                <span>Admin Portal</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="admin.php" class="nav-item active">
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
            <a href="audit_logs.php" class="nav-item">
                <i class="fa-solid fa-file-shield"></i>
                <span>Audit Logs</span>
            </a>
            <a href="system_reports.php" class="nav-item">
                <i class="fa-solid fa-chart-pie"></i>
                <span>System Reports</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="index.html" class="nav-item logout">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>
    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Header -->
        <Header class="dashboard-header">
            <div class="header-search">
                <i class="fa-solid fa-search"></i>
                <input type="text" placeholder="search users,logs,or setting...">
            </div>
            <div class="header-actions">
                <button class="icon-btn">
                    <i class="fa-solid fa-bell"></i>
                    <span class="badge-dot"></span>
                </button>
                <div class="user-profile">
                    <img src="images/avatar.png" alt="Admin" class="avatar-sm">
                    <div class="user-info">
                        <span class="name">Dr. Admin</span>
                        <span class="role">System Administrator</span>
                    </div>
                </div>
            </div>
        </Header>

        <!-- Dashboard Content -->
        <div class="content-wrapper">
            <div class="page-header">
                <h1>System Overview</h1>
                <a href="create_account.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Add New User
                </a>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <?php
                // Get statistics
                $totalUsersQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total FROM users");
                $totalUsers = mysqli_fetch_assoc($totalUsersQuery)['total'];
                
                $activeHEWsQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total FROM users WHERE role='hew' AND status='active'");
                $activeHEWs = mysqli_fetch_assoc($activeHEWsQuery)['total'];
                
                // Placeholder for reports - you can update this when you have a reports table
                $reportsToday = 89;
                ?>
                <div class="stat-card">
                    <div class="stat-icon color-1">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="stat-details">
                        <h3>Total Users</h3>
                        <p class="number"><?php echo $totalUsers; ?></p>
                        <span class="trend positive"><i class="fa-solid fa-arrow-up"></i> Active in system</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon color-2">
                        <i class="fa-solid fa-user-nurse"></i>
                    </div>
                    <div class="stat-details">
                        <h3>Active HEWs</h3>
                        <p class="number"><?php echo $activeHEWs; ?></p>
                        <span class="trend neutral">Lich-Amba, Arada, Lereba</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon color-3">
                        <i class="fa-solid fa-file-circle-check"></i>
                    </div>
                    <div class="stat-details">
                        <h3>Reports Today</h3>
                        <p class="number"><?php echo $reportsToday; ?></p>
                        <span class="trend positive"><i class="fa-solid fa-arrow-up"></i> 5% vs yesterday</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon color-4">
                        <i class="fa-solid fa-server"></i>
                    </div>
                    <div class="stat-details">
                        <h3>System Status</h3>
                        <p class="number">99.9%</p>
                        <span class="trend positive">Operational</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Quick Actions -->
            <div class="dashboard-grid">
                <div class="card-panel table-section">
                    <div class="panel-header">
                        <h2>Recent User Activity</h2>
                        <a href="user_management.php" class="view-all">View All</a>
                    </div>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Kebele</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Get recent 5 users
                                $recentUsersQuery = mysqli_query($dataBaseConnection, "SELECT * FROM users ORDER BY id DESC LIMIT 5");
                                $userIndex = 0;
                                $colorClasses = ['color-1', 'color-2', 'color-3'];
                                
                                while ($user = mysqli_fetch_assoc($recentUsersQuery)) {
                                    $colorClass = $colorClasses[$userIndex % 3];
                                    $initial = strtoupper(substr($user['first_name'], 0, 1));
                                    $fullName = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
                                    
                                    // Format role
                                    $roleClass = strtolower($user['role']);
                                    $roleDisplay = ucfirst($user['role']);
                                    if ($roleClass == 'hew') $roleDisplay = 'HEW';
                                    if ($roleClass == 'coordinator') $roleClass = 'coord';
                                    
                                    // Format kebele
                                    $kebeleDisplay = ucwords(str_replace('-', ' ', $user['kebele']));
                                    
                                    // Format status
                                    $statusClass = strtolower($user['status']);
                                    $statusDisplay = ucfirst($user['status']);
                                    
                                    echo "<tr>";
                                    echo "<td data-label='User' class='primary-cell'>";
                                    echo "<div class='user-cell'>";
                                    echo "<div class='avatar-xs $colorClass'>$initial</div>";
                                    echo "<span>$fullName</span>";
                                    echo "</div>";
                                    echo "</td>";
                                    echo "<td data-label='Role'><span class='role-tag $roleClass'>$roleDisplay</span></td>";
                                    echo "<td data-label='Kebele'>$kebeleDisplay</td>";
                                    echo "<td data-label='Status'><span class='status-tag $statusClass'>$statusDisplay</span></td>";
                                    echo "<td data-label='Last Login'>Recently added</td>";
                                    echo "</tr>";
                                    
                                    $userIndex++;
                                }
                                
                                // If no users found
                                if ($userIndex == 0) {
                                    echo "<tr><td colspan='5' style='text-align: center;'>No users found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- System Health / Quick Config -->
                <div class="card-panel side-panel">
                    <div class="panel-header">
                        <h2>Quick Actions</h2>
                    </div>
                    <div class="action-list">
                        <button class="action-item">
                            <div class="action-icon"><i class="fa-solid fa-user-plus"></i></div>
                            <div class="action-text">
                                <strong>Create Account</strong>
                                <span>Register new staff member</span>
                            </div>
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                        <button class="action-item">
                            <div class="action-icon"><i class="fa-solid fa-key"></i></div>
                            <div class="action-text">
                                <strong>Reset Password</strong>
                                <span>Unlock user account</span>
                            </div>
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                        <button class="action-item">
                            <div class="action-icon"><i class="fa-solid fa-database"></i></div>
                            <div class="action-text">
                                <strong>Backup Data</strong>
                                <span>Manual system backup</span>
                            </div>
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>

                    <div class="system-health">
                        <h3>Storage Usage</h3>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 65%"></div>
                        </div>
                        <div class="progress-labels">
                            <span>65% Used</span>
                            <span>500GB Total</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="js/admin/dashboard.js"></script>
    <script src="js/admin/script.js"></script>
</body>

</html>