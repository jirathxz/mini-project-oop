# 02: G2A Catalog Browsing & Filter API + Storefront Grid

**What to build:** A G2A-style responsive storefront grid and catalog API that allows guest buyers to filter products by Category (Gift Cards, Streaming, Gaming) and Platform (Google Play, Apple, Netflix, Steam). Each item card displays a Region indicator badge (`TH` / `GLOBAL`), an "Instant Digital Key" badge, real-time in-stock availability count, price, and actionable buttons ("Buy Now" and "Add to Cart").

**Blocked by:** 01: Zero-Config Dual Database & Catalog Data Bootstrap

**Status:** resolved

- [x] Domain models and Repository (`CategoryRepository`, `PlatformRepository`, `DenominationRepository`) provide structured catalog data with stock counts.
- [x] Endpoint `api/products.php` returns categorized catalog JSON with region, platform, denominations, and live stock.
- [x] Storefront UI displays G2A dark-theme styling, category navigation tabs, search/platform filters, and responsive product cards with clear badges.
- [x] Integration test verifies catalog retrieval, category filtering, and accurate stock counts.
