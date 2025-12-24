/**
 * Coordinator Dashboard Logic
 * D-HEIRS - Digital Health Extension Information Gathering & Reporting System
 * 
 * Handles real-time updating of dashboard statistics from the database.
 */

document.addEventListener('DOMContentLoaded', function () {
    const statsConfig = {
        activeHews: document.getElementById('activeHewsCount'),
        pendingReports: document.getElementById('pendingReportsCount'),
        validatedToday: document.getElementById('validatedTodayCount'),
        packagesForwarded: document.getElementById('packagesForwardedCount')
    };

    /**
     * Fetch statistics from the API
     */
    function updateDashboardStats() {
        fetch('../api/hew_coordinator.php?action=dashboard_stats')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const stats = data.data;

                    // Update the UI with animation or direct text
                    if (statsConfig.activeHews) statsConfig.activeHews.textContent = stats.active_hews;
                    if (statsConfig.pendingReports) statsConfig.pendingReports.textContent = stats.pending_reports;
                    if (statsConfig.validatedToday) statsConfig.validatedToday.textContent = stats.validated_today;
                    if (statsConfig.packagesForwarded) statsConfig.packagesForwarded.textContent = stats.packages_forwarded;

                    console.log('Dashboard stats refreshed:', new Date().toLocaleTimeString());
                } else {
                    console.error('Failed to fetch dashboard stats:', data.message);
                }
            })
            .catch(error => {
                console.error('Error in stats fetch:', error);
            });
    }

    const notifToggle = document.getElementById('notifToggle');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifBadge = document.getElementById('notifBadge');
    const notifList = document.getElementById('notifList');

    function updateNotifications() {
        fetch('../api/hew_coordinator.php?action=get_notifications')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    const count = data.data.length;
                    notifBadge.textContent = count;
                    notifBadge.style.display = 'block';

                    let html = '';
                    data.data.forEach(n => {
                        html += `
                            <div style="padding: 1rem; border-bottom: 1px solid #f8fafc; cursor: pointer;" onclick="location.href='${n.action_url}'">
                                <div style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.25rem;">${n.title}</div>
                                <div style="font-size: 0.8rem; color: #64748b;">${n.message}</div>
                                <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.5rem;">${new Date(n.created_at).toLocaleTimeString()}</div>
                            </div>
                        `;
                    });
                    notifList.innerHTML = html;
                } else {
                    notifBadge.style.display = 'none';
                    notifList.innerHTML = '<div style="padding: 1rem; text-align: center; color: #64748b;">No new notifications</div>';
                }
            });
    }

    if (notifToggle) {
        notifToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            notifDropdown.style.display = notifDropdown.style.display === 'none' ? 'block' : 'none';

            // When opening, we could mark as read, but for now just toggle
        });
    }

    document.addEventListener('click', () => {
        if (notifDropdown) notifDropdown.style.display = 'none';
    });

    // Initial load and polling
    updateDashboardStats();
    updateNotifications();
    const refreshInterval = setInterval(() => {
        updateDashboardStats();
        updateNotifications();
    }, 30000);

    // Clean up interval if page is hidden to save resources
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            clearInterval(refreshInterval);
        } else {
            updateDashboardStats();
            // Optional: Restart interval here if needed
        }
    });
});
