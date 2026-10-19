<?php

require_once __DIR__ . '/../src/autoload.php';

use CardVault\Services\CheckoutService;
use CardVault\Services\PaymentService;
use CardVault\Config\Database;

echo "=== Running PaymentTest ===\n";

$checkoutService = new CheckoutService();
$paymentService = new PaymentService();
$db = Database::getConnection();

// Reset denomination 2 vouchers to available for test isolation
$db->exec("UPDATE vouchers SET status = 'available', order_id = NULL, reserved_until = NULL WHERE denomination_id = 2");

// 1. Create a fresh order
$orderData = $checkoutService->createOrder([
    ['denomination_id' => 2, 'quantity' => 1] // Google Play 300 THB
], 'payment.test@example.com');

$orderRef = $orderData['order_ref'];
echo "[PASS] Order created for payment testing: {$orderRef}\n";

// 2. Before payment: fulfillment must NOT reveal unmasked PINs
$prePayment = $paymentService->getOrderFulfillment($orderRef);
assert($prePayment['order']['status'] === 'pending', "Status before payment must be pending");
foreach ($prePayment['vouchers'] as $voucher) {
    assert(strpos($voucher['code'], '•') !== false, "Voucher code must be masked before payment");
}
echo "[PASS] Unpaid order codes are strictly masked with bullets (••••)\n";

// 3. Confirm payment
$confirmed = $paymentService->confirmPayment($orderRef);
assert($confirmed['status'] === 'completed', "Order status must transition to completed");
assert(!empty($confirmed['paid_at']), "paid_at timestamp must be recorded");
echo "[PASS] Payment confirmed and status transitioned to 'completed'\n";

// 4. After payment: fulfillment reveals true unmasked PINs
$postPayment = $paymentService->getOrderFulfillment($orderRef);
assert($postPayment['order']['status'] === 'completed', "Status must be completed");
assert(count($postPayment['vouchers']) === 1, "Must return 1 voucher");
$paidVoucher = $postPayment['vouchers'][0];
assert(strpos($paidVoucher['code'], '•') === false, "Voucher code must be unmasked after payment, got: {$paidVoucher['code']}");
assert($paidVoucher['status'] === 'sold', "Voucher status in DB must be sold");
echo "[PASS] Paid voucher successfully unmasked: {$paidVoucher['code']} (Status: {$paidVoucher['status']})\n";

// 5. Idempotency: Confirming already paid order returns success without duplicating
$reconfirm = $paymentService->confirmPayment($orderRef);
assert($reconfirm['status'] === 'completed', "Re-confirmation should remain completed");
echo "[PASS] Idempotent payment re-confirmation handled gracefully\n";

echo "=== PaymentTest: ALL ASSERTIONS PASSED ===\n";
