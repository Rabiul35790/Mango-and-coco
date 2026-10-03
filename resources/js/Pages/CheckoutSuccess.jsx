import { Link } from '@inertiajs/react';
import Layout from '../Layout';

export default function CheckoutSuccess({ meta, flash, trackingId }) {
  return (
    <Layout meta={meta} flash={flash}>
      <section className="mx-auto w-full max-w-2xl px-4 pb-20 pt-28 text-center sm:px-6 sm:pt-32">
        <p className="text-5xl">🐣</p>
        <h1 className="mt-4 text-4xl font-bold">Thank you!</h1>
        <p className="mt-3 text-muted-foreground">Your payment was received. Stickers & coloring books download instantly via email; personalised videos arrive within 48 hours.</p>
        {trackingId && (
          <div className="mx-auto mt-6 max-w-md rounded-3xl border border-border bg-card p-5">
            <p className="text-xs font-bold uppercase tracking-[0.12em] text-muted-foreground">Your tracking ID</p>
            <p className="mt-1 font-display text-2xl font-bold tracking-wide">{trackingId}</p>
            <Link href={`/track?code=${trackingId}`} className="mt-3 inline-block rounded-full bg-primary px-6 py-2.5 font-display font-bold text-primary-foreground">
              Track my order
            </Link>
          </div>
        )}
        <div className="mt-8 flex flex-wrap justify-center gap-3">
          <Link href="/track" className="rounded-full border border-border px-6 py-3 font-display font-bold transition-colors hover:bg-muted">Track an order</Link>
          <Link href="/" className="rounded-full bg-primary px-6 py-3 font-display font-bold text-primary-foreground">Back home</Link>
        </div>
      </section>
    </Layout>
  );
}
