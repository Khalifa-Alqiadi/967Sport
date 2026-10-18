(function () {
    'use strict';

    function initialiseSlider(root) {
        if (root.dataset.matchdayReady === 'true' || typeof bootstrap === 'undefined') return;

        root.dataset.matchdayReady = 'true';
        var interval = Number(root.dataset.sliderInterval) || 5000;
        root.style.setProperty('--matchday-duration', interval + 'ms');
        var isRtl = root.getAttribute('dir') === 'rtl';
        var current = root.querySelector('[data-matchday-current]');
        var indicators = Array.prototype.slice.call(root.querySelectorAll('[data-matchday-to]'));
        var carousel = bootstrap.Carousel.getOrCreateInstance(root, {
            interval: interval,
            ride: root.querySelectorAll('[data-matchday-slide]').length > 1 ? 'carousel' : false,
            pause: 'hover',
            touch: false,
            wrap: true
        });

        function updateNavigation(index) {
            if (current) current.textContent = String(index + 1).padStart(2, '0');
            indicators.forEach(function (indicator, indicatorIndex) {
                var active = indicatorIndex === index;
                indicator.classList.toggle('is-active', active);
                indicator.setAttribute('aria-selected', active ? 'true' : 'false');
            });
        }

        root.querySelectorAll('[data-matchday-action]').forEach(function (button) {
            button.addEventListener('click', function () {
                button.dataset.matchdayAction === 'next' ? carousel.next() : carousel.prev();
            });
        });

        indicators.forEach(function (indicator) {
            indicator.addEventListener('click', function () {
                carousel.to(Number(indicator.dataset.matchdayTo));
            });
        });

        root.addEventListener('slid.bs.carousel', function (event) {
            updateNavigation(event.to);
        });

        var startX = null;
        var startY = null;
        root.addEventListener('pointerdown', function (event) {
            if (event.pointerType === 'mouse') return;
            startX = event.clientX;
            startY = event.clientY;
        }, { passive: true });

        root.addEventListener('pointerup', function (event) {
            if (startX === null) return;
            var deltaX = event.clientX - startX;
            var deltaY = event.clientY - startY;
            startX = null;
            startY = null;
            if (Math.abs(deltaX) < 45 || Math.abs(deltaX) <= Math.abs(deltaY)) return;

            if (isRtl) {
                deltaX > 0 ? carousel.next() : carousel.prev();
            } else {
                deltaX < 0 ? carousel.next() : carousel.prev();
            }
        }, { passive: true });

        updateNavigation(0);
    }

    function initialiseAll() {
        document.querySelectorAll('[data-matchday-carousel]').forEach(initialiseSlider);
    }

    document.readyState === 'loading'
        ? document.addEventListener('DOMContentLoaded', initialiseAll)
        : initialiseAll();
}());
