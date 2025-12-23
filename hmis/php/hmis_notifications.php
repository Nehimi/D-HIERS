<?php
session_start();
include("../../dataBaseConnection.php");

// Check if user is logged in and has HMIS role
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'hmis') {
    header("Location: ../../index.html");
    exit();
}

$fullName = $_SESSION['full_name'] ?? 'HMIS Officer';
$userId = $_SESSION['user_db_id'] ?? 0;

// Handle Mark All as Read
if (isset($_POST['mark_all_read'])) {
    $dataBaseConnection->query("UPDATE activity_notifications SET is_read = 1 WHERE role = 'hmis' AND is_read = 0");
    header("Location: hmis_notifications.php");
    exit();
}

// Fetch Unread Notifications Count for header
$notifCount = 0;
$notifRes = $dataBaseConnection->query("SELECT COUNT(*) as cnt FROM activity_notifications WHERE role='hmis' AND is_read=0");
if ($notifRes) {
    $notifCount = $notifRes->fetch_assoc()['cnt'];
}

// Fetch All Notifications for this role
$notifications = [];
$notifListRes = $dataBaseConnection->query("SELECT * FROM activity_notifications WHERE role='hmis' ORDER BY created_at DESC LIMIT 50");
if ($notifListRes) {
    while ($row = $notifListRes->fetch_assoc()) {
        $notifications[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications | HMIS Officer</title>

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/notifications.css">
    <link rel="stylesheet" href="../../css/logout.css">
    
    <style>
        .notif-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border-left: 5px solid transparent;
            transition: all 0.3s ease;
        }
        .notif-card.unread {
            border-left-color: var(--primary);
            background: rgba(15, 118, 110, 0.02);
        }
        .notif-card.success { border-left-color: #10b981; }
        .notif-card.warning { border-left-color: #f59e0b; }
        .notif-card.info { border-left-color: #3b82f6; }
        
        .notif-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .notif-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.8rem;
        }
        
        .notif-title {
            font-weight: 700;
            font-family: 'Outfit';
            font-size: 1.1rem;
            color: var(--text-main);
        }
        
        .notif-time {
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        
        .notif-message {
            color: var(--text-muted);
            line-height: 1.5;
            margin-bottom: 1rem;
        }
        
        .mark-read-btn {
            background: none;
            border: none;
            color: var(--primary);
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            padding: 0;
        }
        
        .mark-read-btn:hover {
            text-decoration: underline;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-muted);
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            opacity: 0.2;
        }
    </style>
</head>

<body class="dashboard-body">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="brand-icon">
                <img src="../../focal/image.jpg" alt="Logo">
            </div>
            <div class="brand-text">
                D-HEIRS
                <span>HMIS Portal</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="hmis_dashboard.php" class="nav-item">
                <i class="fa-solid fa-gauge-high"></i>
                <span>Summary Dashboard</span>
            </a>
            <a href="generate_reports.php" class="nav-item">
                <i class="fa-solid fa-file-contract"></i>
                <span>Statistical Packages</span>
            </a>
            <a href="hmis_notifications.php" class="nav-item active">
                <i class="fa-solid fa-bell"></i>
                <span>System Notifications</span>
            </a>
            <a href="system_reports.php" class="nav-item">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Analytics & Reports</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="../../index.html" class="nav-item logout" id="logoutBtn">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="main-content">
        <!-- Premium Header -->
        <header class="dashboard-header">
            <div class="header-search">
                <i class="fa-solid fa-search"></i>
                <input type="text" placeholder="Filter notifications...">
            </div>
            <div class="header-actions">
                <button class="icon-btn" onclick="location.href='hmis_notifications.php'">
                    <i class="fa-solid fa-bell"></i>
                    <?php if ($notifCount > 0): ?>
                        <span class="badge-count"><?php echo $notifCount; ?></span>
                    <?php else: ?>
                        <span class="badge-dot"></span>
                    <?php endif; ?>
                </button>
                <div class="user-profile">
                    <img src="../../images/avatar.png" alt="User" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($fullName); ?>&background=0f766e&color=fff'">
                    <div class="user-info">
                        <span class="name"><?php echo htmlspecialchars($fullName); ?></span>
                        <span class="role">HMIS Officer</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-wrapper">
            <div class="page-intro" style="display: flex; justify-content: space-between; align-items: flex-end;">
                <div>
                    <h1>System Notifications</h1>
                    <p>Track all activities and data submissions from the health kebeles.</p>
                </div>
                <?php if ($notifCount > 0): ?>
                <form method="POST">
                    <button type="submit" name="mark_all_read" class="btn-primary" style="padding: 10px 20px; font-size: 0.9rem;">
                        <i class="fa-solid fa-check-double" style="margin-right: 8px;"></i>
                        Mark All as Read
                    </button>
                </form>
                <?php endif; ?>
            </div>

            <div class="notifications-container">
                <?php if (empty($notifications)): ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-bell-slash"></i>
                        <h3>No notifications yet</h3>
                        <p>When there are new data submissions or system alerts, they will appear here.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $notif): ?>
                        <div class="notif-card <?php echo $notif['is_read'] ? '' : 'unread'; ?> <?php echo $notif['type']; ?>">
                            <div class="notif-header">
                                <div class="notif-title">
                                    <?php if ($notif['type'] == 'success'): ?>
                                        <i class="fa-solid fa-circle-check" style="color: #10b981; margin-right: 8px;"></i>
                                    <?php elseif ($notif['type'] == 'warning'): ?>
                                        <i class="fa-solid fa-triangle-exclamation" style="color: #f59e0b; margin-right: 8px;"></i>
                                    <?php else: ?>
                                        <i class="fa-solid fa-circle-info" style="color: #3b82f6; margin-right: 8px;"></i>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($notif['title']); ?>
                                </div>
                                <div class="notif-time">
                                    <?php echo date('M d, H:i', strtotime($notif['created_at'])); ?>
                                </div>
                            </div>
                            <div class="notif-message">
                                <?php 
                                    // Use markdown-style bolding for names/IDs if they exist in message
                                    $msg = htmlspecialchars($notif['message']);
                                    echo str_replace(['**', 'STAT-'], ['<b>', 'STAT-'], $msg);
                                ?>
                            </div>
                            <?php if ($notif['action_url']): ?>
                                <a href="<?php echo htmlspecialchars($notif['action_url']); ?>" class="btn-primary" style="padding: 6px 15px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 5px;">
                                    View Details <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="../../js/logout.js"></script>
</body>

</html>
