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
                    if (statsConfig.validatedToday) statsConfig.validatedToday.textContent = stats.validated_today;
                    if (statsConfig.packagesForwarded) statsConfig.packagesForwarded.textContent = stats.packages_forwarded;

                    // Update Greeting
                    const nameSpan = document.getElementById('coordinatorName');
                    if (nameSpan && stats.user_name) {
                        nameSpan.textContent = stats.user_name;
                    }

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
                    // Count only unread notifications
                    const unreadCount = data.data.filter(n => n.is_read === '0').length;

                    if (unreadCount > 0) {
                        notifBadge.textContent = unreadCount;
                        notifBadge.style.display = 'block';
                    } else {
                        notifBadge.style.display = 'none';
                    }

                    let html = '';
                    data.data.forEach(n => {
                        const bgColor = n.is_read === '0' ? '#f0f9ff' : 'white';
                        const fontWeight = n.is_read === '0' ? '700' : '500';
                        html += `
                            <div data-notif-id="${n.id}" 
                                 style="padding: 1rem; border-bottom: 1px solid #f8fafc; cursor: pointer; background: ${bgColor}; transition: all 0.2s;" 
                                 onclick="handleNotificationClick(${n.id}, '${n.action_url}')"
                                 onmouseover="this.style.backgroundColor='#f8fafc'" 
                                 onmouseout="this.style.backgroundColor='${bgColor}'">
                                <div style="font-weight: ${fontWeight}; font-size: 0.9rem; margin-bottom: 0.25rem; color: #0f172a;">${n.title}</div>
                                <div style="font-size: 0.8rem; color: #64748b; line-height: 1.4;">${n.message}</div>
                                <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.5rem;">
                                    <i class="fa-solid fa-clock" style="margin-right: 0.25rem;"></i>${new Date(n.created_at).toLocaleString()}
                                </div>
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
            const isOpening = notifDropdown.style.display === 'none' || notifDropdown.style.display === '';
            notifDropdown.style.display = isOpening ? 'block' : 'none';

            // Mark notifications as seen when opening
            if (isOpening && notifBadge.style.display !== 'none') {
                markNotificationsAsSeen();
            }
        });
    }

    // Mark all notifications as seen
    function markNotificationsAsSeen() {
        fetch('../api/hew_coordinator.php?action=mark_notifications_seen', {
            method: 'POST'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide badge
                    notifBadge.style.display = 'none';
                    notifBadge.textContent = '0';

                    // Update notification items to show as read
                    const notifItems = notifList.querySelectorAll('[data-notif-id]');
                    notifItems.forEach(item => {
                        item.style.backgroundColor = 'white';
                        item.style.opacity = '0.8';
                    });
                }
            })
            .catch(error => console.error('Error marking notifications as seen:', error));
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
