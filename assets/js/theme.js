(() => {
  const body = document.body;
  const header = document.querySelector('[data-site-header]');
  const searchOverlay = document.querySelector('[data-search-overlay]');
  const mobilePanel = document.querySelector('[data-mobile-panel]');
  const searchOpen = document.querySelector('[data-search-open]');
  const mobileOpen = document.querySelector('[data-mobile-open]');
  let lastTrigger = null;

  const setLocked = () => {
    body.classList.toggle('is-locked', Boolean(
      searchOverlay?.classList.contains('is-open') || mobilePanel?.classList.contains('is-open')
    ));
  };

  const openPanel = (panel, trigger) => {
    if (!panel) return;
    lastTrigger = trigger || null;
    panel.classList.add('is-open');
    panel.setAttribute('aria-hidden', 'false');
    trigger?.setAttribute('aria-expanded', 'true');
    setLocked();
    window.setTimeout(() => panel.querySelector('input, button, a')?.focus(), 60);
  };

  const closePanel = (panel, trigger) => {
    if (!panel) return;
    panel.classList.remove('is-open');
    panel.setAttribute('aria-hidden', 'true');
    trigger?.setAttribute('aria-expanded', 'false');
    setLocked();
    lastTrigger?.focus();
  };

  searchOpen?.addEventListener('click', () => openPanel(searchOverlay, searchOpen));
  document.querySelector('[data-search-close]')?.addEventListener('click', () => closePanel(searchOverlay, searchOpen));
  mobileOpen?.addEventListener('click', () => openPanel(mobilePanel, mobileOpen));
  document.querySelector('[data-mobile-close]')?.addEventListener('click', () => closePanel(mobilePanel, mobileOpen));
  document.querySelector('[data-mobile-search]')?.addEventListener('click', () => {
    closePanel(mobilePanel, mobileOpen);
    openPanel(searchOverlay, searchOpen);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    if (searchOverlay?.classList.contains('is-open')) closePanel(searchOverlay, searchOpen);
    if (mobilePanel?.classList.contains('is-open')) closePanel(mobilePanel, mobileOpen);
  });

  [searchOverlay, mobilePanel].forEach((panel) => {
    panel?.addEventListener('click', (event) => {
      if (event.target === panel) {
        closePanel(panel, panel === searchOverlay ? searchOpen : mobileOpen);
      }
    });
  });

  const updateHeader = () => header?.classList.toggle('is-scrolled', window.scrollY > 20);
  updateHeader();
  window.addEventListener('scroll', updateHeader, { passive: true });

  const revealItems = document.querySelectorAll('[data-reveal]:not(.hero [data-reveal])');
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -4% 0px' });
    revealItems.forEach((item) => observer.observe(item));
  } else {
    revealItems.forEach((item) => item.classList.add('is-visible'));
  }

  document.querySelectorAll('[data-slider]').forEach((slider) => {
    const track = slider.querySelector('[data-slider-track]');
    const section = slider.closest('section') || slider;
    const previous = section.querySelector('[data-slider-prev]');
    const next = section.querySelector('[data-slider-next]');
    const progress = slider.querySelector('[data-slider-progress]');
    if (!track) return;
    track.querySelectorAll('img, a').forEach((item) => item.setAttribute('draggable', 'false'));
    track.addEventListener('dragstart', (event) => event.preventDefault());

    const updateSlider = () => {
      const maximum = Math.max(0, track.scrollWidth - track.clientWidth);
      const position = Math.min(maximum, Math.max(0, track.scrollLeft));
      const visibleRatio = track.scrollWidth ? Math.min(1, track.clientWidth / track.scrollWidth) : 1;
      const travelRatio = maximum ? position / maximum : 0;
      previous?.toggleAttribute('disabled', position <= 2);
      next?.toggleAttribute('disabled', position >= maximum - 2);
      if (progress) {
        progress.style.width = `${visibleRatio * 100}%`;
        progress.style.left = `${travelRatio * (100 - (visibleRatio * 100))}%`;
      }
    };

    const scrollOne = (direction) => {
      const item = track.firstElementChild;
      const gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap || 0);
      const distance = item ? item.getBoundingClientRect().width + gap : track.clientWidth * 0.8;
      track.scrollBy({ left: direction * distance, behavior: 'smooth' });
    };

    previous?.addEventListener('click', () => scrollOne(-1));
    next?.addEventListener('click', () => scrollOne(1));
    track.addEventListener('scroll', updateSlider, { passive: true });
    track.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowLeft') {
        event.preventDefault();
        scrollOne(-1);
      }
      if (event.key === 'ArrowRight') {
        event.preventDefault();
        scrollOne(1);
      }
    });

    let dragging = false;
    let moved = false;
    let suppressClick = false;
    let startX = 0;
    let startScroll = 0;
    track.addEventListener('pointerdown', (event) => {
      if (event.pointerType !== 'mouse' || event.button !== 0) return;
      dragging = true;
      moved = false;
      startX = event.clientX;
      startScroll = track.scrollLeft;
    });
    track.addEventListener('pointermove', (event) => {
      if (!dragging) return;
      const distance = event.clientX - startX;
      if (!moved && Math.abs(distance) <= 5) return;
      if (!moved) {
        moved = true;
        track.classList.add('is-dragging');
        track.setPointerCapture(event.pointerId);
      }
      track.scrollLeft = startScroll - distance;
    });
    const endDrag = (event) => {
      if (!dragging) return;
      dragging = false;
      track.classList.remove('is-dragging');
      if (track.hasPointerCapture(event.pointerId)) track.releasePointerCapture(event.pointerId);
      suppressClick = moved;
      window.setTimeout(() => { suppressClick = false; }, 0);
      updateSlider();
    };
    track.addEventListener('pointerup', endDrag);
    track.addEventListener('pointercancel', endDrag);
    track.addEventListener('click', (event) => {
      if (!suppressClick) return;
      event.preventDefault();
      event.stopPropagation();
      suppressClick = false;
    }, true);

    if ('ResizeObserver' in window) new ResizeObserver(updateSlider).observe(track);
    updateSlider();
  });

  document.querySelector('[data-newsletter-form]')?.addEventListener('submit', (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    const status = document.querySelector('[data-newsletter-status]');
    if (!form.reportValidity()) return;
    if (status) status.textContent = window.orienteTheme?.newsletterSuccess || 'Thank you for joining Oriente.';
    form.reset();
  });
})();
