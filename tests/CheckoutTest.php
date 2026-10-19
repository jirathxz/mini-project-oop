<?php

require_once __DIR__ . '/../src/autoload.php';

use CardVault\Services\CheckoutService;
use CardVault\Config\Database;

echo "=== Running CheckoutTest ===\n";

$db = Database::getConnection();
// Reset denomination 1 vouchers to available for test isolation
$db->exec("UPDATE vouchers SET status = 'available', order_id = NULL, reserved_until = NULL WHERE denomination_id = 1");

$service = new CheckoutService();

// 1. Invalid email rejection
try {
    $service->createOrder([['denomination_id' => 1, 'quantity' => 1]], 'invalid-email');
    assert(false, "Should throw exception on invalid email");
} catch (\InvalidArgumentException $e) {
    echo "[PASS] Invalid email rejected: " . $e->getMessage() . "\n";
}

// 2. Quantity bounds (1-10)
try {
    $service->createOrder([['denomination_id' => 1, 'quantity' => 15]], 'valid@example.com');
    assert(false, "Should throw exception when quantity > 10");
} catch (\InvalidArgumentException $e) {
    echo "[PASS] Excessive quantity (>10) rejected: " . $e->getMessage() . "\n";
}

// 3. Successful voucher reservation & order creation
$email = 'guest@test.com';
$order = $service->createOrder([
    ['denomination_id' => 1, 'quantity' => 2] // 2 x 100 THB = 200 THB
], $email);

assert(!empty($order['order_ref']), "Order must have an order_ref");
assert((float)$order['total_amount'] === 200.00, "Total amount should be 200.00, got: " . $order['total_amount']);
assert($order['status'] === 'pending', "Order status must be pending");
echo "[PASS] Order created successfully: {$order['order_ref']} (Total: {$order['total_amount']} THB)\n";

// 4. Verify vouchers are locked in DB (status = 'reserved' with order_id)
$stmt = $db->prepare("SELECT COUNT(*) as cnt FROM vouchers WHERE order_id = ? AND status = 'reserved'");
$stmt->execute([$order['order_id']]);
$reservedCount = (int)$stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
assert($reservedCount === 2, "Expected 2 vouchers reserved in database, found: {$reservedCount}");
echo "[PASS] Database confirmed 2 vouchers locked in status 'reserved'\n";

// 5. Out of stock rejection when requesting more than available
try {
    // Temporarily exhaust available stock for denomination 1 to guarantee out-of-stock scenario
    $db->exec("UPDATE vouchers SET status = 'reserved' WHERE denomination_id = 1 AND status = 'available' AND order_id IS NULL");
    try {
        $service->createOrder([['denomination_id' => 1, 'quantity' => 1]], 'another@test.com');
        assert(false, "Should throw exception when stock is insufficient");
    } finally {
        $db->exec("UPDATE vouchers SET status = 'available' WHERE denomination_id = 1 AND status = 'reserved' AND order_id IS NULL");
    }
} catch (\RuntimeException $e) {
    echo "[PASS] Insufficient stock rejected: " . $e->getMessage() . "\n";
}

echo "=== CheckoutTest: ALL ASSERTIONS PASSED ===\n";
