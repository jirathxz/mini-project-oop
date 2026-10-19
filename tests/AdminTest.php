<?php

require_once __DIR__ . '/../src/autoload.php';

use CardVault\Config\Database;
use CardVault\Services\AdminService;

echo "=== Running AdminTest ===\n";

$db = Database::getConnection();
$adminService = new AdminService();

// 1. Test Admin Authentication
$invalidAdmin = $adminService->authenticate('admin', 'wrongpassword');
assert($invalidAdmin === null, "Invalid password should return null");
echo "[PASS] Admin authentication rejected invalid credentials\n";

$validAdmin = $adminService->authenticate('admin', 'admin1234');
assert($validAdmin !== null, "Valid password should return admin details");
assert($validAdmin['username'] === 'admin', "Username should match 'admin'");
echo "[PASS] Admin authenticated successfully as: {$validAdmin['name']}\n";

// 2. Test Dashboard Metrics
$metrics = $adminService->getDashboardMetrics();
assert(isset($metrics['revenue']['total_amount']), "Metrics must contain total revenue");
assert(isset($metrics['orders']['total']), "Metrics must contain total orders");
assert(isset($metrics['inventory']['total_available']), "Metrics must contain total available inventory");
assert(is_array($metrics['inventory']['low_stock_items']), "Metrics must contain low stock items array");
echo "[PASS] Dashboard metrics generated successfully (Available stock: {$metrics['inventory']['total_available']} items, Revenue: {$metrics['revenue']['total_amount']} THB)\n";

// 3. Test Bulk Voucher Injection
$targetDenomId = 1; // Google Play 100 THB
$stockBefore = $adminService->getStockOverview();
$denomBefore = array_values(array_filter($stockBefore, fn($i) => (int)$i['denomination_id'] === $targetDenomId))[0];
$countBefore = (int)$denomBefore['available_stock'];

$newPins = [
    'ADMIN-TEST-PIN-1111',
    'ADMIN-TEST-PIN-2222',
    'ADMIN-TEST-PIN-3333'
];
$importedCount = $adminService->bulkImportVouchers($targetDenomId, $newPins);
assert($importedCount === 3, "Expected 3 vouchers imported, got: {$importedCount}");

$stockAfter = $adminService->getStockOverview();
$denomAfter = array_values(array_filter($stockAfter, fn($i) => (int)$i['denomination_id'] === $targetDenomId))[0];
$countAfter = (int)$denomAfter['available_stock'];

assert($countAfter === $countBefore + 3, "Stock after bulk import should increase by 3 (from {$countBefore} to {$countAfter})");
echo "[PASS] Bulk voucher injection verified (+{$importedCount} vouchers into {$denomAfter['denomination_name']})\n";

// 4. Test Orders and Order Details Retrieval
$orders = $adminService->getOrders(10);
assert(is_array($orders), "getOrders should return array");
echo "[PASS] Admin orders list retrieved (" . count($orders) . " orders fetched)\n";

if (!empty($orders)) {
    $firstOrderRef = $orders[0]['order_ref'];
    $details = $adminService->getOrderDetails($firstOrderRef);
    assert($details !== null, "Order details should not be null for existing ref");
    assert(isset($details['order']['order_ref']), "Order details must contain order header");
    assert(isset($details['items']), "Order details must contain line items");
    assert(isset($details['vouchers']), "Order details must contain vouchers");
    echo "[PASS] Order audit details retrieved successfully for {$firstOrderRef}\n";
}

echo "=== AdminTest: ALL ASSERTIONS PASSED ===\n";
