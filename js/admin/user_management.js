// User Management Enhanced JavaScript - Real-time Filtering and Search
document.addEventListener('DOMContentLoaded', function () {
    console.log('User Management Enhanced - Loading...');

    // Initialize all functionality
    initializeFilters();
    initializeSearch();
    initializeTableActions();
    initializeBulkActions();
    updateUserCounts();

    // Auto-update counts when page loads
    setTimeout(updateUserCounts, 500);
});

// Initialize Filter Functionality
function initializeFilters() {
    const roleFilter = document.getElementById('roleFilter');
    const statusFilter = document.getElementById('statusFilter');
    const kebeleFilter = document.getElementById('kebeleFilter');

    if (roleFilter) {
        roleFilter.addEventListener('change', applyFilters);
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
    }

    if (kebeleFilter) {
        kebeleFilter.addEventListener('change', applyFilters);
    }
}

// Initialize Search Functionality
function initializeSearch() {
    const searchInput = document.querySelector('.header-search input');
    if (!searchInput) return;

    let searchTimeout;

    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const searchTerm = e.target.value.trim().toLowerCase();

        // Debounce search
        searchTimeout = setTimeout(() => {
            applyFilters();
        }, 300);
    });

    // Enter key to search
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            applyFilters();
        }
    });
}

// Apply All Filters
function applyFilters() {
    const searchInput = document.querySelector('.header-search input');
    const searchTerm = searchInput ? searchInput.value.trim().toLowerCase() : '';

    const roleFilter = document.getElementById('roleFilter');
    const statusFilter = document.getElementById('statusFilter');
    const kebeleFilter = document.getElementById('kebeleFilter');

    const selectedRole = roleFilter ? roleFilter.value : 'all';
    const selectedStatus = statusFilter ? statusFilter.value : 'all';
    const selectedKebele = kebeleFilter ? kebeleFilter.value : 'all';

    const tableRows = document.querySelectorAll('#usersTableBody tr');
    let visibleCount = 0;
    let activeCount = 0;

    tableRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length === 0) return;

        // Get row data
        const userId = cells[1] ? cells[1].textContent.trim().toLowerCase() : '';
        const fullName = cells[2] ? cells[2].textContent.trim().toLowerCase() : '';
        const email = cells[3] ? cells[3].textContent.trim().toLowerCase() : '';
        const phone = cells[4] ? cells[4].textContent.trim().toLowerCase() : '';
        const role = cells[5] ? cells[5].textContent.trim().toLowerCase() : '';
        const kebele = cells[6] ? cells[6].textContent.trim().toLowerCase() : '';
        const status = cells[7] ? cells[7].textContent.trim().toLowerCase() : '';

        // Check search match
        let matchesSearch = true;
        if (searchTerm) {
            matchesSearch = userId.includes(searchTerm) ||
                fullName.includes(searchTerm) ||
                email.includes(searchTerm) ||
                phone.includes(searchTerm);
        }

        // Check role filter
        let matchesRole = selectedRole === 'all' || role.includes(selectedRole.toLowerCase());

        // Check status filter
        let matchesStatus = selectedStatus === 'all' || status === selectedStatus;

        // Check kebele filter
        let matchesKebele = selectedKebele === 'all' || kebele.includes(selectedKebele.toLowerCase());

        // Show/hide row
        if (matchesSearch && matchesRole && matchesStatus && matchesKebele) {
            row.style.display = '';
            visibleCount++;
            if (status === 'active') activeCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Update counts
    updateFilterCounts(visibleCount, activeCount);
}

// Update Filter Counts
function updateFilterCounts(total, active) {
    const totalCountEl = document.getElementById('totalCount');
    const activeCountEl = document.getElementById('activeCount');

    if (totalCountEl) {
        totalCountEl.textContent = total;
    }

    if (activeCountEl) {
        activeCountEl.textContent = active;
    }
}

// Update User Counts from Table
function updateUserCounts() {
    const tableRows = document.querySelectorAll('#usersTableBody tr');
    let totalCount = 0;
    let activeCount = 0;

    tableRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length === 0) return;

        totalCount++;
        const status = cells[7] ? cells[7].textContent.trim().toLowerCase() : '';
        if (status === 'active') {
            activeCount++;
        }
    });

    updateFilterCounts(totalCount, activeCount);

    // Update showing info
    const showingStartEl = document.getElementById('showingStart');
    const showingEndEl = document.getElementById('showingEnd');
    const showingTotalEl = document.getElementById('showingTotal');

    if (showingStartEl) showingStartEl.textContent = totalCount > 0 ? '1' : '0';
    if (showingEndEl) showingEndEl.textContent = totalCount;
    if (showingTotalEl) showingTotalEl.textContent = totalCount;
}

// Initialize Table Actions
function initializeTableActions() {
    // Refresh Button
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', () => {
            window.location.reload();
        });
    }

    // Export Button
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            exportToCSV();
        });
    }

    // Select All Checkbox
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', (e) => {
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            rowCheckboxes.forEach(checkbox => {
                if (checkbox.closest('tr').style.display !== 'none') {
                    checkbox.checked = e.target.checked;
                }
            });
            updateBulkActionsPanel();
        });
    }

    // Row Checkboxes
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            updateBulkActionsPanel();
        });
    });
}

// Initialize Bulk Actions
function initializeBulkActions() {
    const bulkActionBtns = document.querySelectorAll('.bulk-buttons .btn');

    bulkActionBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const selectedCount = document.querySelectorAll('.row-checkbox:checked').length;
            const action = this.querySelector('i').classList.contains('fa-trash') ? 'delete' :
                this.querySelector('i').classList.contains('fa-user-check') ? 'activate' : 'deactivate';

            if (action === 'delete') {
                if (confirm(`Are you sure you want to delete ${selectedCount} user(s)?`)) {
                    // Implement actual deletion logic here
                }
            } else if (action === 'activate') {
                // Implement actual activation logic here
            } else {
                // Implement actual deactivation logic here
            }
        });
    });
}

// Update Bulk Actions Panel
function updateBulkActionsPanel() {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    const bulkActionsPanel = document.getElementById('bulkActionsPanel');
    const selectedCountEl = document.getElementById('selectedCount');

    if (bulkActionsPanel && selectedCountEl) {
        if (selectedCheckboxes.length > 0) {
            bulkActionsPanel.style.display = 'flex';
            selectedCountEl.textContent = selectedCheckboxes.length;
        } else {
            bulkActionsPanel.style.display = 'none';
        }
    }
}

// Export to CSV
function exportToCSV() {
    const table = document.querySelector('.data-table');
    if (!table) return;

    let csv = [];
    const rows = table.querySelectorAll('tr');

    rows.forEach(row => {
        if (row.style.display === 'none') return; // Skip hidden rows

        const cols = row.querySelectorAll('td, th');
        let rowData = [];

        cols.forEach((col, index) => {
            if (index === 0) return; // Skip checkbox column

            let text = col.textContent.trim();
            // Handle special cases
            if (col.querySelector('.user-cell')) {
                text = col.querySelector('.user-cell span')?.textContent.trim() || text;
            }
            rowData.push(`"${text}"`);
        });

        csv.push(rowData.join(','));
    });

    // Create download
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);

    link.setAttribute('href', url);
    link.setAttribute('download', `users_export_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Listen for URL parameters to show success messages
window.addEventListener('load', () => {
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.get('created') === 'success') {
        // Clean URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    if (urlParams.get('updated') === 'success') {
        // Clean URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    if (urlParams.get('deleted') === 'success') {
        // Clean URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});
