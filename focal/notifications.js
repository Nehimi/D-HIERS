
document.addEventListener('DOMContentLoaded', function () {
    // 1. Create Notification Bell Area if not exists
    const header = document.querySelector('.dashboard-header') || document.querySelector('header') || document.querySelector('.sidebar-header');

    // Fallback: If no standard header, append to body for simple alert
    if (!header) return;

    // Create Notification Container
    const notifContainer = document.createElement('div');
    notifContainer.id = 'notification-bell';
    notifContainer.style.position = 'fixed';
    notifContainer.style.top = '20px';
    notifContainer.style.right = '20px';
    notifContainer.style.zIndex = '1000';
    notifContainer.style.cursor = 'pointer';
    notifContainer.innerHTML = `
        <i class="fa-solid fa-bell" style="font-size: 1.5rem; color: #555;"></i>
        <span id="notif-count" style="display:none; position:absolute; top:-5px; right:-5px; background:red; color:white; border-radius:50%; padding:2px 6px; font-size:10px;">0</span>
        <div id="notif-dropdown" style="display:none; position:absolute; top:30px; right:0; width:300px; background:white; border:1px solid #ccc; shadow:0 4px 8px rgba(0,0,0,0.1); border-radius:8px; padding:10px;">
            <h4 style="margin:0 0 10px 0; border-bottom:1px solid #eee; padding-bottom:5px;">Notifications</h4>
            <ul id="notif-list" style="list-style:none; padding:0; margin:0; max-height:300px; overflow-y:auto;"></ul>
        </div>
    `;

    document.body.appendChild(notifContainer);

    // Toggle Dropdown
    notifContainer.addEventListener('click', function () {
        const dd = document.getElementById('notif-dropdown');
        dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
    });

    // 2. Fetch Notifications
    function checkNotifications() {
        // Assuming role 'linkage' for Focal Person based on context
        fetch('../api/notifications.php?role=linkage')
            .then(res => res.json())
            .then(data => {
                const countBadge = document.getElementById('notif-count');
                const list = document.getElementById('notif-dropdown'); // Fixed variable reference error in previous logic
                const ul = document.getElementById('notif-list');

                if (data.success && data.count > 0) {
                    countBadge.style.display = 'block';
                    countBadge.textContent = data.count;

                    ul.innerHTML = '';
                    data.notifications.forEach(n => {
                        const li = document.createElement('li');
                        li.style.padding = '8px';
                        li.style.borderBottom = '1px solid #eee';
                        li.innerHTML = `
                            <strong>${n.title}</strong><br>
                            <span style="font-size:12px; color:#666;">${n.message}</span>
                            ${n.action_url ? `<br><a href="${n.action_url}" style="font-size:11px; color:blue;">View Action</a>` : ''}
                        `;
                        ul.appendChild(li);
                    });
                } else {
                    countBadge.style.display = 'none';
                    ul.innerHTML = '<li style="padding:10px; text-align:center; color:#999;">No new notifications</li>';
                }
            })
            .catch(err => console.error(err));
    }

    // Poll every 10 seconds
    checkNotifications();
    setInterval(checkNotifications, 10000);
});
