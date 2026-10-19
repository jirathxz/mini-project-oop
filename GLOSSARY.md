# CARDVAULT

Digital key & gift card storefront inspired by G2A, designed for Thai users paying via PromptPay with instant code delivery.

## Language

### Catalog & Products

**Category**:
Top-level group for digital goods (e.g., Gift Cards, Streaming Subscriptions, Game Top-ups).
_Avoid_: Department, section

**Platform**:
The target ecosystem, brand, or service that issues the digital code (e.g., Google Play, Apple, Netflix, Steam).
_Avoid_: Brand, vendor, publisher

**Denomination**:
A specific face value or tier offered for a platform (e.g., 300 THB, 500 THB, 1 Month).
_Avoid_: Variant, sku, tier

**Region**:
The geographic restriction applicable to a digital product (e.g., Thailand, Global).
_Avoid_: Country, locale, zone

**Voucher**:
An individual digital code or PIN in inventory with its lifecycle status (`available`, `reserved`, `sold`).
_Avoid_: Key, serial, card, coupon

### Checkout & Ordering

**Guest Buyer**:
An anonymous customer identified solely by their email address during purchase.
_Avoid_: User, member, account, client

**Instant Buy**:
A single-click checkout pathway bypassing the cart drawer to generate payment immediately.
_Avoid_: Fast checkout, 1-click buy, direct purchase

**Cart Drawer**:
A sliding sidebar holding multiple denominations and quantities before checkout.
_Avoid_: Shopping basket, bag

**Order**:
A purchase commitment for one or more vouchers, bound by a 15-minute payment window.
_Avoid_: Invoice, transaction, bill

**Payment Window**:
The 15-minute lifespan of an unpaid order during which vouchers remain reserved.
_Avoid_: Timeout, hold duration, session

### Fulfillment & Proof

**OTP Inbox**:
A temporary email-validated session allowing a guest buyer to view all past purchased vouchers for their email.
_Avoid_: Customer portal, dashboard, account page

**Order Ref**:
A unique uppercase alphanumeric token assigned to each order for dual-key tracking.
_Avoid_: Tracking number, order ID, invoice number
