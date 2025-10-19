## CI/CD

GitHub Actions workflow runs on every push and PR to `main`:
- composer audit
- pint
- phpstan
- phpunit

Build fails on any lint or test error.

Workflow file: `.github/workflows/ci.yml`

---
## Localization & Currency

- Language files: `resources/lang/ar.json`, `fr.json`, `en.json`
- Use Laravel's `__()` helper for translations.
- Currency conversion: use `App\Services\CurrencyConverter`.

Example:
```php
app(\App\Services\CurrencyConverter::class)->convert(100, 'TND', 'USD');
```

---
# Tuni Olive Hub

**Purpose:**
تونسية زيت الزيتون منصة تجارة إلكترونية بين الشركات والمستهلكين (B2B/B2C)
Tunisian Olive Oil B2B/B2C Marketplace

**Roles:**
- مزارع (Farmer)
- ناقل (Carrier)
- مصدر (Exporter)
- مدير (Admin)
- مشتري (Buyer)

**Core Flows:**
- Listings: Create, update, and browse olive oil products.
- Orders: Place, track, and manage orders between buyers and sellers.
- Export Workflow: RFQ, contract, shipment, and document management for international trade.
- Messaging: Real-time chat and notifications for offers, contracts, and logistics.

**Setup Instructions:**
1. Clone the repository:
	```bash
	git clone <repo-url>
	cd tuni-olive-hub
	```
2. Install dependencies:
	```bash
	composer install
	npm install
	```
3. Copy and edit environment file:
	```bash
	cp .env.example .env
	# Edit DB, APP_URL, etc. as needed
	```
4. Generate app key:
	```bash
	php artisan key:generate
	```
5. Run migrations and seed demo data:
	```bash
	php artisan migrate --seed
	```
6. Start development server:
	```bash
	composer run dev
	```

**Seeding Guide:**
Default seeders create demo users for each role, sample products, listings, orders, and reviews. Customize `DatabaseSeeder.php` for advanced scenarios.

**API Route Examples:**
```http
GET    /api/v1/listings           # Browse listings
POST   /api/v1/listings           # Create listing (seller only)
GET    /api/v1/orders             # List orders (buyer/seller)
POST   /api/v1/orders             # Place order
POST   /api/v1/export/rfq         # Start export RFQ
POST   /api/v1/export/contract    # Create export contract
POST   /api/v1/export/contract/{id}/sign     # Sign contract
POST   /api/v1/export/contract/{id}/fund     # Fund contract
POST   /api/v1/export/contract/{id}/ship     # Mark contract as shipping
POST   /api/v1/export/contract/{id}/close    # Close contract
```

**Key Features:**
- Multi-role authentication and authorization
- Product catalog and advanced filtering
- Order lifecycle management
- Export contract and shipment workflow
- Real-time messaging and notifications
- Trust/reputation scoring
- Daily price ingestion and analytics
- Auditing and event broadcasting

**Comments:**
All code is documented bilingually (AR/EN) for clarity.

**Contact:**
For support or contributions, contact the admin or open an issue.

## Meilisearch Integration

To enable fast product and listing search:

1. Install Meilisearch server: https://docs.meilisearch.com/learn/getting_started/installation.html
2. Install PHP client:

```bash
composer require meilisearch/meilisearch-php
```

3. Set environment variables in `.env`:

```
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=
```

4. Index products and listings using `App\Services\Search\SearchService`.
