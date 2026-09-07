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

CARDVAULT is a digital key marketplace in the CDKeys mold, scoped to Thai gift
cards: a guest picks a denomination, pays, and gets a working PIN instantly on
screen and by email. Success means code-in-hand in under a minute with no
signup, and every code re-viewable later by its buyer.

## Positioning

A CDKeys-style instant-delivery key shop for three Thai gift-card brands, with
guest-only checkout and PromptPay-first payment. Neighboring top-up sites sell
the same cards through accounts, carts, and manual delivery; this one sells
speed and proof: no login, QR payment, PIN revealed immediately with a receipt.

## Operating Context

- Guest purchase flow: pick denomination → email → PromptPay QR (or credit
  card) → PIN revealed on screen, mirrored to email and the OTP inbox.
- Payment window is 15 minutes per order; expiry blocks confirmation and the
  buyer must reorder.
- Post-purchase proof: order-ref + email dual-key tracking page, plus an
  email-OTP inbox holding all paid codes for that address.
- Runs on XAMPP-style MySQL in production-like setups, with SQLite and
  file-simulation fallbacks so the flow demos with zero config.
- Thai-language storefront; THB only.

## Capabilities and Constraints

- Confirmed: 11 denominations across Google Play, Apple, Netflix; cart and
  buy-now; voucher reservation with row locks (`available` → `reserved` →
  `sold`); masked PINs until paid; per-brand redemption guides; order tracking;
  OTP inbox sessions.
- Quantity is 1–10 units per order line (enforced in `CheckoutService`).
- Payment confirmation is currently simulated (buyer self-confirms after
  scanning); a real payment gateway integration is explicitly undecided.
- Scope is fixed at three brands; no new brands planned.
- No real voucher supplier is connected; seed and demo codes are test data.

## Brand Commitments

- Name: CARDVAULT. Thai-language UI with a plain-spoken counter voice
  ("จ่ายปุ๊บ ได้รหัสปั๊บ", action-naming buttons, errors that say how to fix).
- Photo credits ship with the product and must survive redesigns: Google Play
  card photo by joho345 (CC BY-SA 3.0), Apple/iTunes card photo by KKPCW
  (CC BY-SA 4.0), Netflix "N" public-domain mark — see the cover-art comment in
  `public/assets/js/app.js`.

## Evidence on Hand

- Working storefront and API in this repo (`public/`, `api/`, `src/`).
- Test voucher codes in `database/seed.sql` and the SQLite bootstrapper in
  `config/Database.php` — demo data, not real inventory.
- Cover photos in `public/assets/img/` (licensed as above).
- Design rationale in `OOP-OOAD.md`.
- Absent and must not be fabricated: real customer testimonials, real sales
  figures, a live payment gateway, supplier terms.

## Product Principles

1. No signup, ever — an account wall is a lost sale.
2. Code in hand in under a minute, or the sale failed.
3. Pay the Thai way: PromptPay first, cards second.
4. Proof with every order: receipt on screen, copy by email, codes re-viewable.
5. Three brands, done well — catalog sprawl is refused.
