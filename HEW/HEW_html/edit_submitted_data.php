<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Submitted Data | D-HEIRS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Main HEW CSS -->
  <link rel="stylesheet" href="../HEW_css/hew.css">
  <link rel="stylesheet" href="../HEW_css/hew_style.css">
  <script src="../HEW_js/edit_submitted_data.js" defer></script>
  <link rel="stylesheet" href="../HEW_css/edit_submitted_data.css">
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
      <a href="register_household.php" class="nav-item">
        <i class="fa-solid fa-users-gear"></i>
        <span>Register Household</span>
      </a>
      <a href="enter_health_data.html" class="nav-item">
        <i class="fa-solid fa-stethoscope"></i>
        <span>Enter Health Data</span>
      </a>
      <a href="edit_submitted_data.php" class="nav-item active">
        <i class="fa-solid fa-file-pen"></i>
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
    <!-- Pro Header with Search & Profile -->
    <header class="dashboard-header">
      <div class="header-search">
        <i class="fa-solid fa-search"></i>
        <input type="text" placeholder="Search system...">
      </div>
      
      <div class="header-actions">
        <button class="icon-btn">
          <i class="fa-solid fa-bell"></i>
          <span class="badge-dot"></span>
        </button>
        <div class="user-profile">
            <img src="../image/avatar.png" alt="HEW" class="avatar-sm">
            <div class="user-info">
                <span class="name">Semira Kedir</span>
                <span class="role">Health Ext. Worker</span>
            </div>
        </div>
      </div>
    </header>

    <section class="form-section">
        <div class="page-title-area">
            <h1>Find & Edit Household</h1>
            <p>Search by Household ID to view previously registered data</p>
        </div>

      <!-- Step 1: Check household ID -->
      <div id="id-check-section" class="form-container search-container">
        <div class="input-with-icon">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="householdId" placeholder="Enter Household ID (e.g., HH-001)" required>
        </div>
        <button id="checkIdBtn" class="btn-primary">Search</button>
      </div>

      <div class="empty-state-icon">
        <i class="fa-solid fa-file-pen"></i>
        <p>Enter a Household ID to begin editing</p>
      </div>

      <!-- Step 2: Edit form (hidden until ID is found) -->
      <form id="editDataForm" class="form-container hidden">
        <div class="form-header">
            <h3>Editing Household Data</h3> 
            <span id="displayId" class="badge-id"></span>
        </div>

        <div class="form-grid">
            <div class="form-group full-width">
                <label for="memberName">Full Name</label>
                <input type="text" id="memberName" name="memberName" required>
            </div>

            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" id="age" name="age" min="0" required>
            </div>

            <div class="form-group">
                <label for="sex">Sex</label>
                <div class="select-wrapper">
                    <select id="sex" required>
                        <option value="">-- Select Sex --</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                    <i class="fa-solid fa-chevron-down arrow-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="kebele">Kebele</label>
                <div class="select-wrapper">
                    <select id="kebele" required>
                        <option value="">-- Select Kebele --</option>
                        <option value="Lich-Amba">Lich-Amba</option>
                        <option value="Arada">Arada</option>
                        <option value="Lereba">Lereba</option>
                    </select>
                    <i class="fa-solid fa-chevron-down arrow-icon"></i>
                </div>
            </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn-secondary" onclick="window.location.href='hew_dashboard.html'">
            Cancel
          </button>
          <button type="submit" class="btn-primary">
            <i class="fa fa-save"></i> Save Changes
          </button>
        </div>
      </form>
    </section>
  </main>
</body>

</html>
