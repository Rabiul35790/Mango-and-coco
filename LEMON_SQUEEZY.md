# Lemon Squeezy — where to put what (Mango&Coco)

This project sells sticker packs, coloring books and custom AI videos to
USA / Canada / Germany / Mexico / India, so we use **Lemon Squeezy**
(Merchant of Record — handles VAT/GST, cards, Apple Pay, PayPal).

## 1) Create account + store
1. Sign up at https://app.lemonsqueezy.com
2. Create a Store (Settings → Stores). Note the numeric **Store ID** from the URL
   (`.../stores/12345`) → put into `.env` as `LEMON_SQUEEZY_STORE_ID`.
3. Add products (one per catalog row):
   - Mango Everyday Pack (sticker_pack)
   - Coco Sweet Pack (sticker_pack)
   - Mango & Coco Duo Pack (sticker_pack)
   - Personalised Video (video)
   - Animated eCard (video/ecard)
   - Budgie Garden Coloring Book (coloring_book)
   Each product has **Variants** (usually 1). Copy each Variant ID
   (`.../variants/xxxxx`) → paste into Filament → Products → `Lemon Variant ID`.
   Until a Variant ID is set, BuyButton shows "opening soon" instead of breaking.

## 2) API key
Settings → API → Create key → copy → `.env`:
```
LEMON_SQUEEZY_API_KEY=eyJ...
```

## 3) Webhook (order → our DB)
Settings → Webhooks → Add:
- URL: `https://your-domain.com/lemon-squeezy/webhook`
  (local dev: expose Herd URL or use `php artisan lmsqueezy:listen`)
- Events: `order_created`, `order_refunded`, `subscription_*` (if you add subs)
- Secret → `.env` as `LEMON_SQUEEZY_WEBHOOK_SECRET`

The `lemonsqueezy/laravel` package verifies the signature and stores
`lemon_squeezy_orders` / `lemon_squeezy_customers`. Our own `orders` table
mirrors product_slug + status for the shop UI/Filament.

## 4) Test mode
Lemon Squeezy → Settings → enable **Test mode** → use test card `4242 4242 4242 4242`.
Checkout flow: BuyButton → `POST /checkout` (`CreateLemonCheckout` action)
→ hosted Lemon URL → success returns to `/checkout/success`.

## 5) Go live checklist
- [ ] Store approved (business details + payout)
- [ ] Real Variant IDs in Filament for every active product
- [ ] Webhook secret set + one test order refunded
- [ ] `APP_URL` = production domain (used for webhook + success URL)

## Files that use these keys
- `app/Actions/Checkout/CreateLemonCheckout.php` — reads `LEMON_SQUEEZY_API_KEY` + `LEMON_SQUEEZY_STORE_ID`
- `app/Http/Controllers/ShopController.php::checkout` — `POST /checkout`
- `resources/js/components/BuyButton.jsx` — opens hosted URL or "opening soon"
- `config/lemon-squeezy.php` (vendor) — webhook secret
