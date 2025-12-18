<?php
session_start();
include "dataBaseConnection.php";

// Check if password_resets table exists
$tableCheck = mysqli_query($dataBaseConnection, "SHOW TABLES LIKE 'password_resets'");
$tableExists = mysqli_num_rows($tableCheck) > 0;

// Get statistics
$pendingCount = 0;
$completedCount = 0;
$expiredCount = 0;
$totalCount = 0;

if ($tableExists) {
    // Pending (unused and not expired)
    $pendingQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as count FROM password_resets WHERE used = 0 AND expires_at > NOW()");
    $pendingCount = mysqli_fetch_assoc($pendingQuery)['count'];
    
    // Completed (used)
    $completedQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as count FROM password_resets WHERE used = 1");
    $completedCount = mysqli_fetch_assoc($completedQuery)['count'];
    
    // Expired (not used but expired)
    $expiredQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as count FROM password_resets WHERE used = 0 AND expires_at <= NOW()");
    $expiredCount = mysqli_fetch_assoc($expiredQuery)['count'];
    
    $totalCount = $pendingCount + $completedCount + $expiredCount;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Management | D-HEIRS Admin</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="css/admin.css">
    
    <style>
        /* Page Specific Styles */
        .reset-management {
            padding: 24px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }
        
        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .page-header h1 i {
            color: #667eea;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }
        
        .stat-card.pending::before { background: #f5a623; }
        .stat-card.completed::before { background: #38ef7d; }
        .stat-card.expired::before { background: #ff416c; }
        .stat-card.total::before { background: #667eea; }
        
        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        
        .stat-card.pending .stat-icon { background: rgba(245, 166, 35, 0.15); color: #f5a623; }
        .stat-card.completed .stat-icon { background: rgba(56, 239, 125, 0.15); color: #38ef7d; }
        .stat-card.expired .stat-icon { background: rgba(255, 65, 108, 0.15); color: #ff416c; }
        .stat-card.total .stat-icon { background: rgba(102, 126, 234, 0.15); color: #667eea; }
        
        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 4px;
        }
        
        .stat-card .stat-label {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
        }
        
        /* Table Container */
        .table-container {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            overflow: hidden;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            flex-wrap: wrap;
            gap: 16px;
        }
        
        .table-header h2 {
            font-size: 18px;
            font-weight: 600;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .filter-tabs {
            display: flex;
            gap: 8px;
        }
        
        .filter-tab {
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: transparent;
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .filter-tab:hover {
            border-color: rgba(102, 126, 234, 0.5);
            color: #fff;
        }
        
        .filter-tab.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-color: transparent;
            color: #fff;
        }
        
        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th {
            padding: 16px 20px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: rgba(255, 255, 255, 0.5);
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .data-table td {
            padding: 16px 20px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .data-table tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }
        
        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 600;
            color: #fff;
        }
        
        .user-details .user-name {
            font-weight: 600;
            color: #fff;
            margin-bottom: 2px;
        }
        
        .user-details .user-email {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-badge.pending {
            background: rgba(245, 166, 35, 0.15);
            color: #f5a623;
        }
        
        .status-badge.completed {
            background: rgba(56, 239, 125, 0.15);
            color: #38ef7d;
        }
        
        .status-badge.expired {
            background: rgba(255, 65, 108, 0.15);
            color: #ff416c;
        }
        
        /* Action Buttons */
        .action-btns {
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .action-btn.view {
            background: rgba(102, 126, 234, 0.15);
            color: #667eea;
        }
        
        .action-btn.resend {
            background: rgba(56, 239, 125, 0.15);
            color: #38ef7d;
        }
        
        .action-btn.delete {
            background: rgba(255, 65, 108, 0.15);
            color: #ff416c;
        }
        
        .action-btn:hover {
            transform: scale(1.1);
        }
        
        /* Token Display */
        .token-cell {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            background: rgba(0, 0, 0, 0.3);
            padding: 6px 10px;
            border-radius: 6px;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        /* Time Display */
        .time-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        
        .time-info .time {
            font-weight: 500;
            color: #fff;
        }
        
        .time-info .date {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.5);
        }
        
        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
        }
        
        .modal-overlay.show {
            display: flex;
        }
        
        .modal {
            background: #1a1a2e;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .modal-header h3 {
            font-size: 18px;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .modal-close {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: none;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-body {
            padding: 24px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
        }
        
        .detail-value {
            color: #fff;
            font-weight: 500;
            font-size: 14px;
            text-align: right;
        }
        
        .reset-link-box {
            background: rgba(102, 126, 234, 0.1);
            border: 1px solid rgba(102, 126, 234, 0.3);
            border-radius: 10px;
            padding: 16px;
            margin-top: 16px;
        }
        
        .reset-link-box label {
            display: block;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 8px;
        }
        
        .reset-link-box .link-input {
            display: flex;
            gap: 8px;
        }
        
        .reset-link-box input {
            flex: 1;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 10px;
            color: #fff;
            font-size: 12px;
        }
        
        .reset-link-box .copy-btn {
            padding: 10px 16px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 8px;
            color: #fff;
            cursor: pointer;
            font-size: 12px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255, 255, 255, 0.5);
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.3;
        }
        
        .empty-state h3 {
            font-size: 18px;
            color: #fff;
            margin-bottom: 8px;
        }
        
        /* Back Button */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .data-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body class="dashboard-body">
    <main class="reset-management">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fa-solid fa-key"></i> Password Reset Management</h1>
            <a href="admin.php" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card pending">
                <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
                <div class="stat-value"><?php echo $pendingCount; ?></div>
                <div class="stat-label">Pending Requests</div>
            </div>
            <div class="stat-card completed">
                <div class="stat-icon"><i class="fa-solid fa-check"></i></div>
                <div class="stat-value"><?php echo $completedCount; ?></div>
                <div class="stat-label">Completed Resets</div>
            </div>
            <div class="stat-card expired">
                <div class="stat-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                <div class="stat-value"><?php echo $expiredCount; ?></div>
                <div class="stat-label">Expired Tokens</div>
            </div>
            <div class="stat-card total">
                <div class="stat-icon"><i class="fa-solid fa-list"></i></div>
                <div class="stat-value"><?php echo $totalCount; ?></div>
                <div class="stat-label">Total Requests</div>
            </div>
        </div>
        
        <!-- Table Section -->
        <div class="table-container">
            <div class="table-header">
                <h2><i class="fa-solid fa-table-list"></i> Reset Requests</h2>
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="all">All</button>
                    <button class="filter-tab" data-filter="pending">Pending</button>
                    <button class="filter-tab" data-filter="completed">Completed</button>
                    <button class="filter-tab" data-filter="expired">Expired</button>
                </div>
            </div>
            
            <?php if (!$tableExists): ?>
            <div class="empty-state">
                <i class="fa-solid fa-database"></i>
                <h3>No Data Available</h3>
                <p>The password_resets table has not been created yet. It will be automatically created when the first password reset is requested.</p>
            </div>
            <?php else: ?>
            
            <table class="data-table" id="resetTable">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Token</th>
                        <th>Requested</th>
                        <th>Expires</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($dataBaseConnection, "
                        SELECT pr.*, u.first_name, u.last_name, u.userId, u.phone_no 
                        FROM password_resets pr 
                        LEFT JOIN users u ON pr.user_id = u.id 
                        ORDER BY pr.created_at DESC 
                        LIMIT 50
                    ");
                    
                    if (mysqli_num_rows($query) > 0):
                        while ($row = mysqli_fetch_assoc($query)):
                            // Determine status
                            if ($row['used'] == 1) {
                                $status = 'completed';
                                $statusText = 'Completed';
                            } elseif (strtotime($row['expires_at']) < time()) {
                                $status = 'expired';
                                $statusText = 'Expired';
                            } else {
                                $status = 'pending';
                                $statusText = 'Pending';
                            }
                            
                            $initials = strtoupper(substr($row['first_name'] ?? 'U', 0, 1) . substr($row['last_name'] ?? 'N', 0, 1));
                            $createdDate = date('M d, Y', strtotime($row['created_at']));
                            $createdTime = date('h:i A', strtotime($row['created_at']));
                            $expiresDate = date('M d, Y', strtotime($row['expires_at']));
                            $expiresTime = date('h:i A', strtotime($row['expires_at']));
                    ?>
                    <tr data-status="<?php echo $status; ?>">
                        <td>
                            <div class="user-info">
                                <div class="user-avatar"><?php echo $initials; ?></div>
                                <div class="user-details">
                                    <div class="user-name"><?php echo htmlspecialchars(($row['first_name'] ?? 'Unknown') . ' ' . ($row['last_name'] ?? '')); ?></div>
                                    <div class="user-email"><?php echo htmlspecialchars($row['email']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="token-cell" title="<?php echo htmlspecialchars($row['token']); ?>">
                                <?php echo substr($row['token'], 0, 12); ?>...
                            </div>
                        </td>
                        <td>
                            <div class="time-info">
                                <span class="time"><?php echo $createdTime; ?></span>
                                <span class="date"><?php echo $createdDate; ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="time-info">
                                <span class="time"><?php echo $expiresTime; ?></span>
                                <span class="date"><?php echo $expiresDate; ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $status; ?>">
                                <i class="fa-solid fa-<?php echo $status === 'completed' ? 'check-circle' : ($status === 'expired' ? 'clock' : 'hourglass-half'); ?>"></i>
                                <?php echo $statusText; ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-btns">
                                <button class="action-btn view" title="View Details" onclick="viewDetails(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <?php if ($status === 'pending'): ?>
                                <button class="action-btn resend" title="Resend Link" onclick="resendLink('<?php echo $row['email']; ?>')">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </button>
                                <?php endif; ?>
                                <button class="action-btn delete" title="Delete" onclick="deleteRequest(<?php echo $row['id']; ?>)">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fa-solid fa-inbox"></i>
                                <h3>No Password Reset Requests</h3>
                                <p>There are no password reset requests to display.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- View Details Modal -->
    <div class="modal-overlay" id="detailsModal">
        <div class="modal">
            <div class="modal-header">
                <h3><i class="fa-solid fa-circle-info"></i> Request Details</h3>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <span class="detail-label">User Name</span>
                    <span class="detail-value" id="modalUserName">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email</span>
                    <span class="detail-value" id="modalEmail">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">User ID</span>
                    <span class="detail-value" id="modalUserId">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Phone</span>
                    <span class="detail-value" id="modalPhone">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Requested At</span>
                    <span class="detail-value" id="modalCreatedAt">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Expires At</span>
                    <span class="detail-value" id="modalExpiresAt">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value" id="modalStatus">-</span>
                </div>
                
                <div class="reset-link-box" id="resetLinkBox">
                    <label>Reset Link (for manual delivery)</label>
                    <div class="link-input">
                        <input type="text" id="modalResetLink" readonly>
                        <button class="copy-btn" onclick="copyResetLink()">
                            <i class="fa-solid fa-copy"></i> Copy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Filter functionality
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Update active tab
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const filter = this.dataset.filter;
                const rows = document.querySelectorAll('#resetTable tbody tr[data-status]');
                
                rows.forEach(row => {
                    if (filter === 'all' || row.dataset.status === filter) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
        
        // View details modal
        function viewDetails(data) {
            document.getElementById('modalUserName').textContent = (data.first_name || 'Unknown') + ' ' + (data.last_name || '');
            document.getElementById('modalEmail').textContent = data.email || '-';
            document.getElementById('modalUserId').textContent = data.userId || '-';
            document.getElementById('modalPhone').textContent = data.phone_no || '-';
            document.getElementById('modalCreatedAt').textContent = data.created_at || '-';
            document.getElementById('modalExpiresAt').textContent = data.expires_at || '-';
            
            // Determine status
            let status = 'Pending';
            if (data.used == 1) {
                status = 'Completed';
            } else if (new Date(data.expires_at) < new Date()) {
                status = 'Expired';
            }
            document.getElementById('modalStatus').textContent = status;
            
            // Generate reset link
            const baseUrl = window.location.origin + window.location.pathname.replace('password_reset_admin.php', '');
            const resetLink = baseUrl + 'reset_password.html?token=' + data.token;
            document.getElementById('modalResetLink').value = resetLink;
            
            // Show/hide reset link box based on status
            document.getElementById('resetLinkBox').style.display = status === 'Pending' ? 'block' : 'none';
            
            document.getElementById('detailsModal').classList.add('show');
        }
        
        function closeModal() {
            document.getElementById('detailsModal').classList.remove('show');
        }
        
        function copyResetLink() {
            const input = document.getElementById('modalResetLink');
            input.select();
            document.execCommand('copy');
            
            const btn = document.querySelector('.reset-link-box .copy-btn');
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Copied!';
            setTimeout(() => {
                btn.innerHTML = '<i class="fa-solid fa-copy"></i> Copy';
            }, 2000);
        }
        
        // Resend link
        async function resendLink(email) {
            if (!confirm('Resend password reset link to ' + email + '?')) return;
            
            try {
                const response = await fetch('api/forgot_password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'email=' + encodeURIComponent(email)
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    alert('Reset link sent successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                alert('Failed to send reset link.');
            }
        }
        
        // Delete request
        async function deleteRequest(id) {
            if (!confirm('Are you sure you want to delete this reset request?')) return;
            
            try {
                const response = await fetch('api/delete_reset_request.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + id
                });
                
                const data = await response.json();
                
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                alert('Failed to delete request.');
            }
        }
        
        // Close modal on outside click
        document.getElementById('detailsModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
</body>
</html>
