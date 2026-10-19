# 01: Zero-Config Dual Database & Catalog Data Bootstrap

**What to build:** An automatic database connection and bootstrap layer that detects MySQL (e.g. XAMPP) or smoothly falls back to SQLite (`database/cardvault.sqlite`). On first boot in an uninitialized environment, it provisions database schema tables (Categories, Platforms, Denominations, Vouchers, Orders, Order Items) and seeds sample data so the application runs out of the box with zero manual configuration.

**Blocked by:** None (can start immediately)

**Status:** resolved

- [x] `Database` connection class tries MySQL connection and falls back to SQLite `database/cardvault.sqlite` if unreachable.
- [x] SQL DDL migrations run automatically if database tables do not exist.
- [x] Seed data populates G2A-aligned Categories (Gift Cards, Streaming, Gaming), Platforms (Google Play, Apple, Netflix, Steam), Denominations, and demo Vouchers in `available` state.
- [x] A verification script validates connection, schema creation, and seeded inventory records.
