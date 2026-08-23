(() => {
  document.addEventListener('click', (event) => {
    const selectButton = event.target.closest('[data-oriente-category-hero-select]');
    const removeButton = event.target.closest('[data-oriente-category-hero-remove]');

    if (selectButton) {
      event.preventDefault();
      const control = selectButton.closest('.oriente-category-hero-control');
      const input = control?.querySelector('[data-oriente-category-hero-id]');
      const preview = control?.querySelector('.oriente-category-hero-preview');
      if (!control || !input || !preview || !window.wp?.media) return;

      const frame = window.wp.media({
        title: selectButton.dataset.mediaTitle || 'Choose category hero image',
        button: { text: selectButton.dataset.mediaButton || 'Use as category hero' },
        library: { type: 'image' },
        multiple: false,
      });

      frame.on('select', () => {
        const attachment = frame.state().get('selection').first()?.toJSON();
        if (!attachment) return;
        input.value = attachment.id;
        preview.src = attachment.sizes?.medium_large?.url || attachment.sizes?.large?.url || attachment.url;
        preview.hidden = false;
        const remove = control.querySelector('[data-oriente-category-hero-remove]');
        if (remove) remove.hidden = false;
      });
      frame.open();
      return;
    }

    if (removeButton) {
      event.preventDefault();
      const control = removeButton.closest('.oriente-category-hero-control');
      const input = control?.querySelector('[data-oriente-category-hero-id]');
      const preview = control?.querySelector('.oriente-category-hero-preview');
      if (!control || !input || !preview) return;
      input.value = '';
      if (control.dataset.defaultUrl) {
        preview.src = control.dataset.defaultUrl;
        preview.hidden = false;
      } else {
        preview.removeAttribute('src');
        preview.hidden = true;
      }
      removeButton.hidden = true;
    }
  });
})();
