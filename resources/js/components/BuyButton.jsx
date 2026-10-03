import { usePage } from '@inertiajs/react';
import { useState } from 'react';

export function BuyButton({ product, className = '' }) {
  const [busy, setBusy] = useState(false);
  const email = usePage().props.auth?.user?.email ?? null;

  async function handleClick() {
    setBusy(true);
    try {
      const res = await fetch('/checkout', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
          Accept: 'application/json',
        },
        body: JSON.stringify({ productSlug: product.slug, email }),
      });
      const result = await res.json();
      if (result.configured && result.url) {
        window.location.href = result.url;
        return;
      }
      alert(`${product.name} is ready — payments go live the moment the shop opens. 🐣`);
    } catch {
      alert('Something went wrong. Please try again in a moment.');
    } finally {
      setBusy(false);
    }
  }

  return (
    <button
      onClick={handleClick}
      disabled={busy}
      className={`whitespace-nowrap shrink-0 rounded-full bg-primary px-5 py-2.5 font-display font-bold text-primary-foreground transition-opacity disabled:opacity-60 ${className}`}
    >
      {busy ? 'One moment…' : product.ctaLabel}
    </button>
  );
}
