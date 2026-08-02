(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var toggle = document.getElementById('medbook-nav-toggle');
        var nav = document.getElementById('medbook-nav');
        if (!toggle || !nav) {
            return;
        }

        toggle.addEventListener('click', function () {
            var isOpen = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        document.addEventListener('click', function (e) {
            if (!nav.contains(e.target) && !toggle.contains(e.target)) {
                nav.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    });
})();
