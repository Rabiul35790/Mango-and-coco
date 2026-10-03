import { Link, useForm, usePage } from '@inertiajs/react';
import { Star } from 'lucide-react';
import { useState } from 'react';

export function Stars({ value = 5, className = 'size-4' }) {
  return (
    <span className="flex gap-0.5" aria-hidden="true">
      {Array.from({ length: 5 }).map((_, i) => (
        <Star key={i} className={`${className} ${i < Math.round(value) ? 'fill-primary text-primary' : 'text-muted-foreground/40'}`} />
      ))}
    </span>
  );
}

export function ReviewsSection({ product, reviews, canReview, myReview }) {
  const user = usePage().props.auth?.user ?? null;
  const [formOpen, setFormOpen] = useState(!!myReview);
  const { data, setData, post, processing } = useForm({
    productSlug: product.slug,
    rating: myReview?.rating ?? 5,
    title: myReview?.title ?? '',
    body: myReview?.body ?? '',
  });
  const inputCls = 'w-full rounded-xl border border-input bg-card px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-ring';

  return (
    <section className="mx-auto w-full max-w-6xl px-4 pb-4 sm:px-6">
      <div className="rounded-3xl border border-border bg-card p-6 sm:p-8">
        <div className="flex flex-wrap items-center justify-between gap-3">
          <h2 className="font-display text-xl font-bold">
            Reviews {reviews.count > 0 && <span className="text-muted-foreground">({reviews.count})</span>}
          </h2>
          {reviews.count > 0 && (
            <span className="flex items-center gap-2 text-sm font-bold">
              <Stars value={reviews.average} /> {reviews.average.toFixed(1)}
            </span>
          )}
        </div>

        {reviews.items.length === 0
          ? <p className="mt-3 text-sm text-muted-foreground">No reviews yet — yours could be the first.</p>
          : <ul className="mt-5 space-y-5">
              {reviews.items.map((r, i) => (
                <li key={i} className="border-t border-border pt-4 first:border-t-0 first:pt-0">
                  <p className="flex items-center justify-between gap-3 text-sm">
                    <span className="font-bold">{r.name}</span>
                    <span className="text-muted-foreground">{r.date}</span>
                  </p>
                  <span className="mt-1 block"><Stars value={r.rating} className="size-3.5" /></span>
                  {r.title && <p className="mt-1 font-bold">{r.title}</p>}
                  {r.body && <p className="mt-1 text-sm text-muted-foreground">{r.body}</p>}
                </li>
              ))}
            </ul>}

        <div className="mt-6 border-t border-border pt-5">
          {!user ? (
            <p className="text-sm text-muted-foreground">
              Bought this? <Link href="/login" className="font-bold text-foreground hover:underline">Log in</Link> to write a review.
            </p>
          ) : !canReview ? (
            <p className="text-sm text-muted-foreground">Reviews unlock after purchase — only verified buyers can rate.</p>
          ) : myReview && myReview.status === 'approved' && !formOpen ? (
            <button onClick={() => setFormOpen(true)} className="rounded-full border border-border px-5 py-2.5 text-sm font-display font-bold transition-colors hover:bg-muted">
              Edit your review
            </button>
          ) : (
            <form onSubmit={(e) => { e.preventDefault(); post('/account/reviews', { onSuccess: () => setFormOpen(false) }); }} className="space-y-3">
              <p className="font-display font-bold">{myReview ? 'Edit your review' : 'Write a review'}</p>
              {myReview?.status === 'pending' && <p className="text-xs font-semibold text-muted-foreground">Your review is awaiting moderation.</p>}
              <div className="flex gap-1" role="radiogroup" aria-label="Rating">
                {[1, 2, 3, 4, 5].map((n) => (
                  <button key={n} type="button" onClick={() => setData('rating', n)} aria-label={`${n} stars`}>
                    <Star className={`size-7 ${n <= data.rating ? 'fill-primary text-primary' : 'text-muted-foreground/40'}`} />
                  </button>
                ))}
              </div>
              <input className={inputCls} maxLength={160} placeholder="Headline (optional)" value={data.title} onChange={(e) => setData('title', e.target.value)} />
              <textarea className={inputCls} rows={3} maxLength={2000} placeholder="What did you love?" value={data.body} onChange={(e) => setData('body', e.target.value)} />
              <button disabled={processing} className="rounded-full bg-primary px-6 py-2.5 font-display font-bold text-primary-foreground disabled:opacity-60">
                {processing ? 'Saving…' : 'Submit review'}
              </button>
            </form>
          )}
        </div>
      </div>
    </section>
  );
}
