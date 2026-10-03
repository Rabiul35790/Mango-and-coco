import { Link, useForm } from '@inertiajs/react';
import Layout from '../../Layout';
import { GoogleButton } from '../../components/GoogleButton';

export default function Login({ meta, flash, errors }) {
  const { data, setData, post, processing } = useForm({ email: '', password: '', remember: false });
  const inputCls = 'w-full rounded-xl border border-input bg-card px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-ring';

  return (
    <Layout meta={meta} flash={flash}>
      <section className="mx-auto w-full max-w-md px-4 pb-16 pt-28 sm:px-6 sm:pt-32">
        <h1 className="text-3xl font-semibold">Welcome back</h1>
        <p className="mt-2 text-sm text-muted-foreground">Log in to track orders, download files and write reviews.</p>
        <form onSubmit={(e) => { e.preventDefault(); post('/login'); }} className="card-cosy mt-6 space-y-4 p-6">
          <GoogleButton />
          <p className="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-muted-foreground">
            <span className="h-px flex-1 bg-border" /> or <span className="h-px flex-1 bg-border" />
          </p>
          <div>
            <input className={inputCls} type="email" required placeholder="Email" value={data.email} onChange={(e) => setData('email', e.target.value)} />
            {errors?.email && <p className="mt-1 text-xs font-semibold text-destructive">{errors.email}</p>}
          </div>
          <input className={inputCls} type="password" required placeholder="Password" value={data.password} onChange={(e) => setData('password', e.target.value)} />
          <label className="flex items-center gap-2 text-sm text-muted-foreground">
            <input type="checkbox" checked={data.remember} onChange={(e) => setData('remember', e.target.checked)} /> Remember me
          </label>
          <button disabled={processing} className="w-full rounded-full bg-primary py-3 font-display font-bold text-primary-foreground disabled:opacity-60">
            {processing ? 'Logging in…' : 'Log in'}
          </button>
          <p className="flex justify-between text-sm">
            <Link href="/forgot-password" className="text-muted-foreground hover:text-foreground">Forgot password?</Link>
            <Link href="/register" className="font-bold hover:underline">Create account</Link>
          </p>
        </form>
      </section>
    </Layout>
  );
}
