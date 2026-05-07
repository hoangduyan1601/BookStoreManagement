/* Admin Luxury Theme JS */

document.addEventListener('DOMContentLoaded', function() {
    // 1. THEME TOGGLE LOGIC
    const body = document.body;
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = themeToggle ? themeToggle.querySelector('i') : null;
    
    // Check for saved theme
    const savedTheme = localStorage.getItem('admin-theme') || 'light';
    body.setAttribute('data-theme', savedTheme);
    if (themeIcon) updateThemeIcon(savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = body.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            body.setAttribute('data-theme', newTheme);
            localStorage.setItem('admin-theme', newTheme);
            updateThemeIcon(newTheme);
        });
    }

    function updateThemeIcon(theme) {
        if (!themeIcon) return;
        if (theme === 'dark') {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        } else {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        }
    }

    // 2. SIDEBAR TOGGLE (Unified for Desktop & Mobile)
    const desktopToggle = document.getElementById('sidebar-toggle');
    const mobileToggle = document.getElementById('sidebar-toggle-mobile');

    // Load initial sidebar state on desktop
    if (window.innerWidth > 991.98) {
        const sidebarState = localStorage.getItem('sidebar-collapsed');
        if (sidebarState === 'true') {
            body.classList.add('sidebar-collapsed');
        }
    }

    function toggleSidebar() {
        if (window.innerWidth > 991.98) {
            // Desktop behavior: Toggle collapsed state
            body.classList.toggle('sidebar-collapsed');
            // Save state
            localStorage.setItem('sidebar-collapsed', body.classList.contains('sidebar-collapsed'));
        } else {
            // Mobile behavior: Toggle open state
            body.classList.toggle('sidebar-mobile-open');
        }
    }

    if (desktopToggle) {
        desktopToggle.addEventListener('click', toggleSidebar);
    }

    if (mobileToggle) {
        mobileToggle.addEventListener('click', toggleSidebar);
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 991.98) {
            const sidebar = document.getElementById('sidebar');
            if (body.classList.contains('sidebar-mobile-open') && 
                sidebar && !sidebar.contains(e.target) && 
                mobileToggle && !mobileToggle.contains(e.target)) {
                body.classList.remove('sidebar-mobile-open');
            }
        }
    });

    // 3. AUTO-HIDE ALERTS
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.click();
            } else {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });

    // 4. TOOLTIPS & POPOVERS
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
