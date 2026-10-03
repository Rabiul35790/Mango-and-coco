import Layout from '../Layout';
import { ProductCard } from '../components/ProductCard';
import { SectionHeading } from '../components/SectionHeading';

export default function ColoringBooks({ products, meta, flash }) {
  return (
    <Layout meta={meta} flash={flash}>
      <section className="surface-cream">
        <div className="mx-auto w-full max-w-6xl px-4 py-14 sm:px-6 lg:py-20">
          <SectionHeading align="left" eyebrow="Print at home · PDF" title="Coloring Books">
            Cosy Mango &amp; Coco pages to print and color. Instant download, yours forever.
          </SectionHeading>
        </div>
      </section>
      <section className="mx-auto w-full max-w-6xl px-4 py-14 sm:px-6">
        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {products.length === 0 && (
            <p className="text-muted-foreground">First coloring book drops very soon — join the list from Contact.</p>
          )}
          {products.map((p) => <ProductCard key={p.id} product={p} />)}
        </div>
      </section>
    </Layout>
  );
}
