// Admin Dashboard Logic// Admin Dashboard Logic
document.addEventListener('DOMContentLoaded', function () {
    console.log('Admin Dashboard Loaded');

    // Handle Quick Action buttons
    const actionItems = document.querySelectorAll('.action-item');

    actionItems.forEach((item, index) => {
        item.addEventListener('click', function () {
            const actionText = this.querySelector('strong').textContent;

            if (actionText === 'Create Account') {
                window.location.href = 'create_account.php';
            } else if (actionText === 'Reset Password') {
                alert('Password reset functionality coming soon!');
            } else if (actionText === 'Backup Data') {
                alert('Backup functionality coming soon!');
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
});
