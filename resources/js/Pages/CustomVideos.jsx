import Layout from '../Layout';
import { SectionHeading } from '../components/SectionHeading';
import { VideoOrderForm } from '../components/VideoOrderForm';
import { formatPrice, productImage } from '../lib/shop';

export default function CustomVideos({ products, meta, flash }) {
  return (
    <Layout meta={meta} flash={flash}>
      <section className="surface-cream">
        <div className="mx-auto w-full max-w-6xl px-4 py-14 sm:px-6 lg:py-20">
          <SectionHeading align="left" eyebrow="Made for one person" title="Personalised videos">
            Tell us the words. Mango and Coco act them out, with your message hand-lettered right into the scene.
          </SectionHeading>
        </div>
      </section>
      <section className="mx-auto grid w-full max-w-6xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[1fr_1.15fr]">
        <div className="space-y-6">
          {products.map((option) => (
            <article key={option.id} className="card-cosy overflow-hidden">
              <img src={productImage(option.imageKey)} alt={`${option.name} — ${option.tagline}`}
                loading="lazy" width={1536} height={1024} className="aspect-[3/2] w-full object-cover" />
              <div className="space-y-2 p-5">
                <div className="flex items-baseline justify-between gap-3">
                  <h2 className="font-display text-lg font-bold">{option.name}</h2>
                  <span className="font-display font-bold">{formatPrice(option.priceCents, option.currency)}</span>
                </div>
                <p className="text-sm text-muted-foreground">{option.description}</p>
              </div>
            </article>
          ))}
        </div>
        <div>
          <VideoOrderForm products={products} />
        </div>
      </section>
    </Layout>
  );
}
