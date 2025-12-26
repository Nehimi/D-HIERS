<?php
session_start();
include("../../dataBaseConnection.php");
include '../includes/log_helper.php';

// =========================
// CREATE MODE - HANDLE USER CREATION
// =========================
if (isset($_POST['create_user'])) {
  $firstName = $_POST['first_name'] ?? '';
  $lastName = $_POST['last_name'] ?? '';
  $email = $_POST['email'] ?? '';
  $phone = $_POST['phone_no'] ?? '';
  $userId = $_POST['userId'] ?? '';
  $role = $_POST['role'] ?? '';
  $kebele = $_POST['kebele'] ?? '';
  $status = $_POST['status'] ?? 'pending';
  $password = $_POST['password'] ?? '';
  $confirm = $_POST['confirmPassword'] ?? '';

  // Validate required fields
  if (empty($firstName) || empty($lastName) || empty($phone) || empty($userId)) {
    if (isset($_POST['ajax_request'])) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill all required fields.']);
        exit;
    }
    echo "<script>alert('Please fill all required fields.'); window.history.back();</script>";
    exit;
  }

  // Password validation
  if (empty($password) || empty($confirm)) {
    if (isset($_POST['ajax_request'])) {
        echo json_encode(['status' => 'error', 'message' => 'Please enter both password and confirm password.']);
        exit;
    }
    echo "<script>alert('Please enter both password and confirm password.'); window.history.back();</script>";
    exit;
  }

  if ($password !== $confirm) {
    if (isset($_POST['ajax_request'])) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
        exit;
    }
    echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
    exit;
  }

  // Hash password
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  // INSERT QUERY - Clean and simple
  $sql = "INSERT INTO users (first_name, last_name, email, phone_no, userId, role, kebele, status, password)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

  $stmt = $dataBaseConnection->prepare($sql);

  if (!$stmt) {
    if (isset($_POST['ajax_request'])) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $dataBaseConnection->error]);
        exit;
    }
    echo "<script>alert('Database error: " . addslashes($dataBaseConnection->error) . "'); window.history.back();</script>";
    exit;
  }

  $stmt->bind_param("sssssssss", $firstName, $lastName, $email, $phone, $userId, $role, $kebele, $status, $hashedPassword);

  if ($stmt->execute()) {
    $insertId = $stmt->insert_id;
    logAction($dataBaseConnection, "Create User", "Created new user: $firstName $lastName ($userId)");
    
    if (isset($_POST['ajax_request'])) {
        echo json_encode(['status' => 'success', 'message' => "User created successfully! ID: $insertId", 'redirect' => 'user_management.php']);
        exit;
    }
    echo "<script>alert('✅ User created successfully! ID: $insertId'); window.location='user_management.php';</script>";
    exit;
  } else {
    $error = $stmt->error;
    if (isset($_POST['ajax_request'])) {
        echo json_encode(['status' => 'error', 'message' => "Error: $error"]);
        exit;
    }
    echo "<script>alert('❌ Error: " . addslashes($error) . "'); window.history.back();</script>";
    exit;
  }
}

