import { Link, useForm } from '@inertiajs/react';
import Layout from '../../Layout';
import { GoogleButton } from '../../components/GoogleButton';

export default function Register({ meta, flash, errors }) {
  const { data, setData, post, processing } = useForm({ name: '', email: '', password: '', password_confirmation: '' });
  const inputCls = 'w-full rounded-xl border border-input bg-card px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-ring';

  return (
    <Layout meta={meta} flash={flash}>
      <section className="mx-auto w-full max-w-md px-4 pb-16 pt-28 sm:px-6 sm:pt-32">
        <h1 className="text-3xl font-semibold">Create your account</h1>
        <p className="mt-2 text-sm text-muted-foreground">Past guest orders on this email link up automatically.</p>
        <form onSubmit={(e) => { e.preventDefault(); post('/register'); }} className="card-cosy mt-6 space-y-4 p-6">
          <GoogleButton label="Sign up with Google" />
          <p className="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-muted-foreground">
            <span className="h-px flex-1 bg-border" /> or <span className="h-px flex-1 bg-border" />
          </p>
          <div>
            <input className={inputCls} required maxLength={120} placeholder="Your name" value={data.name} onChange={(e) => setData('name', e.target.value)} />
            {errors?.name && <p className="mt-1 text-xs font-semibold text-destructive">{errors.name}</p>}
          </div>
          <div>
            <input className={inputCls} type="email" required placeholder="Email" value={data.email} onChange={(e) => setData('email', e.target.value)} />
            {errors?.email && <p className="mt-1 text-xs font-semibold text-destructive">{errors.email}</p>}
          </div>
          <div>
            <input className={inputCls} type="password" required placeholder="Password (min 8 characters)" value={data.password} onChange={(e) => setData('password', e.target.value)} />
            {errors?.password && <p className="mt-1 text-xs font-semibold text-destructive">{errors.password}</p>}
          </div>
          <input className={inputCls} type="password" required placeholder="Confirm password" value={data.password_confirmation} onChange={(e) => setData('password_confirmation', e.target.value)} />
          <button disabled={processing} className="w-full rounded-full bg-primary py-3 font-display font-bold text-primary-foreground disabled:opacity-60">
            {processing ? 'Creating…' : 'Create account'}
          </button>
          <p className="text-center text-sm text-muted-foreground">Have an account? <Link href="/login" className="font-bold text-foreground hover:underline">Log in</Link></p>
        </form>
      </section>
    </Layout>
  );
}
