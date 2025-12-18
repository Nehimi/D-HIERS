<?php
session_start();
include("../../dataBaseConnection.php");
include "../includes/pagination_helper.php"; 
include "../includes/log_helper.php"; 

// Get page number from URL
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$itemsPerPage = 10;

if (isset($_GET['delete_user'])) {
  $id = intval($_GET['delete_user']); // Sanitize
  mysqli_query($dataBaseConnection, "DELETE FROM users WHERE id='$id'");
  logAction($dataBaseConnection, "Delete User", "Deleted user ID: $id");
  echo "<script>window.location.href = 'user_management.php?deleted=success';</script>";
}

if (isset($_GET['delete_kebele'])) {
  $id = intval($_GET['delete_kebele']); // Sanitize
  mysqli_query($dataBaseConnection, "DELETE FROM kebele WHERE id='$id'");
  logAction($dataBaseConnection, "Delete Kebele", "Deleted kebele ID: $id");
  echo "<script>window.location.href = 'user_management.php?deleted=success';</script>";
}

// Get total users count
$totalUsersQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total FROM users");
$totalUsers = mysqli_fetch_assoc($totalUsersQuery)['total'];

// Calculate pagination
$paginationData = getPaginationData($page, $totalUsers, $itemsPerPage);
$offset = $paginationData['offset'];

