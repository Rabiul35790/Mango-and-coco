import Layout from '../Layout';
import { HeroSection } from '../components/HeroSection';
import { ProductCard } from '../components/ProductCard';
import { SectionHeading } from '../components/SectionHeading';

/** Local poster stand-ins until you upload each category's hero video. */
const FALLBACK_POSTERS = {
  stickers: '/images/sticker-pack-1.jpg',
  wallpaper: '/images/hero-budgies.jpg',
  video: '/images/hero-budgies.jpg',
  'egift-card': '/images/ecard.jpg',
  'coloring-books': '/images/sticker-pack-2.jpg',
};

export default function Category({ category, products, meta, flash }) {
  const hero = category.hero ?? {};

  return (
    <Layout meta={meta} flash={flash}>
      <HeroSection
        videoUrl={hero.videoUrl}
        posterUrl={hero.posterUrl ?? FALLBACK_POSTERS[category.slug] ?? '/images/hero-budgies.jpg'}
        eyebrow={category.tagline ?? category.name}
        title={hero.heading ?? category.name}
        subtitle={hero.subheading ?? category.description}
        primaryCta={{ label: hero.ctaLabel ?? 'Shop the collection', href: hero.ctaUrl ?? '#products' }}
      />

      {category.detailsBody && (
        <section className="mx-auto w-full max-w-6xl px-4 pt-14 sm:px-6">
          <SectionHeading align="left" eyebrow="Good to know" title={category.detailsHeading ?? 'How it works'}>
            {category.detailsBody}
          </SectionHeading>
        </section>
      )}

      <section id="products" className="mx-auto w-full max-w-6xl scroll-mt-24 px-4 py-14 sm:px-6">
        {products.length === 0
          ? <p className="text-muted-foreground">New drops coming soon.</p>
          : <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
              {products.map((p) => <ProductCard key={p.id} product={p} />)}
            </div>}
      </section>
    </Layout>
  );
}
