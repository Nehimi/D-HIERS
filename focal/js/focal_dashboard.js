/**
 * Focal Person Dashboard Notification Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    const notifBell = document.getElementById('notifBell');
    const notifBadge = document.getElementById('notifBadge');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifList = document.getElementById('notifList');
    const markAllRead = document.getElementById('markAllRead');

    if (!notifBell) return;

    // Toggle Dropdown
    notifBell.addEventListener('click', (e) => {
        e.stopPropagation();
        const isVisible = notifDropdown.style.display === 'block';
        notifDropdown.style.display = isVisible ? 'none' : 'block';
    });

    // Close Dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!notifDropdown.contains(e.target) && e.target !== notifBell) {
            notifDropdown.style.display = 'none';
        }
    });

    // Mark all as seen
    markAllRead.addEventListener('click', () => {
        fetch('../api/focal_person.php?action=mark_notifications_seen')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    updateNotifications();
                }
            });
    });

    function updateNotifications() {
        fetch('../api/focal_person.php?action=get_notifications')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    // Count unread (though API already filters for now, good to be safe)
                    const unreadCount = data.data.filter(n => n.is_read === '0' || n.is_read === 0).length;

                    if (unreadCount > 0) {
                        notifBadge.textContent = unreadCount;
                        notifBadge.style.display = 'flex';
                        notifBadge.style.alignItems = 'center';
                        notifBadge.style.justifyContent = 'center';
                        notifBadge.style.fontSize = '0.7rem';
                        notifBadge.style.color = 'white';
                        notifBadge.style.background = '#e74c3c';
                        notifBadge.style.width = '18px';
                        notifBadge.style.height = '18px';
                        notifBadge.style.borderRadius = '50%';
                        notifBadge.style.position = 'absolute';
                        notifBadge.style.top = '-5px';
                        notifBadge.style.right = '-5px';
                    } else {
                        notifBadge.style.display = 'none';
                    }

                    let html = '';
                    data.data.forEach(n => {
                        const bgColor = n.is_read == '0' ? '#f0f9ff' : 'white';
                        const fontWeight = n.is_read == '0' ? '700' : '500';
                        html += `
                            <div style="padding: 1rem; border-bottom: 1px solid #f8fafc; cursor: pointer; background: ${bgColor}; transition: all 0.2s; "
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
                    notifList.innerHTML = `
                        <div style="padding: 3rem 2rem; text-align: center; color: #94a3b8;">
                            <i class="fa-solid fa-bell-slash" style="font-size: 2.5rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                            <p style="font-size: 0.9rem;">Clean slate! No notifications.</p>
                        </div>
                    `;
                }
            });
    }

    window.handleNotificationClick = function (id, url) {
        fetch(`../api/focal_person.php?action=mark_notification_read&id=${id}`)
            .then(res => res.json())
            .then(data => {
                window.location.href = url;
            })
            .catch(() => {
                window.location.href = url;
            });
    };

    // Poll every 30 seconds
    updateNotifications();
    setInterval(updateNotifications, 30000);
});
