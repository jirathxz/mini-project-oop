<?php

require_once __DIR__ . '/../src/autoload.php';

use CardVault\Contracts\PaymentGatewayInterface;
use CardVault\Contracts\VoucherRepositoryInterface;
use CardVault\Contracts\OrderRepositoryInterface;
use CardVault\Contracts\CatalogRepositoryInterface;
use CardVault\Repositories\VoucherRepository;
use CardVault\Repositories\OrderRepository;
use CardVault\Repositories\CatalogRepository;
use CardVault\Services\Payment\PaymentGatewayFactory;
use CardVault\Services\Payment\PromptPayGateway;
use CardVault\Services\Payment\TrueMoneyGateway;
use CardVault\Models\Voucher;
use CardVault\Models\Order;

echo "=== Running OopArchitectureTest ===\n";

// 1. Test Encapsulation & Information Hiding
$voucher = new Voucher(1, 10, 'SECRET-PIN-CODE-1234', 'SN001', 'available');
assert($voucher->getId() === 1, "Getter encapsulation failed");
assert($voucher->getMaskedCode() === '••••-••••-••••-1234', "Voucher PIN masking encapsulation failed");
assert(!$voucher->isSold(), "available voucher isSold() should be false");

$soldVoucher = new Voucher(2, 10, 'REVEALED-PIN-5678', 'SN002', 'sold');
assert($soldVoucher->getMaskedCode() === 'REVEALED-PIN-5678', "Sold voucher should reveal exact PIN");
echo "[PASS] 1. Encapsulation: Models enforce information hiding and private state\n";

// 2. Test Abstraction (Interfaces & Contracts)
$voucherRepo = new VoucherRepository();
assert($voucherRepo instanceof VoucherRepositoryInterface, "VoucherRepository must implement VoucherRepositoryInterface");

$orderRepo = new OrderRepository();
assert($orderRepo instanceof OrderRepositoryInterface, "OrderRepository must implement OrderRepositoryInterface");

$catalogRepo = new CatalogRepository();
assert($catalogRepo instanceof CatalogRepositoryInterface, "CatalogRepository must implement CatalogRepositoryInterface");
echo "[PASS] 2. Abstraction: All repositories implement clear decoupled interfaces\n";

// 3. Test Polymorphism (Interchangeable Strategy via Interface)
$promptPay = PaymentGatewayFactory::create('promptpay');
assert($promptPay instanceof PaymentGatewayInterface, "PromptPayGateway must implement PaymentGatewayInterface");
assert($promptPay instanceof PromptPayGateway, "Factory should return PromptPayGateway");
$ppPayload = $promptPay->generatePaymentPayload('CV-TEST01', 150.00);
assert(isset($ppPayload['qr_payload']), "PromptPay must generate QR payload");

$trueMoney = PaymentGatewayFactory::create('truemoney');
assert($trueMoney instanceof PaymentGatewayInterface, "TrueMoneyGateway must implement PaymentGatewayInterface");
assert($trueMoney instanceof TrueMoneyGateway, "Factory should return TrueMoneyGateway");
$tmPayload = $trueMoney->generatePaymentPayload('CV-TEST01', 150.00);
assert(isset($tmPayload['barcode']), "TrueMoney must generate barcode payload");

// Polymorphic invocation loop
$gateways = [$promptPay, $trueMoney];
foreach ($gateways as $gw) {
    $verification = $gw->verifyPayment('CV-POLY', 200.00);
    assert($verification['success'] === true, "Polymorphic payment verification must succeed");
    assert(isset($verification['transaction_id']), "Polymorphic transaction ID must be present");
}
echo "[PASS] 3. Polymorphism: Payment gateways are interchangeable strategies under PaymentGatewayInterface\n";

// 4. Test Factory Pattern and Open-Closed Principle
$available = PaymentGatewayFactory::getAvailableGateways();
assert(count($available) >= 2, "PaymentGatewayFactory must list registered gateways");
try {
    PaymentGatewayFactory::create('unsupported_gateway');
    assert(false, "Should throw exception for unknown gateway");
} catch (\InvalidArgumentException $e) {
    echo "[PASS] 4. Factory Pattern: PaymentGatewayFactory validates supported gateways\n";
}

echo "=== OopArchitectureTest: ALL ASSERTIONS PASSED ===\n";
