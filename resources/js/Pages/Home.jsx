import { Link, usePage } from '@inertiajs/react';
import { Download, Heart, Sparkles } from 'lucide-react';
import Layout from '../Layout';
import { HeroSection } from '../components/HeroSection';
import { ProductCard } from '../components/ProductCard';
import { SectionHeading } from '../components/SectionHeading';
import { formatPrice } from '../lib/shop';

export default function Home({ products, meta, flash }) {
  const videos = products.filter((p) => p.kind === 'video');
  const stickers = products.filter((p) => p.kind === 'sticker_pack');
  const cheapest = stickers.reduce((min, p) => (min === null || p.priceCents < min ? p.priceCents : min), null);
  const hero = usePage().props.site?.settings?.hero ?? {};

  return (
    <Layout meta={meta} flash={flash}>
      <HeroSection
        videoUrl={hero.videoUrl}
        posterUrl={hero.posterUrl ?? '/images/hero-budgies.jpg'}
        eyebrow="New · Personalised videos"
        title={hero.heading ?? 'Little budgie things, made to send.'}
        subtitle={hero.subheading ?? 'Meet Mango and Coco. They star in hand-drawn sticker packs and personalised videos with your own message written right into the scene.'}
        primaryCta={{ label: hero.ctaLabel ?? 'Create a personalised gift', href: hero.ctaUrl ?? '/order/personalised-video' }}
        secondaryCta={{ label: hero.secondaryCtaLabel ?? `See the sticker packs${cheapest !== null ? ` · from ${formatPrice(cheapest)}` : ''}`, href: hero.secondaryCtaUrl ?? '/shop/stickers' }}
      />

      <section className="mx-auto w-full max-w-6xl px-4 py-16 sm:px-6 lg:py-24">
        <SectionHeading eyebrow="Gifts" title="Send some love to your special someone">
          Made by Mango &amp; Coco, delivered to your inbox within 48 hours.
        </SectionHeading>
        <div className="mt-10 grid gap-6 sm:grid-cols-2">
          {videos.map((p) => <ProductCard key={p.id} product={p} />)}
        </div>
      </section>

      <section className="surface-sun">
        <div className="mx-auto grid w-full max-w-6xl items-center gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:py-20">
          <img src="/images/sticker-pack-1.jpg" alt="A sheet of cut-out Mango&Coco budgie stickers"
            loading="lazy" width={1024} height={1024}
            className="w-full rounded-3xl object-cover shadow-[var(--shadow-soft)]" />
          <div>
            <p className="eyebrow">Instant download</p>
            <h2 className="mt-2 text-3xl font-bold sm:text-4xl">Sticker Packs</h2>
            <p className="mt-4 text-base text-foreground/80">
              24 cut-out stickers in every pack — Mango on his own, Coco on hers, or the two of them together. Everything you already say, said cuter.
            </p>
            <Link href="/stickers" className="mt-7 inline-block rounded-full bg-primary px-6 py-3 font-display font-bold text-primary-foreground">
              See the packs
            </Link>
          </div>
        </div>
      </section>

      <section className="mx-auto w-full max-w-6xl px-4 py-16 sm:px-6 lg:py-24">
        <SectionHeading eyebrow="How it works" title="Three small steps, one sweet gift" />
        <ol className="mt-10 grid gap-6 md:grid-cols-3">
          {[
            { icon: Heart, title: 'Pick your gift', body: 'Choose a sticker pack for yourself or a personalised video for someone you love.' },
            { icon: Sparkles, title: 'Tell us the message', body: 'Write the words you want in the scene. We hand-letter them into the artwork.' },
            { icon: Download, title: 'Get it in your inbox', body: 'Stickers download instantly. Personalised gifts arrive within 48 hours.' },
          ].map((step, i) => (
            <li key={step.title} className="card-cosy p-6">
              <span className="grid size-11 place-items-center rounded-full bg-primary/25">
                <step.icon className="size-5" aria-hidden="true" />
              </span>
              <h3 className="mt-4 font-display text-lg font-bold">{i + 1}. {step.title}</h3>
              <p className="mt-2 text-sm text-muted-foreground">{step.body}</p>
            </li>
          ))}
        </ol>
      </section>
    </Layout>
  );
}
