(() => {
  const config = window.orienteWhatsAppCart || {};
  const checkout = window.wc?.blocksCheckout;

  if (!checkout?.registerCheckoutFilters) return;

  const decodeText = (value) => {
    const textarea = document.createElement('textarea');
    textarea.innerHTML = String(value || '');
    return textarea.value.trim();
  };

  const cartItems = (cart) => {
    if (Array.isArray(cart?.items)) return cart.items;
    if (Array.isArray(cart?.cartItems)) return cart.cartItems;
    return [];
  };

  const whatsappLink = (defaultValue, extensions, args) => {
    const items = cartItems(args?.cart).filter((item) => Number(item?.quantity) > 0);
    const number = String(config.number || '').replace(/\D/g, '');

    if (!items.length || !number) return defaultValue;

    const lineBreak = '\r\n';
    const introduction = config.introduction || 'Здраво, сакам да направам нарачка:';
    const sections = [`*${introduction}*`];

    items.forEach((item, index) => {
      const name = decodeText(item.name);
      const sku = decodeText(item.sku) || 'Не е достапна';
      const quantity = Number(item.quantity);
      sections.push(
        `*${index + 1}. Производ:* ${name}${lineBreak}`
        + `*Шифра:* ${sku}${lineBreak}`
        + `*Количина:* ${quantity}`,
      );
    });

    return `https://wa.me/${number}?text=${encodeURIComponent(sections.join(lineBreak + lineBreak))}`;
  };

  checkout.registerCheckoutFilters('oriente-whatsapp-order', {
    proceedToCheckoutButtonLabel: () => config.buttonLabel || 'Order via WhatsApp',
    proceedToCheckoutButtonLink: whatsappLink,
  });
})();
