export function formatPrice(cents, currency = 'USD') {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency,
    minimumFractionDigits: 2,
  }).format(cents / 100);
}

export function productImage(imageKey) {
  const map = {
    hero: '/images/hero-budgies.jpg',
    ecard: '/images/ecard.jpg',
    'sticker-pack-1': '/images/sticker-pack-1.jpg',
    'sticker-pack-2': '/images/sticker-pack-2.jpg',
  };
  return map[imageKey] ?? '/images/hero-budgies.jpg';
}

/** Dynamic cover: admin upload wins, legacy image key is the fallback. */
export function coverOf(product) {
  return product?.coverUrl ?? productImage(product?.imageKey);
}
