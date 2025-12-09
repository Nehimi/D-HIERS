<?php
include 'dataBaseConnection.php';
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
  <link rel="stylesheet" href="css/admin.css">
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
      <a href="admin.html" class="nav-item active">
        <i class="fa-solid fa-grid-2"></i>
        <span>Create Account</span>
      </a>
      <a href="user_management.html" class="nav-item">
        <i class="fa-solid fa-users-gear"></i>
        <span>User Management</span>
      </a>
      <a href="kebele_config.html" class="nav-item">
        <i class="fa-solid fa-map-location-dot"></i>
        <span>Kebele Config</span>
      </a>
      <a href="audit_logs.html" class="nav-item">
        <i class="fa-solid fa-file-shield"></i>
        <span>Audit Logs</span>
      </a>
      <a href="System_report.html" class="nav-item">
        <i class="fa-solid fa-chart-pie"></i>
        <span>System Reports</span>
      </a>
    </nav>
    <div class="sidebar-footer">
      <a href="index.html" class="nav-item logout">
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
        <a href="admin.html" class="btn btn-outline">
          <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>
      </div>

      <!-- Form Card -->
      <div class="form-card">
        <form action="create_account.php" method="POST" id="createAccountForm" class="account-form">

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
                <input name="emali" type="email" id="email" placeholder="user@lichamba.health.et"
                  value="<?php echo isset($user['emali']) ? $user['emali'] : ''; ?>">
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
                  <option value="lich-amba">Lich-Amba</option>
                  <option value="arada">Arada</option>
                  <option value="lereba">Lereba</option>
                  <option value="phcu-hq">PHCU Headquarters</option>
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
            <h3 class="section-title">
              <i class="fa-solid fa-lock"></i> Security Settings
            </h3>

            <?php if (!isset($_GET['edit'])): ?>
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
            <button type="button" class="btn btn-outline" onclick="window.location.href='admin.html'">
              Cancel
            </button>
            <?php if (isset($_GET['edit'])): ?>
              <button type="submit" name="update_data" class="btn btn-primary">
                <i class="fa-solid fa-pen"></i> Update Account
              </button>
            <?php else: ?>
              <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-user-plus"></i> Create Account
              </button>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>
  </main>
  <!-- <script src="js/admin/script.js"></script> -->
</body>

</html>

<?php
include 'dataBaseConnection.php';

if (isset($_GET['edit'])) {
  $id = $_GET['edit'];
  $result = mysqli_query($dataBaseConnection, "SELECT * FROM users WHERE id='$id'");
  $user = mysqli_fetch_assoc($result);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $firstName = $_POST['first_name'];
  $lastName = $_POST['last_name'];
  $email = $_POST['emali'];
  $phone = $_POST['phone_no'];
  $userId = $_POST['userId'];
  $role = $_POST['role'];
  $kebele = $_POST['kebele'];
  $status = $_POST['status'];

  $password = $_POST['password'] ?? '';
  $confirm = $_POST['confirmPassword'] ?? '';


  if (empty($password) || empty($confirm)) {
    echo "Please enter both password and confirm password.";
    exit;
  }

  if ($password !== $confirm) {
    echo "Passwords do not match!";
    exit;
  }


  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);




  $sql = "INSERT INTO users 
            (first_name, last_name, emali, phone_no, userId, role, kebele, status, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

  $stmt = $dataBaseConnection->prepare($sql);

  if (!$stmt) {
    die("Prepare failed: " . $dataBaseConnection->error);
  }

  $stmt->bind_param(
    "sssssssss",
    $firstName,
    $lastName,
    $email,
    $phone,
    $userId,
    $role,
    $kebele,
    $status,
    $hashedPassword
  );

  if ($stmt->execute()) {
    echo "<script>alert('User Created Successfully!'); window.location='create_account.php';</script>";
  } else {
    echo "Error: " . $stmt->error;
  }
} else if (isset($_POST['update_data'])) {

  $id = $_POST['id'];
  $firstName = $_POST['first_name'];
  $lastName = $_POST['last_name'];
  $email = $_POST['emali'];
  $phone = $_POST['phone_no'];
  $userId = $_POST['userId'];
  $role = $_POST['role'];
  $kebele = $_POST['kebele'];
  $status = $_POST['status'];

  // Fetch current user data
  $check = mysqli_query($dataBaseConnection, "SELECT * FROM users WHERE id = '$id'");
  $data = mysqli_fetch_assoc($check);

  // If new password is provided, old password must be correct
  if (!empty($_POST['new_password'])) {

    // 1. Old password incorrect → STOP update
    if (!password_verify($_POST['old_password'], $data['password'])) {
      echo "<script>alert('Old password is incorrect!'); window.history.back();</script>";
      exit();
    }

    // 2. Hash new password
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // 3. Update including password
    $update = "
            UPDATE users SET 
                first_name='$firstName',
                last_name='$lastName',
                emali='$email',
                phone_no='$phone',
                userId='$userId',
                role='$role',
                kebele='$kebele',
                status='$status',
                password='$new_password'
                password='$new_password'
            WHERE id='$id'
        ";

    mysqli_query($dataBaseConnection, $update);

    echo "<script>alert('✔ Password Updated Successfully'); window.location='user_management.php';</script>";
    exit();
  }

  // IF new_password is empty → update profile ONLY
  $update = "
        UPDATE users SET 
            first_name='$firstName',
            last_name='$lastName',
            emali='$email',
            phone_no='$phone',
            userId='$userId',
            role='$role',
            kebele='$kebele',
            status='$status'
        WHERE id='$id'
    ";

  mysqli_query($dataBaseConnection, $update);

  echo "<script>alert('✔ Profile Updated Successfully'); window.location='user_management.php';</script>";
}

?>