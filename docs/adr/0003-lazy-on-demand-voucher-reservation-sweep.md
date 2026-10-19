# 3. Lazy On-Demand Voucher Reservation Sweep

Release expired voucher reservations back to `available` state during incoming web requests rather than using a background scheduler.

## Context

When orders are created, vouchers transition from `available` to `reserved` with a 15-minute expiration timestamp. In a conventional production system, an asynchronous cron or message queue worker releases expired reservations. Running a background daemon complicates evaluation and portable PHP hosting environments.

## Decision

We execute a lightweight SQL sweep (`UPDATE vouchers SET status = 'available', order_id = NULL WHERE status = 'reserved' AND reserved_until < NOW()`) during request lifecycle events (specifically catalog stock checks and checkout initialization).