// Kebele pagination
$totalKebelesQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total FROM kebele");
$totalKebeles = mysqli_fetch_assoc($totalKebelesQuery)['total'];
$kebelePaginationData = getPaginationData($page, $totalKebeles, $itemsPerPage);
$kebeleOffset = $kebelePaginationData['offset'];
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Managment | D-HEIRS</title>
  <link rel="stylesheet" href="../css/admin.css">
  <link rel="stylesheet" href="../../css/table-responsive.css">
  <link rel="stylesheet" href="../../css/status_management.css">
  <!-- ICONS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="dashboard-body">
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-header">
      <div class="brand-icon">
        <img src="../../images/logo.png" alt="">
      </div>
      <div class="brand-text">
        D-HEIRS
        <span>Admin Portal</span>
      </div>
    </div>
    <nav class="sidebar-nav">
      <a href="dashboard.php" class="nav-item ">
        <i class="fa-solid fa-grid-2"></i>
        <span>Dashboard</span>
      </a>
      <a href="user_management.php" class="nav-item active">
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
      <a href="../../index.html" class="nav-item logout">
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
        <div>
          <h1>User Management</h1>
          <p class="page-subtitle">Manage all system users, roles, and permissions</p>
        </div>
        <a href="create_account.php" class="btn btn-primary">
          <i class="fa-solid fa-user-plus"></i> Add New User
        </a>
      </div>
      <!-- Filter and Stats Bar -->
      <div class="filter-bar">
        <div class="filter-group">
          <label>Filter by Role:</label>
          <select id="roleFilter" class="filter-select">
            <option value="all">All Roles</option>
            <option value="hew">HEW</option>
            <option value="coordinator">Coordinator</option>
            <option value="linkage">Linkage Focal</option>
            <option value="hmis">HMIS Officer</option>
            <option value="supervisor">Supervisor</option>
            <option value="admin">Administrator</option>
          </select>
        </div>

        <div class="filter-group">
          <label>Filter by Status:</label>
          <select id="statusFilter" class="filter-select">
            <option value="all">All Statuses</option>
            <option value="active">Active</option>
            <option value="pending">Pending</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>

        <div class="filter-group">
          <label>Filter by Kebele:</label>
          <select id="kebeleFilter" class="filter-select">
            <option value="all">All Kebeles</option>
            <?php
            // Load kebeles from database for filter
            $kebeleFilterQuery = mysqli_query($dataBaseConnection, "SELECT DISTINCT kebele FROM users WHERE kebele IS NOT NULL AND kebele != '' ORDER BY kebele ASC");
            while ($kebeleFilterRow = mysqli_fetch_assoc($kebeleFilterQuery)) {
              $kebeleValue = htmlspecialchars($kebeleFilterRow['kebele']);
              $kebeleDisplay = ucwords(str_replace('-', ' ', $kebeleValue));
              echo "<option value='$kebeleValue'>$kebeleDisplay</option>";
            }
            ?>
          </select>
        </div>

        <div class="filter-stats">
          <div class="stat-badge">
            <i class="fa-solid fa-users"></i>
            <span>Total: <strong id="totalCount"><?php echo $totalUsers; ?></strong></span>
          </div>
          <div class="stat-badge active">
            <i class="fa-solid fa-circle-check"></i>
            <span>Active: <strong id="activeCount"><?php 
              $activeQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total FROM users WHERE status='active'");
              echo mysqli_fetch_assoc($activeQuery)['total'];
            ?></strong></span>
          </div>
        </div>
      </div>
      <!-- Users Table -->
      <div class="card-panel">
        <div class="table-header">
          <h2>All Users</h2>
          <div class="table-actions">
            <button class="btn-icon" id="refreshBtn" title="Refresh">
              <i class="fa-solid fa-rotate"></i>
            </button>
            <button class="btn-icon" id="exportBtn" title="Export to CSV">
              <i class="fa-solid fa-download"></i>
            </button>
          </div>
        </div>

        <div class="table-wrapper">
          <table class="data-table">
            <thead>
              <tr>
                <th>
                  <input type="checkbox" id="selectAll">
                </th>
                <th>UserId</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Kebele</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="usersTableBody">
              <?php
              $sql = mysqli_query( $dataBaseConnection, "SELECT * FROM users ORDER BY id DESC LIMIT $itemsPerPage OFFSET $offset");
              while ($row = mysqli_fetch_assoc($sql)) {
                echo "<tr>";
                echo "<td data-label='Select'>" . "<input type = 'checkbox' class = 'row-checkbox'>" . "</td>";
                echo "<td data-label='User' class='primary-cell'>" . $row['userId'] . "</td>";
                echo "<td>" . $row["first_name"] . " " . $row["last_name"] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['phone_no'] . "</td>";
                echo "<td>" . $row['role'] . "</td>";
                echo "<td>" . $row['kebele'] . "</td>";
                
                // Interactive status dropdown
                $statusClass = strtolower($row['status']);
                echo "<td data-label='Status'>
                  <select class='status-select status-tag $statusClass' data-user-id='{$row['id']}' data-old-status='{$row['status']}' onchange='changeUserStatus({$row['id']}, this.value)'>
                    <option value='active' " . ($row['status'] == 'active' ? 'selected' : '') . ">Active</option>
                    <option value='inactive' " . ($row['status'] == 'inactive' ? 'selected' : '') . ">Inactive</option>
                    <option value='pending' " . ($row['status'] == 'pending' ? 'selected' : '') . ">Pending</option>
                  </select>
                </td>";
                echo "<td>
                <form class='action-buttons' method='post'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>

                    <a href='create_account.php?edit={$row['id']}' class='btn-icon'>
                        <i class='fa-solid fa-pen'></i>
                    </a>
      
                  <a href='user_management.php?delete_user={$row['id']}' class='btn-icon' onclick=\"return confirm('Are you sure you want to delete this user?');\">
                  <i class='fa-solid fa-trash'></i>
                  </a>
                </form>
                </td>";
                echo "</tr>";
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
      <div style="margin-top: 30px;" class="card-panel">
        <div class="table-header">
          <h2>All Kebele</h2>
          <div class="table-actions">
            <button class="btn-icon" id="refreshBtn" title="Refresh">
              <i class="fa-solid fa-rotate"></i>
            </button>
            <button class="btn-icon" id="exportBtn" title="Export to CSV">
              <i class="fa-solid fa-download"></i>
            </button>
          </div>
        </div>

        <div class="table-wrapper">
          <table class="data-table">
            <thead>
              <tr>
                <th>
                  <input type="checkbox" id="selectAll">
                </th>
                <th>Kebele Name</th>
                <th>Kebele Code</th>
                <th>Woreda</th>
                <th>Zone</th>
                <th>Population</th>
                <th>Households No.</th>
                <th>Health Post Name</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="usersTableBody">
              <?php
              $sql = mysqli_query($dataBaseConnection, "SELECT * FROM kebele ORDER BY id DESC LIMIT $itemsPerPage OFFSET $kebeleOffset");
              while ($row = mysqli_fetch_assoc($sql)) {
                echo "<tr>";
                echo "<td data-label='Select'>" . "<input type = 'checkbox' class = 'row-checkbox'>" . "</td>";
                echo "<td data-label='User' class='primary-cell'>" . $row['kebeleName'] . "</td>";
                echo "<td>" . $row['kebeleCode'] . "</td>";
                echo "<td>" . $row['woreda'] . "</td>";
                echo "<td>" . $row['zone'] . "</td>";
                echo "<td>" . $row['population'] . "</td>";
                echo "<td>" . $row['households'] . "</td>";
                echo "<td>" . $row['healthPostName'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>
                <form class='action-buttons' method='post'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>

                    <a href='kebele_config.php?edit={$row['id']}' class='btn-icon'>
                        <i class='fa-solid fa-pen'></i>
                    </a>
      
                  <a href='user_management.php?delete_kebele={$row['id']}' class='btn-icon' onclick=\"return confirm('Are you sure you want to delete this kebele?');\">
                  <i class='fa-solid fa-trash'></i>
                  </a>
                </form>
                </td>";
                echo "</tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
        <!-- Pagination for Kebele -->
        <?php echo renderPagination($kebelePaginationData); ?>
        </div>
      </div>
      <!-- Bulk Actions Panel (shown when users are selected) -->
      <div class="bulk-actions-panel" id="bulkActionsPanel" style="display: none;">
        <div class="bulk-info">
          <i class="fa-solid fa-check-circle"></i>
          <span><strong id="selectedCount">0</strong> users selected</span>
        </div>
        <div class="bulk-buttons">
          <button class="btn btn-secondary">
            <i class="fa-solid fa-user-check"></i> Activate
          </button>
          <button class="btn btn-secondary">
            <i class="fa-solid fa-user-xmark"></i> Deactivate
          </button>
          <button class="btn btn-danger">
            <i class="fa-solid fa-trash"></i> Delete Selected
          </button>
        </div>
      </div>
    </div>
  </main>
  <script src="../js/status_management.js"></script>
  <script src="../js/user_management.js"></script>
  <script src="../js/script.js"></script>
</body>

</html>