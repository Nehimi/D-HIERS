/**
 * Premium Mobile Navigation Logic
 * Handles sidebar drawer toggling with accessibility and smooth transitions.
 */
document.addEventListener('DOMContentLoaded', function () {
    // 1. Inject Mobile Header
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    if (!sidebar || !mainContent) return;

    // Create Navigation Toggle UI
    const mobileHeader = document.createElement('div');
    mobileHeader.className = 'mobile-nav-header';
    mobileHeader.innerHTML = `
        <div class="brand" style="display:flex; align-items:center; gap:0.5rem;">
            <img src="images.jpg" style="width:32px; height:32px; border-radius:6px;">
            <span style="font-weight:700; color:var(--primary-dark); font-size:1.1rem;">D-HEIRS</span>
        </div>
        <button class="mobile-menu-toggle" id="menuToggleBtn">
            <i class="fa-solid fa-bars-staggered"></i>
        </button>
    `;
    document.body.appendChild(mobileHeader);

    // Create Overlay
    const overlay = document.createElement('div');
    overlay.className = 'nav-overlay';
    document.body.appendChild(overlay);

    const toggleBtn = document.getElementById('menuToggleBtn');

    function toggleSidebar() {
        const isActive = sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.style.overflow = isActive ? 'hidden' : ''; // Prevent scroll behind drawer

        // Dynamic Icon Change
        const icon = toggleBtn.querySelector('i');
        if (isActive) {
            icon.className = 'fa-solid fa-xmark';
        } else {
            icon.className = 'fa-solid fa-bars-staggered';
        }
    }

    // Event Listeners
    toggleBtn.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);

    // Auto-close on link click (important for mobile UX)
    const sidebarLinks = sidebar.querySelectorAll('a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (sidebar.classList.contains('active')) {
                toggleSidebar();
            }
        });
    });

    // Handle window resize (safety check)
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768 && sidebar.classList.contains('active')) {
            toggleSidebar();
        }
    });

    console.log("Premium Mobile Nav Initialized");
});
