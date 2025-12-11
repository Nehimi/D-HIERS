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
    initUserManagement();
    initNotificationDropdown();
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

// User Management
function initUserManagement() {
    const userTableBody = document.getElementById('userTableBody');
    if (userTableBody) {
        loadUsers();
    }

    const createAccountForm = document.getElementById('createAccountForm');
    if (createAccountForm) {
        createAccountForm.addEventListener('submit', handleCreateAccount);
    }
}

function handleCreateAccount(e) {
    e.preventDefault();

    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (password !== confirmPassword) {
        alert('Passwords do not match!');
        return;
    }

    const userData = {
        firstName: document.getElementById('firstName').value,
        lastName: document.getElementById('lastName').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        userId: document.getElementById('userId').value,
        role: document.getElementById('role').value,
        kebele: document.getElementById('kebele').value,
        status: document.getElementById('status').value,
        createdAt: new Date().toISOString()
    };

    saveUser(userData);

    const btn = e.target.querySelector('button[type="submit"]');
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Creating Account...';
    btn.disabled = true;

    setTimeout(() => {
        alert('Account created successfully!');
        window.location.href = 'admin.html';
    }, 1500);
}

function saveUser(userData) {
    let users = JSON.parse(localStorage.getItem('dheirs_users')) || [];
    users.unshift(userData);
    localStorage.setItem('dheirs_users', JSON.stringify(users));
}

function loadUsers() {
    const users = JSON.parse(localStorage.getItem('dheirs_users')) || [];
    const userTableBody = document.getElementById('userTableBody');

    if (!userTableBody) return;

    while (userTableBody.children.length > 3) {
        userTableBody.removeChild(userTableBody.lastChild);
    }

    users.forEach((user, index) => {
        const row = createUserRow(user, index);
        userTableBody.insertBefore(row, userTableBody.firstChild);
    });

    const totalUsersCount = document.getElementById('totalUsersCount');
    if (totalUsersCount) {
        totalUsersCount.textContent = 124 + users.length;
    }
}

function createUserRow(user, index) {
    const row = document.createElement('tr');
    const roleInfo = getRoleInfo(user.role);
    const statusClass = user.status === 'active' ? 'active' : user.status === 'pending' ? 'pending' : 'offline';
    const avatarColor = `color-${(index % 3) + 1}`;
    const initial = user.firstName.charAt(0).toUpperCase();
    const timeAgo = getTimeAgo(user.createdAt);

    row.innerHTML = `
        <td>
            <div class="user-cell">
                <div class="avatar-xs ${avatarColor}">${initial}</div>
                <span>${user.firstName} ${user.lastName}</span>
            </div>
        </td>
        <td><span class="role-tag ${roleInfo.class}">${roleInfo.name}</span></td>
        <td>${getKebeleDisplay(user.kebele)}</td>
        <td><span class="status-tag ${statusClass}">${user.status.charAt(0).toUpperCase() + user.status.slice(1)}</span></td>
        <td>${timeAgo}</td>
    `;

    return row;
}

function getRoleInfo(role) {
    const roles = {
        'hew': { name: 'HEW', class: 'hew' },
        'coordinator': { name: 'Coordinator', class: 'coord' },
        'linkage': { name: 'Linkage Focal', class: 'coord' },
        'hmis': { name: 'HMIS Officer', class: 'hmis' },
        'supervisor': { name: 'Supervisor', class: 'coord' },
        'admin': { name: 'Administrator', class: 'hmis' }
    };
    return roles[role] || { name: role, class: 'hew' };
}

function getKebeleDisplay(kebele) {
    const kebeles = {
        'lich-amba': 'Lich-Amba',
        'arada': 'Arada',
        'lereba': 'Lereba',
        'phcu-hq': 'PHCU HQ'
    };
    return kebeles[kebele] || kebele;
}

function getTimeAgo(dateString) {
    const now = new Date();
    const created = new Date(dateString);
    const diffMs = now - created;
    const diffMins = Math.floor(diffMs / 60000);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins} min${diffMins > 1 ? 's' : ''} ago`;

    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;

    const diffDays = Math.floor(diffHours / 24);
    if (diffDays === 1) return 'Yesterday';
    if (diffDays < 7) return `${diffDays} days ago`;

    return created.toLocaleDateString();
}