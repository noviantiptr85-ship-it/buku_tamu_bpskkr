(function () {
    'use strict';

    /* -------------------------------------------------------------- */
    /* Jam realtime                                                   */
    /* -------------------------------------------------------------- */
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

    /* -------------------------------------------------------------- */
    /* Slideshow infografis                                           */
    /* -------------------------------------------------------------- */
    var slideshow = document.getElementById('slideshow');

    if (slideshow) {
        var slides = Array.prototype.slice.call(slideshow.querySelectorAll('.slide'));
        var dots = Array.prototype.slice.call(slideshow.querySelectorAll('.dot'));
        var interval = Math.max(3, parseInt(slideshow.getAttribute('data-interval') || '5', 10)) * 1000;
        var index = 0;
        var timer = null;
        var paused = false;
        var resumeTimer = null;

        function show(next) {
            if (slides.length === 0) {
                return;
            }
            index = (next + slides.length) % slides.length;
            slides.forEach(function (slide, i) {
                slide.classList.toggle('is-active', i === index);
            });
            dots.forEach(function (dot, i) {
                dot.classList.toggle('is-active', i === index);
            });
        }

        function stop() {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        }

        function start() {
            stop();
            if (slides.length > 1) {
                timer = setInterval(function () {
                    if (!paused) {
                        show(index + 1);
                    }
                }, interval);
            }
        }

        function pauseTemporarily() {
            paused = true;
            if (resumeTimer) {
                clearTimeout(resumeTimer);
            }
            resumeTimer = setTimeout(function () {
                paused = false;
            }, 10000);
        }

        dots.forEach(function (dot, i) {
            dot.addEventListener('click', function () {
                show(i);
                start();
                pauseTemporarily();
            });
        });

        var startX = null;
        var startY = null;

        slideshow.addEventListener('pointerdown', function (event) {
            startX = event.clientX;
            startY = event.clientY;
            pauseTemporarily();
        });

        slideshow.addEventListener('pointerup', function (event) {
            if (startX === null) {
                return;
            }
            var dx = event.clientX - startX;
            var dy = event.clientY - startY;
            startX = null;
            startY = null;

            if (Math.abs(dx) > 45 && Math.abs(dx) > Math.abs(dy)) {
                show(dx < 0 ? index + 1 : index - 1);
            }
        });

        slideshow.addEventListener('pointercancel', function () {
            startX = null;
            startY = null;
        });

        slideshow.addEventListener('mouseenter', function () {
            paused = true;
        });

        slideshow.addEventListener('mouseleave', function () {
            paused = false;
        });

        show(0);
        start();
    }

    /* -------------------------------------------------------------- */
    /* Hitung mundur layar sukses                                     */
    /* -------------------------------------------------------------- */
    var success = document.querySelector('[data-seconds]');

    if (success) {
        var total = Math.max(1, parseInt(success.getAttribute('data-seconds') || '5', 10));
        var redirectUrl = success.getAttribute('data-redirect') || '/';
        var counter = document.getElementById('countdown');
        var bar = document.getElementById('countdown-bar');
        var left = total;

        var tick = function () {
            if (counter) {
                counter.textContent = String(left);
            }
            if (bar) {
                bar.style.width = (((total - left) / total) * 100) + '%';
            }
            if (left <= 0) {
                window.location.href = redirectUrl;
                return;
            }
            left -= 1;
            setTimeout(tick, 1000);
        };

        tick();
    }
})();
