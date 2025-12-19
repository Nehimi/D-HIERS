document.addEventListener('DOMContentLoaded', () => {
    // 1. Inject Modal HTML
    const modalHtml = `
        <div class="logout-modal-backdrop" id="logoutModal">
            <div class="logout-modal">
                <div class="logout-modal-header">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <h2>Confirm Logout</h2>
                </div>
                <div class="logout-modal-body">
                    <p>Are you sure you want to log out of the system?</p>
                </div>
                <div class="logout-modal-actions">
                    <button class="logout-btn logout-btn-cancel" id="cancelLogout">Cancel</button>
                    <button class="logout-btn logout-btn-confirm" id="confirmLogout">Yes, Logout</button>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // 2. State & Elements
    const modal = document.getElementById('logoutModal');
    const cancelBtn = document.getElementById('cancelLogout');
    const confirmBtn = document.getElementById('confirmLogout');
    let targetUrl = '';

    // 3. Functions
    const showModal = (e, url) => {
        e.preventDefault();
        targetUrl = url;
        modal.classList.add('active');
    };

    const hideModal = () => {
        modal.classList.remove('active');
        targetUrl = '';
    };

    // 4. Event Listeners

    // Auto-detect logout buttons
    // Strategy: Look for specific classes or links pointing to index.html/project.html with "Logout" text
    const attachLogoutListeners = () => {
        const potentialLogoutLinks = document.querySelectorAll('a, button');

        potentialLogoutLinks.forEach(link => {
            const isLogoutClass = link.classList.contains('logout') || link.innerText.toLowerCase().includes('logout');
            // Check href for common logout destinations if it's an anchor
            const href = link.tagName === 'A' ? link.getAttribute('href') : '';
            const isLogoutHref = href && (href.includes('index.html') || href.includes('project.html') || href === '#' || href.includes('logout'));

            if (isLogoutClass && isLogoutHref) {
                link.addEventListener('click', (e) => {
                    // Start animation or show modal
                    showModal(e, href);
                });
            }
        });
    };

    attachLogoutListeners();

    // Modal Actions
    cancelBtn.addEventListener('click', hideModal);

    confirmBtn.addEventListener('click', () => {
        if (targetUrl) {
            window.location.href = targetUrl;
        }
    });

    // Close on backdrop click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            hideModal();
        }
    });
});
