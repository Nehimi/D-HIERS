// ========================================
// MOBILE MENU & RESPONSIVE FUNCTIONALITY
// ========================================

document.addEventListener('DOMContentLoaded', () => {

    // --- Mobile Sidebar Toggle for Dashboards ---
    initMobileSidebar();

    // --- Existing functionality ---
    initMobileMenu();
    initNavbarScroll();
    initScrollAnimations();
    initPasswordToggle();
    // initUserManagement(); // Disabled: Using PHP backend now
    initNotificationDropdown();

    // Custom Logout Dialog
    initLogoutModal();
});

// Mobile Sidebar for Dashboard Pages
function initMobileSidebar() {
    const sidebar = document.querySelector('.sidebar');

    if (!sidebar) return; // Only run on dashboard pages

    // Create mobile menu toggle button
    const mobileToggle = document.createElement('button');
    mobileToggle.className = 'mobile-menu-toggle';
    mobileToggle.innerHTML = '<i class="fa-solid fa-bars"></i>';
    mobileToggle.setAttribute('aria-label', 'Toggle Menu');
    document.body.appendChild(mobileToggle);

    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);

    // Toggle sidebar
    function toggleSidebar() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    }

    // Event listeners
    mobileToggle.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);

    // Close sidebar when clicking nav links on mobile
    const navLinks = sidebar.querySelectorAll('.nav-item');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 992) {
                toggleSidebar();
            }
        });
    });

    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (window.innerWidth > 992) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }, 250);
    });
}

// Mobile Menu for Landing Page
function initMobileMenu() {
    const mobileToggle = document.querySelector('.mobile-toggle');
    const navMenu = document.querySelector('.nav-menu');

    if (!mobileToggle || !navMenu) return;

    mobileToggle.addEventListener('click', () => {
        const isOpen = navMenu.style.display === 'flex';

        if (isOpen) {
            navMenu.style.display = 'none';
        } else {
            navMenu.style.display = 'flex';
            navMenu.style.position = 'absolute';
            navMenu.style.top = '100%';
            navMenu.style.left = '0';
            navMenu.style.right = '0';
            navMenu.style.width = '100%';
            navMenu.style.background = 'white';
            navMenu.style.flexDirection = 'column';
            navMenu.style.padding = '1rem';
            navMenu.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
            navMenu.style.borderRadius = '0 0 1rem 1rem';
            navMenu.style.zIndex = '999';
        }
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!navMenu.contains(e.target) && !mobileToggle.contains(e.target)) {
            navMenu.style.display = 'none';
        }
    });

    // Close menu when clicking a link
    const navLinks = navMenu.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            navMenu.style.display = 'none';
        });
    });
}

// Navbar Scroll Effect
function initNavbarScroll() {
    const navbar = document.getElementById('navbar');

    if (!navbar) return;

    let lastScroll = 0;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        if (currentScroll > 50) {
            navbar.style.background = 'rgba(255, 255, 255, 0.95)';
            navbar.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
            navbar.style.padding = '0.5rem 0';
        } else {
            navbar.style.background = 'rgba(255, 255, 255, 0.8)';
            navbar.style.boxShadow = 'none';
            navbar.style.padding = '1rem 0';
        }

        lastScroll = currentScroll;
    });
}

// Scroll Animations
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px"
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    const animatedElements = document.querySelectorAll('.bento-box, .timeline-item, .actor-card, .hero-text, .image-card, .stat-card, .card-panel');

    animatedElements.forEach((el) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s cubic-bezier(0.16, 1, 0.3, 1)';
        observer.observe(el);
    });
}

// Password Toggle
function initPasswordToggle() {
    const toggleButtons = document.querySelectorAll('.toggle-password');

    toggleButtons.forEach(button => {
        button.addEventListener('click', () => {
            const input = button.parentElement.querySelector('input');
            const icon = button.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
}

// Notification Dropdown (Placeholder)
function initNotificationDropdown() {
    // Add logic if needed
}

// =========================
// LOGOUT MODAL FUNCTIONALITY
// =========================
function initLogoutModal() {
    const logoutLinks = document.querySelectorAll('.logout');
    if (logoutLinks.length === 0) return;

    // Create Modal HTML and append to body
    const modalHTML = `
    <style>
        .custom-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            align-items: center;
            justify-content: center;
        }
        .custom-modal.active {
            display: flex;
            animation: fadeIn 0.3s ease;
        }
        .custom-modal-content {
            background-color: #fff;
            padding: 2rem;
            border-radius: 1rem;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            text-align: center;
            position: relative;
        }
        .custom-modal-header h2 {
            margin-top: 0;
            color: #1f2937;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .custom-modal-body p {
            color: #4b5563;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        .custom-modal-footer {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
    <div id="logoutConfirmModal" class="custom-modal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h2><i class="fa-solid fa-right-from-bracket" style="color: #dc2626;"></i> Confirm Logout</h2>
            </div>
            <div class="custom-modal-body">
                <p>Are you sure you want to log out of the system?</p>
            </div>
            <div class="custom-modal-footer">
                <button id="cancelLogout" class="btn btn-outline" style="border: 1px solid #d1d5db; color: #374151;">Cancel</button>
                <a href="index.html" class="btn btn-danger" style="background-color: #dc2626; color: white;">Yes, Logout</a>
            </div>
        </div>
    </div>
    `;

    // Convert string to node
    const parser = new DOMParser();
    const doc = parser.parseFromString(modalHTML, 'text/html');
    const style = doc.querySelector('style');
    const modal = doc.querySelector('.custom-modal');

    document.head.appendChild(style);
    document.body.appendChild(modal);

    const cancelBtn = document.getElementById('cancelLogout');

    // Event Listeners
    logoutLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            modal.classList.add('active');
        });
    });

    cancelBtn.addEventListener('click', () => {
        modal.classList.remove('active');
    });

    // Close on click outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
}