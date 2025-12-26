/**
 * User Status Management
 * Professional-grade status toggle functionality
 */

// Change user status (active/inactive/pending)
async function changeUserStatus(userId, newStatus) {
    try {
        const formData = new FormData();
        formData.append('action', 'change_status');
        formData.append('user_id', userId);
        formData.append('status', newStatus);

        const response = await fetch('../../api/user_status.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            console.log('Status changed successfully:', data);
            // Reload the page to refresh counts
            window.location.reload();
            return true;
        } else {
            alert('Error: ' + data.message);
            return false;
        }
    } catch (error) {
        console.error('Status change error:', error);
        alert('Failed to change status. Please try again.');
        return false;
    }
}

// Toggle user status (active <-> inactive)
async function toggleUserStatus(userId) {
    try {
        const formData = new FormData();
        formData.append('action', 'toggle_status');
        formData.append('user_id', userId);

        const response = await fetch('../../api/user_status.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            console.log('Status toggled:', data);
            window.location.reload();
            return true;
        } else {
            alert('Error: ' + data.message);
            return false;
        }
    } catch (error) {
        console.error('Toggle error:', error);
        alert('Failed to toggle status.');
        return false;
    }
}

// Bulk status change
async function bulkChangeStatus(userIds, newStatus) {
    try {
        const formData = new FormData();
        formData.append('action', 'bulk_status_change');
        formData.append('user_ids', userIds.join(','));
        formData.append('status', newStatus);

        const response = await fetch('../../api/user_status.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            alert(data.message);
            window.location.reload();
            return true;
        } else {
            alert('Error: ' + data.message);
            return false;
        }
    } catch (error) {
        console.error('Bulk change error:', error);
        alert('Failed to change status.');
        return false;
    }
}

// Get status counts
async function getStatusCounts() {
    try {
        const response = await fetch('../../api/user_status.php?action=get_status_counts');
        const data = await response.json();

        if (data.success) {
            return data.data;
        } else {
            console.error('Failed to get counts:', data.message);
            return null;
        }
    } catch (error) {
        console.error('Count fetch error:', error);
        return null;
    }
}

// Update counts display on page
async function updateStatusCountsDisplay() {
    const counts = await getStatusCounts();

    if (counts) {
        // Update total count
        const totalElement = document.getElementById('totalCount');
        if (totalElement) {
            totalElement.textContent = counts.total;
        }

        // Update active count
        const activeElement = document.getElementById('activeCount');
        if (activeElement) {
            activeElement.textContent = counts.active;
        }

        // Update inactive count (if element exists)
        const inactiveElement = document.getElementById('inactiveCount');
        if (inactiveElement) {
            inactiveElement.textContent = counts.inactive;
        }

        // Update pending count (if element exists)
        const pendingElement = document.getElementById('pendingCount');
        if (pendingElement) {
            pendingElement.textContent = counts.pending;
        }

        console.log('Counts updated:', counts);
    }
}

// Initialize status dropdowns for each user row
function initializeStatusDropdowns() {
    const statusSelects = document.querySelectorAll('.status-select');

    statusSelects.forEach(select => {
        select.addEventListener('change', async function () {
            const userId = this.dataset.userId;
            const newStatus = this.value;
            const oldStatus = this.dataset.oldStatus;

            const confirmed = confirm(`Change user status to "${newStatus}"?`);

            if (confirmed) {
                const success = await changeUserStatus(userId, newStatus);
                if (!success) {
                    // Revert to old status if failed
                    this.value = oldStatus;
                }
            } else {
                // Revert to old status if cancelled
                this.value = oldStatus;
            }
        });
    });
}

// Initialize bulk actions
function initializeBulkStatusActions() {
    const bulkActions = document.querySelector('.bulk-actions-select');
    const applyButton = document.querySelector('.apply-bulk-action');

    if (bulkActions && applyButton) {
        applyButton.addEventListener('click', async function () {
            const selectedAction = bulkActions.value;
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            const userIds = Array.from(checkboxes).map(cb => cb.dataset.userId);

            if (userIds.length === 0) {
                alert('Please select at least one user');
                return;
            }

            let newStatus = null;
            if (selectedAction === 'activate') {
                newStatus = 'active';
            } else if (selectedAction === 'deactivate') {
                newStatus = 'inactive';
            } else if (selectedAction === 'set_pending') {
                newStatus = 'pending';
            }

            if (newStatus) {
                const confirmed = confirm(`Change status of ${userIds.length} user(s) to "${newStatus}"?`);
                if (confirmed) {
                    await bulkChangeStatus(userIds, newStatus);
                }
            }
        });
    }
}

// Auto-refresh counts every 30 seconds
setInterval(updateStatusCountsDisplay, 30000);

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    console.log('Status management initialized');
    initializeStatusDropdowns();
    initializeBulkStatusActions();

    // Update counts immediately on load
    setTimeout(updateStatusCountsDisplay, 500);
});