// =========================
// UPDATE MODE - HANDLE USER UPDATE
// =========================
if (isset($_POST['update_data'])) {
  $id = intval($_POST['id']); // Sanitize ID as integer
  $firstName = $_POST['first_name'];
  $lastName = $_POST['last_name'];
  $email = $_POST['email'];
  $phone = $_POST['phone_no'];
  $userId = $_POST['userId'];
  $role = $_POST['role'];
  $kebele = $_POST['kebele'];
  $status = $_POST['status'];

  // Fetch current user password using prepared statement
  $checkStmt = $dataBaseConnection->prepare("SELECT * FROM users WHERE id = ?");
  $checkStmt->bind_param("i", $id);
  $checkStmt->execute();
  $checkResult = $checkStmt->get_result();
  $data = $checkResult->fetch_assoc();
  $checkStmt->close();

  // If new password is provided:
  if (!empty($_POST['new_password'])) {
    if (!password_verify($_POST['old_password'], $data['password'])) {
      if (isset($_POST['ajax_request'])) {
          echo json_encode(['status' => 'error', 'message' => 'Old password is incorrect!']);
          exit;
      }
      echo "<script>alert('Old password is incorrect!'); window.history.back();</script>";
      exit;
    }

    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Update including password - SECURE prepared statement
    $updateStmt = $dataBaseConnection->prepare("UPDATE users SET first_name=?, last_name=?, email=?, phone_no=?, userId=?, role=?, kebele=?, status=?, password=? WHERE id=?");
    $updateStmt->bind_param("sssssssssi", $firstName, $lastName, $email, $phone, $userId, $role, $kebele, $status, $new_password, $id);
    $updateStmt->execute();
    $updateStmt->close();

    logAction($dataBaseConnection, "Update User", "Updated user (with password): $userId");
    
    if (isset($_POST['ajax_request'])) {
        echo json_encode(['status' => 'success', 'message' => 'User updated successfully!', 'redirect' => 'user_management.php']);
        exit;
    }

    echo "<script>alert('User updated successfully!'); window.location='user_management.php';</script>";
    exit;
  }

  // Update without password - SECURE prepared statement
  $updateStmt = $dataBaseConnection->prepare("UPDATE users SET first_name=?, last_name=?, email=?, phone_no=?, userId=?, role=?, kebele=?, status=? WHERE id=?");
  $updateStmt->bind_param("ssssssssi", $firstName, $lastName, $email, $phone, $userId, $role, $kebele, $status, $id);
  $updateStmt->execute();
  $updateStmt->close();

  logAction($dataBaseConnection, "Update User", "Updated user details: $userId");
  
  if (isset($_POST['ajax_request'])) {
       echo json_encode(['status' => 'success', 'message' => 'User updated successfully!', 'redirect' => 'user_management.php']);
       exit;
  }

  echo "<script>alert('User updated successfully!'); window.location='user_management.php';</script>";
  exit;
}

