import { useState } from 'react';

function csrf() {
  return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

/** Secure download button: verifies paid order, then opens 15-min signed R2 URL. */
export function DownloadButton({ productSlug, className = '' }) {
  const [email, setEmail] = useState('');
  const [busy, setBusy] = useState(false);
  const [msg, setMsg] = useState('');

  async function handle(e) {
    e.preventDefault();
    setBusy(true);
    setMsg('');
    try {
      const res = await fetch(`/download/${productSlug}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf(), Accept: 'application/json' },
        body: JSON.stringify({ email }),
      });
      const data = await res.json();
      if (res.ok && data.url) {
        window.location.href = data.url;
      } else {
        setMsg(data.message ?? 'No paid order found for this email yet.');
      }
    } catch {
      setMsg('Download failed. Try again.');
    } finally {
      setBusy(false);
    }
  }

  return (
    <form onSubmit={handle} className={`card-cosy space-y-3 p-5 ${className}`}>
      <h3 className="font-display font-bold">Download your files</h3>
      <p className="text-sm text-muted-foreground">Enter the email you paid with. We verify the order, then give a secure 15-minute link.</p>
      <div className="flex gap-2">
        <input
          type="email" required placeholder="you@email.com" value={email}
          onChange={(e) => setEmail(e.target.value)}
          className="flex-1 rounded-xl border border-input bg-card px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-ring"
        />
        <button disabled={busy} className="rounded-full bg-primary px-5 py-2 font-display font-bold text-primary-foreground disabled:opacity-60">
          {busy ? '…' : 'Download ZIP'}
        </button>
      </div>
      {msg && <p className="text-sm text-muted-foreground">{msg}</p>}
    </form>
  );
}
