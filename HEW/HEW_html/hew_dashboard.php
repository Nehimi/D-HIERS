<?php
session_start();
include "../../dataBaseConnection.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>D-HEIRS | HEW Dashboard</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Main CSS -->
  <link rel="stylesheet" href="../HEW_css/hew.css" />
  <link rel="stylesheet" href="../HEW_css/hew_style.css" />
  <link rel="stylesheet" href="../HEW_css/hew_dashbord.css" />


  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="../HEW_js/hew_dashboard.js" defer></script>
</head>

<body class="dashboard-body">
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-header">
      <div class="brand-icon">
        <img src="../image/logo.png" alt="">
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
      <a href="enter_health_data.html" class="nav-item">
        <i class="fa-solid fa-stethoscope"></i>
        <span>Enter Health Data</span>
      </a>
      <a href="edit_submitted_data.html" class="nav-item">
        <i class="fa-solid fa-file-pen"></i>
        <span>Edit Submitted Data</span>
      </a>
      <a href="submit_reports.html" class="nav-item">
        <i class="fa-solid fa-chart-pie"></i>
        <span>Submit Reports</span>
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
    <header class="dashboard-header">
      <h2>Welcome, HEW</h2>
      <p>Here’s an overview of your recent health activities</p>
    </header>

    <div class="dashboard-container">
      <!-- Summary Cards -->
      <section class="summary-cards">
        <div class="card">
          <i class="fa-solid fa-house"></i>
          <h3>Total Households</h3>
          <p>254</p>
        </div>
        <div class="card">
          <i class="fa-solid fa-user-group"></i>
          <h3>People Served</h3>
          <p>1,324</p>
        </div>
        <div class="card">
          <i class="fa-solid fa-syringe"></i>
          <h3>Immunizations</h3>
          <p>78</p>
        </div>
        <div class="card">
          <i class="fa-solid fa-person-pregnant"></i>
          <h3>Maternal Visits</h3>
          <p>43</p>
        </div>
      </section>

      <!-- Charts -->
      <section class="charts">
        <canvas id="monthlyChart"></canvas>
        <canvas id="serviceChart"></canvas>
      </section>

      <!-- Bottom Section -->
      <div class="table-wrapper">
        <table class="data-table">
          <thead>
            <tr>
              <th>
                <input type="checkbox" id="selectAll">
              </th>
              <th>region</th>
              <th>zone</th>
              <th>Woreda</th>
              <th>kebele</th>
              <th>householdId</th>
              <th>memberName</th>
              <th>age</th>
              <th>sex</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="usersTableBody">
            <?php
            $sql = mysqli_query($dataBaseConnection, "SELECT * FROM household ORDER BY id DESC");
            while ($row = mysqli_fetch_assoc($sql)) {
              echo "<tr>";
              echo "<td data-label='Select'>" . "<input type = 'checkbox' class = 'row-checkbox'>" . "</td>";
              echo "<td data-label='User' class='primary-cell'>" . $row['region'] . "</td>";
              echo "<td>" . $row['zone'] . "</td>";
              echo "<td>" . $row['woreda'] . "</td>";
              echo "<td>" . $row['kebele'] . "</td>";
              echo "<td>" . $row['householdId'] . "</td>";
              echo "<td>" . $row['memberName'] . "</td>";
              echo "<td>" . $row['age'] . "</td>";
              echo "<td>" . $row['sex'] . "</td>";
              echo "<td>
                <form class='action-buttons' method='post'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>

                    <a href='kebele_config.php?edit={$row['id']}' class='btn-icon'>
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

      <!-- Quick Actions -->
      <section class="quick-actions">
        <h3>Quick Actions</h3>
        <button onclick="window.location.href='register_household.php'">
          ➕ Register Household
        </button>
        <button onclick="window.location.href='enter_health_data.html'">
          🩺 Enter Health Data
        </button>
        <button onclick="window.location.href='submit_reports.html'">
          📤 Submit Report
        </button>
      </section>
    </div>
  </main>
</body>

</html>