import { ChevronDown } from 'lucide-react';
import Layout from '../Layout';
import { ProductCard } from '../components/ProductCard';
import { SectionHeading } from '../components/SectionHeading';

const FAQ = [
  { q: 'What exactly do I get?', a: 'A zip file with 24 high-resolution cut-out PNG stickers plus one ready-to-use sticker sheet, sized for chat apps, digital planners and printing at home.' },
  { q: 'Can I use them in WhatsApp or Telegram?', a: 'Yes. Every sticker comes with a transparent background, so you can add them to WhatsApp, Telegram, iMessage or any digital journal app.' },
  { q: 'Can I print them?', a: 'Absolutely. Files are 300 DPI, so they print beautifully on sticker paper for personal use.' },
  { q: 'Do the packs ever expire?', a: 'No. Your download link stays yours, and any free additions to a pack you bought are sent to your email.' },
];

export default function Stickers({ products, meta, flash }) {
  return (
    <Layout meta={meta} flash={flash}>
      <section className="surface-cream">
        <div className="mx-auto w-full max-w-6xl px-4 py-14 sm:px-6 lg:py-20">
          <SectionHeading align="left" eyebrow="Instant download" title="Sticker Packs">
            Hand-drawn budgie stickers for your chats, journals and planners. Buy once, keep forever.
          </SectionHeading>
        </div>
      </section>
      <section className="mx-auto w-full max-w-6xl px-4 py-14 sm:px-6">
        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {products.map((p) => <ProductCard key={p.id} product={p} />)}
        </div>
      </section>
      <section className="mx-auto w-full max-w-3xl px-4 pb-8 sm:px-6">
        <SectionHeading eyebrow="Good to know" title="Sticker questions" />
        <dl className="mt-8 divide-y divide-border border-y border-border">
          {FAQ.map((item) => (
            <details key={item.q} className="group py-4">
              <summary className="flex cursor-pointer list-none items-center justify-between gap-4 font-display font-semibold">
                {item.q}
                <ChevronDown className="size-4 shrink-0 transition-transform group-open:rotate-180" aria-hidden="true" />
              </summary>
              <dd className="mt-3 text-sm text-muted-foreground">{item.a}</dd>
            </details>
          ))}
        </dl>
      </section>
    </Layout>
  );
}
