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
        <div style="position: relative;">
            <button class="icon-btn" title="Notifications" id="notifBell">
                <i class="fa-solid fa-bell"></i>
                <span class="badge-dot" id="notifBadge" style="display: none;"></span>
            </button>
            
            <!-- Notification Dropdown -->
            <div id="notifDropdown" class="notif-dropdown" style="display: none; position: fixed; right: 2rem; top: 5rem; width: 350px; background: white; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; z-index: 9999; overflow: hidden;">
                <div style="padding: 1.25rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
                    <h4 style="margin: 0; font-size: 1.1rem; color: #1e293b;">Notifications</h4>
                    <span id="markAllRead" style="font-size: 0.8rem; color: #2ecc71; cursor: pointer; font-weight: 500;">Mark all as seen</span>
                </div>
                <div id="notifList" style="max-height: 400px; overflow-y: auto;">
                    <!-- Notifications will be loaded here -->
                    <div style="padding: 2rem; text-align: center; color: #64748b;">
                        <i class="fa-solid fa-bell-slash" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                        <p>No new notifications</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- User Profile -->
        <div class="user-profile">
            <img src="../images/avatar.png" alt="<?php echo htmlspecialchars($userName); ?>" class="avatar-sm">
            <div class="user-info">
                <span class="name"><?php echo htmlspecialchars($userName); ?></span>
                <span class="role"><?php echo htmlspecialchars($userRole); ?></span>
            </div>
        </div>
    </div>
</header>
