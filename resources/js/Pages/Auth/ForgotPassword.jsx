import { useForm } from '@inertiajs/react';
import Layout from '../../Layout';

export default function ForgotPassword({ meta, flash, errors }) {
  const { data, setData, post, processing } = useForm({ email: '' });
  const inputCls = 'w-full rounded-xl border border-input bg-card px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-ring';

  return (
    <Layout meta={meta} flash={flash}>
      <section className="mx-auto w-full max-w-md px-4 pb-16 pt-28 sm:px-6 sm:pt-32">
        <h1 className="text-3xl font-semibold">Reset password</h1>
        <p className="mt-2 text-sm text-muted-foreground">We'll email you a reset link.</p>
        <form onSubmit={(e) => { e.preventDefault(); post('/forgot-password'); }} className="card-cosy mt-6 space-y-4 p-6">
          <input className={inputCls} type="email" required placeholder="Email" value={data.email} onChange={(e) => setData('email', e.target.value)} />
          {errors?.email && <p className="text-xs font-semibold text-destructive">{errors.email}</p>}
          <button disabled={processing} className="w-full rounded-full bg-primary py-3 font-display font-bold text-primary-foreground disabled:opacity-60">
            {processing ? 'Sending…' : 'Send reset link'}
          </button>
        </form>
      </section>
    </Layout>
  );
}
