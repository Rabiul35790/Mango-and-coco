# Deploy checklist — Mango&Coco (Laravel 12 + Inertia React + Filament)

## 1) Server requirements
- PHP 8.2+ with `intl, mbstring, openssl, pdo_mysql, fileinfo, gd`
- MySQL 8, Node 20+, Composer 2
- HTTPS обязателен: set `APP_URL=https://your-domain.com`. Behind Cloudflare/LB
  with flexible TLS, also trust proxies in `bootstrap/app.php`:
  `$middleware->trustProxies(at: '*')` inside the `withMiddleware` closure.

## 2) First deploy
```bash
cp .env.example .env   # then fill every blank key below
php artisan key:generate
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan storage:link
php artisan make:filament-user   # admin login for /admin
# …then promote that user (only is_admin users pass the panel gate):
php artisan tinker --execute='App\Models\User::where("email","YOU@domain.com")->update(["is_admin" => true]);'
```

## 3) Keys to fill (nothing works without its key — each fails gracefully, see table)
| Key | Where | What breaks if empty |
|---|---|---|
| `LEMON_SQUEEZY_API_KEY` + `LEMON_SQUEEZY_STORE_ID` | Lemon dashboard → API / Stores | Buy buttons show "opening soon", no payment |
| `LEMON_SQUEEZY_WEBHOOK_SECRET` | Lemon → Webhooks (URL: `https://domain/lemon-squeezy/webhook`, events `order_created`, `order_refunded`) | Paid orders never recorded → no downloads/reviews/account |
| `R2_*` | Cloudflare R2 → bucket + API token | Deliverable ZIP uploads + signed downloads fail |
| `GOOGLE_CLIENT_ID` + `GOOGLE_CLIENT_SECRET` | Google Cloud → OAuth client (redirect: `https://domain/auth/google/callback`) | Google button shows a friendly error, email login unaffected |
| `MAIL_*` | Gmail SMTP + app password (free) to start | Password resets never arrive (login/register unaffected) |

## 4) Go-live commands (every deploy)
```bash
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan filament:optimize
php artisan queue:restart
npm run build
```
Run a queue worker under supervisor (database driver is fine to start):
`php artisan queue:work --tries=3 --max-time=3600`

## 5) Post-deploy smoke test
1. `/` loads, products listed · 2. `/admin` login works
2. Lemon **test mode** purchase → webhook arrives → order appears in Filament
   with tracking ID → `/track?code=MC-…` shows Paid + download works
3. Video brief → payment auto-confirms it → admin moves it to Delivered
4. `php artisan test` → 16 passed
