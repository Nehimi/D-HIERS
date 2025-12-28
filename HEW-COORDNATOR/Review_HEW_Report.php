<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Review Reports | D-HEIRS</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@500;700&display=swap"
    rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- CSS -->
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/logout.css">
  <link rel="stylesheet" href="../focal/focal_dashboard.css">
  <link rel="stylesheet" href="coordinator_dashboard.css">

  <style>
    .split-layout {
      display: grid;
      grid-template-columns: 300px 1fr;
      gap: 2rem;
    }

    .selection-card {
      background: white;
      padding: 2rem;
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-sm);
      border: 1px solid #f3f4f6;
      height: fit-content;
    }

    .results-card {
      background: white;
      padding: 2rem;
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-sm);
      border: 1px solid #f3f4f6;
      min-height: 600px;
    }

    .form-label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: var(--text-main);
      font-size: 0.9rem;
    }

    .form-select {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid #d1d5db;
      border-radius: 0.5rem;
      font-family: var(--font-body);
      font-size: 0.95rem;
      margin-bottom: 1.25rem;
    }

    .form-select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.1);
    }

    .btn-review {
      width: 100%;
      padding: 0.75rem;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 0.5rem;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
      margin-top: 0.5rem;
    }

    .btn-review:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
    }

    /* Review Table Styles */
    .review-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }

    .review-table th {
      text-align: left;
      padding: 1rem;
      background: #f8fafc;
      color: var(--text-muted);
      font-weight: 600;
      border-bottom: 1px solid #e5e7eb;
      font-size: 0.85rem;
      text-transform: uppercase;
    }

    .review-table td {
      padding: 1rem;
      border-bottom: 1px solid #f3f4f6;
      color: var(--text-main);
      font-size: 0.95rem;
    }

    .review-table tr:hover {
      background: #f0fdfa;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
      .split-layout {
        grid-template-columns: 1fr; /* Stack vertically on small PC screens */
      }
      .selection-card {
        margin-bottom: 0;
      }
    }
  </style>
</head>

<body class="dashboard-body">

  <aside class="sidebar">
    <div class="sidebar-header">
      <div class="brand-icon"><img src="images/images.jpg" alt="Logo"></div>
      <div class="brand-text">D-HEIRS <span>Coordinator Portal</span></div>
    </div>
    <nav class="sidebar-nav">
      <a href="coordinator_dashboard.html" class="nav-item"><i class="fa-solid fa-grid-2"></i>
        <span>Dashboard</span></a>
      <a href="monitor2.html" class="nav-item"><i class="fa-solid fa-chart-line"></i> <span>Monitor Activity</span></a>
      <a href="Review_HEW_Report.php" class="nav-item active"><i class="fa-solid fa-file-invoice"></i> <span>Review
          Reports</span></a>
      <a href="validate2.html" class="nav-item"><i class="fa-solid fa-check-double"></i> <span>Validate Data</span></a>
      <a href="forward2.html" class="nav-item"><i class="fa-solid fa-paper-plane"></i> <span>Forward Data</span></a>
    </nav>
    <div class="sidebar-footer">
      <a href="../index.html" class="nav-item logout"><i class="fa-solid fa-arrow-right-from-bracket"></i>
        <span>Logout</span></a>
    </div>
  </aside>

  <main class="main-content">
    <header class="dashboard-header">
      <div class="header-text">
        <h1>Review Submitted Reports</h1>
        <p style="color: var(--text-muted);">Inspect field data and household surveys before validation.</p>
      </div>
    </header>

    <div class="split-layout">

      <section class="selection-card">
        <h3 style="margin-bottom:1.5rem; color:var(--primary); font-size:1.1rem;"><i class="fa-solid fa-sliders"></i>
          Filter Criteria</h3>

        <form id="reviewForm"> <!-- Preserving ID for ReviewCode.js -->
          <label for="kebeleSelect" class="form-label">Select Kebele</label>
          <select id="kebeleSelect" class="form-select">
            <option value="">-- Choose Region --</option>
            <option value="Arade">Arade</option>
            <option value="Lich-Amba">Lich-Amba</option> <!-- Fixed Check casing matches DB -->
            <option value="Lereba">Lereba</option>
          </select>

          <label for="dataTypeSelect" class="form-label">Data Type</label>
          <select id="dataTypeSelect" class="form-select">
            <option value="">-- Choose Data Category --</option>
            <option value="household data">Household Demographics</option>
            <option value="maternal_health">Maternal Health (ANC/PNC)</option>
            <option value="child_health">Child Health</option>
            <option value="immunization">Immunization (Child)</option>
            <option value="sanitation">Environmental Sanitation</option>
            <option value="disease_surveillance">Disease Surveillance (Malaria/TB)</option>
          </select>

          <button type="button" id="loadDataBtn" class="btn-review">
            <i class="fa-solid fa-magnifying-glass"></i> Load Reports
          </button>
          <!-- Note: JS attaches event listener to button or form submit -->
        </form>
      </section>

      <section class="results-card">
        <h3 style="margin-bottom:1.5rem; border-bottom:1px solid #f3f4f6; padding-bottom:1rem;">
          <i class="fa-solid fa-table-list"></i> Report Data
        </h3>

        <div id="reviewDisplayArea"> <!-- Preserving ID for ReviewCode.js to populate -->
          <div style="text-align:center; padding:4rem; color:var(--text-light);">
            <i class="fa-solid fa-folder-open" style="font-size:3rem; margin-bottom:1rem; display:block;"></i>
            <p>Select criteria and click "Load Reports" to view data.</p>
          </div>
        </div>
      </section>
    </div>

  </main>
  <script src="ReviewCode.js"></script>
  <script src="../js/logout.js"></script>
  <script src="mobile_nav.js"></script>
</body>

</html>