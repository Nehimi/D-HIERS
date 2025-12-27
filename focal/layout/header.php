<?php
// Get session data
$userName = $_SESSION['full_name'] ?? 'Focal Person';
$userRole = 'Linkage Focal Person';
?>
<!-- Dashboard Header -->
<header class="dashboard-header">
    <!-- Search Bar -->
    <div class="header-search">
        <i class="fa-solid fa-search"></i>
        <input type="text" placeholder="Search data, reports, or settings...">
    </div>
    
    <!-- Header Actions -->
    <div class="header-actions">
        <!-- Notification Bell -->
        <button class="icon-btn" title="Notifications">
            <i class="fa-solid fa-bell"></i>
            <span class="badge-dot"></span>
        </button>
        
        <!-- User Profile -->
        <div class="user-profile">
            <img src="../../images/avatar.png" alt="<?php echo htmlspecialchars($userName); ?>" class="avatar-sm">
            <div class="user-info">
                <span class="name"><?php echo htmlspecialchars($userName); ?></span>
                <span class="role"><?php echo htmlspecialchars($userRole); ?></span>
            </div>
        </div>
    </div>
</header>
