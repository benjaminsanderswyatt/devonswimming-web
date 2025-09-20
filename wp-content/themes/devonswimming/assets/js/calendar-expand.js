(() => {
  /*--------- CONFIG ---------*/
  const CONFIG = {
    WRAPPER_SELECTOR: '.ics-calendar-date-wrapper',
    MONTH_LABEL_SELECTOR: '.ics-calendar-label', // e.g. August 2025
    TIME_SELECTOR: 'time[datetime]', // optional if present
    PARAM_KEY: 'r34icsym', // YYYYMM for url
    // Accept these month names (short/long, case insensitive)
    MONTHS: {
      jan: 1, january: 1,
      feb: 2, february: 2,
      mar: 3, march: 3,
      apr: 4, april: 4,
      may: 5,
      jun: 6, june: 6,
      jul: 7, july: 7,
      aug: 8, august: 8,
      sep: 9, sept: 9, september: 9,
      oct: 10, october: 10,
      nov: 11, november: 11,
      dec: 12, december: 12
    }
  };

  /*--------- HELPERS ---------*/
  const pad2 = n => (n < 10 ? '0' + n : '' + n);

  const sameYearMonth = (a, b) => a && b && a.y === b.y && a.m === b.m;

  const getCurrentYM = () => {
    const d = new Date();
    return { y: d.getFullYear(), m: d.getMonth() + 1 };
  };


  // Find the WordPress home URL (preferred), else use the origin.
  const getSiteBaseUrl = () => {
    const loc = new URL(location.href);
    const homeEl = document.querySelector('link[rel="home"]');
    if (homeEl?.href) {
      try {
        const homeURL = new URL(homeEl.href, loc);
        if (homeURL.host === loc.host && homeURL.protocol === loc.protocol) {
          if (!homeURL.pathname.endsWith('/')) homeURL.pathname += '/';
          return homeURL;
        }
      } catch { }
    }
    return new URL('/', loc);
  };


  // Always produce <site-base>/calendar/ as an absolute URL
  const getCalendarBaseUrl = () => {
    const siteBase = getSiteBaseUrl();
    const cal = new URL('calendar/', siteBase);
    return cal.href;
  };

  const ymFromTime = (wrapper) => {
    const t = wrapper.querySelector(CONFIG.TIME_SELECTOR);
    if (!t) return null;
    const iso = t.getAttribute('datetime');
    if (!iso) return null;
    const d = new Date(iso);
    if (isNaN(d)) return null;
    return { y: d.getFullYear(), m: d.getMonth() + 1 };
  };

  // Find the nearest previous ".ics-calendar-label" (e.g. August 2025)
  const findPrevMonthLabel = (wrapper) => {

    let p = wrapper.parentElement;
    // Stop climbing at the calendar root or document
    while (p && !p.classList.contains('ics-calendar') && !p.classList.contains('ics-calendar-list-wrapper')) {
      p = p.parentElement;
    }
    // Now walk backwards from wrapper within this container to find a label
    let node = wrapper;
    while (node && node !== p) node = node.previousElementSibling;
    // If we hit p directly, start from wrapper and walk prev siblings
    node = wrapper.previousElementSibling;
    while (node) {
      if (node.matches(CONFIG.MONTH_LABEL_SELECTOR)) return node;
      node = node.previousElementSibling;
    }
    // Fallback look upward then scan backwards across siblings of that parent
    if (p) {
      node = p.previousElementSibling;
      while (node) {
        if (node.matches && node.matches(CONFIG.MONTH_LABEL_SELECTOR)) return node;
        node = node.previousElementSibling;
      }
    }
    return null;
  };

  // Parse August 2025 (or "Aug 2025") to {y:2025, m:8}
  const ymFromLabelText = (text) => {
    if (!text) return null;
    const m = text.trim().match(/([A-Za-z]+)\s+(\d{4})/);
    if (!m) return null;
    const name = m[1].toLowerCase();
    const year = parseInt(m[2], 10);
    const month = CONFIG.MONTHS[name];
    if (!month || !year) return null;
    return { y: year, m: month };
  };

  const ymFromLabel = (wrapper) => {
    const label = findPrevMonthLabel(wrapper);
    if (!label) return null;
    return ymFromLabelText(label.textContent);
  };

  // Derive {y,m} for the clicked wrapper
  const getYearMonthForWrapper = (wrapper) => {
    return ymFromTime(wrapper) || ymFromLabel(wrapper) || getCurrentYM();
  };

  const buildMonthUrl = ({ y, m }) => {
    const now = getCurrentYM();
    const base = getCalendarBaseUrl(); // dynamic, site-aware
    if (sameYearMonth({ y, m }, now)) {
      // Current month: plain /calendar/ with no param
      return base;
    }
    const url = new URL(base);
    url.searchParams.set(CONFIG.PARAM_KEY, `${y}${pad2(m)}`);
    return url.href;
  };

  // Respect modifier keys and middle click
  const navigate = (url, evt) => {
    const target = new URL(url, location.href);
    // Strip hash for equality check; keep query/path/protocol/host
    const current = new URL(location.href);
    if (!target.pathname.endsWith('/')) target.pathname += '/';
    if (!current.pathname.endsWith('/')) current.pathname += '/';
    target.hash = '';
    current.hash = '';

    if (target.href === current.href) return; // no-op; don't create a new history entry

    if (evt && (evt.metaKey || evt.ctrlKey || evt.shiftKey || evt.altKey || evt.button === 1)) {
      window.open(target.href, '_blank', 'noopener');
    } else {
      location.assign(target.href); // assign vs href is fine; both create a single entry
    }
  };

  // Dont hijack real links inside
  const clickIsOnInteractive = (e) =>
    !!e.target.closest('a, button, input, textarea, select, [role="button"]');

  /*--------- Bindings ---------*/
  const installWrapper = (wrapper) => {
    if (wrapper._navBound) return;
    wrapper._navBound = true;

    // Make it feel like a link
    wrapper.setAttribute('role', 'link');
    if (!wrapper.hasAttribute('tabindex')) wrapper.setAttribute('tabindex', '0');
    wrapper.style.cursor = 'pointer';

    // Click go to month
    wrapper.addEventListener('click', (e) => {
      if (clickIsOnInteractive(e)) return; // Let native links/buttons work
      const ym = getYearMonthForWrapper(wrapper);
      const url = buildMonthUrl(ym);
      e.preventDefault();
      navigate(url, e);
    });

    // Middle click support
    wrapper.addEventListener('auxclick', (e) => {
      if (e.button !== 1) return;
      if (clickIsOnInteractive(e)) return;
      const ym = getYearMonthForWrapper(wrapper);
      const url = buildMonthUrl(ym);
      e.preventDefault();
      navigate(url, e);
    });

    // Keyboard (Enter/Space)
    wrapper.addEventListener('keydown', (e) => {
      if (e.key !== 'Enter' && e.key !== ' ') return;
      if (clickIsOnInteractive(e)) return;
      const ym = getYearMonthForWrapper(wrapper);
      const url = buildMonthUrl(ym);
      e.preventDefault();
      navigate(url, e);
    });
  };

  const initExisting = () => {
    document.querySelectorAll(CONFIG.WRAPPER_SELECTOR).forEach(installWrapper);
  };

  // Observe for dynamically injected calendar items
  const mo = new MutationObserver((muts) => {
    for (const m of muts) {
      m.addedNodes.forEach(node => {
        if (!(node instanceof Element)) return;
        if (node.matches?.(CONFIG.WRAPPER_SELECTOR)) {
          installWrapper(node);
        } else {
          node.querySelectorAll?.(CONFIG.WRAPPER_SELECTOR).forEach(installWrapper);
        }
      });
    }
  });

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      initExisting();
      mo.observe(document.body, { childList: true, subtree: true });
    });
  } else {
    initExisting();
    mo.observe(document.body, { childList: true, subtree: true });
  }
})();




























// Close ICS Calendar lightbox when clicking outside the card or pressing Esc
(function () {
  function closeLightbox(lb) {
    if (!lb) return;
    lb.classList.remove('open');
    document.body.classList.remove('r34ics-noscroll');
  }

  // click on backdrop (outside content) closes
  document.addEventListener('click', function (e) {
    // is there an open lightbox?
    const openBoxes = document.querySelectorAll('.r34ics_lightbox.open');
    if (!openBoxes.length) return;

    // if the click target IS the backdrop (not the inner content), close it
    openBoxes.forEach(lb => {
      if (e.target === lb) {
        closeLightbox(lb);
      }
    });
  });

  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.r34ics_lightbox_close');
    if (!btn) return;
    const lb = btn.closest('.r34ics_lightbox');
    closeLightbox(lb);
  });

  // Esc key closes
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    document.querySelectorAll('.r34ics_lightbox.open').forEach(closeLightbox);
  });
})();
