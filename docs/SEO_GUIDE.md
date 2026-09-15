# ZinToop SEO Guide — Rules, Fixes & Best Practices

> **⚠️ CRITICAL: Any agent or developer MUST read this document before making ANY changes to views, routes, schemas, or meta tags.**

---

## 🔒 Golden Rules (NEVER Break These)

1. **Never change page titles or meta descriptions** without explicit user approval
2. **Never modify or remove `hreflang` alternate tags** — they drive multilingual traffic
3. **Never add incomplete structured data** — if you add a schema field, complete ALL its required sub-fields
4. **All public routes MUST be inside the `{locale}` prefix group** — see Architecture below
5. **The canonical URL includes the locale prefix** (e.g., `https://zintoop.com/fr/listings/42`)
6. **Root `/` uses smart `Accept-Language` detection**: Arab/Tunisian browsers → `/ar`, French browsers → `/fr`, all others (international) → `/en`. The `x-default` hreflang always points to `/en` (the international version).
7. **Test JSON-LD with Google Rich Results Test** (https://search.google.com/test/rich-results) before deploying any schema changes
8. **Always deploy via the one-liner in `HOSTINGER_DEPLOYMENT_GUIDE.md`** — includes `route:clear`, never skip it
9. **Never use `@@context` in JSON-LD** — always use `json_encode()` to avoid Blade escaping issues
10. **Never use `APP_KEY` as an obfuscation salt** — use the dedicated `OBFUSCATOR_SALT` env variable

---

## 🏗️ Architecture Overview

### Multilingual URL System (Locale Prefix)

- **Strategy**: URL-prefix routing (`/ar/...`, `/fr/...`, `/en/...`)
- **Supported**: Arabic (`ar`), French (`fr`), English (`en`)
- **Default**: Arabic (`ar`)
- **How it works**:
  1. All public routes are inside `Route::prefix('{locale}')->where(['locale' => 'ar|fr|en'])->group(...)` in `routes/web.php`
  2. Middleware `SetLocale` reads the `{locale}` route parameter and sets `App::setLocale()`
  3. `URL::defaults(['locale' => $locale])` ensures `route()` helper auto-includes the locale
  4. Each language version has its own distinct URL: `/ar/listings/42`, `/fr/listings/42`, `/en/listings/42`
  5. Translation files: `resources/lang/ar.json`, `resources/lang/fr.json`, `resources/lang/en.json`

### URL Structure

```
https://zintoop.com/ar/              → Arabic home page
https://zintoop.com/fr/              → French home page
https://zintoop.com/en/              → English home page
https://zintoop.com/ar/listings/42   → Arabic listing
https://zintoop.com/fr/listings/42   → French listing
https://zintoop.com/                 → 302 redirect to /ar/
https://zintoop.com/listings/42      → 301 redirect to /ar/listings/42
```

### Canonical URL & hreflang Strategy
```html
<!-- Each page generates path-based hreflang (NOT query-param) -->
<link rel="alternate" hreflang="ar" href="https://zintoop.com/ar/listings/42">
<link rel="alternate" hreflang="fr" href="https://zintoop.com/fr/listings/42">
<link rel="alternate" hreflang="en" href="https://zintoop.com/en/listings/42">
<!-- x-default ALWAYS points to /en — for international Google searchers -->
<link rel="alternate" hreflang="x-default" href="https://zintoop.com/en/listings/42">

<!-- Canonical = current locale version of the page -->
<link rel="canonical" href="https://zintoop.com/fr/listings/42">
```

> [!CAUTION]
> `x-default` must NEVER point to `/ar`. This was a known bug fixed September 7, 2026.
> It caused Google to serve Arabic snippets to international importers searching in English.

### Routes That Stay OUTSIDE the Locale Prefix

These routes do NOT get a language prefix (they are language-neutral):
- **Admin routes**: `/admin/...`
- **Auth routes**: `/login`, `/logout`, `/password/...` (from `auth.php`)
- **Social auth**: `/auth/facebook/...`, `/auth/google/...`
- **API routes**: `/api/...`
- **Feeds**: `/sitemap.xml`, `/feed.rss`, `/google-merchant-feed.xml`
- **Infrastructure**: `/healthz`, `/email-preview/...`, `/og-image/...`
- **Livewire**: `/livewire/...`

### Adding a New Public Route

When adding any new user-facing page, **always add it inside the locale prefix group**:

```php
// In routes/web.php — inside one of the Route::prefix('{locale}') groups:
Route::get('my-new-page', [MyController::class, 'index'])->name('my-page');
```

The route will automatically be available at `/ar/my-new-page`, `/fr/my-new-page`, `/en/my-new-page`.

Use `route('my-page')` in Blade — the locale is injected automatically via `URL::defaults`.

### Language Switcher Pattern

All language switchers use direct URL navigation (no redirect route):

```blade
@php
    $switchPath = preg_replace('#^/(ar|fr|en)#', '', request()->getPathInfo()) ?: '/';
@endphp
<a href="{{ url('ar' . $switchPath) }}">العربية</a>
<a href="{{ url('fr' . $switchPath) }}">Français</a>
<a href="{{ url('en' . $switchPath) }}">English</a>
```

The `lang.switch` route is kept for backward compatibility only (redirects to locale URL).

---

### Layout Files
| Layout | Used By | Location |
|--------|---------|----------|
| `app.blade.php` | All authenticated + public pages | `resources/views/layouts/app.blade.php` |
| `guest.blade.php` | Login, register pages | `resources/views/layouts/guest.blade.php` |

Both layouts contain: canonical URL, hreflang tags, Organization schema.

> [!WARNING]
> `guest.blade.php` previously had legacy `?lang=ar` hreflang format (fixed Sept 7, 2026).
> Any future changes to `guest.blade.php` hreflang must use locale-prefixed paths, NOT query params.

---

## 📋 Problems Fixed (August 2026)

### Fix 1: Canonical URL Deduplication
| Before | After |
|--------|-------|
| Canonical = `url()?lang=fr` for French users | Canonical = always `url()` (clean, no params) |
| **Result**: 270 "duplicate page" warnings | **Result**: All pages consolidate to single canonical |

**Files changed**: `app.blade.php`, `guest.blade.php`

**Google errors resolved**:
- ✅ "Autre page avec balise canonique correcte" (270 pages)
- ✅ "Page en double : Google n'a pas choisi la même URL canonique" (17 pages)
- ✅ "Page en double sans URL canonique sélectionnée" (2 pages)

---

### Fix 2: JSON-LD Product Schema (Listing Detail Pages)
**File**: `resources/views/listings/show.blade.php`

| Field | Before | After |
|-------|--------|-------|
| `shippingDetails` | Incomplete (missing rate & time) | ✅ Complete with shippingRate, deliveryTime |
| `shippingRate` | Missing | ✅ `0 TND` (FOB / free for B2B) |
| `deliveryTime` | Missing | ✅ Handling 1-3 days, Transit 2-7 days |
| `hasMerchantReturnPolicy` | Missing | ✅ `MerchantReturnNotPermitted` (B2B wholesale) |
| `validFrom` | Missing | ✅ Set to listing creation date |

**Google errors resolved**:
- ✅ "Champ shippingDetails manquant" (127 items)
- ✅ "Champ shippingRate manquant" (4 items)
- ✅ "Champ deliveryTime manquant" (4 items)
- ✅ "Champ hasMerchantReturnPolicy manquant" (131 items)
- ✅ "Champ validFrom manquant" (131 items)

---

### Fix 3: JSON-LD Product Schema (Marketplace Page)
**File**: `resources/views/home_marketplace.blade.php`

Same fields added as Fix 2 to the `ItemList > Product` schema on the main marketplace page.

---

### Fix 4: Google Merchant Feed
**Files**: `GoogleMerchantFeedController.php`, `google_merchant_feed.blade.php`

| Issue | Fix |
|-------|-----|
| "Devise non acceptée" (TND not accepted) | All prices converted to **USD** via `CurrencyConverter` |
| Wrong product images (profile photos) | Feed uses listing/product media only |
| Non-oil products in feed | Filter: `product.type = 'oil'` only |
| Products without photos | Excluded from feed entirely (no placeholders) |
| Empty `shipping_weight` tag | Only output when weight > 0 |

**Google Merchant errors resolved**:
- ✅ "Devise non acceptée"
- ✅ "Type d'image non accepté"
- ✅ Products without photos excluded

---

### Fix 5: OG Image for Listings
**File**: `resources/views/listings/show.blade.php`

| Before | After |
|--------|-------|
| Used dynamic OG image generator route | Uses real product photo URL directly |
| Fallback: non-existent placeholder | Fallback: ZinToop logo (`images/zintoop-logo.png`) |

---

### Fix 6: Multilingual SEO — URL Prefix Routing (August 15, 2026)

**Problem**: French/English pages were invisible to Google because the site used `?lang=` query strings. Google treated all language versions as duplicates of the Arabic page.

**Solution**: Switched to URL-prefix routing (`/ar/`, `/fr/`, `/en/`).

**Files changed**:
| File | Change |
|------|--------|
| `SetLocale.php` | Reads locale from `{locale}` route param instead of `?lang=` query |
| `AppServiceProvider.php` | Added `URL::defaults(['locale' => ...])` for route() helper |
| `routes/web.php` | Wrapped public routes in `{locale}` prefix group, added catch-all 301 redirects |
| `app.blade.php` | Path-based hreflang, canonical, JSON-LD via `json_encode()`, direct lang switcher links |
| `sitemap.blade.php` | 3 URLs per page (ar/fr/en) with `xhtml:link` hreflang alternates |
| `SEO_GUIDE.md` | Updated architecture documentation |

**Impact**:
- ✅ Each language version gets its own Google-indexed URL
- ✅ hreflang tags use clean paths (not query params)
- ✅ Old URLs 301-redirect to locale-prefixed URLs (SEO authority preserved)
- ✅ No database changes, no controller changes, no translation file changes

---

## 📄 Structured Data Reference

### JSON-LD Convention

**ALWAYS use `json_encode()` for JSON-LD**, never raw JSON with `@@context`:

```blade
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $product->name,
    // ...
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
```

### Listing Detail Page (`show.blade.php`)
```json
{
  "@type": "Product",
  "name": "chemlali — Extra Virgin Olive Oil (EVOO) | Tunisia",
  "image": ["https://zintoop.com/storage/listings/100/photo.webp"],
  "description": "Buy EVOO directly from...",
  "brand": { "@type": "Brand", "name": "ZinToop" },
  "category": "Cooking Oils",
  "offers": {
    "@type": "Offer",
    "price": "12.000",
    "priceCurrency": "TND",
    "priceValidUntil": "2026-09-07",
    "validFrom": "2026-07-31",
    "availability": "https://schema.org/InStock",
    "itemCondition": "https://schema.org/NewCondition",
    "seller": { "@type": "Organization", "name": "Seller Name" },
    "shippingDetails": {
      "@type": "OfferShippingDetails",
      "shippingDestination": { "@type": "DefinedRegion", "addressCountry": "TN" },
      "shippingRate": { "@type": "MonetaryAmount", "value": "0", "currency": "TND" },
      "deliveryTime": {
        "@type": "ShippingDeliveryTime",
        "handlingTime": { "minValue": 1, "maxValue": 3, "unitCode": "DAY" },
        "transitTime": { "minValue": 2, "maxValue": 7, "unitCode": "DAY" }
      }
    },
    "hasMerchantReturnPolicy": {
      "@type": "MerchantReturnPolicy",
      "applicableCountry": "TN",
      "returnPolicyCategory": "https://schema.org/MerchantReturnNotPermitted"
    }
  }
}
```

### Marketplace Page (`home_marketplace.blade.php`)
- Uses `ItemList` with `ListItem` containing `Product` entries
- Each product has the same complete `offers` block as above

### Organization Schema (in `app.blade.php`)
```json
{
  "@type": "Organization",
  "name": "ZinToop",
  "url": "https://zintoop.com",
  "logo": "https://zintoop.com/images/zintoop-logo.png"
}
```

---

## 🗺️ Sitemap

**URL**: `https://zintoop.com/sitemap.xml`  
**Controller**: `PublicController@sitemap`  
**Template**: `resources/views/public/sitemap.blade.php`

**Structure**: Each page generates 3 URL entries (one per language) with `xhtml:link` hreflang alternates:

```xml
<url>
  <loc>https://zintoop.com/ar/listings/42</loc>
  <xhtml:link rel="alternate" hreflang="ar" href="https://zintoop.com/ar/listings/42"/>
  <xhtml:link rel="alternate" hreflang="fr" href="https://zintoop.com/fr/listings/42"/>
  <xhtml:link rel="alternate" hreflang="en" href="https://zintoop.com/en/listings/42"/>
  <!-- x-default always points to /en -->
  <xhtml:link rel="alternate" hreflang="x-default" href="https://zintoop.com/en/listings/42"/>
</url>
```

**Includes**:
- Static pages: `/`, `/register`, `/about`, `/how-it-works`, `/olive-varieties`, `/servicehub`
- Dynamic listings: all active listings (`status = active`) — URLs use Hashids after Sept 7, 2026
- Dynamic articles: all active articles
- Gulf products: premium + export-ready products

> [!WARNING]
> `/gulf/catalog` and `/catalog` are **NOT** in the sitemap — they are legacy paths that redirect 301 to `/#products`.
> Never add them back to the sitemap.

> **Note**: After deploying, resubmit the sitemap in Google Search Console for faster re-indexing.

---

## 🛒 Google Merchant Center Feed

**URL**: `https://zintoop.com/google-merchant-feed.xml`  
**Controller**: `GoogleMerchantFeedController@feed`  
**Template**: `resources/views/public/google_merchant_feed.blade.php`  
**Cache**: 1 hour (`google:merchant:feed` key)

### Feed Rules
- **Products**: Only `product.type = 'oil'` (olive oil, NOT table olives)
- **Photos**: Only listings with real uploaded media (no placeholders)
- **Currency**: All prices converted to **USD** via `CurrencyConverter` service
- **Images**: Uses listing media → product media (in that priority order)
- **Category**: Google Product Category `422` (Cooking Oils)

### After Adding New Listings
1. Clear feed cache: `php artisan cache:forget google:merchant:feed`
2. Or wait 1 hour for auto-refresh
3. In Merchant Center: click "Mettre à jour" to fetch immediately

### Merchant Center Shipping Config
- **National (Tunisia)**: By weight, 2-7 days
- **International (57 countries)**: Free (B2B Bulk), 9-40 days

---

## 🔍 SEO Target Keywords

The following keywords are embedded in titles, descriptions, and schema across the site:

| Language | Keywords |
|----------|----------|
| English | olive oil prices, bulk olive oil, Tunisian olive oil, EVOO Tunisia, premium olive oil, olive oil market, olive oil direct from producers |
| French | huile d'olive tunisienne, prix huile d'olive, huile d'olive en gros, huile d'olive vierge extra |
| Arabic | زيت زيتون تونسي, أسعار زيت الزيتون, زيت زيتون بالجملة |

---

## ⚠️ Known Issues Log

| Issue | Count | Status | Resolution |
|-------|-------|--------|------------|
| "Introuvable (404)" old listings | 55 | ✅ Fixed Sept 7, 2026 | Smart 301 redirects: deleted listings → `/#products`, deleted articles → `/articles` |
| "Page avec redirection" | 65+ | ✅ Normal | Old URLs 301-redirect to locale-prefixed. Preserves SEO authority. |
| "Explorée, actuellement non indexée" | 8 | 🟡 Monitoring | Google crawled but not yet indexed. Resolves as domain authority grows. |
| `guest.blade.php` `?lang=` hreflang | - | ✅ Fixed Sept 7, 2026 | Updated to locale-prefixed paths |
| `x-default` pointing to `/ar` | - | ✅ Fixed Sept 7, 2026 | Now correctly points to `/en` in all 3 locations |
| `/catalog` in sitemap | - | ✅ Fixed Sept 7, 2026 | Removed from sitemap static paths |

---

## 🧪 How to Validate SEO Changes Before Deploying

1. **JSON-LD Schema**: Paste page URL into [Google Rich Results Test](https://search.google.com/test/rich-results)
2. **Structured Data**: Use [Schema.org Validator](https://validator.schema.org/)
3. **OG Tags**: Use [Facebook Sharing Debugger](https://developers.facebook.com/tools/debug/)
4. **hreflang Check**: `curl -s http://127.0.0.1:8000/ar/ | grep -E 'hreflang'` (should show /ar/, /fr/, /en/ paths)
5. **Canonical Check**: `curl -s http://127.0.0.1:8000/fr/ | grep 'canonical'` (should show `/fr/` path)
6. **JSON-LD Check**: `curl -s http://127.0.0.1:8000/ar/ | grep -c '@@context'` (should return 0)
7. **Sitemap Check**: `curl -s http://127.0.0.1:8000/sitemap.xml | grep -c 'xhtml:link'` (should be > 0)
8. **Feed Test**: `curl -s https://zintoop.com/google-merchant-feed.xml | grep -c "<item>"` (count products)

---

## 🔐 URL Obfuscation (Hashids) — Added September 7, 2026

Public profile and listing URLs use **reversible Base62 Hashids** instead of raw numeric IDs:

| Before | After |
|--------|-------|
| `/ar/listings/42` | `/ar/listings/k9XqL` |
| `/ar/user/12` | `/ar/user/Vm8Qw` |

### Rules:
- **Salt**: Use `OBFUSCATOR_SALT` env variable — NEVER `APP_KEY` (changing `APP_KEY` would break all shared links)
- **Backward compat**: Legacy numeric URLs (`/ar/listings/42`) still work via `resolveRouteBinding()` — returns HTTP 200
- **Invalid hash**: Returns HTTP 404 (not 500)
- **Sitemap**: Uses Hashids in `<loc>` — not raw IDs
- **Emails**: Use `route('listings.show', $listing)` — NOT `route('listings.show', $listing->id)`

### Implementation files:
- `app/Helpers/Obfuscator.php` — encode/decode logic
- `app/Models/User.php` — `getRouteKey()` + `resolveRouteBinding()`
- `app/Models/Listing.php` — `getRouteKey()` + `resolveRouteBinding()`

---

## 🤖 robots.txt Rules

**File**: `public/robots.txt`

Google is allowed to crawl public pages but blocked from private/admin pages:

```
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /api/
Disallow: /dashboard
Disallow: /profile
Disallow: /messages/
Disallow: /notifications/
Disallow: /email-preview/

Sitemap: https://zintoop.com/sitemap.xml
```

> [!IMPORTANT]
> Never add `Disallow: /ar/` or `Disallow: /en/` — Google needs to crawl all locale versions.

---

## 📁 Key SEO Files Reference

| File | Purpose |
|------|---------|
| `routes/web.php` | Locale-prefixed route groups, catch-all 301 redirects, Accept-Language root detection |
| `SetLocale.php` | Reads `{locale}` from route param, sets `URL::defaults` |
| `AppServiceProvider.php` | Default locale for `route()` helper in CLI/queues |
| `layouts/app.blade.php` | Canonical URL, hreflang (x-default→en), Organization schema, language switchers |
| `layouts/guest.blade.php` | Canonical URL, hreflang locale-prefixed (NOT query-param) |
| `listings/show.blade.php` | Product JSON-LD schema, OG tags |
| `home_marketplace.blade.php` | ItemList Product schema |
| `public/sitemap.blade.php` | XML sitemap with hreflang alternates per language (x-default→en) |
| `public/google_merchant_feed.blade.php` | Google Shopping feed |
| `GoogleMerchantFeedController.php` | Feed query logic (oil-only, with photos) |
| `PublicController.php` | Sitemap generation |
| `app/Helpers/Obfuscator.php` | URL Hashid encode/decode |
| `public/robots.txt` | Crawler access rules |

---

*Last updated: September 7, 2026*
