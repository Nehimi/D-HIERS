// Admin Dashboard Logic - Enhanced with Real-Time Data
document.addEventListener('DOMContentLoaded', function () {
    console.log('Admin Dashboard Enhanced - Loading Real Data...');

    // Initialize dashboard
    loadDashboardStats();
    initializeSearch();
    initializeQuickActions();
    initializeRefresh();

    // Auto-refresh every 30 seconds
    setInterval(loadDashboardStats, 30000);
});

// Load Dashboard Statistics from Backend
async function loadDashboardStats() {
    try {
        const response = await fetch('api/dashboard_stats.php');
        const data = await response.json();

        if (data.success) {
            updateStatCards(data.stats);
            updateRecentUsers(data.recentUsers);
            console.log('Dashboard stats loaded successfully:', data);
        } else {
            console.error('Failed to load dashboard stats');
        }
    } catch (error) {
        console.error('Error fetching dashboard stats:', error);
    }
}

// Update Stat Cards with Real Data
function updateStatCards(stats) {
    // Total Users
    const totalUsersEl = document.querySelector('.stat-card:nth-child(1) .number');
    if (totalUsersEl) {
        animateCounter(totalUsersEl, parseInt(totalUsersEl.textContent) || 0, stats.totalUsers);
    }

    // Active HEWs
    const activeHEWsEl = document.querySelector('.stat-card:nth-child(2) .number');
    if (activeHEWsEl) {
        animateCounter(activeHEWsEl, parseInt(activeHEWsEl.textContent) || 0, stats.activeHEWs);
    }

    // Reports Today
    const reportsTodayEl = document.querySelector('.stat-card:nth-child(3) .number');
    if (reportsTodayEl) {
        animateCounter(reportsTodayEl, parseInt(reportsTodayEl.textContent) || 0, stats.reportsToday);
    }

    // System Status
    const systemStatusEl = document.querySelector('.stat-card:nth-child(4) .number');
    if (systemStatusEl) {
        systemStatusEl.textContent = stats.systemStatus;
    }
}

// Animate Counter
function animateCounter(element, start, end, duration = 1000) {
    const range = end - start;
    const increment = range / (duration / 16); // 60fps
    let current = start;

    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = Math.round(current);
    }, 16);
}

// Update Recent Users Table
function updateRecentUsers(users) {
    const tableBody = document.querySelector('#usersTableBody, .data-table tbody');
    if (!tableBody || !users || users.length === 0) return;

    // Clear existing rows except headers
    tableBody.innerHTML = '';

    users.forEach((user, index) => {
        const row = createUserRow(user, index);
        tableBody.appendChild(row);
    });
}

// Create User Row
function createUserRow(user, index) {
    const row = document.createElement('tr');
    const avatarColor = `color-${(index % 3) + 1}`;
    const initial = user.firstName ? user.firstName.charAt(0).toUpperCase() : 'U';

    // Format role for display
    const roleDisplay = formatRole(user.role);
    const roleClass = getRoleClass(user.role);

    // Format kebele for display
    const kebeleDisplay = formatKebele(user.kebele);

    // Format status
    const statusClass = user.status ? user.status.toLowerCase() : 'offline';

    row.innerHTML = `
        <td data-label="User" class="primary-cell">
            <div class="user-cell">
                <div class="avatar-xs ${avatarColor}">${initial}</div>
                <span>${user.firstName} ${user.lastName}</span>
            </div>
        </td>
        <td data-label="Role"><span class="role-tag ${roleClass}">${roleDisplay}</span></td>
        <td data-label="Kebele">${kebeleDisplay}</td>
        <td data-label="Status"><span class="status-tag ${statusClass}">${capitalizeFirst(user.status || 'Unknown')}</span></td>
        <td data-label="Last Login">Recently added</td>
    `;

    // Add hover effect
    row.style.cursor = 'pointer';
    row.addEventListener('click', () => {
        window.location.href = `user_management.php`;
    });

    return row;
}

