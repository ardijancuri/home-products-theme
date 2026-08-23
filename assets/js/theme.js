(() => {
  const body = document.body;
  const header = document.querySelector('[data-site-header]');
  const searchOverlay = document.querySelector('[data-search-overlay]');
  const mobilePanel = document.querySelector('[data-mobile-panel]');
  const cartDrawer = document.querySelector('[data-cart-drawer]');
  const searchOpen = document.querySelector('[data-search-open]');
  const mobileOpen = document.querySelector('[data-mobile-open]');
  const mobileToggleLabel = mobileOpen?.querySelector('.mobile-toggle-label');
  let lastTrigger = null;
  let cartLastTrigger = null;

  const setMobileToggleLabel = (isOpen) => {
    if (!mobileOpen) return;
    const label = isOpen ? mobileOpen.dataset.closeLabel : mobileOpen.dataset.openLabel;
    mobileOpen.setAttribute('aria-label', label);
    if (mobileToggleLabel) mobileToggleLabel.textContent = label;
  };

  const syncMobilePanelPosition = () => {
    if (!mobilePanel || !header) return;
    const headerBottom = Math.max(0, Math.round(header.getBoundingClientRect().bottom));
    mobilePanel.style.setProperty('--mobile-panel-top', `${headerBottom}px`);
  };

  const updateCartBadge = (count) => {
    const cartLink = document.querySelector('.cart-action');
    const badge = cartLink?.querySelector('.cart-count');
    if (!cartLink || !badge) return;
    const normalizedCount = Math.max(0, Math.round(Number(count) || 0));
    const labelTemplate = normalizedCount === 1
      ? cartLink.dataset.cartLabelSingular
      : cartLink.dataset.cartLabelPlural;
    badge.hidden = normalizedCount < 1;
    badge.textContent = normalizedCount > 0 ? String(normalizedCount) : '';
    if (labelTemplate) cartLink.setAttribute('aria-label', labelTemplate.replace('%d', normalizedCount));
  };

  const cartPage = document.querySelector('.wp-block-woocommerce-cart, .woocommerce-cart-form, .oriente-empty-cart');
  if (cartPage) {
    let cartSyncFrame = 0;
    const syncCartBadgeFromPage = () => {
      cartSyncFrame = 0;
      if (cartPage.querySelector('.wc-block-cart__empty-cart__title, .oriente-empty-cart')) {
        updateCartBadge(0);
        return;
      }
      const quantityInputs = [...cartPage.querySelectorAll('.wc-block-components-quantity-selector__input, input.qty')];
      if (!quantityInputs.length) return;
      const cartCount = quantityInputs.reduce((total, input) => total + Math.max(0, Number(input.value) || 0), 0);
      updateCartBadge(cartCount);
    };
    const queueCartBadgeSync = () => {
      window.cancelAnimationFrame(cartSyncFrame);
      cartSyncFrame = window.requestAnimationFrame(syncCartBadgeFromPage);
    };
    new MutationObserver(queueCartBadgeSync).observe(cartPage, {
      attributes: true,
      attributeFilter: ['value'],
      childList: true,
      subtree: true,
    });
    cartPage.addEventListener('input', queueCartBadgeSync);
    cartPage.addEventListener('change', queueCartBadgeSync);
    queueCartBadgeSync();
  }

  const setLocked = () => {
    body.classList.toggle('is-locked', Boolean(
      searchOverlay?.classList.contains('is-open')
      || mobilePanel?.classList.contains('is-open')
      || cartDrawer?.classList.contains('is-open')
    ));
  };

  const openPanel = (panel, trigger) => {
    if (!panel) return;
    lastTrigger = trigger || null;
    panel.classList.add('is-open');
    panel.setAttribute('aria-hidden', 'false');
    trigger?.setAttribute('aria-expanded', 'true');
    if (trigger === mobileOpen) setMobileToggleLabel(true);
    setLocked();
    window.setTimeout(() => panel.querySelector('input, button, a')?.focus(), 60);
  };

  const closePanel = (panel, trigger) => {
    if (!panel) return;
    panel.classList.remove('is-open');
    panel.setAttribute('aria-hidden', 'true');
    trigger?.setAttribute('aria-expanded', 'false');
    if (trigger === mobileOpen) setMobileToggleLabel(false);
    setLocked();
    lastTrigger?.focus();
  };

  const syncCartDrawerState = () => {
    document.querySelector('.cart-action')?.setAttribute(
      'aria-expanded',
      cartDrawer?.classList.contains('is-open') ? 'true' : 'false'
    );
  };

  const openCartDrawer = (trigger) => {
    if (!cartDrawer) return;
    if (searchOverlay?.classList.contains('is-open')) closePanel(searchOverlay, searchOpen);
    if (mobilePanel?.classList.contains('is-open')) closePanel(mobilePanel, mobileOpen);
    cartLastTrigger = trigger instanceof HTMLElement ? trigger : document.querySelector('.cart-action');
    cartDrawer.classList.add('is-open');
    cartDrawer.setAttribute('aria-hidden', 'false');
    syncCartDrawerState();
    setLocked();
    window.setTimeout(() => cartDrawer.querySelector('[data-cart-close]')?.focus(), 60);
  };

  const closeCartDrawer = (restoreFocus = true) => {
    if (!cartDrawer) return;
    cartDrawer.classList.remove('is-open');
    cartDrawer.setAttribute('aria-hidden', 'true');
    syncCartDrawerState();
    setLocked();
    if (restoreFocus) cartLastTrigger?.focus();
  };

  const removeViewCartLinks = () => {
    document.querySelectorAll('a.added_to_cart.wc-forward').forEach((link) => link.remove());
  };

  document.addEventListener('click', (event) => {
    const cartOpen = event.target.closest('[data-cart-open]');
    if (cartOpen) {
      event.preventDefault();
      openCartDrawer(cartOpen);
      return;
    }
    if (event.target.closest('[data-cart-close]')) closeCartDrawer();
  });

  if (window.jQuery) {
    const cartDrawerVersion = window.orienteTheme?.cartDrawerVersion;
    if (cartDrawerVersion) {
      let cachedDrawerVersion = '';
      try {
        cachedDrawerVersion = window.localStorage.getItem('orienteCartDrawerVersion') || '';
        window.localStorage.setItem('orienteCartDrawerVersion', cartDrawerVersion);
      } catch (error) {
        cachedDrawerVersion = cartDrawerVersion;
      }
      if (cachedDrawerVersion !== cartDrawerVersion) {
        window.jQuery(document.body).trigger('wc_fragment_refresh');
      }
    }

    window.jQuery(document.body)
      .on('added_to_cart', (event, fragments, cartHash, button) => {
        removeViewCartLinks();
        openCartDrawer(button?.get?.(0) || document.activeElement);
        window.requestAnimationFrame(() => {
          removeViewCartLinks();
          syncCartDrawerState();
        });
        window.setTimeout(() => {
          removeViewCartLinks();
          syncCartDrawerState();
        }, 180);
      })
      .on('removed_from_cart wc_fragments_refreshed', () => {
        removeViewCartLinks();
        syncCartDrawerState();
      });
  }

  document.body.addEventListener('wc-blocks_added_to_cart', () => {
    removeViewCartLinks();
    openCartDrawer(document.activeElement);
  });

  removeViewCartLinks();
  if (cartDrawer && 'MutationObserver' in window) {
    new MutationObserver(() => {
      removeViewCartLinks();
      if (cartDrawer.classList.contains('is-open')) syncCartDrawerState();
    }).observe(body, { childList: true, subtree: true });
  }

  searchOpen?.addEventListener('click', () => openPanel(searchOverlay, searchOpen));
  document.querySelector('[data-search-close]')?.addEventListener('click', () => closePanel(searchOverlay, searchOpen));
  mobileOpen?.addEventListener('click', () => {
    if (mobilePanel?.classList.contains('is-open')) {
      closePanel(mobilePanel, mobileOpen);
      return;
    }
    syncMobilePanelPosition();
    openPanel(mobilePanel, mobileOpen);
  });
  document.querySelector('[data-mobile-search]')?.addEventListener('click', () => {
    closePanel(mobilePanel, mobileOpen);
    openPanel(searchOverlay, searchOpen);
  });
  const megaMenus = [...document.querySelectorAll('[data-mega-menu]')];
  const closeMegaMenus = (exception = null) => {
    megaMenus.forEach((menu) => {
      if (menu === exception) return;
      menu.classList.remove('is-open');
      menu.querySelector('[data-mega-toggle]')?.setAttribute('aria-expanded', 'false');
    });
  };

  megaMenus.forEach((menu) => {
    const toggle = menu.querySelector('[data-mega-toggle]');
    const openFromPointer = () => {
      window.clearTimeout(menu.orienteMegaCloseTimer);
      if (!window.matchMedia('(min-width: 783px)').matches) return;
      closeMegaMenus(menu);
      menu.classList.add('is-open');
      toggle?.setAttribute('aria-expanded', 'true');
    };
    const closeFromPointer = () => {
      window.clearTimeout(menu.orienteMegaCloseTimer);
      menu.orienteMegaCloseTimer = window.setTimeout(() => {
        if (menu.matches(':hover') || menu.contains(document.activeElement)) return;
        menu.classList.remove('is-open');
        toggle?.setAttribute('aria-expanded', 'false');
      }, 160);
    };
    menu.addEventListener('mouseenter', openFromPointer);
    menu.addEventListener('mouseleave', closeFromPointer);
    toggle?.addEventListener('click', (event) => {
      event.preventDefault();
      event.stopPropagation();
      window.clearTimeout(menu.orienteMegaCloseTimer);
      const willOpen = !menu.classList.contains('is-open');
      closeMegaMenus(menu);
      menu.classList.toggle('is-open', willOpen);
      toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
    });
    menu.addEventListener('focusout', () => {
      window.setTimeout(() => {
        if (menu.contains(document.activeElement)) return;
        menu.classList.remove('is-open');
        toggle?.setAttribute('aria-expanded', 'false');
      }, 0);
    });
  });

  document.addEventListener('click', (event) => {
    if (!event.target.closest('[data-mega-menu]')) closeMegaMenus();
  });

  document.querySelectorAll('[data-mobile-category]').forEach((group) => {
    const toggle = group.querySelector('[data-mobile-category-toggle]');
    const panel = group.querySelector('[data-mobile-category-panel]');
    toggle?.addEventListener('click', () => {
      const willOpen = panel?.hasAttribute('hidden');
      document.querySelectorAll('[data-mobile-category]').forEach((otherGroup) => {
        if (otherGroup === group) return;
        otherGroup.querySelector('[data-mobile-category-panel]')?.setAttribute('hidden', '');
        otherGroup.querySelector('[data-mobile-category-toggle]')?.setAttribute('aria-expanded', 'false');
        otherGroup.classList.remove('is-open');
      });
      panel?.toggleAttribute('hidden', !willOpen);
      toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
      group.classList.toggle('is-open', willOpen);
    });
  });

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    const openMegaToggle = document.querySelector('[data-mega-menu].is-open [data-mega-toggle]');
    closeMegaMenus();
    openMegaToggle?.focus();
    if (searchOverlay?.classList.contains('is-open')) closePanel(searchOverlay, searchOpen);
    if (mobilePanel?.classList.contains('is-open')) closePanel(mobilePanel, mobileOpen);
    if (cartDrawer?.classList.contains('is-open')) closeCartDrawer();
  });

  [searchOverlay, mobilePanel].forEach((panel) => {
    panel?.addEventListener('click', (event) => {
      if (event.target === panel) {
        closePanel(panel, panel === searchOverlay ? searchOpen : mobileOpen);
      }
    });
  });

  const updateHeader = () => {
    header?.classList.toggle('is-scrolled', window.scrollY > 20);
    if (mobilePanel?.classList.contains('is-open')) syncMobilePanelPosition();
  };
  updateHeader();
  window.addEventListener('scroll', updateHeader, { passive: true });
  window.addEventListener('resize', () => {
    if (mobilePanel?.classList.contains('is-open')) syncMobilePanelPosition();
  }, { passive: true });

  const aboutParallaxImage = document.querySelector('[data-about-parallax]');
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  if (aboutParallaxImage && !prefersReducedMotion.matches) {
    let aboutParallaxFrame = 0;
    const updateAboutParallax = () => {
      aboutParallaxFrame = 0;
      const hero = aboutParallaxImage.closest('.about-hero');
      if (!hero) return;
      const bounds = hero.getBoundingClientRect();
      if (bounds.bottom < 0 || bounds.top > window.innerHeight) return;
      const distance = Math.min(26, Math.max(0, -bounds.top * 0.055));
      aboutParallaxImage.style.setProperty('--about-parallax-y', `${distance}px`);
    };
    const queueAboutParallax = () => {
      if (aboutParallaxFrame) return;
      aboutParallaxFrame = window.requestAnimationFrame(updateAboutParallax);
    };
    window.addEventListener('scroll', queueAboutParallax, { passive: true });
    window.addEventListener('resize', queueAboutParallax, { passive: true });
    updateAboutParallax();
  }

  document.querySelectorAll('[data-home-hero-slider]').forEach((slider) => {
    const slides = [...slider.querySelectorAll('[data-home-hero-slide]')];
    const status = slider.querySelector('[data-home-hero-status]');
    if (slides.length < 2) return;

    let activeIndex = Math.max(0, slides.findIndex((slide) => slide.classList.contains('is-active')));
    let autoplayTimer = 0;

    const setSlideFocusable = (slide, isActive) => {
      slide.querySelectorAll('a, button, input, select, textarea, [tabindex]').forEach((element) => {
        if (isActive) {
          if (element.dataset.heroPreviousTabindex !== undefined) {
            const previousTabindex = element.dataset.heroPreviousTabindex;
            delete element.dataset.heroPreviousTabindex;
            if (previousTabindex) element.setAttribute('tabindex', previousTabindex);
            else element.removeAttribute('tabindex');
          }
          return;
        }
        if (element.dataset.heroPreviousTabindex === undefined) {
          element.dataset.heroPreviousTabindex = element.getAttribute('tabindex') || '';
        }
        element.setAttribute('tabindex', '-1');
      });
    };

    const showSlide = (requestedIndex) => {
      activeIndex = (requestedIndex + slides.length) % slides.length;
      slides.forEach((slide, index) => {
        const isActive = index === activeIndex;
        slide.classList.toggle('is-active', isActive);
        slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
        setSlideFocusable(slide, isActive);
      });
      if (status) {
        const title = slides[activeIndex].dataset.slideTitle || '';
        status.textContent = `Slide ${activeIndex + 1} of ${slides.length}${title ? `: ${title}` : ''}`;
      }
    };

    const stopAutoplay = () => {
      window.clearTimeout(autoplayTimer);
      autoplayTimer = 0;
    };

    const startAutoplay = () => {
      stopAutoplay();
      if (prefersReducedMotion.matches || document.hidden) return;
      autoplayTimer = window.setTimeout(() => {
        showSlide(activeIndex + 1);
        startAutoplay();
      }, 5000);
    };

    slider.addEventListener('keydown', (event) => {
      if ('ArrowLeft' !== event.key && 'ArrowRight' !== event.key) return;
      event.preventDefault();
      stopAutoplay();
      showSlide(activeIndex + ('ArrowRight' === event.key ? 1 : -1));
    });
    slider.addEventListener('mouseenter', stopAutoplay);
    slider.addEventListener('mouseleave', () => {
      if (!slider.contains(document.activeElement)) startAutoplay();
    });
    slider.addEventListener('focusin', stopAutoplay);
    slider.addEventListener('focusout', () => {
      window.setTimeout(() => {
        if (!slider.contains(document.activeElement) && !slider.matches(':hover')) startAutoplay();
      }, 0);
    });
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) stopAutoplay();
      else startAutoplay();
    });

    showSlide(activeIndex);
    startAutoplay();
  });

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
      const maximum = Math.max(0, track.scrollWidth - track.clientWidth);
      const firstItem = track.firstElementChild;
      if (!firstItem || maximum <= 0) return;
      const origin = firstItem.offsetLeft;
      const positions = [...track.children]
        .map((item) => Math.min(maximum, Math.max(0, item.offsetLeft - origin)))
        .filter((position, index, items) => index === 0 || Math.abs(position - items[index - 1]) > 2);
      const current = Math.min(maximum, Math.max(0, track.scrollLeft));
      const target = direction > 0
        ? positions.find((position) => position > current + 2) ?? maximum
        : [...positions].reverse().find((position) => position < current - 2) ?? 0;
      track.scrollTo({ left: target, behavior: 'smooth' });
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
