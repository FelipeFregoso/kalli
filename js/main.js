(() => {
  'use strict';

  const header = document.querySelector('[data-header]');
  const menuToggle = document.querySelector('[data-menu-toggle]');
  const mobileMenu = document.querySelector('[data-mobile-menu]');
  const mobileLinks = document.querySelectorAll('[data-mobile-link]');
  const phaseLinks = document.querySelectorAll('[data-phase-link]');
  const toast = document.querySelector('[data-toast]');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  let toastTimer;

  const updateHeader = () => {
    if (header) header.classList.toggle('is-scrolled', window.scrollY > 24);
  };

  const closeMenu = () => {
    if (!menuToggle || !mobileMenu) return;
    menuToggle.classList.remove('is-open');
    menuToggle.setAttribute('aria-expanded', 'false');
    menuToggle.setAttribute('aria-label', 'Abrir menú');
    mobileMenu.hidden = true;
    document.body.classList.remove('menu-open');
  };

  const openMenu = () => {
    if (!menuToggle || !mobileMenu) return;
    mobileMenu.hidden = false;
    menuToggle.classList.add('is-open');
    menuToggle.setAttribute('aria-expanded', 'true');
    menuToggle.setAttribute('aria-label', 'Cerrar menú');
    document.body.classList.add('menu-open');
  };

  const showToast = (message) => {
    if (!toast) return;
    window.clearTimeout(toastTimer);
    toast.textContent = message;
    toast.classList.add('is-visible');
    toastTimer = window.setTimeout(() => toast.classList.remove('is-visible'), 3600);
  };

  updateHeader();
  window.addEventListener('scroll', updateHeader, { passive: true });

  menuToggle?.addEventListener('click', () => {
    const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
    isOpen ? closeMenu() : openMenu();
  });

  mobileLinks.forEach((link) => link.addEventListener('click', closeMenu));
  window.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeMenu();
  });

  phaseLinks.forEach((link) => {
    link.addEventListener('click', (event) => {
      event.preventDefault();
      closeMenu();
      showToast('Esta sección se integrará en la siguiente fase de Kalli.');
    });
  });

  if (!reducedMotion && window.gsap) {
    const animateHero = () => {
      const tl = window.gsap.timeline({ defaults: { ease: 'power3.out' } });
      tl.from('.hero__media img', { duration: 1.5, scale: 1.1, opacity: 0.55 })
        .from('[data-hero-item]', { duration: 0.85, y: 28, opacity: 0, stagger: 0.13 }, '-=0.85');
    };

    if (document.readyState === 'complete') {
      animateHero();
    } else {
      window.addEventListener('load', animateHero, { once: true });
    }
  }
})();
