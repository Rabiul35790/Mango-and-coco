import { Link } from '@inertiajs/react';
import Layout from '../../Layout';
import { AccountNav, StatusBadge } from '../../components/AccountNav';

export default function Dashboard({ meta, flash, stats, recentOrders, recentVideoOrders }) {
  const cards = [
    { label: 'Shop orders', value: stats.orders, href: '/account/orders' },
    { label: 'Video briefs', value: stats.videoOrders, href: '/account/orders' },
    { label: 'Downloads ready', value: stats.downloads, href: '/account/downloads' },
    { label: 'My reviews', value: stats.reviews, href: '/account/orders' },
  ];

  return (
    <Layout meta={meta} flash={flash}>
      <section className="mx-auto w-full max-w-6xl px-4 pb-16 pt-28 sm:px-6 sm:pt-32">
        <AccountNav />
        <div className="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
          {cards.map((c) => (
            <Link key={c.label} href={c.href} className="card-cosy p-5">
              <p className="font-display text-3xl font-bold">{c.value}</p>
              <p className="mt-1 text-sm text-muted-foreground">{c.label}</p>
            </Link>
          ))}
        </div>

        <div className="mt-8 grid gap-6 lg:grid-cols-2">
          <div className="rounded-3xl border border-border bg-card p-6">
            <div className="flex items-center justify-between">
              <h2 className="font-display text-lg font-bold">Recent orders</h2>
              <Link href="/account/orders" className="text-sm font-bold hover:underline">View all</Link>
            </div>
            {recentOrders.length === 0
              ? <p className="mt-3 text-sm text-muted-foreground">No orders yet. <Link href="/shop/stickers" className="font-bold text-foreground hover:underline">Browse the shop</Link></p>
              : <ul className="mt-4 divide-y divide-border">
                  {recentOrders.map((o) => (
                    <li key={o.id} className="flex items-center justify-between gap-3 py-2.5 text-sm">
                      <span className="font-semibold">{o.product} <span className="font-normal text-muted-foreground">· {o.date}</span></span>
                      <StatusBadge status={o.status} />
                    </li>
                  ))}
                </ul>}
          </div>
          <div className="rounded-3xl border border-border bg-card p-6">
            <div className="flex items-center justify-between">
              <h2 className="font-display text-lg font-bold">Video briefs</h2>
              <Link href="/account/orders" className="text-sm font-bold hover:underline">View all</Link>
            </div>
            {recentVideoOrders.length === 0
              ? <p className="mt-3 text-sm text-muted-foreground">No briefs yet. <Link href="/order/personalised-video" className="font-bold text-foreground hover:underline">Personalise a video</Link></p>
              : <ul className="mt-4 divide-y divide-border">
                  {recentVideoOrders.map((v) => (
                    <li key={v.id} className="flex items-center justify-between gap-3 py-2.5 text-sm">
                      <span className="font-semibold">{v.product} <span className="font-normal text-muted-foreground">· {v.date}</span></span>
                      <StatusBadge status={v.status} />
                    </li>
                  ))}
                </ul>}
          </div>
        </div>
      </section>
    </Layout>
  );
}
