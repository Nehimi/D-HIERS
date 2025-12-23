/**
 * Authentication Module Logic
 * Handles View Switching and AJAX Submissions
 */

document.addEventListener('DOMContentLoaded', () => {
    // Determine initial view
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');

    if (token) {
        showView('reset');
        document.getElementById('token').value = token;
        verifyToken(token);
    } else {
        showView('login');
    }

    // Event Listeners for switching
    document.querySelectorAll('[data-switch]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const target = btn.getAttribute('data-switch');
            showView(target);
        });
    });

    // Form Submissions
    setupForm('loginForm', '../php/login.php');
    setupForm('forgotForm', '../php/forgot_password.php');
    setupForm('resetForm', '../php/reset_password.php');

    // Password Toggle
    window.togglePassword = function (inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fa-solid fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fa-solid fa-eye';
        }
    };
});

// Copy Link Function
window.copyLink = function () {
    const input = document.getElementById('resetLinkInput');
    if (!input) return;

    input.select();
    input.setSelectionRange(0, 99999); // For mobile

    navigator.clipboard.writeText(input.value).then(() => {
        const btn = input.nextElementSibling;
        const originalHTML = btn.innerHTML;

        btn.innerHTML = '<i class="fa-solid fa-check"></i> Copied!';
        btn.style.background = '#22c55e';

        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.style.background = '#0f766e';
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy: ', err);
        alert('Failed to copy link. Please select manually.');
    });
};

function showView(viewName) {
    // Hide all views
    document.querySelectorAll('.view-section').forEach(el => el.classList.remove('active'));

    // Show target view
    const target = document.getElementById(`view-${viewName}`);
    if (target) {
        target.classList.add('active');

        // Update header text based on view
        const title = document.getElementById('page-title');
        const subtitle = document.getElementById('page-subtitle');
        const icon = document.getElementById('page-icon');

        if (viewName === 'login') {
            title.textContent = 'Welcome Back';
            subtitle.textContent = 'Sign in to access your dashboard';
            icon.className = 'fa-solid fa-user-shield';
        } else if (viewName === 'forgot') {
            title.textContent = 'Forgot Password?';
            subtitle.textContent = 'Enter your email to receive a reset link';
            icon.className = 'fa-solid fa-key';
        } else if (viewName === 'reset') {
            title.textContent = 'Reset Password';
            subtitle.textContent = 'Create a new secure password';
            icon.className = 'fa-solid fa-lock-open';
        }
    }
}

function setupForm(formId, endpoint) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const formData = new URLSearchParams(new FormData(form));

        setLoading(btn, true);
        hideMessage();

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData
            });

            const data = await response.json();

            if (data.status === 'success') {
                showMessage('success', 'Success', data.message);

                if (data.redirect) {
                    setTimeout(() => window.location.href = `../../${data.redirect}`, 1500);
                }
            } else {
                showMessage('error', 'Error', data.message);
            }
        } catch (error) {
            console.error(error);
            showMessage('error', 'Connection Error', 'Unable to reach the server.');
        } finally {
            setLoading(btn, false);
        }
    });
}

function verifyToken(token) {
    // Optional: Verify token capability if API supports it
    // Logic similar to reset_password.html
}

function setLoading(btn, isLoading) {
    const spinner = btn.querySelector('.spinner');
    const text = btn.querySelector('.btn-text');

    if (isLoading) {
        btn.classList.add('loading');
        btn.disabled = true;
    } else {
        btn.classList.remove('loading');
        btn.disabled = false;
    }
}

function showMessage(type, title, text) {
    const box = document.getElementById('globalMessage');
    const titleEl = document.getElementById('msgTitle');
    const textEl = document.getElementById('msgText');
    const icon = box.querySelector('i');

    box.className = `message-box ${type} show`;
    titleEl.textContent = title;
    textEl.innerHTML = text;

    if (type === 'success') icon.className = 'fa-solid fa-circle-check';
    else if (type === 'error') icon.className = 'fa-solid fa-circle-exclamation';

    box.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function hideMessage() {
    document.getElementById('globalMessage').classList.remove('show');
}
