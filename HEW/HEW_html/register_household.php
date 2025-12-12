<?php
session_start();
include "../../dataBaseConnection.php";

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Household | D-HEIRS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../HEW_css/hew.css">
  <link rel="stylesheet" href="../HEW_css/hew_style.css">
  <script src="../HEW_js/register_household.js"></script>
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
      <a href="hew_dashboard.php" class="nav-item">
        <i class="fa-solid fa-grid-2"></i>
        <span>Dashboard</span>
      </a>
      <a href="register_household.php" class="nav-item active">
        <i class="fa-solid fa-users-gear"></i>
        <span>Register Household</span>
      </a>
      <a href="enter_health_data.html" class="nav-item">
        <i class="fa-solid fa-map-location-dot"></i>
        <span>Enter Health Data</span>
      </a>
      <a href="edit_submitted_data.html" class="nav-item">
        <i class="fa-solid fa-file-shield"></i>
        <span>Edit Submitted Data</span>
      </a>
      <a href="submit_reports.html" class="nav-item">
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
    <header class="dashboard-header">
      <h2>Register Household</h2>
    </header>

    <section class="form-section">
      <form method="POST" action="register_household.php" id="householdForm" class="form-container">


        <h3 class="form-title">Address Information</h3>
        <div class="form-group">
          <label for="region">Region</label>
          <input type="text" id="region" name="region" placeholder="Enter region" required>
        </div>

        <div class="form-group">
          <label for="zone">Zone</label>
          <input type="text" id="zone" name="zone" placeholder="Enter zone" required>
        </div>

        <div class="form-group">
          <label for="woreda">Woreda</label>
          <input type="text" id="woreda" name="woreda" placeholder="Enter woreda" required>
        </div>

        <div class="form-group">
          <label for="kebele">Select Kebele:</label>
          <select id="kebele" name="kebele" class="form-control" required>
            <option value="">-- Choose Kebele --</option>
            <option value="Lich-Amba">Lich-Amba</option>
            <option value="Arada">Arada</option>
            <option value="Lereba">Lereba</option>
          </select>
        </div>


        <h3 class="form-title">Family Member Information</h3>
        <div class="form-group">
          <label for="householdId">Household ID</label>
          <input type="text" id="householdId" name="householdId" placeholder="e.g. HH-001" required>
        </div>

        <div class="form-group">
          <label for="memberName">Full Name</label>
          <input type="text" id="memberName" name="memberName" placeholder="Enter full name" required>
        </div>

        <div class="form-group">
          <label for="age">Age</label>
          <input type="number" id="age" name="age" placeholder="Enter age" required>
        </div>

        <div class="form-group">
          <label for="sex">Sex</label>
          <select id="sex" name="sex" required>
            <option value="">-- Select --</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
          </select>
        </div>


        <div class="form-actions">
          <button name="SaveFamily" type="submit" class="btn-primary">
            <i class="fa fa-save"></i> Save Family Member
          </button>

          <button type="button" class="btn-secondary" onclick="window.location.href='hew_dashboard.html'">
            <i class="fa fa-arrow-left"></i> Back
          </button>
        </div>
      </form>
    </section>
  </main>
</body>

</html>


<?php
include "../../dataBaseConnection.php";

if (isset($_POST['SaveFamily'])) {
  $region = $_POST['region'];
  $zone = $_POST['zone'];
  $woreda = $_POST['woreda'];
  $kebele = $_POST['kebele'];
  $householdId = $_POST['householdId'];
  $memberName = $_POST['memberName'];
  $age = $_POST['age'];
  $sex = $_POST['sex'];

  $sql = "INSERT INTO household(region, zone, woreda, kebele, householdId, memberName, age, sex) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
  $stmt = $dataBaseConnection->prepare($sql);

  if (!$stmt) {
    die("prepare failed: " . $dataBaseConnection->error);
  }

  $stmt->bind_param("ssssssis", $region, $zone, $woreda, $kebele, $householdId, $memberName, $age, $sex);

  if ($stmt->execute()) {
    echo "<script>alert('Household Add Successfully!'); window.location='register_household.php'; </script>";
  } else {
    echo "Error: " . $stmt->error;
  }
}
?>