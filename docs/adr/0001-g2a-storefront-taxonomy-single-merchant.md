# 1. G2A Storefront Taxonomy on Single-Merchant Inventory

Adopt G2A's storefront taxonomy (Category → Platform → Denomination, Region badges, Instant Delivery badges, Cart Drawer, and Instant Buy) while maintaining a single-merchant inventory backend.

## Context

The product requires a familiar, gaming-marketplace feel similar to G2A. G2A operates as a multi-vendor peer-to-peer marketplace with third-party sellers and payout balances. Implementing full multi-vendor mechanics for a mini-project introduces massive complexity without advancing core course objectives.

## Decision

We separate the presentation model from the inventory ownership model:
1. Front-end and catalog structure follow G2A patterns: categorized platforms, region restriction tags (`TH` / `GLOBAL`), instant digital key delivery badges, and dual purchase pathways (Cart Drawer vs Instant Buy).
2. Backend inventory remains single-merchant with direct row-locked voucher fulfillment.
