/*
  Header menu toggle for mobile view
*/

document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('.main-nav');
  const menu = document.getElementById('primary-menu');  // used to close after click
  const panel = document.getElementById('menus-panel');
  
  // Ensure all elements exist
  if (!toggle || !nav || !panel) return;


  // Config
  const DURATION = 320; // ms
  const EASING = 'ease';
  const mq = window.matchMedia('(max-width: 900px)');

  // State flag. prevent double-taps
  let animating = false;

  // Helpers
  const isOpen = () => nav.classList.contains('is-open');

  /* Apply the "closed" baseline for mobile */
  const applyMobileBaseline = () => {
    panel.style.transition = [
      `max-height ${DURATION}ms ${EASING}`,
      `opacity ${DURATION}ms ${EASING}`,
      `transform ${DURATION}ms ${EASING}`,
    ].join(', ');
    panel.style.maxHeight = '0px';
    panel.style.opacity   = '0';
    panel.style.transform = 'translateY(-6px)';

    nav.classList.remove('is-open');
    toggle.setAttribute('aria-expanded', 'false');
    panel.setAttribute('aria-hidden', 'true');
  };

  /* Remove mobile only styles so desktop CSS overrides */
  const clearInlineStyles = () => {
    panel.style.transition = '';
    panel.style.maxHeight  = '';
    panel.style.opacity    = '';
    panel.style.transform  = '';

    nav.classList.remove('is-open');
    toggle.setAttribute('aria-expanded', 'false');
    panel.setAttribute('aria-hidden', 'false');
  };

  // Initial state based on current viewport
  if (mq.matches) applyMobileBaseline();
  else clearInlineStyles();

  /* Open animation */
  const openMenu = () => {
    if (!mq.matches || animating || isOpen()) return;
    animating = true;

    nav.classList.add('is-open');
    toggle.setAttribute('aria-expanded', 'true');
    panel.setAttribute('aria-hidden', 'false');

    if (panel.style.maxHeight === 'none') panel.style.maxHeight = '0px';

    requestAnimationFrame(() => {
      panel.style.opacity   = '1';
      panel.style.transform = 'translateY(0)';
      panel.style.maxHeight = panel.scrollHeight + 'px';
    });

    const onEnd = (e) => {
      if (e.target !== panel || e.propertyName !== 'max-height') return;
      panel.removeEventListener('transitionend', onEnd);
      panel.style.maxHeight = 'none';
      animating = false;
    };
    panel.addEventListener('transitionend', onEnd);
  };

  /* Close animation */
  const closeMenu = () => {
    if (!mq.matches || animating || !isOpen()) return;
    animating = true;

    if (panel.style.maxHeight === '' || panel.style.maxHeight === 'none') {
      panel.style.maxHeight = panel.scrollHeight + 'px';
    }

    requestAnimationFrame(() => {
      panel.style.opacity   = '0';
      panel.style.transform = 'translateY(-6px)';
      // Queue height collapse one frame later to ensure transition
      requestAnimationFrame(() => {
        panel.style.maxHeight = '0px';
      });
    });

    const onEnd = (e) => {
      if (e.target !== panel || e.propertyName !== 'max-height') return;
      panel.removeEventListener('transitionend', onEnd);

      nav.classList.remove('is-open');
      toggle.setAttribute('aria-expanded', 'false');
      panel.setAttribute('aria-hidden', 'true');
      animating = false;
    };
    panel.addEventListener('transitionend', onEnd);
  };

  // Toggle via button (mobile)
  toggle.addEventListener('click', () => (isOpen() ? closeMenu() : openMenu()));

  // Close on Esc key (mobile)
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && mq.matches && isOpen()) closeMenu();
  });

  // Close on outside click (mobile)
  document.addEventListener('click', (e) => {
    if (!mq.matches || !isOpen()) return;
    const within = nav.contains(e.target) || toggle.contains(e.target);
    if (!within) closeMenu();
  });

  // Close after clicking any link in the primary menu (mobile)
  if (menu) {
    menu.addEventListener('click', (e) => {
      if (!mq.matches) return;
      const link = e.target.closest('a');
      if (link) closeMenu();
    });
  }

  // Swap behavior when crossing the breakpoint
  const onBreakpointChange = (e) => {
    if (e.matches) {
      applyMobileBaseline();
    } else {
      clearInlineStyles();
    }
  };

  // Modern + legacy Safari
  if (mq.addEventListener) mq.addEventListener('change', onBreakpointChange);
  else mq.addListener(onBreakpointChange);

  /* Keep height in sync */
  const syncOpenHeight = () => {
    if (mq.matches && isOpen()) {
      panel.style.maxHeight = panel.scrollHeight + 'px';
    }
  };
  window.addEventListener('resize', syncOpenHeight);
});





