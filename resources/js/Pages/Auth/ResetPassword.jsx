import { useForm } from '@inertiajs/react';
import Layout from '../../Layout';

export default function ResetPassword({ meta, flash, errors, token, email }) {
  const { data, setData, post, processing } = useForm({ token, email: email ?? '', password: '', password_confirmation: '' });
  const inputCls = 'w-full rounded-xl border border-input bg-card px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-ring';

  return (
    <Layout meta={meta} flash={flash}>
      <section className="mx-auto w-full max-w-md px-4 pb-16 pt-28 sm:px-6 sm:pt-32">
        <h1 className="text-3xl font-semibold">Set new password</h1>
        <form onSubmit={(e) => { e.preventDefault(); post('/reset-password'); }} className="card-cosy mt-6 space-y-4 p-6">
          <input className={inputCls} type="email" required value={data.email} onChange={(e) => setData('email', e.target.value)} />
          {errors?.email && <p className="text-xs font-semibold text-destructive">{errors.email}</p>}
          <input className={inputCls} type="password" required placeholder="New password" value={data.password} onChange={(e) => setData('password', e.target.value)} />
          <input className={inputCls} type="password" required placeholder="Confirm password" value={data.password_confirmation} onChange={(e) => setData('password_confirmation', e.target.value)} />
          <button disabled={processing} className="w-full rounded-full bg-primary py-3 font-display font-bold text-primary-foreground disabled:opacity-60">
            {processing ? 'Saving…' : 'Update password'}
          </button>
        </form>
      </section>
    </Layout>
  );
}
