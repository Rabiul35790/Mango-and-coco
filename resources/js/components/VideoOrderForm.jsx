import { useForm } from '@inertiajs/react';

const EMPTY = {
  customerName: '',
  customerEmail: '',
  recipientName: '',
  occasion: '',
  message: '',
  notes: '',
};

export function VideoOrderForm({ products }) {
  const { data, setData, post, processing, errors, reset } = useForm({
    productSlug: products[0]?.slug ?? 'personalised-video',
    ...EMPTY,
  });

  function submit(e) {
    e.preventDefault();
    post('/video-orders', {
      onSuccess: () => reset(),
    });
  }

  const inputCls = 'w-full rounded-xl border border-input bg-card px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-ring';

  return (
    <form onSubmit={submit} className="card-cosy space-y-5 p-6 sm:p-8">
      <fieldset className="space-y-3" disabled={processing}>
        <legend className="eyebrow">What are we making?</legend>
        <div className="grid gap-3 sm:grid-cols-2">
          {products.map((product) => {
            const selected = product.slug === data.productSlug;
            return (
              <button
                key={product.slug}
                type="button"
                onClick={() => setData('productSlug', product.slug)}
                aria-pressed={selected}
                className={`rounded-xl border p-4 text-left transition-colors ${selected ? 'border-primary bg-primary/15' : 'border-border bg-card hover:bg-muted/60'}`}
              >
                <span className="block font-display font-bold">{product.name}</span>
                <span className="mt-1 block text-sm text-muted-foreground">{product.tagline}</span>
              </button>
            );
          })}
        </div>
      </fieldset>

      <div className="grid gap-4 sm:grid-cols-2">
        <div className="space-y-2">
          <label className="text-sm font-semibold" htmlFor="customerName">Your name</label>
          <input id="customerName" required maxLength={120} className={inputCls}
            value={data.customerName} onChange={(e) => setData('customerName', e.target.value)} />
        </div>
        <div className="space-y-2">
          <label className="text-sm font-semibold" htmlFor="customerEmail">Your email</label>
          <input id="customerEmail" type="email" required maxLength={200} className={inputCls}
            value={data.customerEmail} onChange={(e) => setData('customerEmail', e.target.value)} />
        </div>
        <div className="space-y-2">
          <label className="text-sm font-semibold" htmlFor="recipientName">Who is it for?</label>
          <input id="recipientName" maxLength={120} placeholder="Coco's favourite human" className={inputCls}
            value={data.recipientName} onChange={(e) => setData('recipientName', e.target.value)} />
        </div>
        <div className="space-y-2">
          <label className="text-sm font-semibold" htmlFor="occasion">Occasion</label>
          <input id="occasion" maxLength={120} placeholder="Birthday, anniversary, just because" className={inputCls}
            value={data.occasion} onChange={(e) => setData('occasion', e.target.value)} />
        </div>
      </div>

      <div className="space-y-2">
        <label className="text-sm font-semibold" htmlFor="message">The message in the scene</label>
        <textarea id="message" required maxLength={400} rows={3}
          placeholder="Happy birthday, Nadia — you make every day brighter 🐥"
          className={inputCls} value={data.message} onChange={(e) => setData('message', e.target.value)} />
        <p className="text-xs text-muted-foreground">{data.message.length}/400 characters</p>
      </div>

      <div className="space-y-2">
        <label className="text-sm font-semibold" htmlFor="notes">Anything else? (optional)</label>
        <textarea id="notes" maxLength={1000} rows={3}
          placeholder="Favourite colours, inside jokes, when you need it by…"
          className={inputCls} value={data.notes} onChange={(e) => setData('notes', e.target.value)} />
      </div>

      <button type="submit" disabled={processing} className="w-full rounded-full bg-primary py-3 font-display font-bold text-primary-foreground disabled:opacity-60">
        {processing ? 'Sending…' : 'Send my brief'}
      </button>
      <p className="text-center text-xs text-muted-foreground">No payment yet — we confirm the details by email first.</p>
    </form>
  );
}
