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

    const lines = [config.introduction || 'Здраво, сакам да направам нарачка:'];

    items.forEach((item, index) => {
      const name = decodeText(item.name);
      const sku = decodeText(item.sku) || 'Не е достапна';
      const quantity = Number(item.quantity);
      lines.push(`${index + 1}. Производ: ${name}\nШифра: ${sku}\nКоличина: ${quantity}`);
    });

    return `https://wa.me/${number}?text=${encodeURIComponent(lines.join('\n\n'))}`;
  };

  checkout.registerCheckoutFilters('oriente-whatsapp-order', {
    proceedToCheckoutButtonLabel: () => config.buttonLabel || 'Order via WhatsApp',
    proceedToCheckoutButtonLink: whatsappLink,
  });
})();
