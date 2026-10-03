import { ChevronLeft, ChevronRight } from 'lucide-react';
import { useState } from 'react';

/**
 * Reference-style gallery: thumbnail rail + main visual with
 * arrows and a 1/n counter. Pure component — images come from admin.
 */
export function ProductGallery({ images, name }) {
  const [index, setIndex] = useState(0);
  if (!images || images.length === 0) return null;

  const total = images.length;
  const go = (dir) => setIndex((i) => (i + dir + total) % total);

  return (
    <div className="flex gap-3">
      {total > 1 && (
        <div className="hidden w-16 shrink-0 flex-col gap-3 sm:flex">
          {images.map((src, i) => (
            <button
              key={i}
              type="button"
              onClick={() => setIndex(i)}
              aria-label={`View image ${i + 1}`}
              className={`overflow-hidden rounded-2xl border transition-colors ${i === index ? 'border-primary' : 'border-border hover:border-muted-foreground/40'}`}
            >
              <img src={src} alt="" loading="lazy" decoding="async" className="aspect-square w-full object-cover" />
            </button>
          ))}
        </div>
      )}

      <div className="relative min-w-0 flex-1 overflow-hidden rounded-3xl shadow-[var(--shadow-soft)]">
        <img
          key={images[index]}
          src={images[index]}
          alt={name}
          fetchPriority="high"
          decoding="async"
          className="aspect-square w-full object-cover"
        />
        {total > 1 && (
          <>
            <span className="absolute right-4 top-4 rounded-full bg-ink/60 px-2.5 py-1 text-xs font-bold text-white backdrop-blur-sm">
              {index + 1} / {total}
            </span>
            <button
              type="button"
              onClick={() => go(-1)}
              aria-label="Previous image"
              className="absolute left-3 top-1/2 grid size-10 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-ink shadow-[var(--shadow-soft)] transition-colors hover:bg-white"
            >
              <ChevronLeft className="size-5" />
            </button>
            <button
              type="button"
              onClick={() => go(1)}
              aria-label="Next image"
              className="absolute right-3 top-1/2 grid size-10 -translate-y-1/2 place-items-center rounded-full bg-white/90 text-ink shadow-[var(--shadow-soft)] transition-colors hover:bg-white"
            >
              <ChevronRight className="size-5" />
            </button>
          </>
        )}
      </div>
    </div>
  );
}
