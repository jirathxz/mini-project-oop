# 2. Dual Database with SQLite Fallback

Auto-detect MySQL connection and fall back to a local SQLite database (`database/cardvault.sqlite`) with automatic schema migration and seed data.

## Context

The application is deployed primarily on XAMPP with MySQL for evaluation, but developers and evaluators may run the code in zero-config environments without a running MySQL daemon or pre-created databases.

## Decision

The `Database` singleton probes MySQL PDO connection with a short timeout. If connection fails or MySQL is unreachable, it seamlessly switches to SQLite PDO pointing to `database/cardvault.sqlite`, auto-executing initialization DDL and seed fixtures on first boot. All repository queries must strictly adhere to SQL constructs supported by both MySQL and SQLite.
