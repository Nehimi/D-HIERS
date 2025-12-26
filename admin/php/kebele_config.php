<?php
session_start();
include("../../dataBaseConnection.php");
include "../includes/log_helper.php";

// =========================
// LOAD KEBELE DATA FOR EDIT
// =========================
$kebele = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $dataBaseConnection->prepare("SELECT * FROM kebele WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $kebele = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Note: kebele_configuration.css was not found in move list, assuming usage of admin.css or global style -->
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../../css/logout.css">
    <title>kebele Configuration | D-HEIRS</title>
</head>

<body class="dashboard-body">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="brand-icon">
                <img src="../../images/logo.png" alt="">
            </div>
            <div class="brand-text">
                D-HEIRS
                <span>Admin Portal</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item">
                <i class="fa-solid fa-grid-2"></i>
                <span>Dashboard</span>
            </a>
            <a href="user_management.php" class="nav-item">
                <i class="fa-solid fa-users-gear"></i>
                <span>User Management</span>
            </a>
            <a href="kebele_config.php" class="nav-item active">
                <i class="fa-solid fa-map-location-dot"></i>
                <span>Kebele Config</span>
            </a>
            <a href="audit_logs.php" class="nav-item">
                <i class="fa-solid fa-file-shield"></i>
                <span>Audit Logs</span>
            </a>
            <a href="system_reports.php" class="nav-item">
                <i class="fa-solid fa-chart-pie"></i>
                <span>System Reports</span>
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
        <!-- Top Header -->
        <header class="dashboard-header">
            <div class="header-search">
                <i class="fa-solid fa-search"></i>
                <input type="text" placeholder="Search settings...">
            </div>

            <div class="header-actions">
                <a href="messages.html" class="icon-btn">
                    <i class="fa-solid fa-bell"></i>
                    <span class="badge-dot"></span>
                </a>
                <a href="admin_profile.html" class="user-profile" style="cursor: pointer; text-decoration: none;">
                    <img src="../../images/avatar.png" alt="Admin" class="avatar-sm">
                    <div class="user-info">
                        <span class="name">Dr. Admin</span>
                        <span class="role">System Administrator</span>
                    </div>
                </a>
            </div>
        </header>

        <!-- Form Content -->
        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h1><?php echo $kebele ? 'Edit Kebele' : 'Add New Kebele'; ?></h1>
                    <p class="page-subtitle"><?php echo $kebele ? 'Update existing administrative Kebele unit' : 'Register a new administrative Kebele unit'; ?></p>
                </div>
                <a href="kebele_config.html" class="btn btn-outline">
                    <i class="fa-solid fa-arrow-left"></i> Back to Config
                </a>
            </div>

            <!-- Form Card -->
            <div class="form-card">
                <form action="kebele_config.php" method="POST" id="addKebeleForm" class="account-form">

                    <!-- Basic Information Section -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fa-solid fa-map-location"></i> Kebele Information
                        </h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="kebeleName">Kebele Name <span class="required">*</span></label>
                                <input name="kebeleName" type="text" id="kebeleName" placeholder="e.g., Lich-Amba"
                                    required value="<?php echo $kebele['kebeleName'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="kebeleCode">Kebele Code <span class="required">*</span></label>
                                <input name="kebeleCode" type="text" id="kebeleCode" placeholder="e.g., KB-005"
                                    required value="<?php echo $kebele['kebeleCode'] ?? ''; ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="woreda">Woreda / District</label>
                                <input name="woreda" type="text" id="woreda" placeholder="e.g., Libo Kemkem">
                            </div>
                            <div class="form-group">
                                <label for="zone">Zone</label>
                                <input name="zone" type="text" id="zone" placeholder="e.g., South Gondar">
                            </div>
                        </div>
                    </div>

                    <!-- Statistics & Settings Section -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fa-solid fa-chart-simple"></i> Demographics & Settings
                        </h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="population">Total Population <span class="required">*</span></label>
                                <input name="population" type="number" id="population" placeholder="e.g., 5000"
                                    required value="<?php echo $kebele['population'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="households">Estimated Households</label>
                                <input name="households" type="number" id="households" placeholder="e.g., 1200"
                                 value="<?php echo $kebele['households'] ?? ''; ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="healthPostName">Health Post Name</label>
                                <input name="healthPostName" type="text" id="healthPostName"
                                    placeholder="e.g., Lich-Amba Health Post">
                            </div>
                            <div class="form-group">
                                <label for="status">Status <span class="required">*</span></label>
                                <select name="status" id="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="planning">Planning Phase</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="button" class="btn btn-outline"
                            onclick="window.location.href='system_configuration.html'">
                            Cancel
                        </button>
                        <button name="<?php echo $kebele ? 'update_kebele' : 'kebeleReg'; ?>" type="submit" class="btn btn-primary">
                            <i class="fa-solid <?php echo $kebele ? 'fa-save' : 'fa-plus'; ?>"></i> 
                            <?php echo $kebele ? 'Update Kebele' : 'Add Kebele'; ?>
                        </button>
                    </div>
                    <?php if ($kebele): ?>
                    <input type="hidden" name="id" value="<?php echo $kebele['id']; ?>">
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </main>
    <script src="../../js/logout.js"></script>
</body>

</html>

<?php
// Database connection already included at top of file
if (isset($_POST['kebeleReg'])) {
    $kebeleName = $_POST['kebeleName'];
    $kebeleCode = $_POST['kebeleCode'];
    $woreda = $_POST['woreda'] ?? '';
    $zone = $_POST['zone'] ?? '';
    $population = $_POST['population'];
    $households = $_POST['households'] ?? 0;
    $healthPostName = $_POST['healthPostName'] ?? '';
    $status = $_POST['status'];

    $sql = "INSERT INTO kebele (kebeleName, kebeleCode, woreda, zone, population, households, healthPostName, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $dataBaseConnection->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssiiss", $kebeleName, $kebeleCode, $woreda, $zone, $population, $households, $healthPostName, $status);
        if ($stmt->execute()) {
            logAction($dataBaseConnection, "Create Kebele", "Created new Kebele: $kebeleName ($kebeleCode)");
            echo "<script>alert('Kebele Added Successfully!'); window.location='user_management.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Handle Update
if (isset($_POST['update_kebele'])) {
    $id = $_POST['id'];
    $kebeleName = $_POST['kebeleName'];
    $kebeleCode = $_POST['kebeleCode'];
    $woreda = $_POST['woreda'];
    $zone = $_POST['zone'];
    $population = $_POST['population'];
    $households = $_POST['households'];
    $healthPostName = $_POST['healthPostName'];
    $status = $_POST['status'];

    $sql = "UPDATE kebele SET kebeleName=?, kebeleCode=?, woreda=?, zone=?, population=?, households=?, healthPostName=?, status=? WHERE id=?";
    $stmt = $dataBaseConnection->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ssssiissi", $kebeleName, $kebeleCode, $woreda, $zone, $population, $households, $healthPostName, $status, $id);
        if ($stmt->execute()) {
            logAction($dataBaseConnection, "Update Kebele", "Updated Kebele ID $id: $kebeleName");
            echo "<script>alert('Kebele Updated Successfully!'); window.location='user_management.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>