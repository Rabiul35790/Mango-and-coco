import { Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import Layout from '../Layout';
import { BuyButton } from '../components/BuyButton';
import { DownloadButton } from '../components/DownloadButton';
import { ProductGallery } from '../components/ProductGallery';
import { ReviewsSection } from '../components/Reviews';
import { coverOf, formatPrice } from '../lib/shop';

export default function ProductDetails({ category, product, siblings, meta, flash, reviews, canReview, myReview }) {
  const wizard = product.usesWizard;
  const orderUrl = `/order/${product.slug}`;
  // Rule: eGift cards are still images only — never a video player.
  const showVideo = category.slug !== 'egift-card' && !!product.demoVideoUrl;
  const cover = coverOf(product);
  const extras = product.gallery ?? [];
  // Gallery never repeats the banner frame: video banner → cover leads the
  // gallery; still banner (cover) → gallery shows extras, or cover alone.
  const galleryImages = showVideo ? [cover, ...extras] : extras.length > 0 ? extras : [cover];

  return (
    <Layout meta={meta} flash={flash}>
      {/* Half-height banner hero: demo video (full width, ~50vh) or still cover */}
      <section className="relative flex h-[50svh] min-h-[320px] w-full items-end overflow-hidden bg-ink">
        {showVideo ? (
          <video
            key={product.demoVideoUrl}
            className="absolute inset-0 h-full w-full object-cover"
            src={product.demoVideoUrl}
            poster={product.demoVideoPoster ?? cover}
            autoPlay
            muted
            loop
            playsInline
            preload="metadata"
            aria-hidden="true"
          />
        ) : (
          <img
            src={cover}
            alt=""
            aria-hidden="true"
            fetchPriority="high"
            decoding="async"
            className="absolute inset-0 h-full w-full object-cover"
          />
        )}
        <div className="absolute inset-0 bg-gradient-to-t from-ink/70 via-ink/20 to-ink/10" aria-hidden="true" />
        <div className="relative mx-auto w-full max-w-6xl px-4 pb-6 sm:px-6">
          <nav className="text-sm text-white/70" aria-label="Breadcrumb">
            <Link href="/" className="hover:text-white">Home</Link>
            {' / '}<Link href={`/shop/${category.slug}`} className="hover:text-white">{category.name}</Link>
            {' / '}<span className="font-semibold text-white">{product.name}</span>
          </nav>
        </div>
      </section>

      <section className="mx-auto w-full max-w-6xl px-4 py-10 sm:px-6 lg:py-14">
        <div className="grid gap-10 lg:grid-cols-[1.05fr_1fr] lg:gap-14">
          <ProductGallery images={galleryImages} name={product.name} />

          <div>
            <p className="eyebrow">
              {category.name}{product.tagline ? ` · ${product.tagline}` : ''}
            </p>
            <h1 className="mt-2 text-balance text-3xl font-semibold sm:text-4xl lg:text-[2.75rem] lg:leading-[1.1]">
              {product.name}
            </h1>

            {product.tagline && (
              <p className="mt-3 font-semibold text-foreground/90">{product.tagline}</p>
            )}
            <p className="mt-3 leading-relaxed text-foreground/80">{product.description}</p>

            {/* Purchase card */}
            <div className="mt-8 rounded-3xl border border-border bg-card p-6">
              <p className="text-muted-foreground">
                Only <span className="font-display text-3xl font-bold text-foreground">{formatPrice(product.priceCents, product.currency)}</span>
              </p>
              <div className="mt-4">
                {wizard
                  ? <Link href={orderUrl} className="flex h-12 w-full items-center justify-center whitespace-nowrap rounded-full bg-primary font-display font-bold leading-none text-primary-foreground">Personalise — {product.ctaLabel}</Link>
                  : <BuyButton product={product} className="h-12 w-full text-base leading-none" />}
              </div>
              <p className="mt-4 text-center text-sm text-muted-foreground">
                {wizard
                  ? 'Hand-made and delivered to your inbox within 48 hours.'
                  : "You'll get a download link straight away, with how to install."}
              </p>
              <p className="mt-1 text-center text-xs text-muted-foreground/80">
                Yours to keep, for personal use. Not for resale.{' '}
                <Link href="/contact" className="underline hover:text-foreground">Terms</Link>
              </p>
            </div>

            {/* Accordions */}
            <div className="mt-6">
              {product.whatsIncluded && (
                <Accordion title="What's included">
                  <p className="whitespace-pre-line">{product.whatsIncluded}</p>
                </Accordion>
              )}
              {(product.instructions || (product.usageSteps ?? []).length > 0) && (
                <Accordion title="How to use them">
                  {product.instructions && <p className="whitespace-pre-line">{product.instructions}</p>}
                  {(product.usageSteps ?? []).length > 0 && (
                    <ol className="mt-3 list-decimal space-y-1 pl-5">
                      {product.usageSteps.map((s, i) => <li key={i}>{s}</li>)}
                    </ol>
                  )}
                </Accordion>
              )}
            </div>

            {product.hasDownload && <DownloadButton productSlug={product.slug} className="mt-6" />}
          </div>
        </div>
      </section>

      {reviews && <ReviewsSection product={product} reviews={reviews} canReview={canReview} myReview={myReview} />}
    </Layout>
  );
}

function Accordion({ title, children }) {
  return (
    <details className="group border-b border-border py-4 first:border-t">
      <summary className="flex cursor-pointer list-none items-center justify-between gap-4 font-display font-bold [&::-webkit-details-marker]:hidden">
        {title}
        <Plus className="size-4 shrink-0 transition-transform duration-200 group-open:rotate-45" aria-hidden="true" />
      </summary>
      <div className="mt-3 text-sm leading-relaxed text-muted-foreground">{children}</div>
    </details>
  );
}
