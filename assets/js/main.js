/* File: assets/js/main.js
   Interaksi header: menu hamburger untuk tampilan mobile. */

(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('menu-toggle');
    var menu = document.getElementById('mobile-menu');

    if (!toggle || !menu) {
      return;
    }

    var label = toggle.querySelector('.sr-only');
    var iconOpen = toggle.querySelector('[data-icon="open"]');
    var iconClose = toggle.querySelector('[data-icon="close"]');

    function setMenu(open) {
      menu.classList.toggle('hidden', !open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');

      if (label) {
        label.textContent = open ? 'Tutup menu' : 'Buka menu';
      }

      if (iconOpen && iconClose) {
        iconOpen.classList.toggle('hidden', open);
        iconClose.classList.toggle('hidden', !open);
      }
    }

    toggle.addEventListener('click', function () {
      setMenu(menu.classList.contains('hidden'));
    });

    // Tutup menu setelah salah satu link diklik.
    menu.addEventListener('click', function (event) {
      if (event.target instanceof Element && event.target.closest('a')) {
        setMenu(false);
      }
    });

    // Tutup dengan tombol Escape dan kembalikan fokus ke tombol.
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && !menu.classList.contains('hidden')) {
        setMenu(false);
        toggle.focus();
      }
    });
  });
})();
