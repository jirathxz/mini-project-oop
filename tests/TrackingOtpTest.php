<?php

require_once __DIR__ . '/../src/autoload.php';

use CardVault\Config\Database;
use CardVault\Repositories\VoucherRepository;
use CardVault\Services\CheckoutService;
use CardVault\Services\PaymentService;
use CardVault\Services\OrderTrackingService;
use CardVault\Services\OtpService;

echo "=== Running TrackingOtpTest ===\n";

$db = Database::getConnection();
// Reset test vouchers for test isolation
$db->exec("UPDATE vouchers SET status = 'available', order_id = NULL, reserved_until = NULL WHERE denomination_id = 3");

$voucherRepo = new VoucherRepository($db);
$checkoutService = new CheckoutService();
$paymentService = new PaymentService();
$trackingService = new OrderTrackingService();
$otpService = new OtpService();

// 1. Test Lazy Sweep: expired reserved voucher is reclaimed to 'available'
// Create an expired reservation manually
$db->exec("UPDATE vouchers SET status = 'reserved', reserved_until = '2020-01-01 00:00:00', order_id = 999 WHERE id = 1");
$reclaimed = $voucherRepo->releaseExpiredVouchers();
assert($reclaimed >= 1, "Expected at least 1 expired voucher to be reclaimed");

$stmt = $db->query("SELECT status, order_id, reserved_until FROM vouchers WHERE id = 1");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
assert($row['status'] === 'available', "Voucher status must be reset to 'available'");
assert($row['order_id'] === null, "Voucher order_id must be cleared");
echo "[PASS] Lazy Sweep: Expired reservations successfully reclaimed to 'available'\n";

// 2. Test Dual-Key Tracking (Order Ref + Email)
$buyerEmail = 'dual.key@example.com';
$order = $checkoutService->createOrder([
    ['denomination_id' => 3, 'quantity' => 1] // Google Play 500 THB
], $buyerEmail);
$orderRef = $order['order_ref'];
$paymentService->confirmPayment($orderRef);

// A. Mismatched email should be rejected
try {
    $trackingService->trackOrder($orderRef, 'wrong.email@example.com');
    assert(false, "Should reject tracking with wrong email");
} catch (\InvalidArgumentException $e) {
    echo "[PASS] Dual-Key Security: Rejected mismatched email tracking\n";
}

// B. Matching email should succeed and return unmasked PIN
$tracked = $trackingService->trackOrder($orderRef, $buyerEmail);
assert($tracked['order']['order_ref'] === $orderRef, "Tracked order ref must match");
assert(count($tracked['vouchers']) === 1, "Must return 1 voucher");
assert(strpos($tracked['vouchers'][0]['code'], '•') === false, "Paid tracked code must be unmasked");
echo "[PASS] Dual-Key Security: Verified order lookup with matching email\n";

// 3. Test OTP Inbox
// A. Request OTP
$otpData = $otpService->requestOtp($buyerEmail);
assert(strlen($otpData['otp_code']) === 6, "OTP code must be 6 digits");
echo "[PASS] OTP Generated for {$buyerEmail}: {$otpData['otp_code']}\n";

// B. Verify wrong OTP code
$invalidVerify = $otpService->verifyOtp($buyerEmail, '000000');
assert($invalidVerify === false, "Incorrect OTP should return false");
echo "[PASS] Invalid OTP correctly rejected\n";

// C. Verify correct OTP code
$validVerify = $otpService->verifyOtp($buyerEmail, $otpData['otp_code']);
assert($validVerify === true, "Valid OTP should return true");
echo "[PASS] Valid OTP verified successfully\n";

// D. Retrieve buyer order history
$history = $otpService->getBuyerOrderHistory($buyerEmail);
assert(count($history) >= 1, "Buyer must have at least 1 historical order");
assert($history[0]['order_ref'] === $orderRef, "History must contain the paid order");
assert(count($history[0]['vouchers']) === 1, "History order must contain the unmasked voucher");
echo "[PASS] OTP Inbox: Retrieved all purchased vouchers for verified guest buyer\n";

echo "=== TrackingOtpTest: ALL ASSERTIONS PASSED ===\n";
