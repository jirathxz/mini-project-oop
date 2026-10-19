# 05: Lazy Expiry Sweep & Dual-Key Order Tracking with OTP Inbox

**What to build:** An automatic lazy sweep mechanism that releases expired reservations (> 15 minutes) back to `available` state upon catalog/checkout requests, paired with a post-purchase proof system: a dual-key tracking page (`Order Ref + Email`) to look up receipts, and an email-based OTP Inbox allowing guest buyers to enter a temporary 6-digit OTP code to view all historically purchased vouchers for their email.

**Blocked by:** 04: PromptPay QR Simulated Payment & Instant PIN Fulfillment

**Status:** resolved

- [x] Lazy sweep routine executes an atomic SQL query resetting expired `reserved` vouchers whose `reserved_until` has lapsed.
- [x] Tracking endpoint and UI look up order details and unmasked PINs using `Order Ref` + matching guest `Email`.
- [x] `OtpService` generates a 6-digit verification code, validates the code, and creates an authenticated session granting access to all past orders for that email.
- [x] Integration tests verify: (a) expired vouchers return to available pool, (b) dual-key security rejects mismatched emails, (c) OTP flow retrieves full buyer history.
