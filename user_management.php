<?php
session_start();
include "dataBaseConnection.php";

if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  mysqli_query($dataBaseConnection, "DELETE FROM users WHERE id='$id'");
  echo "
<script>alert('User deleted'); window.location.href = 'user_management.php';</script>";
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Managment | D-HEIRS</title>
  <link rel="stylesheet" href="css/admin.css">
  <link rel="stylesheet" href="css/table-responsive.css">
  <!-- ICONS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
      <a href="admin.html" class="nav-item active">
        <i class="fa-solid fa-grid-2"></i>
        <span>Dashboard</span>
      </a>
      <a href="user_management.html" class="nav-item">
        <i class="fa-solid fa-users-gear"></i>
        <span>User Management</span>
      </a>
      <a href="kebele_config.html" class="nav-item">
        <i class="fa-solid fa-map-location-dot"></i>
        <span>Kebele Config</span>
      </a>
      <a href="audit_logs.html" class="nav-item">
        <i class="fa-solid fa-file-shield"></i>
        <span>Audit Logs</span>
      </a>
      <a href="System_report.html" class="nav-item">
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
            <option value="lich-amba">Lich-Amba</option>
            <option value="arada">Arada</option>
            <option value="lereba">Lereba</option>
            <option value="phcu-hq">PHCU HQ</option>
          </select>
        </div>

        <div class="filter-stats">
          <div class="stat-badge">
            <i class="fa-solid fa-users"></i>
            <span>Total: <strong id="totalCount">0</strong></span>
          </div>
          <div class="stat-badge active">
            <i class="fa-solid fa-circle-check"></i>
            <span>Active: <strong id="activeCount">0</strong></span>
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
              $sql = mysqli_query($dataBaseConnection, "SELECT * FROM users ORDER BY id DESC");
              while ($row = mysqli_fetch_assoc($sql)) {
                echo "<tr>";
                echo "<td data-label='Select'>" . "<input type = 'checkbox' class = 'row-checkbox'>" . "</td>";
                echo "<td data-label='User' class='primary-cell'>" . $row['userId'] . "</td>";
                echo "<td>" . $row["first_name"] . " " . $row["last_name"] . "</td>";
                echo "<td>" . $row['emali'] . "</td>";
                echo "<td>" . $row['phone_no'] . "</td>";
                echo "<td>" . $row['role'] . "</td>";
                echo "<td>" . $row['kebele'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>
                <form class='action-buttons' method='post'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>

                    <a href='create_account.php?edit={$row['id']}' class='btn-icon'>
                        <i class='fa-solid fa-pen'></i>
                    </a>
      
                  <a href='user_management.php?delete={$row['id']}' class='btn-icon' onclick=\"return confirm('Are you sure you want to delete this user?');\">
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
        <div class="table-footer">
          <div class="showing-info">
            Showing <strong id="showingStart">1</strong> to <strong id="showingEnd">3</strong> of <strong
              id="showingTotal">3</strong> users
          </div>
          <div class="pagination">
            <button class="page-btn" disabled>
              <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button class="page-btn active">1</button>
            <button class="page-btn">2</button>
            <button class="page-btn">3</button>
            <button class="page-btn">
              <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>
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
  <script src="user_management.js"></script>
  <script src="js/admin/script.js"></script>
</body>

</html>