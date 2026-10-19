# 03: Instant Buy & Cart Drawer with 15-Minute Voucher Reservation

**What to build:** Dual checkout pathways where a guest buyer can either click "Buy Now" on a specific card to jump straight into direct checkout, or click "Add to Cart" to open an animated sliding Cart Drawer allowing multi-item purchases (1–10 units per item). Upon initiating checkout with only a valid email address, the system reserves matching available vouchers (`available` → `reserved`), binds them with an order record, and starts a 15-minute reservation timer.

**Blocked by:** 02: G2A Catalog Browsing & Filter API + Storefront Grid

**Status:** resolved

- [x] Cart Drawer UI component supports adding items, adjusting quantities (1–10 limit), removing items, and calculating running total.
- [x] Direct "Buy Now" button packages a single denomination and opens the checkout prompt immediately.
- [x] `CheckoutService` validates guest email, creates an order, locks matching available vouchers using row-level locking, and sets `reserved_until = NOW() + 15 minutes`.
- [x] Integration test verifies reservation integrity, concurrency protection, and quantity limits.
