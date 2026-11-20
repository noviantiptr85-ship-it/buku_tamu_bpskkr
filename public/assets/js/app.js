(function () {
    'use strict';

    // Jam realtime (kiosk)
    var clock = document.getElementById('clock');
    var dateEl = document.getElementById('date');

    function updateClock() {
        var now = new Date();
        if (clock) {
            clock.textContent = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        if (dateEl) {
            dateEl.textContent = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }
    }

    if (clock || dateEl) {
        updateClock();
        setInterval(updateClock, 1000);
    }

    // Konfirmasi aksi berbahaya
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            var message = form.getAttribute('data-confirm') || 'Lanjutkan aksi ini?';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    // Alert hilang otomatis
    document.querySelectorAll('.alert-success').forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity .4s ease';
            alert.style.opacity = '0';
            setTimeout(function () {
                alert.remove();
            }, 420);
        }, 6000);
    });
})();
