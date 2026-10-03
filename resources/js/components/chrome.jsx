import { Head, Link, usePage } from '@inertiajs/react';
import { Bird, Menu, X } from 'lucide-react';
import { useState } from 'react';

function useSite() {
  const { site } = usePage().props;
  return site ?? { settings: {}, categories: [] };
}

function useAuth() {
  return usePage().props.auth?.user ?? null;
}

export function TrackingScripts() {
  const { settings } = useSite();
  const t = settings?.tracking ?? {};
  return (
    <>
      {t.metaPixelId && (
        <script dangerouslySetInnerHTML={{ __html: `!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','${t.metaPixelId}');fbq('track','PageView');` }} />
      )}
      {t.googleAnalyticsId && (
        <>
          <script async src={`https://www.googletagmanager.com/gtag/js?id=${t.googleAnalyticsId}`} />
          <script dangerouslySetInnerHTML={{ __html: `window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','${t.googleAnalyticsId}');` }} />
        </>
      )}
      {t.microsoftClarityId && (
        <script dangerouslySetInnerHTML={{ __html: `(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window,document,"clarity","script","${t.microsoftClarityId}");` }} />
      )}
      {t.tiktokPixelId && (
        <script dangerouslySetInnerHTML={{ __html: `!function(w,d,t){w.TiktokAnalyticsObject=t;var t=w[t]=w[t]||[];t.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"];t.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<t.methods.length;i++)t.setAndDefer(t,t.methods[i]);t.instance=function(t){var e=t._i[t]||[];return t._i||(t._i={}),t._i[t]=t._i[t]||[],e};t.load=function(id){var n="https://analytics.tiktok.com/i18n/pixel/events.js";t._i=t._i||{},t._i[id]=[],t._i[id]._u=n;var o=d.createElement("script");o.type="text/javascript",o.async=!0,o.src=n+"?sdkid="+id+"&lib="+t;var a=d.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};t.load("${t.tiktokPixelId}");t.page();}(window,document,'ttq');` }} />
      )}
      {settings?.extraHeadScripts && <div dangerouslySetInnerHTML={{ __html: settings.extraHeadScripts }} />}
    </>
  );
}

