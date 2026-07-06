(function () {
    const config = window.TaxPortalConfig || {};
    const csrf = config.csrfToken || '';

    function postPreference(data) {
        if (!config.preferencesUrl) {
            return;
        }

        const body = new URLSearchParams(data);
        fetch(config.preferencesUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': csrf
            },
            body
        }).catch(function () {});
    }

    function setThemeClass(type, value) {
        document.body.className = document.body.className
            .split(/\s+/)
            .filter(function (name) {
                return name && !name.startsWith(type + '-');
            })
            .concat(type + '-' + value)
            .join(' ');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('appSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const modeToggle = document.getElementById('modeToggle');
        const themeSelect = document.getElementById('themeSelect');

        function closeSidebar() {
            sidebar && sidebar.classList.remove('open');
            backdrop && backdrop.classList.remove('show');
        }

        if (sidebarToggle && sidebar && backdrop) {
            sidebarToggle.addEventListener('click', function () {
                sidebar.classList.toggle('open');
                backdrop.classList.toggle('show');
            });
            backdrop.addEventListener('click', closeSidebar);
        }

        if (themeSelect) {
            themeSelect.addEventListener('change', function () {
                setThemeClass('theme', this.value);
                postPreference({ theme_color: this.value });
            });
        }

        if (modeToggle) {
            modeToggle.addEventListener('click', function () {
                const isDark = document.body.classList.contains('mode-dark');
                const nextMode = isDark ? 'light' : 'dark';
                setThemeClass('mode', nextMode);
                this.innerHTML = '<i class="fa-solid ' + (nextMode === 'dark' ? 'fa-sun' : 'fa-moon') + '"></i>';
                postPreference({ theme_mode: nextMode });
            });
        }

        if (config.sessionMinutes && config.logoutUrl) {
            let logoutTimer = null;
            const logoutMs = Number(config.sessionMinutes) * 60 * 1000;
            const resetTimer = function () {
                clearTimeout(logoutTimer);
                logoutTimer = setTimeout(function () {
                    window.location.href = config.logoutUrl;
                }, logoutMs);
            };

            ['click', 'keydown', 'mousemove', 'scroll', 'touchstart'].forEach(function (eventName) {
                document.addEventListener(eventName, resetTimer, { passive: true });
            });
            resetTimer();
        }
    });
})();
