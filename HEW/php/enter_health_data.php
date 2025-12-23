<?php
session_start();
include "../../dataBaseConnection.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_health_data'])) {
    $householdId = $_POST['householdId'];
    $serviceType = $_POST['serviceType'];
    $totalServed = $_POST['totalServed'];
    $details = $_POST['details'];
    
    // Fetch member name and kebele for the household to ensure data integrity
    $stmtCheck = $dataBaseConnection->prepare("SELECT memberName, kebele FROM household WHERE householdId = ?");
    $stmtCheck->bind_param("s", $householdId);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();
    
    if ($result->num_rows > 0) {
        $houseRow = $result->fetch_assoc();
        $patientName = $houseRow['memberName'];
        $kebele = $houseRow['kebele'];

        // Insert including patient name and kebele, using Pro column names
        $stmt = $dataBaseConnection->prepare("INSERT INTO health_data (householdId, patient_name, kebele, service_type, count, details) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssis", $householdId, $patientName, $kebele, $serviceType, $totalServed, $details);
        
        if ($stmt->execute()) {
            $message = "Health data saved successfully!";
            $messageType = "success";
        } else {
             $message = "Error saving data: " . $stmt->error;
             $messageType = "error";
        }
    } else {
        $message = "Invalid Household ID.";
        $messageType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enter Health Data | D-HEIRS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Main HEW CSS -->
  <link rel="stylesheet" href="../css/hew.css">
  <link rel="stylesheet" href="../css/hew_health.css">
  <link rel="stylesheet" href="../css/hew_style.css">
  <link rel="stylesheet" href="../../css/logout.css">
  
  <style>
      .error-message { color: #f43f5e; font-size: 0.9em; margin-top: 5px; display: none; }
      .success-message { color: #059669; font-size: 0.9em; margin-top: 5px; display: none; }
      .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1rem; }
      .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
      .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
  </style>
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
      <a href="hew_dashboard.php" class="nav-item">
        <i class="fa-solid fa-grid-2"></i>
        <span>Dashboard</span>
      </a>
      <a href="register_household.php" class="nav-item">
        <i class="fa-solid fa-users-gear"></i>
        <span>Register Household</span>
      </a>
      <a href="enter_health_data.php" class="nav-item active">
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
    <header class="dashboard-header">
      <h2>Enter Health Data</h2>
    </header>

    <section class="form-section">
      <?php if ($message): ?>
      <div class="alert alert-<?php echo $messageType; ?>">
          <?php echo $message; ?>
      </div>
      <?php endif; ?>

      <!-- Step 1: Check household ID -->
      <div id="id-check-section" class="form-container">
        <label for="checkHouseholdId">Enter Registered Household ID:</label>
        <div style="position:relative;">
            <input type="text" id="checkHouseholdId" placeholder="e.g. HH-001">
            <!-- Inline Error Messages -->
            <div id="idError" class="error-message"><i class="fa-solid fa-circle-exclamation"></i> Household ID not found! Please register first.</div>
            <div id="idSuccess" class="success-message"><i class="fa-solid fa-circle-check"></i> ID Verified.</div>
        </div>
        <button id="checkIdBtn" class="btn-primary" style="margin-top:10px;"><i class="fa fa-check"></i> Check ID</button>
      </div>

      <form method="POST" action="enter_health_data.php" id="healthDataForm" class="form-container hidden">
        <h3>Health Data for Household: <span id="displayId"></span></h3>
        <input type="hidden" name="householdId" id="hiddenHouseholdId">

        <div class="form-group">
          <label for="serviceType">Health Service Type</label>
          <select name="serviceType" id="serviceType" required>
            <option value="">-- Select Service --</option>
            <option value="maternal_health">Maternal Health</option>
            <option value="child_health">Child Health</option>
            <option value="immunization">Immunization</option>
            <option value="sanitation">Sanitation</option>
            <option value="disease_surveillance">Disease Surveillance</option>
          </select>
        </div>
        <div class="form-group">
          <label for="totalServed">Total Number of People Served</label>
          <input type="number" id="totalServed" name="totalServed" placeholder="Enter total number" min="1" required>
        </div>

        <div class="form-group">
          <label for="notes">Service Details / Notes</label>
          <textarea name="details" id="notes" rows="4" placeholder="Enter service details..." required></textarea>
        </div>

        <div class="form-actions">
          <button type="submit" name="save_health_data" class="btn-primary">
            <i class="fa fa-save"></i> Save Health Data
          </button>
          <button type="button" class="btn-secondary" onclick="window.location.href='hew_dashboard.php'">
            <i class="fa fa-arrow-left"></i> Back
          </button>
        </div>
      </form>
    </section>
  </main>

  <script>
      const checkBtn = document.getElementById('checkIdBtn');
      const idInput = document.getElementById('checkHouseholdId');
      const idError = document.getElementById('idError');
      const idSuccess = document.getElementById('idSuccess');
      const form = document.getElementById('healthDataForm');
      const displayId = document.getElementById('displayId');
      const hiddenId = document.getElementById('hiddenHouseholdId');

      checkBtn.addEventListener('click', function() {
          const id = idInput.value.trim();
          if(!id) {
              alert("Please enter an ID");
              return;
          }

          // AJAX check
          const formData = new FormData();
          formData.append('householdId', id);

          fetch('check_household_id.php', {
              method: 'POST',
              body: formData
          })
          .then(response => response.json())
          .then(data => {
              if(data.exists) {
                  // Valid ID
                  idError.style.display = 'none';
                  idSuccess.style.display = 'block';
                  form.classList.remove('hidden');
                  displayId.textContent = id;
                  hiddenId.value = id;
                  // idInput.disabled = true; // Optional: lock input
              } else {
                  // Invalid ID
                  idError.style.display = 'block';
                  idSuccess.style.display = 'none';
                  form.classList.add('hidden');
              }
          })
          .catch(error => console.error('Error:', error));
      });
  </script>
    <script src="../../js/logout.js"></script>
</body>
</html>