export function SiteHeader() {
  const [open, setOpen] = useState(false);
  const { url } = usePage();
  const { settings, categories } = useSite();
  const user = useAuth();

  const nav = (categories ?? []).map((c) => ({ href: `/shop/${c.slug}`, label: c.name }));

  return (
    <header className="fixed inset-x-0 top-3 z-50 px-3 sm:top-5 sm:px-6">
      <div className="mx-auto w-full max-w-5xl">
        <div className="rounded-full border border-black/5 bg-white/85 shadow-[var(--shadow-soft)] backdrop-blur-xl">
          <div className="flex h-14 items-center justify-between gap-2 pl-4 pr-2 sm:pl-5">
            <Link href="/" className="flex shrink-0 items-center gap-2" aria-label={`${settings?.siteName ?? 'Mango&Coco'} home`}>
              {settings?.logoUrl
                ? <img src={settings.logoUrl} alt="logo" className="size-8 rounded-full object-cover" loading="eager" />
                : <span className="grid size-8 place-items-center rounded-full bg-primary text-primary-foreground">
                    <Bird className="size-4" aria-hidden="true" />
                  </span>}
              <span className="font-display text-lg font-bold tracking-tight text-foreground">
                {settings?.siteName ?? 'Mango&Coco'}
              </span>
            </Link>

            <nav className="hidden items-center gap-6 md:flex" aria-label="Main">
              {nav.map((item) => (
                <Link
                  key={item.href}
                  href={item.href}
                  className={`whitespace-nowrap text-sm font-semibold transition-colors hover:text-foreground ${url.startsWith(item.href) ? 'text-foreground' : 'text-muted-foreground'}`}
                >
                  {item.label}
                </Link>
              ))}
            </nav>

            <div className="flex shrink-0 items-center gap-1">
              {user ? (
                <Link
                  href="/account"
                  className="hidden whitespace-nowrap rounded-full bg-primary px-4 py-2 text-sm font-display font-bold text-primary-foreground sm:inline-flex"
                >
                  My account
                </Link>
              ) : (
                <>
                  <Link
                    href="/login"
                    className="hidden whitespace-nowrap px-2 py-2 text-sm font-semibold text-muted-foreground transition-colors hover:text-foreground sm:inline-flex"
                  >
                    Log in
                  </Link>
                  <Link
                    href="/order/personalised-video"
                    className="hidden whitespace-nowrap rounded-full bg-primary px-4 py-2 text-sm font-display font-bold text-primary-foreground sm:inline-flex"
                  >
                    Make a gift
                  </Link>
                </>
              )}
              <button
                onClick={() => setOpen(!open)}
                className="grid size-10 place-items-center rounded-full text-foreground transition-colors hover:bg-muted md:hidden"
                aria-label="Toggle menu"
              >
                {open ? <X className="size-5" /> : <Menu className="size-5" />}
              </button>
            </div>
          </div>
        </div>

        {open && (
          <nav className="mt-2 rounded-3xl border border-black/5 bg-white/95 p-2 shadow-[var(--shadow-lift)] backdrop-blur-xl md:hidden" aria-label="Mobile">
            {nav.map((item) => (
              <Link
                key={item.href}
                href={item.href}
                onClick={() => setOpen(false)}
                className="block rounded-2xl px-4 py-2.5 text-base font-semibold text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
              >
                {item.label}
              </Link>
            ))}
            <Link
              href="/order/personalised-video"
              onClick={() => setOpen(false)}
              className="mt-1 block rounded-2xl bg-primary px-4 py-2.5 text-center font-display font-bold text-primary-foreground"
            >
              Make a gift
            </Link>
            {user ? (
              <Link
                href="/account"
                onClick={() => setOpen(false)}
                className="block rounded-2xl px-4 py-2.5 text-base font-semibold text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
              >
                My account
              </Link>
            ) : (
              <Link
                href="/login"
                onClick={() => setOpen(false)}
                className="block rounded-2xl px-4 py-2.5 text-base font-semibold text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
              >
                Log in
              </Link>
            )}
          </nav>
        )}
      </div>
    </header>
  );
}

export function SiteFooter() {
  const { settings, categories } = useSite();
  const user = useAuth();
  return (
    <footer className="mt-24 border-t border-border/70 surface-cream">
      <div className="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-12 sm:px-6 md:flex-row md:items-end md:justify-between">
        <div className="max-w-sm">
          <p className="font-display text-xl font-bold">{settings?.siteName ?? 'Mango&Coco'}</p>
          <p className="mt-2 text-sm text-muted-foreground">
            Little budgie things made to send. Digital stickers, wallpapers, coloring books and personalised videos.
          </p>
          {settings?.address && <p className="mt-2 text-xs text-muted-foreground">{settings.address}</p>}
          {(settings?.contactEmail || settings?.supportEmail) && (
            <p className="mt-1 text-xs text-muted-foreground">
              {[settings.contactEmail, settings.supportEmail].filter(Boolean).join(' · ')}
            </p>
          )}
        </div>
        <nav className="flex flex-wrap gap-x-6 gap-y-2 text-sm font-semibold" aria-label="Footer">
          {(categories ?? []).map((c) => (
            <Link key={c.slug} href={`/shop/${c.slug}`} className="text-muted-foreground hover:text-foreground">{c.name}</Link>
          ))}
          <Link href="/about" className="text-muted-foreground hover:text-foreground">Our Story</Link>
          <Link href="/contact" className="text-muted-foreground hover:text-foreground">Contact</Link>
          <Link href="/track" className="text-muted-foreground hover:text-foreground">Track order</Link>
          <Link href={user ? '/account' : '/login'} className="text-muted-foreground hover:text-foreground">
            {user ? 'My account' : 'Log in'}
          </Link>
        </nav>
      </div>
      <div className="border-t border-border/60 px-4 py-5 text-center text-xs text-muted-foreground sm:px-6">
        {settings?.copyright ?? `© ${new Date().getFullYear()} Mango&Coco. All artwork made with love.`} Secure checkout by Lemon Squeezy.
      </div>
      {settings?.extraBodyScripts && <div dangerouslySetInnerHTML={{ __html: settings.extraBodyScripts }} />}
    </footer>
  );
}
