import { useForm } from '@inertiajs/react';
import Layout from '../Layout';
import { SectionHeading } from '../components/SectionHeading';

export default function Contact({ meta, flash }) {
  const { data, setData, post, processing } = useForm({ name: '', email: '', subject: '', message: '' });
  const inputCls = 'w-full rounded-xl border border-input bg-card px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-ring';
  return (
    <Layout meta={meta} flash={flash}>
      <section className="surface-cream">
        <div className="mx-auto w-full max-w-3xl px-4 pb-14 pt-28 sm:px-6 sm:pt-32">
          <SectionHeading align="left" eyebrow="Say hello" title="Contact">
            Questions about an order, a custom video brief, or wholesale? We reply within 2 working days.
          </SectionHeading>
          <form onSubmit={(e) => { e.preventDefault(); post('/contact'); }}
            className="card-cosy mt-8 space-y-4 p-6">
            <div className="grid gap-4 sm:grid-cols-2">
              <input className={inputCls} placeholder="Your name" required value={data.name} onChange={(e) => setData('name', e.target.value)} />
              <input className={inputCls} placeholder="Email" type="email" required value={data.email} onChange={(e) => setData('email', e.target.value)} />
            </div>
            <input className={inputCls} placeholder="Subject (optional)" value={data.subject} onChange={(e) => setData('subject', e.target.value)} />
            <textarea className={inputCls} rows={5} placeholder="Your message…" required value={data.message} onChange={(e) => setData('message', e.target.value)} />
            <button disabled={processing} className="w-full rounded-full bg-primary py-3 font-display font-bold text-primary-foreground disabled:opacity-60">
              {processing ? 'Sending…' : 'Send message'}
            </button>
          </form>
        </div>
      </section>
    </Layout>
  );
}
