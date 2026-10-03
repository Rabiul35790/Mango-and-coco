import { Link } from '@inertiajs/react';
import { BuyButton } from './BuyButton';
import { coverOf, formatPrice } from '../lib/shop';

export function ProductCard({ product }) {
  const detailsUrl = product.detailsUrl ?? `/shop/${product.categorySlug ?? 'all'}/${product.slug}`;
  const orderUrl = `/order/${product.slug}`;

  return (
    <article className="card-cosy group flex flex-col overflow-hidden">
      <Link href={detailsUrl} aria-label={`View ${product.name}`} className="block overflow-hidden">
        <img
          src={coverOf(product)}
          alt={`${product.name} — ${product.tagline ?? ''}`}
          loading="lazy"
          decoding="async"
          width={1024}
          height={1024}
          className="img-cosy aspect-square w-full object-cover"
        />
      </Link>
      <div className="flex flex-1 flex-col gap-3 p-5">
        <div className="flex items-start justify-between gap-3">
          <Link href={detailsUrl} className="font-display text-lg font-bold leading-tight hover:underline">
            {product.name}
          </Link>
          <span className="shrink-0 rounded-full bg-secondary px-3 py-1 font-display text-sm font-bold">
            {formatPrice(product.priceCents, product.currency)}
          </span>
        </div>
        <p className="text-sm text-muted-foreground">{product.description}</p>
        <div className="mt-auto flex items-end justify-between gap-2 pt-2">
          <span className="max-w-[104px] shrink text-[11px] font-semibold uppercase leading-tight tracking-wide text-muted-foreground">
            {product.isInstantDownload ? 'Instant download' : 'Made in 48 hours'}
          </span>
          <div className="flex shrink-0 items-center gap-2">
            <Link href={detailsUrl} className="whitespace-nowrap rounded-full border border-border px-4 py-2 text-sm font-display font-bold leading-none transition-colors hover:bg-muted">
              Details
            </Link>
            {product.usesWizard
              ? <Link href={orderUrl} className="whitespace-nowrap rounded-full bg-primary px-4 py-2 text-sm font-display font-bold leading-none text-primary-foreground">{product.ctaLabel}</Link>
              : <BuyButton product={product} className="px-4 py-2 text-sm leading-none" />}
          </div>
        </div>
      </div>
    </article>
  );
}
