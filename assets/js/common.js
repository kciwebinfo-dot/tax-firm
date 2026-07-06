window.TaxPortal = {
    toast(type, message) {
        Swal.fire({
            toast: true,
            icon: type,
            title: message,
            position: 'top-end',
            timer: 2500,
            showConfirmButton: false
        });
    }
};

$(function () {
    setTimeout(() => $('#appLoader').fadeOut(160), 250);

    $('#sidebarToggle').on('click', function () {
        $('#sidebar').toggleClass('open');
    });

    $('.datatable').DataTable({
        responsive: true,
        pageLength: 5,
        lengthChange: false
    });

    $('#themeSelect').on('change', function () {
        const theme = this.value;
        document.documentElement.dataset.theme = theme;
        TaxAjax.post(window.TaxPortalConfig.preferencesUrl, { theme }).done(() => TaxPortal.toast('success', 'Theme updated'));
    });

    $('#modeToggle').on('click', function () {
        const nextMode = document.documentElement.dataset.mode === 'dark' ? 'light' : 'dark';
        document.documentElement.dataset.mode = nextMode;
        TaxAjax.post(window.TaxPortalConfig.preferencesUrl, { mode: nextMode }).done(() => TaxPortal.toast('success', 'Mode updated'));
    });

    $('.upload-box input[type="file"]').each(function () {
        const input = $(this);
        input.data('defaultText', input.closest('.upload-box').find('span').text());
    }).on('change', function () {
        const file = this.files && this.files[0];
        const box = $(this).closest('.upload-box');
        box.toggleClass('has-file', Boolean(file));
        box.find('span').text(file ? file.name : $(this).data('defaultText'));
    });

    if ($('#collectionChart').length) {
        new ApexCharts(document.querySelector('#collectionChart'), {
            chart: { type: 'area', height: 320, toolbar: { show: false } },
            series: [{ name: 'Collection', data: [42, 55, 48, 72, 69, 88, 93, 84, 101, 96, 114, 126] }],
            xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] },
            colors: ['var(--primary)'],
            fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.03 } },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 }
        }).render();
    }

    if ($('#statusChart').length) {
        new ApexCharts(document.querySelector('#statusChart'), {
            chart: { type: 'donut', height: 320 },
            series: [44, 28, 18, 10],
            labels: ['Completed', 'Pending', 'Review', 'Overdue'],
            colors: ['#22c55e', '#f59e0b', '#38bdf8', '#ef4444'],
            legend: { position: 'bottom' }
        }).render();
    }

    if (window.TaxPortalConfig && window.TaxPortalConfig.sessionMinutes) {
        let warningTimer;
        let logoutTimer;
        const warningMs = Math.max((window.TaxPortalConfig.sessionMinutes * 60 - 60) * 1000, 1000);
        const logoutMs = window.TaxPortalConfig.sessionMinutes * 60 * 1000;

        const resetInactivityTimers = () => {
            clearTimeout(warningTimer);
            clearTimeout(logoutTimer);
            warningTimer = setTimeout(() => {
                Swal.fire({
                    title: 'Session expiring soon',
                    text: 'You will be logged out in about one minute because of inactivity.',
                    icon: 'warning',
                    confirmButtonText: 'Stay logged in'
                }).then(resetInactivityTimers);
            }, warningMs);
            logoutTimer = setTimeout(() => {
                window.location.href = window.TaxPortalConfig.logoutUrl;
            }, logoutMs);
        };

        ['click', 'keydown', 'mousemove', 'scroll', 'touchstart'].forEach((eventName) => {
            document.addEventListener(eventName, resetInactivityTimers, { passive: true });
        });
        resetInactivityTimers();
    }
});
