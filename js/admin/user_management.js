// User Management Page JavaScript

document.addEventListener('DOMContentLoaded', () => {
    // Initialize the page
    loadAllUsers();
    updateCounts();
    setupFilters();
    setupBulkActions();
    setupSearch();

    // Refresh button
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', () => {
            loadAllUsers();
            updateCounts();
            showNotification('Users list refreshed!');
        });
    }

    // Export button
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', exportToCSV);
    }
});

// Load all users from localStorage
function loadAllUsers() {
    const users = JSON.parse(localStorage.getItem('dheirs_users')) || [];
    const usersTableBody = document.getElementById('usersTableBody');

    if (!usersTableBody) return;

    // Keep the default 3 rows, add new users before them
    const defaultRows = Array.from(usersTableBody.children).slice(0, 3);
    usersTableBody.innerHTML = '';

    // Add new users
    users.forEach((user, index) => {
        const row = createUserManagementRow(user, index);
        usersTableBody.appendChild(row);
    });

    // Add back default rows
    defaultRows.forEach(row => usersTableBody.appendChild(row));

    // Update showing info
    updateShowingInfo();
}

// Create a user row for the management table
function createUserManagementRow(user, index) {
    const row = document.createElement('tr');

    const roleInfo = getRoleInfo(user.role);
    let statusClass = 'offline';
    if (user.status === 'active') statusClass = 'active';
    else if (user.status === 'pending') statusClass = 'pending';

    const avatarColor = `color-${(index % 3) + 1}`;
    const initial = user.firstName.charAt(0).toUpperCase();
    const timeAgo = getTimeAgo(user.createdAt);

    row.innerHTML = `
        <td><input type="checkbox" class="row-checkbox"></td>
        <td>
            <div class="user-cell">
                <div class="avatar-xs ${avatarColor}">${initial}</div>
                <div>
                    <div class="user-name">${user.firstName} ${user.lastName}</div>
                    <div class="user-id">${user.userId || 'N/A'}</div>
                </div>
            </div>
        </td>
        <td>${user.email}</td>
        <td>${user.phone || 'N/A'}</td>
        <td><span class="role-tag ${roleInfo.class}">${roleInfo.name}</span></td>
        <td>${getKebeleDisplay(user.kebele)}</td>
        <td><span class="status-tag ${statusClass} clickable-status" data-user-index="${index}" title="Click to change status">${user.status.charAt(0).toUpperCase() + user.status.slice(1)}</span></td>
        <td>${timeAgo}</td>
        <td>
            <div class="action-buttons">
                <button class="btn-icon" title="Edit" onclick="editUser(${index})">
                    <i class="fa-solid fa-pen"></i>
                </button>
                <button class="btn-icon btn-danger" title="Delete" onclick="deleteUser(${index})">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </td>
    `;

    // Add click event to status tag
    const statusTag = row.querySelector('.status-tag.clickable-status');
    if (statusTag) {
        statusTag.addEventListener('click', function () {
            toggleUserStatus(index, this);
            setTimeout(() => {
                updateCounts();
                applyFilters();
            }, 100);
        });
    }

    return row;
}

// Setup filters
function setupFilters() {
    const roleFilter = document.getElementById('roleFilter');
    const statusFilter = document.getElementById('statusFilter');
    const kebeleFilter = document.getElementById('kebeleFilter');

    if (roleFilter) roleFilter.addEventListener('change', applyFilters);
    if (statusFilter) statusFilter.addEventListener('change', applyFilters);
    if (kebeleFilter) kebeleFilter.addEventListener('change', applyFilters);
}

// Apply filters to the table
function applyFilters() {
    const roleFilter = document.getElementById('roleFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const kebeleFilter = document.getElementById('kebeleFilter').value;
    const searchTerm = document.getElementById('globalSearch').value.toLowerCase();

    const rows = document.querySelectorAll('#usersTableBody tr');
    let visibleCount = 0;

    rows.forEach(row => {
        const roleTag = row.querySelector('.role-tag');
        const statusTag = row.querySelector('.status-tag');
        const kebeleCell = row.cells[5];
        const userCell = row.querySelector('.user-name');
        const emailCell = row.cells[2];

        if (!roleTag || !statusTag) return;

        const role = roleTag.textContent.toLowerCase();
        const status = statusTag.textContent.toLowerCase();
        const kebele = kebeleCell ? kebeleCell.textContent : '';
        const userName = userCell ? userCell.textContent.toLowerCase() : '';
        const email = emailCell ? emailCell.textContent.toLowerCase() : '';

        let show = true;

        // Role filter
        if (roleFilter !== 'all') {
            const roleMatch = role.includes(roleFilter.toLowerCase()) ||
                role.includes(getRoleInfo(roleFilter).name.toLowerCase());
            if (!roleMatch) show = false;
        }

        // Status filter
        if (statusFilter !== 'all' && !status.includes(statusFilter)) {
            show = false;
        }

        // Kebele filter
        if (kebeleFilter !== 'all' && !kebele.includes(getKebeleDisplay(kebeleFilter))) {
            show = false;
        }

        // Search filter
        if (searchTerm && !userName.includes(searchTerm) && !email.includes(searchTerm)) {
            show = false;
        }

        row.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });

    updateShowingInfo(visibleCount);
}

// Setup search
function setupSearch() {
    const searchInput = document.getElementById('globalSearch');
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }
}

