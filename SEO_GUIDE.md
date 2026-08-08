# ZinToop SEO Guide — Rules, Fixes & Best Practices

> **⚠️ CRITICAL: Any agent or developer MUST read this document before making ANY changes to views, routes, schemas, or meta tags.**

---

## 🔒 Golden Rules (NEVER Break These)

1. **Never change page titles or meta descriptions** without explicit user approval
2. **Never modify or remove `hreflang` alternate tags** — they drive multilingual traffic
3. **Never add incomplete structured data** — if you add a schema field, complete ALL its required sub-fields
4. **Never change URL structures** — all existing URLs must remain functional
5. **The canonical URL is ALWAYS the clean base URL** (no `?lang=` parameter)
6. **Arabic is the default language** — the base URL always renders Arabic content
7. **Test JSON-LD with Google Rich Results Test** (https://search.google.com/test/rich-results) before deploying any schema changes
8. **Always deploy via `./deploy_hostinger.sh`** — never manually edit production files

---

## 🏗️ Architecture Overview

### Language System
- **Type**: Session-based translation (NOT separate URL paths)
- **Supported**: Arabic (`ar`), French (`fr`), English (`en`)
- **Default**: Arabic (`ar`)
- **How it works**:
  - User clicks language switch → hits `/lang/{locale}` → saves to session → redirects back
  - Middleware `SetLocale` also accepts `?lang=` query param and saves to session
  - All pages render at the **same URL** regardless of language
  - Translation files: `resources/lang/ar.json`, `resources/lang/fr.json`, `resources/lang/en.json`

### Canonical URL Strategy
```html
<!-- hreflang alternates tell Google about language versions -->
<link rel="alternate" hreflang="ar" href="https://zintoop.com/page?lang=ar">
<link rel="alternate" hreflang="fr" href="https://zintoop.com/page?lang=fr">
<link rel="alternate" hreflang="en" href="https://zintoop.com/page?lang=en">
<link rel="alternate" hreflang="x-default" href="https://zintoop.com/page">

<!-- Canonical is ALWAYS the clean URL (no query params) -->
<link rel="canonical" href="https://zintoop.com/page">
```

### Layout Files
| Layout | Used By | Location |
|--------|---------|----------|
| `app.blade.php` | All authenticated + public pages | `resources/views/layouts/app.blade.php` |
| `guest.blade.php` | Login, register pages | `resources/views/layouts/guest.blade.php` |

Both layouts contain: canonical URL, hreflang tags, Organization schema.

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

## 📄 Structured Data Reference

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

**Includes**:
- Static pages: `/`, `/register`, `/gulf-catalog`, `/olive-varieties`
- Dynamic listings: all active listings (`status = active`)
- Dynamic articles: all active articles
- Gulf products: premium + export-ready products

> **Note**: The sitemap includes ALL active listings. If a listing is deleted/deactivated, it will be removed from the sitemap automatically. Google will take 2-4 weeks to de-index old 404 URLs.

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

## ⚠️ Known Issues (Self-Resolving)

| Issue | Count | Resolution |
|-------|-------|------------|
| "Introuvable (404)" | 36 | Deleted listings still in Google's index. Will drop naturally in 2-4 weeks. |
| "Page avec redirection" | 65 | Old URLs redirecting. Normal behavior, not harmful. |
| "Explorée, actuellement non indexée" | 8 | Google has crawled but not yet indexed. Will resolve as domain authority grows. |

---

## 🧪 How to Validate SEO Changes Before Deploying

1. **JSON-LD Schema**: Paste page URL into [Google Rich Results Test](https://search.google.com/test/rich-results)
2. **Structured Data**: Use [Schema.org Validator](https://validator.schema.org/)
3. **OG Tags**: Use [Facebook Sharing Debugger](https://developers.facebook.com/tools/debug/)
4. **Local Test**: `curl -s http://127.0.0.1:8000/listings/100 | grep -E 'canonical|hreflang|shippingRate|validFrom'`
5. **Feed Test**: `curl -s https://zintoop.com/google-merchant-feed.xml | grep -c "<item>"` (count products)

---

## 📁 Key SEO Files Reference

| File | Purpose |
|------|---------|
| `layouts/app.blade.php` | Canonical URL, hreflang, Organization schema |
| `layouts/guest.blade.php` | Canonical URL, hreflang (guest pages) |
| `listings/show.blade.php` | Product JSON-LD schema, OG tags |
| `home_marketplace.blade.php` | ItemList Product schema |
| `public/sitemap.blade.php` | XML sitemap template |
| `public/google_merchant_feed.blade.php` | Google Shopping feed |
| `GoogleMerchantFeedController.php` | Feed query logic (oil-only, with photos) |
| `PublicController.php` | Sitemap generation |
| `SetLocale.php` | Language middleware |

---

*Last updated: August 8, 2026*
