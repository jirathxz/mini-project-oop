<?php

require_once __DIR__ . '/../src/autoload.php';

use CardVault\Config\Database;

echo "=== Running DatabaseTest ===\n";

// 1. Check connection returns PDO
$db = Database::getConnection();
assert($db instanceof PDO, "Expected Database::getConnection() to return a PDO instance");
echo "[PASS] Database returns valid PDO instance (Driver: " . $db->getAttribute(PDO::ATTR_DRIVER_NAME) . ")\n";

// 2. Check required tables exist
$expectedTables = ['categories', 'platforms', 'denominations', 'vouchers', 'orders', 'order_items'];
foreach ($expectedTables as $table) {
    $stmt = $db->query("SELECT 1 FROM {$table} LIMIT 1");
    assert($stmt !== false, "Table '{$table}' must exist in database");
}
echo "[PASS] All core database tables exist\n";

// 3. Check seeded categories
$stmt = $db->query("SELECT COUNT(*) as cnt FROM categories");
$catCount = (int) $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
assert($catCount >= 3, "Expected at least 3 categories, found: {$catCount}");
echo "[PASS] Categories seeded: {$catCount}\n";

// 4. Check seeded platforms
$stmt = $db->query("SELECT COUNT(*) as cnt FROM platforms");
$platCount = (int) $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
assert($platCount >= 4, "Expected at least 4 platforms, found: {$platCount}");
echo "[PASS] Platforms seeded: {$platCount}\n";

// 5. Check seeded denominations & available vouchers
$stmt = $db->query("SELECT COUNT(*) as cnt FROM vouchers WHERE status = 'available'");
$voucherCount = (int) $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
assert($voucherCount > 0, "Expected available vouchers in inventory, found: {$voucherCount}");
echo "[PASS] Available vouchers seeded: {$voucherCount}\n";

echo "=== DatabaseTest: ALL ASSERTIONS PASSED ===\n";
