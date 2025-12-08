// Login Logic with routing to all dashboards
function openPopup(){
  document.getElementById("popup").style.display = "flex";
}

function closePopup(){
  document.getElementById("popup").style.display = "none";
}

document.getElementById('loginForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = this.querySelector('.login-btn');
    const originalText = btn.innerHTML;

    // Loading state
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Authenticating...';
    btn.style.opacity = '0.8';

    setTimeout(() => {
        const username = document.getElementById('username').value.toLowerCase();

        // Routing based on username/role
        if (username.includes('admin')) {
            window.location.href = 'admin.html';
        } else if (username.includes('hew')) {
            window.location.href = 'hew_dashboard.html';
        } else if (username.includes('coord')) {
            window.location.href = 'coordinator_dashboard.html';
        } else if (username.includes('linkage') || username.includes('focal')) {
            window.location.href = 'linkage_dashboard.html';
        } else if (username.includes('hmis')) {
            window.location.href = 'hmis_dashboard.html';
        } else if (username.includes('supervisor')) {
            window.location.href = 'supervisor_dashboard.html';
        } else {
            // Default fallback to admin
            window.location.href = 'admin.html';// or ERRO page to display the error message
        }
    }, 1500);
});
