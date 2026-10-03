import Layout from '../Layout';
import { SectionHeading } from '../components/SectionHeading';

export default function About({ meta, flash }) {
  return (
    <Layout meta={meta} flash={flash}>
      <section className="surface-cream">
        <div className="mx-auto w-full max-w-3xl px-4 pb-14 pt-28 sm:px-6 sm:pt-32 lg:pb-20">
          <SectionHeading align="left" eyebrow="Our story" title="Two budgies, one sunny idea">
            Mango (the yellow chatterbox) and Coco (the cream daydreamer) started as sketches on a kitchen table.
          </SectionHeading>
          <div className="mt-8 space-y-4 text-base text-foreground/80">
            <p>We draw everything by hand — every feather, every blush, every tiny beak. Then we turn the drawings into sticker packs you can use instantly, coloring books you can print at home, and personalised videos with your own message written into the scene.</p>
            <p>Our audience spans the USA, Canada, Germany, Mexico and India, so checkout is powered by Lemon Squeezy: global cards, Apple Pay, PayPal-ready, with VAT/GST handled for you.</p>
            <img src="/images/hero-budgies.jpg" alt="Mango and Coco" loading="lazy" className="w-full rounded-3xl object-cover shadow-[var(--shadow-soft)]" />
          </div>
        </div>
      </section>
    </Layout>
  );
}
