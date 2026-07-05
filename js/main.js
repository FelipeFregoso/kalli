(() => {
  'use strict';

  const header = document.querySelector('[data-header]');
  const menuToggle = document.querySelector('[data-menu-toggle]');
  const menuPanel = document.querySelector('[data-menu-panel]');
  const menuClose = document.querySelector('[data-menu-close]');
  const menuLinks = document.querySelectorAll('[data-menu-link]');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const setHeaderState = () => header?.classList.toggle('is-scrolled', window.scrollY > 12);

  const closeMenu = () => {
    if (!menuPanel || !menuToggle) return;
    menuPanel.hidden = true;
    menuToggle.setAttribute('aria-expanded', 'false');
    menuToggle.setAttribute('aria-label', 'Abrir menú');
    document.body.classList.remove('menu-open');
  };

  const openMenu = () => {
    if (!menuPanel || !menuToggle) return;
    menuPanel.hidden = false;
    menuToggle.setAttribute('aria-expanded', 'true');
    menuToggle.setAttribute('aria-label', 'Cerrar menú');
    document.body.classList.add('menu-open');
    menuClose?.focus();
  };

  setHeaderState();
  window.addEventListener('scroll', setHeaderState, { passive: true });

  menuToggle?.addEventListener('click', () => {
    menuPanel?.hidden ? openMenu() : closeMenu();
  });
  menuClose?.addEventListener('click', closeMenu);
  menuLinks.forEach((link) => link.addEventListener('click', closeMenu));
  window.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !menuPanel?.hidden) closeMenu();
  });

  document.querySelectorAll('[data-year]').forEach((node) => {
    node.textContent = new Date().getFullYear();
  });

  const revealItems = document.querySelectorAll('[data-reveal]');
  if (reducedMotion || !('IntersectionObserver' in window)) {
    revealItems.forEach((item) => item.classList.add('is-visible'));
    return;
  }

  const observer = new IntersectionObserver((entries, activeObserver) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('is-visible');
      activeObserver.unobserve(entry.target);
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -32px' });

  revealItems.forEach((item) => observer.observe(item));
})();