// Initialize Global Search
function initializeSearch() {
    const searchInput = document.querySelector('.header-search input');
    if (!searchInput) return;

    let searchTimeout;

    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const searchTerm = e.target.value.trim();

        // Debounce search
        searchTimeout = setTimeout(() => {
            if (searchTerm.length >= 2) {
                performGlobalSearch(searchTerm);
            } else if (searchTerm.length === 0) {
                loadDashboardStats(); // Reload original data
            }
        }, 500);
    });

    // Enter key to search
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            const searchTerm = e.target.value.trim();
            if (searchTerm.length >= 2) {
                performGlobalSearch(searchTerm);
            }
        }
    });
}

// Perform Global Search
async function performGlobalSearch(searchTerm) {
    try {
        const response = await fetch(`api/search_users.php?search=${encodeURIComponent(searchTerm)}`);
        const data = await response.json();

        if (data.success) {
            console.log(`Found ${data.count} users`);
            console.log('No users found');
        }
    } catch (error) {
        console.error('Search error:', error);
    }
}

// Initialize Quick Actions
function initializeQuickActions() {
    const actionItems = document.querySelectorAll('.action-item');

    actionItems.forEach((item) => {
        item.addEventListener('click', function () {
            const actionText = this.querySelector('strong').textContent;

            if (actionText === 'Create Account') {
                window.location.href = 'create_account.php';
            } else {
                console.log('Feature coming soon');
            }
        });
    });

    // Handle "View All" link for user activity
    const viewAllLink = document.querySelector('.view-all');
    if (viewAllLink) {
        viewAllLink.addEventListener('click', function (e) {
            e.preventDefault();
            window.location.href = 'user_management.php';
        });
    }
}

// Initialize Refresh Functionality
function initializeRefresh() {
    // Add manual refresh button if it doesn't exist
    const header = document.querySelector('.page-header');
    if (header && !document.querySelector('#refreshDashboard')) {
        const refreshBtn = document.createElement('button');
        refreshBtn.id = 'refreshDashboard';
        refreshBtn.className = 'btn btn-secondary';
        refreshBtn.innerHTML = '<i class="fa-solid fa-rotate"></i> Refresh';
        refreshBtn.style.marginLeft = '1rem';

        refreshBtn.addEventListener('click', async () => {
            refreshBtn.disabled = true;
            refreshBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Refreshing...';

            await loadDashboardStats();

            refreshBtn.disabled = false;
            refreshBtn.innerHTML = '<i class="fa-solid fa-rotate"></i> Refresh';
        });

        // Insert before the "Add New User" button if it exists
        const addUserBtn = header.querySelector('.btn-primary');
        if (addUserBtn) {
            addUserBtn.parentNode.insertBefore(refreshBtn, addUserBtn);
        }
    }
}

// Utility Functions
function formatRole(role) {
    const roles = {
        'HEW': 'HEW',
        'hew': 'HEW',
        'coordinator': 'Coordinator',
        'Coordinator': 'Coordinator',
        'linkage': 'Linkage Focal',
        'Linkage': 'Linkage Focal',
        'hmis': 'HMIS Officer',
        'HMIS': 'HMIS Officer',
        'supervisor': 'Supervisor',
        'Supervisor': 'Supervisor',
        'admin': 'Administrator',
        'Admin': 'Administrator'
    };
    return roles[role] || role;
}

function getRoleClass(role) {
    const roleClasses = {
        'HEW': 'hew',
        'hew': 'hew',
        'coordinator': 'coord',
        'Coordinator': 'coord',
        'linkage': 'linkage',
        'Linkage': 'linkage',
        'hmis': 'hmis',
        'HMIS': 'hmis',
        'supervisor': 'supervisor',
        'Supervisor': 'supervisor',
        'admin': 'admin',
        'Admin': 'admin'
    };
    return roleClasses[role] || 'hew';
}

function formatKebele(kebele) {
    if (!kebele) return 'N/A';

    const kebeles = {
        'lich-amba': 'Lich-Amba',
        'arada': 'Arada',
        'lereba': 'Lereba',
        'phcu-hq': 'PHCU HQ'
    };
    return kebeles[kebele.toLowerCase()] || kebele;
}

function capitalizeFirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
}
