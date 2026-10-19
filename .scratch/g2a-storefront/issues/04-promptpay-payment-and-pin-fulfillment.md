# 04: PromptPay QR Simulated Payment & Instant PIN Fulfillment

**What to build:** An instant fulfillment payment screen showing a PromptPay QR code with payment amount, active 15-minute countdown clock, and a one-click simulated payment confirmation. When payment is confirmed, the order transitions to paid, reserved vouchers change from `reserved` to `sold`, unmasked PIN codes are revealed on screen immediately with redemption instructions, and an order receipt with Order Ref is generated.

**Blocked by:** 03: Instant Buy & Cart Drawer with 15-Minute Voucher Reservation

**Status:** resolved

- [x] Checkout modal / page renders valid PromptPay QR layout, payment instructions, order summary, and live countdown timer.
- [x] `PaymentService` processes payment confirmation, transitions order status to `completed`, and marks vouchers as `sold`.
- [x] Post-payment fulfillment screen renders unmasked PINs with "Copy" button, step-by-step redemption guide per platform, and Order Ref.
- [x] Integration test verifies status transitions, PIN decryption/unmasking on paid order, and prevention of double-redemption.
