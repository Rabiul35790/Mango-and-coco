import Layout from '../../Layout';
import { AccountNav, StatusBadge } from '../../components/AccountNav';

export default function Orders({ meta, flash, orders, videoOrders }) {
  return (
    <Layout meta={meta} flash={flash}>
      <section className="mx-auto w-full max-w-6xl px-4 pb-16 pt-28 sm:px-6 sm:pt-32">
        <AccountNav />

        <h2 className="mt-8 font-display text-xl font-bold">Shop orders</h2>
        {orders.length === 0
          ? <p className="mt-2 text-sm text-muted-foreground">No shop orders yet.</p>
          : <div className="mt-3 overflow-hidden rounded-3xl border border-border bg-card">
              <ul className="divide-y divide-border">
                {orders.map((o) => (
                  <li key={o.id} className="flex flex-wrap items-center justify-between gap-2 px-5 py-3.5 text-sm">
                    <span><strong>#{o.id}</strong> · {o.product} <span className="text-muted-foreground">· {o.date}</span>
                      {o.trackingId && <a href={`/track?code=${o.trackingId}`} className="ml-2 font-mono text-xs font-bold text-primary hover:underline">{o.trackingId}</a>}
                    </span>
                    <span className="flex items-center gap-3">
                      <span className="font-display font-bold">{o.total}</span>
                      <StatusBadge status={o.status} />
                      {o.hasDownload && o.status === 'paid' && (
                        <a href="/account/downloads" className="rounded-full bg-primary px-3 py-1.5 text-xs font-display font-bold text-primary-foreground">Download</a>
                      )}
                    </span>
                  </li>
                ))}
              </ul>
            </div>}

        <h2 className="mt-8 font-display text-xl font-bold">Personalised video & card briefs</h2>
        {videoOrders.length === 0
          ? <p className="mt-2 text-sm text-muted-foreground">No briefs yet.</p>
          : <div className="mt-3 overflow-hidden rounded-3xl border border-border bg-card">
              <ul className="divide-y divide-border">
                {videoOrders.map((v) => (
                  <li key={v.id} className="px-5 py-3.5 text-sm">
                    <span className="flex flex-wrap items-center justify-between gap-2">
                      <span><strong>#{v.id}</strong> · {v.product} <span className="text-muted-foreground">· {v.date}</span>
                        {v.trackingId && <a href={`/track?code=${v.trackingId}`} className="ml-2 font-mono text-xs font-bold text-primary hover:underline">{v.trackingId}</a>}
                      </span>
                      <StatusBadge status={v.status} />
                    </span>
                    <p className="mt-1 text-muted-foreground">“{v.message}” {v.deliverTo === 'other' ? '· gift delivery' : ''}</p>
                  </li>
                ))}
              </ul>
            </div>}
      </section>
    </Layout>
  );
}