// =========================
// LOAD USER DATA FOR EDIT MODE
// =========================
$user = null;
if (isset($_GET['edit'])) {
  $id = $_GET['edit'];
  $result = mysqli_query($dataBaseConnection, "SELECT * FROM users WHERE id='$id'");
  $user = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Create Account | D-HEIRS</title>
  <link rel="stylesheet" href="../css/admin.css">
  <link rel="stylesheet" href="../../css/logout.css">
</head>

<body class="dashboard-body">
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-header">
      <div class="brand-icon">
        <img src="images/logo.png" alt="">
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
      <a href="create_account.php" class="nav-item active">
        <i class="fa-solid fa-user-plus"></i>
        <span>Create Account</span>
      </a>
      <a href="user_management.php" class="nav-item">
        <i class="fa-solid fa-users-gear"></i>
        <span>User Management</span>
      </a>
      <a href="kebele_config.php" class="nav-item">
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
    <Header class="dashboard-header">
      <div class="header-search">
        <i class="fa-solid fa-search"></i>
        <input type="text" placeholder="search users,logs,or setting...">
      </div>
      <div class="header-actions">
        <button class="icon-btn">
          <i class="fa-solid fa-bell"></i>
          <span class="badge-dot"></span>
        </button>
        <div class="user-profile">
          <img src="images/avatar.png" alt="Admin" class="avatar-sm">
          <div class="user-info">
            <span class="name">Dr. Admin</span>
            <span class="role">System Administrator</span>
          </div>
        </div>
      </div>
    </Header>
    <!-- Form Content -->
    <div class="content-wrapper">
      <div class="page-header">
        <div>
          <h1>Create New Account</h1>
          <p class="page-subtitle">Register a new user in the D-HEIRS system</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline">
          <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>
      </div>

      <!-- Form Card -->
      <div class="form-card">
        <form action="create_account.php" method="POST" id="createAccountForm" class="account-form">
          <div id="formMessage" class="message-container"></div>

          <!-- Personal Information Section -->
          <div class="form-section">
            <h3 class="section-title">
              <i class="fa-solid fa-user"></i> Personal Information
            </h3>
            <div class="form-row">
              <div class="form-group">
                <label for="firstName">First Name <span class="required">*</span></label>
                <input name="first_name" type="text" id="firstName" placeholder="e.g., Sara" required
                  value="<?php echo isset($user['first_name']) ? $user['first_name'] : ''; ?>">
              </div>
              <div class="form-group">
                <label for="lastName">Last Name <span class="required">*</span></label>
                <input name="last_name" type="text" id="lastName" placeholder="e.g., Tadesse" required
                  value="<?php echo isset($user['last_name']) ? $user['last_name'] : ''; ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="email">Email Address</label>
                <input name="email" type="email" id="email" placeholder="user@lichamba.health.et"
                  value="<?php echo isset($user['email']) ? $user['email'] : ''; ?>">
              </div>
              <div class="form-group">
                <label for="phone">Phone Number <span class="required">*</span></label>
                <input name="phone_no" type="tel" id="phone" placeholder="+251 " required
                  value="<?php echo isset($user['phone_no']) ? $user['phone_no'] : ''; ?>">
              </div>
            </div>
          </div>

          <!-- Account Details Section -->
          <div class="form-section">
            <h3 class="section-title">
              <i class="fa-solid fa-id-card"></i> Account Details
            </h3>
            <div class="form-row">
              <div class="form-group">
                <label for="userId">User ID <span class="required">*</span></label>
                <input name="userId" type="text" id="userId" placeholder="e.g., HEW-001" required
                  value="<?php echo isset($user['userId']) ? $user['userId'] : ''; ?>">
                <small class="help-text">Format: ROLE-XXX (e.g., HEW-001, COORD-012)</small>
              </div>
              <div class="form-group">
                <label for="role">Role <span class="required">*</span></label>
                <select name="role" id="role" required value="<?php echo isset($user['role']) ? $user['role'] : ''; ?>">
                  <option value="">Select Role</option>
                  <option value="hew">Health Extension Worker (HEW)</option>
                  <option value="coordinator">HEW Coordinator</option>
                  <option value="linkage">Linkage Focal Person</option>
                  <option value="hmis">HMIS Officer</option>
                  <option value="supervisor">Supervisor</option>
                  <option value="admin">Administrator</option>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="kebele">Assigned Kebele <span class="required">*</span></label>
                <select name="kebele" id="kebele" required
                  value=" <?php echo isset($user['kebele']) ? $user['kebele'] : ''; ?>">
                  <option value="">Select Kebele</option>
                  <?php
                  // Load kebeles from database
                  $kebeleQuery = mysqli_query($dataBaseConnection, "SELECT kebeleName, kebeleCode FROM kebele WHERE status='active' ORDER BY kebeleName ASC");
                  while ($kebeleRow = mysqli_fetch_assoc($kebeleQuery)) {
                    $kebeleName = htmlspecialchars($kebeleRow['kebeleName']);
                    $kebeleCode = htmlspecialchars($kebeleRow['kebeleCode']);
                    $kebeleValue = strtolower(str_replace(' ', '-', $kebeleName));
                    $selected = (isset($user['kebele']) && $user['kebele'] == $kebeleValue) ? 'selected' : '';
                    echo "<option value='$kebeleValue' $selected>$kebeleName</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="form-group">
                <label for="status">Account Status <span class="required">*</span></label>
                <select name="status" id="status" required
                  value="<?php echo isset($user['status']) ? $user['status'] : ''; ?>">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                  <option value="pending">Pending Approval</option>
                </select>
              </div>
            </div>
          </div>



          <!-- Security Section -->
          <div class="form-section">
            <?php if (!isset($_GET['edit'])): ?>
              <h3 class="section-title">
                <i class="fa-solid fa-lock"></i> Security Settings
              </h3>
              <div class="form-row">
                <div class="form-group">
                  <label for="password">Initial Password <span class="required">*</span></label>
                  <div class="input-wrapper">
                    <input name="password" type="password" id="password" placeholder="••••••••" required>
                    <button type="button" class="toggle-password">
                      <i class="fa-solid fa-eye"></i>
                    </button>
                  </div>
                  <small class="help-text">Min. 8 characters, include uppercase, lowercase, and
                    number</small>
                </div>
                <div class="form-group">
                  <label for="confirmPassword">Confirm Password <span class="required">*</span></label>
                  <div class="input-wrapper">
                    <input name="confirmPassword" type="password" id="confirmPassword" placeholder="••••••••" required>
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <?php if (isset($_GET['edit'])): ?>
              <div class="form-section">
                <h3 class="section-title">
                  <i class="fa-solid fa-lock"></i> Update Password
                </h3>

                <div class="form-row">
                  <!-- OLD PASSWORD -->
                  <div class="form-group">
                    <label for="old_password">Old Password <span class="required">*</span></label>
                    <div class="input-wrapper">
                      <input type="password" name="old_password" id="old_password" placeholder="Enter old password">
                      <button type="button" class="toggle-password">
                        <i class="fa-solid fa-eye"></i>
                      </button>
                    </div>
                  </div>

                  <!-- NEW PASSWORD -->
                  <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="input-wrapper">
                      <input type="password" name="new_password" id="new_password" placeholder="Enter new password">
                      <button type="button" class="toggle-password">
                        <i class="fa-solid fa-eye"></i>
                      </button>
                    </div>
                    <small class="help-text">Leave blank if you don't want to change password.</small>
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <?php if (isset($_GET['edit'])): ?>
              <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            <?php endif; ?>

            <div class="form-row">
              <div class="form-group checkbox-group">
                <label class="checkbox-container">
                  <input name="forcepassword" type="checkbox" id="forcePasswordChange">
                  <span class="checkmark"></span>
                  Force password change on first login
                </label>
              </div>
              <div class="form-group checkbox-group">
                <label class="checkbox-container">
                  <input name="sendEmail" type="checkbox" id="sendEmail">
                  <span class="checkmark"></span>
                  Send credentials via email
                </label>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="form-actions">
            <!-- Logout Modal Trigger Logic is handled in Sidebar, but here we just have a Cancel button -->
            <button type="button" class="btn btn-outline" onclick="window.location.href='dashboard.php'">
              Cancel
            </button>
            <?php if (isset($_GET['edit'])): ?>
              <button type="submit" name="update_data" class="btn btn-primary">
                <i class="fa-solid fa-pen"></i> Update Account
              </button>
            <?php else: ?>
              <button name="create_user" type="submit" class="btn btn-primary">
                <i class="fa-solid fa-user-plus"></i> Create Account
              </button>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>
  </main>
  <script src="../../js/logout.js"></script>
    <script src="../js/script.js?v=<?php echo time(); ?>"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
        const accountForm = document.getElementById('createAccountForm');
        const messageContainer = document.getElementById('formMessage');

        if (accountForm) {
            accountForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                formData.append('ajax_request', '1');
                
                // Determine action type
                if (this.querySelector('input[name="id"]')) {
                    formData.append('update_data', 'true');
                } else {
                    formData.append('create_user', 'true');
                }

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
                submitBtn.disabled = true;

                fetch('create_account.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        messageContainer.innerHTML = `<div class="success-message" style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center;">${data.message}</div>`;
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1000);
                    } else {
                        messageContainer.innerHTML = `<div class="error-message" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center;">${data.message}</div>`;
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;
                        messageContainer.scrollIntoView({ behavior: 'smooth' });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    messageContainer.innerHTML = `<div class="error-message" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center;">An unexpected error occurred.</div>`;
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                    messageContainer.scrollIntoView({ behavior: 'smooth' });
                });
            });
        }
    });
  </script>
</body>

</html>