import { Link, router, usePage } from '@inertiajs/react';

function csrf() {
  return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

export function AccountNav() {
  const { url, auth } = usePage().props;
  const items = [
    { href: '/account', label: 'Overview' },
    { href: '/account/orders', label: 'Orders' },
    { href: '/account/downloads', label: 'Downloads' },
  ];
  return (
    <div className="flex flex-wrap items-center justify-between gap-3">
      <div>
        <p className="eyebrow">My account</p>
        <h1 className="mt-1 text-3xl font-semibold">Hi, {auth?.user?.name?.split(' ')[0] ?? 'there'}</h1>
      </div>
      <nav className="flex gap-1 rounded-full border border-border bg-card p-1" aria-label="Account">
        {items.map((i) => (
          <Link
            key={i.href}
            href={i.href}
            className={`whitespace-nowrap rounded-full px-4 py-2 text-sm font-bold transition-colors ${url === i.href ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground'}`}
          >
            {i.label}
          </Link>
        ))}
        <button
          onClick={() => router.post('/logout', {}, { headers: { 'X-CSRF-TOKEN': csrf() } })}
          className="whitespace-nowrap rounded-full px-4 py-2 text-sm font-bold text-muted-foreground transition-colors hover:text-foreground"
        >
          Log out
        </button>
      </nav>
    </div>
  );
}

export function StatusBadge({ status }) {
  const map = {
    paid: 'bg-primary/15 text-primary',
    pending: 'bg-secondary text-secondary-foreground',
    failed: 'bg-destructive/10 text-destructive',
    refunded: 'bg-muted text-muted-foreground',
    new: 'bg-secondary text-secondary-foreground',
    confirmed: 'bg-primary/15 text-primary',
    in_production: 'bg-primary/15 text-primary',
    delivered: 'bg-primary text-primary-foreground',
    cancelled: 'bg-muted text-muted-foreground',
  };
  return (
    <span className={`whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-bold ${map[status] ?? 'bg-muted text-muted-foreground'}`}>
      {String(status).replace(/_/g, ' ')}
    </span>
  );
}
