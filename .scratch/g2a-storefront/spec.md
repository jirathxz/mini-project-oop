# Spec: G2A-Style Digital Key & Gift Card Storefront (CARDVAULT)

Status: ready-for-agent

## Problem Statement

Thai gamers and digital entertainment consumers who lack international credit cards struggle to purchase digital gift cards and game top-up keys quickly and reliably. Existing local options force users through tedious account registration, slow manual verification, and fragmented payment methods, resulting in friction, cart abandonment, and prolonged waiting times for working codes.

## Solution

CARDVAULT provides a G2A-inspired instant-delivery digital key storefront tailored for Thai buyers. Customers browse a structured catalog with clear platform, category, and regional badges (`TH` / `GLOBAL`), select denominations, and purchase either directly via "Instant Buy" or using a sliding Cart Drawer. Guest-only checkout requires only an email address, payments are made through PromptPay QR (or simulated card) within a 15-minute payment window, and working unmasked voucher PINs are displayed immediately upon payment confirmation, backed by email copies and a self-service OTP inbox.

## User Stories

1. As a Guest Buyer, I want to browse products grouped by Category (Gift Cards, Streaming, Gaming), so that I can quickly find the digital services I use.
2. As a Guest Buyer, I want to filter or view items by Platform (Google Play, Apple, Netflix, Steam), so that I only see denominations relevant to my desired service.
3. As a Guest Buyer, I want to see clear Region badges (`TH` vs `GLOBAL`) on every product card, so that I don't mistakenly buy a voucher incompatible with my account region.
4. As a Guest Buyer, I want to see an "Instant Key Delivery" badge and real-time stock indicator on each denomination, so that I have confidence the code will be delivered immediately.
5. As a Guest Buyer, I want to click "Buy Now" on a specific denomination, so that I can bypass the cart and proceed directly to payment in one step.
6. As a Guest Buyer, I want to click "Add to Cart" to open a sliding Cart Drawer, so that I can combine multiple denominations and adjust quantities between 1 and 10 units before checking out.
7. As a Guest Buyer, I want to enter only my email address during checkout without creating a password or user account, so that I can complete my purchase in under one minute.
8. As a Guest Buyer, I want to see an exact PromptPay QR code with payment amount and a countdown timer for the 15-minute payment window, so that I know exactly how and when to complete payment.
9. As a Guest Buyer, I want to simulate payment confirmation or upload a payment slip, so that I can immediately verify my transaction without waiting for external gateway delays.
10. As a Guest Buyer, I want to see my unmasked voucher PIN code and redemption instructions on screen immediately upon payment confirmation, so that I can copy and redeem it without delay.
11. As a Guest Buyer, I want to receive an email receipt containing my Order Ref and voucher details, so that I retain persistent proof of purchase.
12. As a Guest Buyer, I want to track my order status anytime using my Order Ref and email address on a dual-key tracking page, so that I can retrieve my codes even if I accidentally close my browser tab.
13. As a Guest Buyer, I want to request an OTP code sent to my email to unlock my OTP Inbox, so that I can view all past paid voucher codes associated with my email address in one place.
14. As a Store Administrator/Evaluator, I want the system to automatically detect MySQL and fall back to SQLite with seeded demo data if MySQL is absent, so that the application runs zero-config on any machine.
15. As a Store Administrator, I want expired order reservations (older than 15 minutes) to be lazily released back to inventory when new requests arrive, so that reserved stock does not remain locked indefinitely without requiring external background daemons.

## Implementation Decisions

### 1. Catalog & Storefront Taxonomy (ADR-0001)
- The storefront adopts G2A's taxonomy: `Category` (e.g. Gift Cards, Streaming, Gaming) → `Platform` (e.g. Google Play, Apple, Netflix) → `Denomination` (e.g. 300 THB, 500 THB).
- Each denomination is tagged with:
  - `Region`: `TH` (Thailand) or `GLOBAL`.
  - `Delivery Type`: Instant Digital Key.
  - `Stock Status`: Real-time availability indicator (`In Stock` / `Out of Stock`).
- Single-merchant inventory model: all vouchers are sourced directly from internal inventory tables; no multi-vendor seller accounts or payouts are introduced.

### 2. Purchase Pathways: Instant Buy vs Cart Drawer
- **Instant Buy Pathway**: Clicking "Buy Now" skips the cart, creates an instant checkout payload for the single selected denomination, and routes straight to checkout/QR modal.
- **Cart Drawer Pathway**: Clicking "Add to Cart" adds items to an in-memory / session Cart Drawer that slides from the right, allowing multi-line item checkout with quantity controls (1–10 per line item).

### 3. Inventory Reservation & Lazy Expiry Sweep (ADR-0003)
- Vouchers maintain a strict state lifecycle: `available` → `reserved` → `sold`.
- Checkout locks available vouchers using atomic updates and sets `reserved_until = NOW() + 15 minutes`.
- PINs remain masked/hashed until status reaches `sold`.
- Any access to catalog stock or checkout initiation executes a lazy SQL sweep:
  `UPDATE vouchers SET status = 'available', order_id = NULL WHERE status = 'reserved' AND reserved_until < NOW()`

### 4. Dual Database with Auto-Fallback (ADR-0002)
- The Database layer probes MySQL PDO. If connection fails or credentials do not resolve, it switches to SQLite PDO pointing to `database/cardvault.sqlite`.
- Auto-initialization executes DDL migrations and seed fixtures on first boot if tables do not exist.
- All SQL queries use portable ANSI-SQL compatible with both MySQL 8 and SQLite 3.

### 5. Fulfillment & OTP Inbox
- Order confirmation transitions reserved vouchers to `sold`, assigns the order payment timestamp, and reveals decrypted PINs.
- Guest buyers can access an "OTP Inbox" by entering their email; a 6-digit OTP code is generated (simulated on-screen / logged) and grants a temporary session to view all historical orders and voucher codes for that email.

## Testing Decisions

### Testing Seam
- Tests will exercise the application at the **Service / API Integration Seam** (the highest internal seam before the UI rendering layer).
- Test suite verifies external contracts and behavior rather than private implementation details:
  1. `CatalogServiceTest`: Verifies category filtering, platform grouping, regional tagging, and stock count calculation.
  2. `CheckoutServiceTest`: Verifies voucher reservation, row-locking behavior, quantity boundary enforcement (1–10), and order expiration timer calculation.
  3. `PaymentFulfillmentTest`: Verifies order payment confirmation, status transition from `reserved` to `sold`, PIN unmasking, and receipt generation.
  4. `LazySweepTest`: Verifies that reservations older than 15 minutes are reset to `available` upon stock query.
  5. `OtpServiceTest`: Verifies OTP generation, session validation, and order history retrieval for guest emails.

## Out of Scope

- Multi-vendor third-party marketplace features (seller accounts, commissions, seller payouts).
- External production payment gateway integrations (2C2P, Omise, Stripe); payment confirmation is simulated for local evaluation.
- Physical card delivery or shipping logistics.
- Permanent user registration, passwords, or customer login portals (guest-only checkout with OTP inbox).

## Further Notes

- Design and typography adhere to modern G2A dark-mode aesthetics (dark graphite background, vibrant amber/orange primary accents, crisp regional badges).
- Built with clean PHP Object-Oriented Architecture (Domain Models, Repositories, Service layer, Factory patterns, Strategy pattern for payment methods).
