import { useState } from 'react';
import { usePage } from '@inertiajs/react';
import { Check } from 'lucide-react';
import Layout from '../Layout';
import { OptimizedVideo } from '../components/OptimizedVideo';
import { formatPrice, coverOf } from '../lib/shop';

const STEPS = ['Template', 'Message', 'Your details', 'Recipient', 'Summary & Pay'];

function csrf() {
  return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

export default function OrderWizard({ product, siblings, meta, flash }) {
  const formats = siblings.length > 0 ? siblings : [product];
  // Templates live INSIDE the product (Birthday, Congratulations…). When the
  // admin added them, buyers pick a template here; otherwise fall back to the
  // sibling-format chooser.
  const templates = product.templates ?? [];
  const isVideoCat = (product.categorySlug ?? '') === 'video';
  const authUser = usePage().props.auth?.user ?? null;
  const [step, setStep] = useState(0);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState('');
  const [notice, setNotice] = useState(null);
  const [form, setForm] = useState({
    productSlug: product.slug,
    formatLabel: product.name,
    templateLabel: '',
    message: '',
    occasion: '',
    notes: '',
    customerName: authUser?.name ?? '',
    customerEmail: authUser?.email ?? '',
    deliverTo: 'self',
    recipientName: '',
    recipientEmail: '',
  });

  const selected = formats.find((f) => f.slug === form.productSlug) ?? product;
  const set = (k, v) => setForm((p) => ({ ...p, [k]: v }));
  const inputCls = 'w-full rounded-xl border border-input bg-card px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-ring';

  function next() {
    setError('');
    if (step === 0 && templates.length > 0 && !form.templateLabel) return setError('Please pick a template to personalise.');
    if (step === 1 && !form.message.trim()) return setError('Please write the message for the scene.');
    if (step === 2 && (!form.customerName.trim() || !/.+@.+\..+/.test(form.customerEmail))) return setError('Please add your name and a valid email.');
    if (step === 3 && form.deliverTo === 'other' && (!form.recipientName.trim() || !/.+@.+\..+/.test(form.recipientEmail))) return setError('Please add the recipient name and email.');
    setStep((s) => Math.min(s + 1, STEPS.length - 1));
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  async function pay() {
    setBusy(true);
    setError('');
    setNotice(null);
    try {
      const res = await fetch('/video-orders', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf(), Accept: 'application/json' },
        body: JSON.stringify(form),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message ?? 'Could not save your brief.');
      const co = data.checkout;
      if (co?.configured && co?.url) {
        window.location.href = co.url;
      } else {
        setNotice({
          title: 'Brief saved!',
          body: `Your tracking ID is ${data.trackingId ?? 'on its way'}. Lemon checkout isn't connected yet — we'll confirm by email, or follow progress anytime:`,
          trackingId: data.trackingId ?? null,
        });
      }
    } catch (e) {
      setError(e.message);
    } finally {
      setBusy(false);
    }
  }

  return (
    <Layout meta={meta} flash={flash}>
      <section className="mx-auto w-full max-w-3xl px-4 pb-10 pt-28 sm:px-6 sm:pt-32">
        <p className="eyebrow">Step {step + 1} of {STEPS.length} · {STEPS[step]}</p>
        <h1 className="mt-2 text-3xl font-bold sm:text-4xl">Personalise: {selected.name}</h1>

        <ol className="mt-6 flex gap-2">
          {STEPS.map((s, i) => (
            <li key={s} className={`h-2 flex-1 rounded-full ${i <= step ? 'bg-primary' : 'bg-muted'}`} title={s} />
          ))}
        </ol>

        <div className="card-cosy mt-6 p-6 sm:p-8">
          {step === 0 && (
            templates.length > 0 ? (
              <div className="space-y-4">
                <p className="font-display text-lg font-bold">
                  Pick the {isVideoCat ? 'video' : 'card'} you'd like to personalise
                </p>
                <div className="grid grid-cols-2 gap-3 sm:grid-cols-3">
                  {templates.map((t) => {
                    const active = form.templateLabel === t.label;
                    return (
                      <button
                        key={t.id}
                        type="button"
                        onClick={() => { set('templateLabel', t.label); set('formatLabel', product.name); }}
                        aria-pressed={active}
                        className={`group relative overflow-hidden rounded-lg border-2 text-left transition-colors ${active ? 'border-primary' : 'border-border hover:border-muted-foreground/40'}`}
                      >
                        {t.isVideo && t.fileUrl ? (
                          <video
                            key={t.fileUrl}
                            className="aspect-[9/16] w-full object-cover"
                            src={t.fileUrl}
                            poster={t.posterUrl ?? coverOf(product)}
                            autoPlay
                            muted
                            loop
                            playsInline
                            preload="metadata"
                            ref={(el) => { if (el) el.muted = true; }}
                          />
                        ) : (
                          <img
                            src={t.thumbUrl ?? coverOf(product)}
                            alt={t.label}
                            loading="lazy"
                            decoding="async"
                            className="aspect-[9/16] w-full object-cover"
                          />
                        )}
                        {active && (
                          <span className="absolute left-2 top-2 grid size-7 place-items-center rounded-full bg-primary text-primary-foreground" aria-hidden="true">
                            <Check className="size-4" />
                          </span>
                        )}
                        <span className="absolute inset-x-0 bottom-0 bg-gradient-to-t from-ink/75 to-transparent px-3 pb-2.5 pt-8 font-display text-sm font-bold text-white">
                          {t.label}
                        </span>
                      </button>
                    );
                  })}
                </div>
              </div>
            ) : (
            <div className="space-y-3">
              <p className="font-display font-bold">Which format do you want?</p>
              <div className="grid gap-3 sm:grid-cols-2">
                {formats.map((f) => (
                  <button key={f.slug} type="button" onClick={() => { set('productSlug', f.slug); set('formatLabel', f.name); }}
                    aria-pressed={f.slug === form.productSlug}
                    className={`rounded-xl border p-4 text-left transition-colors ${f.slug === form.productSlug ? 'border-primary bg-primary/15' : 'border-border hover:bg-muted/60'}`}>
                    {(f.categorySlug ?? product.categorySlug) === 'video' && f.demoVideoUrl ? (
                      <span className="block overflow-hidden rounded-lg" onClick={(e) => e.stopPropagation()}>
                        <OptimizedVideo src={f.demoVideoUrl} poster={f.demoVideoPoster ?? coverOf(f)} ratio="aspect-video" />
                      </span>
                    ) : (
                      <img src={coverOf(f)} alt={f.name} loading="lazy" decoding="async" className="aspect-video w-full rounded-lg object-cover" />
                    )}
                    <span className="mt-2 block font-display font-bold">{f.name}</span>
                    <span className="block text-sm text-muted-foreground">{formatPrice(f.priceCents, f.currency)}</span>
                  </button>
                ))}
              </div>
              {selected.demoVideoUrl && (selected.categorySlug ?? product.categorySlug) === 'video' && (
                <OptimizedVideo src={selected.demoVideoUrl} poster={selected.demoVideoPoster} />
              )}
            </div>
            )
          )}

          {step === 1 && (
            <div className="space-y-4">
              <div>
                <label className="text-sm font-semibold">The message in the scene *</label>
                <textarea required maxLength={400} rows={3} value={form.message} onChange={(e) => set('message', e.target.value)}
                  placeholder="Happy birthday, Nadia — you make every day brighter 🐥" className={inputCls} />
                <p className="text-xs text-muted-foreground">{form.message.length}/400</p>
              </div>
              <div className="grid gap-4 sm:grid-cols-2">
                <div>
                  <label className="text-sm font-semibold">Occasion</label>
                  <input maxLength={120} placeholder="Birthday, anniversary…" value={form.occasion} onChange={(e) => set('occasion', e.target.value)} className={inputCls} />
                </div>
                <div>
                  <label className="text-sm font-semibold">Notes (optional)</label>
                  <input maxLength={1000} placeholder="Colours, jokes, deadline…" value={form.notes} onChange={(e) => set('notes', e.target.value)} className={inputCls} />
                </div>
              </div>
            </div>
          )}

          {step === 2 && (
            <div className="grid gap-4 sm:grid-cols-2">
              <div>
                <label className="text-sm font-semibold">Your name *</label>
                <input required value={form.customerName} onChange={(e) => set('customerName', e.target.value)} className={inputCls} />
              </div>
              <div>
                <label className="text-sm font-semibold">Your email *</label>
                <input required type="email" value={form.customerEmail} onChange={(e) => set('customerEmail', e.target.value)} className={inputCls} />
              </div>
            </div>
          )}

          {step === 3 && (
            <div className="space-y-4">
              <p className="font-display font-bold">After the video is ready, send it to…</p>
              <div className="grid gap-3 sm:grid-cols-2">
                {[
                  { v: 'self', t: 'Me (the buyer)', d: 'We deliver to your email.' },
                  { v: 'other', t: 'Someone else', d: 'A gift — we send it to them.' },
                ].map((o) => (
                  <button key={o.v} type="button" onClick={() => set('deliverTo', o.v)} aria-pressed={form.deliverTo === o.v}
                    className={`rounded-xl border p-4 text-left transition-colors ${form.deliverTo === o.v ? 'border-primary bg-primary/15' : 'border-border hover:bg-muted/60'}`}>
                    <span className="flex items-center gap-2 font-display font-bold">
                      <span className={`grid size-4 place-items-center rounded-full border ${form.deliverTo === o.v ? 'border-primary' : 'border-muted-foreground'}`}>
                        {form.deliverTo === o.v && <span className="size-2 rounded-full bg-primary" />}
                      </span>
                      {o.t}
                    </span>
                    <span className="mt-1 block text-sm text-muted-foreground">{o.d}</span>
                  </button>
                ))}
              </div>
              {form.deliverTo === 'other' && (
                <div className="grid gap-4 sm:grid-cols-2">
                  <div>
                    <label className="text-sm font-semibold">Recipient name *</label>
                    <input value={form.recipientName} onChange={(e) => set('recipientName', e.target.value)} className={inputCls} />
                  </div>
                  <div>
                    <label className="text-sm font-semibold">Recipient email *</label>
                    <input type="email" value={form.recipientEmail} onChange={(e) => set('recipientEmail', e.target.value)} className={inputCls} />
                  </div>
                </div>
              )}
            </div>
          )}

          {step === 4 && (
            <div className="space-y-3 text-sm">
              <h2 className="font-display text-lg font-bold">Order summary</h2>
              <dl className="divide-y divide-border rounded-2xl border border-border">
                {[['Format', `${selected.name} — ${formatPrice(selected.priceCents, selected.currency)}`],
                  ...(form.templateLabel ? [['Template', form.templateLabel]] : []),
                  ['Message', form.message],
                  ['Occasion', form.occasion || '—'],
                  ['Buyer', `${form.customerName} · ${form.customerEmail}`],
                  ['Deliver to', form.deliverTo === 'self' ? `You (${form.customerEmail})` : `${form.recipientName} · ${form.recipientEmail}`],
                ].map(([k, v]) => (
                  <div key={k} className="flex gap-4 px-4 py-2.5"><dt className="w-28 shrink-0 font-semibold">{k}</dt><dd className="text-muted-foreground">{v}</dd></div>
                ))}
              </dl>
              <p className="text-xs text-muted-foreground">Pay securely via Lemon Squeezy. We hand-make and deliver within 48 hours.</p>
            </div>
          )}

          {error && <p className="rounded-xl bg-destructive/10 px-4 py-2 text-sm font-semibold">{error}</p>}
          {notice && (
            <div className="rounded-2xl border border-primary/30 bg-primary/10 px-4 py-3 text-sm">
              <p className="font-display font-bold">{notice.title}</p>
              <p className="mt-1 text-muted-foreground">{notice.body}</p>
              {notice.trackingId && (
                <a href={`/track?code=${notice.trackingId}`} className="mt-2 inline-block rounded-full bg-primary px-4 py-2 font-display text-sm font-bold text-primary-foreground">
                  Track {notice.trackingId}
                </a>
              )}
            </div>
          )}

          <div className="mt-6 flex justify-between gap-3">
            <button type="button" disabled={step === 0 || busy} onClick={() => setStep((s) => s - 1)}
              className="rounded-full border border-border px-5 py-2.5 font-display font-bold disabled:opacity-40">Back</button>
            {step < STEPS.length - 1
              ? <button type="button" onClick={next} className="rounded-full bg-primary px-6 py-2.5 font-display font-bold text-primary-foreground">Continue</button>
              : <button type="button" onClick={pay} disabled={busy} className="rounded-full bg-primary px-6 py-2.5 font-display font-bold text-primary-foreground disabled:opacity-60">
                  {busy ? 'Saving…' : `Pay ${formatPrice(selected.priceCents, selected.currency)}`}
                </button>}
          </div>
        </div>
      </section>
    </Layout>
  );
}
