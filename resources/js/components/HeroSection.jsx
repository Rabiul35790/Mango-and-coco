/**
 * HeroSection — full-viewport (100svh) cinematic hero.
 * - Video background when the admin uploaded one, else a slow Ken Burns poster
 *   (placeholder until you upload — no external dependencies).
 * - Left-aligned minimal copy + max 2 CTAs. White pills read on any footage.
 */
export function HeroSection({
  videoUrl,
  posterUrl,
  eyebrow,
  title,
  subtitle,
  primaryCta,
  secondaryCta,
}) {
  return (
    <section className="relative flex min-h-[100svh] w-full items-center overflow-hidden bg-ink">
      {videoUrl ? (
        <video
          key={videoUrl}
          className="absolute inset-0 h-full w-full object-cover"
          src={videoUrl}
          poster={posterUrl}
          autoPlay
          muted
          loop
          playsInline
          preload="metadata"
          aria-hidden="true"
        />
      ) : posterUrl ? (
        <img
          src={posterUrl}
          alt=""
          aria-hidden="true"
          fetchPriority="high"
          decoding="async"
          className="kenburns absolute inset-0 h-full w-full object-cover"
        />
      ) : null}

      {/* Legibility: dark left wash + soft landing into page background */}
      <div className="absolute inset-0 bg-gradient-to-r from-ink/75 via-ink/35 to-ink/10" aria-hidden="true" />
      <div className="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-background to-transparent" aria-hidden="true" />

      <div className="relative mx-auto w-full max-w-6xl px-4 pb-24 pt-32 sm:px-6">
        <div className="max-w-xl">
          {eyebrow && (
            <p className="font-display text-sm font-semibold uppercase tracking-[0.18em] text-white/70">
              {eyebrow}
            </p>
          )}
          <h1 className="mt-4 text-balance text-4xl font-semibold leading-[1.05] text-white sm:text-5xl lg:text-6xl">
            {title}
          </h1>
          {subtitle && (
            <p className="mt-5 max-w-md text-lg leading-relaxed text-white/85">
              {subtitle}
            </p>
          )}
          {(primaryCta || secondaryCta) && (
            <div className="mt-8 flex flex-wrap gap-3">
              {primaryCta && (
                <a
                  href={primaryCta.href}
                  className="whitespace-nowrap rounded-full bg-white px-6 py-3 font-display font-bold text-ink transition-[filter] hover:brightness-95 active:brightness-90"
                >
                  {primaryCta.label}
                </a>
              )}
              {secondaryCta && (
                <a
                  href={secondaryCta.href}
                  className="whitespace-nowrap rounded-full border border-white/40 px-6 py-3 font-display font-bold text-white backdrop-blur-sm transition-colors hover:bg-white/10"
                >
                  {secondaryCta.label}
                </a>
              )}
            </div>
          )}
        </div>
      </div>
    </section>
  );
}
