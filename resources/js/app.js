import '../css/app.css';
import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const shell = document.querySelector('[data-app-shell]');
    const overlay = document.querySelector('[data-app-overlay]');
    const mobileTriggers = document.querySelectorAll('[data-mobile-sidebar-toggle]');
    const desktopTriggers = document.querySelectorAll('[data-sidebar-toggle]');
    const sidebarStateKey = 'app-sidebar-collapsed';

    if (! shell) {
        return;
    }

    const persistSidebarState = () => {
        try {
            window.localStorage.setItem(
                sidebarStateKey,
                shell.classList.contains('sidebar-collapsed') ? '1' : '0',
            );
        } catch (error) {
            // Ignore storage failures and keep the sidebar usable for this session.
        }
    };

    try {
        if (window.localStorage.getItem(sidebarStateKey) === '1') {
            shell.classList.add('sidebar-collapsed');
        }
    } catch (error) {
        // Ignore storage failures and keep the default expanded layout.
    }

    document.documentElement.classList.remove('sidebar-collapsed-pref');

    const closeMobileSidebar = () => {
        shell.classList.remove('mobile-sidebar-open');
    };

    const openMobileSidebar = () => {
        shell.classList.add('mobile-sidebar-open');
    };

    desktopTriggers.forEach((trigger) => {
        trigger.addEventListener('click', () => {
            shell.classList.toggle('sidebar-collapsed');
            persistSidebarState();
        });
    });

    mobileTriggers.forEach((trigger) => {
        trigger.addEventListener('click', () => {
            if (shell.classList.contains('mobile-sidebar-open')) {
                closeMobileSidebar();
                return;
            }

            openMobileSidebar();
        });
    });

    overlay?.addEventListener('click', closeMobileSidebar);

    window.addEventListener('resize', () => {
        if (window.innerWidth > 960) {
            closeMobileSidebar();
        }
    });
});
