(() => {
  /* =========================
     CONFIG
  ========================== */
  const CONFIG = {
    WRAPPER_SELECTOR: '.ics-calendar-date-wrapper',
    MONTH_LABEL_SELECTOR: '.ics-calendar-label',           // e.g. "August 2025"
    TIME_SELECTOR: 'time[datetime]',                       // optional, if present
    CAL_URL: 'https://devonswimming-new.ddev.site/calendar/',
    PARAM_KEY: 'r34icsym',                                 // YYYYMM
    // Accept these month names (short/long, case-insensitive)
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

  /* =========================
     HELPERS
  ========================== */
  const pad2 = n => (n < 10 ? '0' + n : '' + n);

  const sameYearMonth = (a, b) => a && b && a.y === b.y && a.m === b.m;

  const getCurrentYM = () => {
    const d = new Date();
    return { y: d.getFullYear(), m: d.getMonth() + 1 };
  };

  // Try 1: a <time datetime="..."> somewhere in the wrapper
  const ymFromTime = (wrapper) => {
    const t = wrapper.querySelector(CONFIG.TIME_SELECTOR);
    if (!t) return null;
    const iso = t.getAttribute('datetime');
    if (!iso) return null;
    const d = new Date(iso);
    if (isNaN(d)) return null;
    return { y: d.getFullYear(), m: d.getMonth() + 1 };
  };

  // Find the nearest previous ".ics-calendar-label" (e.g., "August 2025")
  const findPrevMonthLabel = (wrapper) => {
    // Climb to a logical list container boundary
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
    // As a fallback, look upward then scan backwards across siblings of that parent
    if (p) {
      node = p.previousElementSibling;
      while (node) {
        if (node.matches && node.matches(CONFIG.MONTH_LABEL_SELECTOR)) return node;
        node = node.previousElementSibling;
      }
    }
    return null;
  };

  // Parse "August 2025" (or "Aug 2025") → {y:2025, m:8}
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
    if (sameYearMonth({ y, m }, now)) {
      // current month: plain calendar URL (no param)
      return CONFIG.CAL_URL;
    }
    return CONFIG.CAL_URL + '?' + CONFIG.PARAM_KEY + '=' + y + pad2(m);
  };

  // Respect modifier keys and middle click
  const navigate = (url, evt) => {
    if (evt && (evt.metaKey || evt.ctrlKey || evt.shiftKey || evt.altKey || evt.button === 1)) {
      window.open(url, '_blank', 'noopener');
      return;
    }
    window.location.href = url;
  };

  // Don’t hijack real links inside
  const clickIsOnInteractive = (e) =>
    !!e.target.closest('a, button, input, textarea, select, [role="button"]');

  /* =========================
     BINDINGS
  ========================== */
  const installWrapper = (wrapper) => {
    if (wrapper._navBound) return;
    wrapper._navBound = true;

    // Make it feel like a link
    wrapper.setAttribute('role', 'link');
    if (!wrapper.hasAttribute('tabindex')) wrapper.setAttribute('tabindex', '0');
    wrapper.style.cursor = 'pointer';

    // Click → go to month
    wrapper.addEventListener('click', (e) => {
      if (clickIsOnInteractive(e)) return; // let native links/buttons work
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