// Setup bulk actions
function setupBulkActions() {
    const selectAll = document.getElementById('selectAll');
    const bulkPanel = document.getElementById('bulkActionsPanel');

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => {
                if (cb.closest('tr').style.display !== 'none') {
                    cb.checked = this.checked;
                }
            });
            updateBulkPanel();
        });
    }

    // Listen to individual checkboxes
    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('row-checkbox')) {
            updateBulkPanel();
        }
    });
}

// Update bulk actions panel
function updateBulkPanel() {
    const checkboxes = document.querySelectorAll('.row-checkbox:checked');
    const bulkPanel = document.getElementById('bulkActionsPanel');
    const selectedCount = document.getElementById('selectedCount');

    if (checkboxes.length > 0) {
        bulkPanel.style.display = 'flex';
        selectedCount.textContent = checkboxes.length;
    } else {
        bulkPanel.style.display = 'none';
    }
}

// Update counts
function updateCounts() {
    const users = JSON.parse(localStorage.getItem('dheirs_users')) || [];
    const totalCount = users.length + 3; // +3 for default users
    const activeCount = users.filter(u => u.status === 'active').length + 2; // +2 default active

    const totalEl = document.getElementById('totalCount');
    const activeEl = document.getElementById('activeCount');

    if (totalEl) totalEl.textContent = totalCount;
    if (activeEl) activeEl.textContent = activeCount;
}

// Update showing info
function updateShowingInfo(visibleCount) {
    const rows = document.querySelectorAll('#usersTableBody tr');
    const total = rows.length;
    const visible = visibleCount !== undefined ? visibleCount : total;

    const showingStart = document.getElementById('showingStart');
    const showingEnd = document.getElementById('showingEnd');
    const showingTotal = document.getElementById('showingTotal');

    if (showingStart) showingStart.textContent = visible > 0 ? 1 : 0;
    if (showingEnd) showingEnd.textContent = visible;
    if (showingTotal) showingTotal.textContent = total;
}

// Edit user function
function editUser(index) {
    const users = JSON.parse(localStorage.getItem('dheirs_users')) || [];
    const user = users[index];

    if (!user) return;

    // Store user data for editing
    localStorage.setItem('edit_user_index', index);
    localStorage.setItem('edit_user_data', JSON.stringify(user));

    // Redirect to create account page (which can be used for editing)
    alert(`Edit functionality for ${user.firstName} ${user.lastName} will be implemented soon!`);
}

// Delete user function
function deleteUser(index) {
    const users = JSON.parse(localStorage.getItem('dheirs_users')) || [];
    const user = users[index];

    if (!user) return;

    const confirmed = confirm(`Are you sure you want to delete ${user.firstName} ${user.lastName}?`);

    if (confirmed) {
        users.splice(index, 1);
        localStorage.setItem('dheirs_users', JSON.stringify(users));
        loadAllUsers();
        updateCounts();
        showNotification('User deleted successfully!', 'success');
    }
}

// Export to CSV
function exportToCSV() {
    const users = JSON.parse(localStorage.getItem('dheirs_users')) || [];

    if (users.length === 0) {
        alert('No users to export!');
        return;
    }

    let csv = 'First Name,Last Name,Email,Phone,User ID,Role,Kebele,Status,Created At\n';

    users.forEach(user => {
        csv += `${user.firstName},${user.lastName},${user.email},${user.phone || ''},${user.userId || ''},${user.role},${user.kebele},${user.status},${user.createdAt}\n`;
    });

    // Create download link
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `dheirs_users_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);

    showNotification('Users exported successfully!', 'success');
}
