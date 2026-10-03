import { Download } from 'lucide-react';
import Layout from '../../Layout';
import { AccountNav } from '../../components/AccountNav';

export default function Downloads({ meta, flash, items }) {
  return (
    <Layout meta={meta} flash={flash}>
      <section className="mx-auto w-full max-w-6xl px-4 pb-16 pt-28 sm:px-6 sm:pt-32">
        <AccountNav />
        <p className="mt-6 max-w-2xl text-sm text-muted-foreground">
          Your files, anytime — no expiry, no email hunt. Links are signed fresh on every visit and stay valid for 30 minutes.
        </p>
        {items.length === 0
          ? <p className="mt-4 rounded-3xl border border-border bg-card p-6 text-sm text-muted-foreground">No downloads yet. Sticker packs, wallpapers and coloring books appear here after payment.</p>
          : <div className="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
              {items.map((i) => (
                <div key={i.orderId} className="card-cosy flex items-center justify-between gap-3 p-5">
                  <div>
                    <p className="font-display font-bold">{i.product}</p>
                    <p className="text-xs text-muted-foreground">Order #{i.orderId} · {i.date}</p>
                  </div>
                  <a
                    href={i.url}
                    className="grid size-11 shrink-0 place-items-center rounded-full bg-primary text-primary-foreground"
                    aria-label={`Download ${i.product}`}
                  >
                    <Download className="size-5" />
                  </a>
                </div>
              ))}
            </div>}
      </section>
    </Layout>
  );
}
