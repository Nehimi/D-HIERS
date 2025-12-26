<?php
// Get current page to set active class
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="brand-icon">
            <img src="image.jpg" alt="Logo">
        </div>
        <div class="brand-text">
            D-HEIRS
            <span>Focal Portal</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="focal_dashboard.php" class="nav-item <?php echo $current_page == 'focal_dashboard.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-grid-2"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="validate_incoming_data.php" class="nav-item <?php echo $current_page == 'validate_incoming_data.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-clipboard-check"></i>
            <span>Validate Data</span>
        </a>
        
        <a href="statistical_report.php" class="nav-item <?php echo $current_page == 'statistical_report.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-pie"></i>
            <span>Generate Reports</span>
        </a>
        
        <a href="hmis_data_submission.php" class="nav-item <?php echo $current_page == 'hmis_data_submission.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-file-export"></i>
            <span>Submit to HMIS</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="../index.html" class="nav-item logout">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>
