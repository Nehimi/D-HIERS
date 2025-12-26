<?php
session_start();
include("../dataBaseConnection.php");

// Security Check
if (!isset($_SESSION['user_db_id'])) {
    header("Location: ../index.html");
    exit();
}

// --- DATA FETCHING FOR DASHBOARD ---

// 1. Pending Validations
$pendingSql = "SELECT COUNT(*) as count FROM health_data WHERE status = 'Forwarded'";
$pendingRes = $dataBaseConnection->query($pendingSql);
$pendingCount = $pendingRes->fetch_assoc()['count'] ?? 0;

// 2. Completed Reports (this month)
$curMonth = date('Y-m');
$reportSql = "SELECT COUNT(*) as count FROM hmis_reports WHERE date_format(generated_at, '%Y-%m') = '$curMonth'";
$reportRes = $dataBaseConnection->query($reportSql);
$doneCount = $reportRes->fetch_assoc()['count'] ?? 0;

// 3. Recent Activity (Last 5 validations)
$activitySql = "SELECT * FROM health_data WHERE status = 'Focal-Validated' ORDER BY updated_at DESC LIMIT 5";
$activityRes = $dataBaseConnection->query($activitySql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layout/head.php'; ?>
    <title>D-HEIRS | Focal Person Dashboard</title>
    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="dashboard-body">
  
  <!-- Sidebar -->
  <?php include 'layout/sidebar.php'; ?>

  <!-- Main Content -->
  <main class="main-content">
      
      <!-- Top Header -->
      <?php 
      $pageTitle = 'Overview';
      include 'layout/header.php'; 
      ?>

      <!-- Stats Grid -->
      <section class="stats-grid">
          <!-- Pending Card -->
          <div class="stat-card primary">
              <div class="stat-icon">
                  <i class="fa-solid fa-hourglass-half"></i>
              </div>
              <div>
                  <h3>Pending Validations</h3>
                  <div class="value"><?php echo $pendingCount; ?></div>
                  <div class="trend text-warning">
                      <i class="fa-solid fa-arrow-right"></i> Requires Action
                  </div>
              </div>
          </div>

          <!-- Reports Card -->
          <div class="stat-card secondary">
              <div class="stat-icon">
                  <i class="fa-solid fa-file-circle-check"></i>
              </div>
              <div>
                  <h3>Reports Generated</h3>
                  <div class="value"><?php echo $doneCount; ?></div>
                  <div class="trend text-success">
                      <i class="fa-solid fa-calendar-check"></i> This Month
                  </div>
              </div>
          </div>

          <!-- Efficiency Card -->
          <div class="stat-card warning">
              <div class="stat-icon">
                  <i class="fa-solid fa-bolt"></i>
              </div>
              <div>
                  <h3>System Status</h3>
                  <div class="value">Active</div>
                  <div class="trend text-success">
                      <i class="fa-solid fa-check-circle"></i> Operational
                  </div>
              </div>
          </div>
      </section>

      <!-- Quick Actions Removed -->

      <!-- Recent Activity Table -->
      <section>
          <h2 class="section-title">Recent Validations</h2>
          <div class="table-container">
               <div class="table-header">
                   <span>Service Type</span>
                   <span>Date</span>
               </div>
               <ul class="activity-list" style="padding:0;">
                   <?php while($row = $activityRes->fetch_assoc()): ?>
                   <li style="padding: 1rem 1.5rem; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
                       <span style="font-weight:500; color:var(--text-main);">
                           <i class="fa-solid fa-check-circle text-success" style="margin-right:0.5rem"></i>
                           <?php echo htmlspecialchars($row['service_type']); ?>
                       </span>
                       <span style="color:var(--text-sec); font-size:0.9rem;">
                           <?php echo date('M j, H:i', strtotime($row['updated_at'])); ?>
                       </span>
                   </li>
                   <?php endwhile; ?>
                   <?php if($activityRes->num_rows == 0): ?>
                       <li style="padding: 2rem; text-align:center; color:var(--text-muted);">No recent activity found.</li>
                   <?php endif; ?>
               </ul>
          </div>
      </section>

  </main>
  <script src="../js/logout.js"></script>
</body>
</html>
