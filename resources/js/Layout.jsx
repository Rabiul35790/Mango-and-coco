import { Head } from '@inertiajs/react';
import { SiteFooter, SiteHeader, TrackingScripts } from './components/chrome';

export default function Layout({ meta, flash, children }) {
  return (
    <>
      <Head title={meta?.title ?? 'Mango&Coco'}>
        <TrackingScripts />
      </Head>
      <SiteHeader />
      {flash?.success && (
        <div className="mx-auto mt-4 w-full max-w-6xl px-4 sm:px-6">
          <p className="rounded-2xl border border-leaf/30 bg-leaf/10 px-4 py-3 text-sm font-semibold">{flash.success}</p>
        </div>
      )}
      {flash?.error && (
        <div className="mx-auto mt-4 w-full max-w-6xl px-4 sm:px-6">
          <p className="rounded-2xl border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm font-semibold">{flash.error}</p>
        </div>
      )}
      <main>{children}</main>
      <SiteFooter />
    </>
  );
}
