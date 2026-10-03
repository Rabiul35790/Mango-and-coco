import { Link, router } from '@inertiajs/react';
import { Check, Download } from 'lucide-react';
import { useState } from 'react';
import Layout from '../Layout';

export default function Track({ meta, flash, code, email, result, lookupError }) {
  const [form, setForm] = useState({ code: code ?? '', email: email ?? '' });
  const inputCls = 'w-full rounded-xl border border-input bg-card px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-ring';

  function submit(e) {
    e.preventDefault();
    router.get('/track', { code: form.code.trim(), email: form.email.trim() }, { preserveState: false });
  }

  return (
    <Layout meta={meta} flash={flash}>
      <section className="mx-auto w-full max-w-2xl px-4 pb-16 pt-28 sm:px-6 sm:pt-32">
        <p className="eyebrow">Order tracking</p>
        <h1 className="mt-1 text-3xl font-semibold sm:text-4xl">Where's my order?</h1>
        <p className="mt-2 text-sm text-muted-foreground">
          Enter the tracking ID from your confirmation (MC-XXXXXXXX). Using the Lemon receipt number instead? Add the purchase email too.
        </p>

        <form onSubmit={submit} className="card-cosy mt-6 grid gap-3 p-5 sm:grid-cols-[1fr_1fr_auto]">
          <input className={inputCls} placeholder="Tracking ID or receipt no." value={form.code} onChange={(e) => setForm({ ...form, code: e.target.value })} />
          <input className={inputCls} type="email" placeholder="Purchase email (for receipt no.)" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} />
          <button className="whitespace-nowrap rounded-full bg-primary px-6 py-2.5 font-display font-bold text-primary-foreground">
            Track
          </button>
        </form>
        {lookupError && <p className="mt-3 rounded-2xl bg-destructive/10 px-4 py-3 text-sm font-semibold">{lookupError}</p>}

        {result && (
          <article className="mt-6 rounded-3xl border border-border bg-card p-6 sm:p-8">
            <div className="flex flex-wrap items-start justify-between gap-3">
              <div>
                <p className="text-xs font-bold uppercase tracking-[0.12em] text-muted-foreground">
                  {result.type === 'brief' ? 'Personalised order' : 'Shop order'} · {result.trackingId}
                </p>
                <h2 className="mt-1 font-display text-2xl font-bold">{result.product}</h2>
                <p className="mt-1 text-sm text-muted-foreground">Ordered {result.date}</p>
              </div>
            </div>

            {/* Status timeline */}
            <ol className="mt-6 space-y-0">
              {result.steps.map((s, i) => (
                <li key={s.label} className="flex gap-3">
                  <span className="flex flex-col items-center">
                    <span className={`grid size-6 place-items-center rounded-full ${s.done ? 'bg-primary text-primary-foreground' : 'border border-border text-transparent'}`} aria-hidden="true">
                      <Check className="size-3.5" />
                    </span>
                    {i < result.steps.length - 1 && <span className={`w-0.5 flex-1 ${s.done ? 'bg-primary' : 'bg-border'}`} aria-hidden="true" />}
                  </span>
                  <p className={`pb-5 text-sm ${s.current ? 'font-bold' : s.done ? 'font-semibold' : 'text-muted-foreground'}`}>
                    {s.label}{s.current && ' — current'}
                  </p>
                </li>
              ))}
            </ol>

            {result.eta && (
              <p className="rounded-2xl bg-secondary px-4 py-3 text-sm font-semibold text-secondary-foreground">
                {result.etaPast ? `Was estimated for ${result.eta} — almost there.` : `Estimated delivery: ${result.eta}`}
              </p>
            )}

            {result.message && (
              <p className="mt-4 text-sm text-muted-foreground">Your message: “{result.message}”</p>
            )}

            <div className="mt-5 flex flex-wrap gap-3">
              {result.downloadUrl && (
                <a href={result.downloadUrl} className="inline-flex items-center gap-2 rounded-full bg-primary px-6 py-3 font-display font-bold text-primary-foreground">
                  <Download className="size-4" /> Download files
                </a>
              )}
              {result.payUrl && (
                <a href={result.payUrl} className="inline-flex items-center gap-2 rounded-full bg-primary px-6 py-3 font-display font-bold text-primary-foreground">
                  Complete payment
                </a>
              )}
              <Link href="/contact" className="inline-flex items-center rounded-full border border-border px-6 py-3 font-display font-bold transition-colors hover:bg-muted">
                Need help?
              </Link>
            </div>
          </article>
        )}
      </section>
    </Layout>
  );
}
