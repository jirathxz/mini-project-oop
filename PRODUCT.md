# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Primary: Thai buyers without credit cards who need a working digital code within
minutes. They pay with bank apps (PromptPay QR), will not create accounts, and
judge the store on one thing: the code arrives and works. Includes gamers
topping up Google Play, Apple users buying credit or iCloud+, and viewers
paying Netflix without a card on file.

## Product Purpose

CARDVAULT is a digital key & gift card storefront structured in the G2A mold,
scoped to Thai digital entertainment: a guest buyer browses by category and
platform, selects a denomination, and completes checkout via Instant Buy or the
Cart Drawer. Payment via PromptPay QR yields a working PIN instantly on screen,
mirrored to email, and re-accessible via the OTP inbox.

## Positioning

A G2A-style instant-delivery digital key storefront tailored for Thai buyers,
with guest-only checkout and PromptPay-first payment. Unlike multi-vendor
marketplaces that require seller management or accounts, CARDVAULT pairs G2A's
browsing taxonomy (Category → Platform → Denomination, Region tags, Instant Key
badges) with single-merchant direct fulfillment for speed and certainty.

## Operating Context

- Browsing & Selection: G2A-inspired catalog with Category filtering (Gift Cards,
  Streaming, Gaming), Platform badges, Region indicators (`TH` / `GLOBAL`), and
  Instant Digital Delivery badges.
- Guest purchase flow: pick denomination → email → Instant Buy (direct) or Cart
  Drawer → PromptPay QR (or simulated card) → PIN revealed on screen, mirrored
  to email and the OTP inbox.
- Payment window is 15 minutes per order; expired reservations are swept lazily
  on incoming requests.
- Post-purchase proof: order-ref + email dual-key tracking page, plus an
  email-OTP inbox holding all paid codes for that address.
- Runs on XAMPP-style MySQL in production-like setups, with SQLite auto-fallback
  (`database/cardvault.sqlite`) and auto-seed so the flow demos with zero config.
- Thai-language storefront; THB only.

## Capabilities and Constraints

- Catalog Taxonomy: 3-tier hierarchy (`Category` → `Platform` → `Denomination`).
- Confirmed Denominations: 11 denominations across Google Play, Apple, Netflix;
  Cart Drawer and Instant Buy; voucher reservation with row locks (`available` →
  `reserved` → `sold`); masked PINs until paid; redemption guides; order tracking;
  OTP inbox sessions.
- Quantity: 1–10 units per order line (enforced in `CheckoutService`).
- Payment confirmation is simulated (buyer confirms after scanning PromptPay QR);
  zero-dependency instant demo.
- Inventory: Single-merchant curated inventory; seed and demo codes are test data.

## Brand Commitments

- Name: CARDVAULT. Thai-language UI with a plain-spoken counter voice
  ("จ่ายปุ๊บ ได้รหัสปั๊บ", action-naming buttons, errors that say how to fix).
- Photo credits ship with the product and must survive redesigns: Google Play
  card photo by joho345 (CC BY-SA 3.0), Apple/iTunes card photo by KKPCW
  (CC BY-SA 4.0), Netflix "N" public-domain mark — see the cover-art comment in
  `public/assets/js/app.js`.

## Evidence on Hand

- Documentation: [PRODUCT.md](file:///d:/_Work/mini-project-oop/PRODUCT.md), [GLOSSARY.md](file:///d:/_Work/mini-project-oop/GLOSSARY.md), and [ADRs](file:///d:/_Work/mini-project-oop/docs/adr/).
- Zero-config bootstrap: SQLite fallback in `database/cardvault.sqlite`.
- Absent and must not be fabricated: real customer testimonials, real sales
  figures, a live payment gateway, supplier terms.

## Product Principles

1. No signup, ever — an account wall is a lost sale.
2. Code in hand in under a minute, or the sale failed.
3. Pay the Thai way: PromptPay first, cards second.
4. G2A clarity: Clear region tags, instant delivery badges, and instant buy pathways.
5. Three platforms, done well — catalog sprawl is refused.
