# Cloudflare R2 — deliverable ZIPs (stickers / wallpaper / coloring-books)

Admin uploads the ZIP in Filament → Products → Deliverable file. It lands in R2.
Buyers verify with their payment email → we return a 15-minute signed URL.

## 1) Create bucket + token
1. Cloudflare dashboard → R2 → Create bucket: `mango-coco-deliverables` (private).
2. R2 → Manage API tokens → Create (Object Read & Write on that bucket).
3. Note: Access Key ID, Secret, endpoint `https://<account-id>.r2.cloudflarestorage.com`.

## 2) `.env`
```
R2_ACCESS_KEY_ID=
R2_SECRET_ACCESS_KEY=
R2_BUCKET=mango-coco-deliverables
R2_DEFAULT_REGION=auto
R2_URL=                       # optional public dev URL, leave empty = private
R2_ENDPOINT=https://<account-id>.r2.cloudflarestorage.com
R2_USE_PATH_STYLE_ENDPOINT=false
```

## 3) How it flows
- Upload: Filament `FileUpload(disk: r2, directory: deliverables)` → `products.download_path`.
- Payment: Lemon Squeezy webhook marks `orders.status = paid` (mirror into our `orders`).
- Download: buyer enters payment email on details page → `POST /download/{product}`
  → checks `orders` for `paid` + email → `Storage::disk('r2')->temporaryUrl(path, +15 min)`.
- Dev without R2: set product `download_disk = public` and upload to `storage/app/public`.

## 4) Video performance (no slow pages)
- `OptimizedVideo.jsx`: `preload="metadata"`, poster-first, IntersectionObserver lazy-src,
  `playsInline`, no autoplay. Demo videos never block the page.
- Keep mp4s < 8 MB (720p, H.264, faststart). Host demos on R2/public CDN, not in git.
