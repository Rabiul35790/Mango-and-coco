/**
 * OptimizedVideo — keeps pages lightning fast even with many videos.
 * - preload="metadata" (no full download until play)
 * - poster image shown first, lazy loading
 * - playsInline + disablePictureInPicture for product demos
 * - IntersectionObserver: only sets src when in viewport
 */
import { useEffect, useRef, useState } from 'react';

export function OptimizedVideo({ src, poster, className = '', ratio = 'aspect-video' }) {
  const ref = useRef(null);
  const [visible, setVisible] = useState(false);

  useEffect(() => {
    const el = ref.current;
    if (!el) return;
    if (!('IntersectionObserver' in window)) {
      setVisible(true);
      return;
    }
    const io = new IntersectionObserver(
      (entries) => entries.forEach((e) => e.isIntersecting && (setVisible(true), io.disconnect())),
      { rootMargin: '400px' },
    );
    io.observe(el);
    return () => io.disconnect();
  }, []);

  if (!src) return null;

  return (
    <div ref={ref} className={`${ratio} w-full overflow-hidden rounded-3xl bg-muted ${className}`}>
      {visible ? (
        <video
          className="h-full w-full object-cover"
          src={src}
          poster={poster}
          preload="metadata"
          playsInline
          controls
          disablePictureInPicture
          controlsList="nodownload"
        />
      ) : (
        poster
          ? <img src={poster} alt="Video preview" loading="lazy" decoding="async" className="h-full w-full object-cover" />
          : <div className="grid h-full w-full place-items-center text-4xl">▶</div>
      )}
    </div>
  );
}
