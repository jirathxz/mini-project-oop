<?php

namespace CardVault\Services;

use CardVault\Config\Database;
use CardVault\Contracts\OrderRepositoryInterface;
use CardVault\Contracts\VoucherRepositoryInterface;
use CardVault\Repositories\OrderRepository;
use CardVault\Repositories\VoucherRepository;
use CardVault\Services\Payment\PaymentGatewayFactory;
use InvalidArgumentException;
use RuntimeException;
use PDO;

class PaymentService
{
    private PDO $db;
    private OrderRepositoryInterface $orderRepo;
    private VoucherRepositoryInterface $voucherRepo;

    public function __construct(
        ?OrderRepositoryInterface $orderRepo = null,
        ?VoucherRepositoryInterface $voucherRepo = null,
        ?PDO $db = null
    ) {
        $this->db = $db ?? Database::getConnection();
        $this->orderRepo = $orderRepo ?? new OrderRepository($this->db);
        $this->voucherRepo = $voucherRepo ?? new VoucherRepository($this->db);
    }

    /**
     * Generate dynamic payment instruction via Polymorphic Payment Gateway
     */
    public function getPaymentInstruction(string $orderRef): array
    {
        $order = $this->orderRepo->getOrderByRef($orderRef);
        if (!$order) {
            throw new InvalidArgumentException("ไม่พบคำสั่งซื้อหมายเลข {$orderRef}");
        }

        $gateway = PaymentGatewayFactory::create($order->getPaymentMethod() ?: 'promptpay');
        return $gateway->generatePaymentPayload($orderRef, $order->getTotalAmount());
    }

    /**
     * Confirm simulated payment for an order
     */
    public function confirmPayment(string $orderRef): array
    {
        $order = $this->orderRepo->getOrderByRef($orderRef);
        if (!$order) {
            throw new InvalidArgumentException("ไม่พบคำสั่งซื้อหมายเลข {$orderRef}");
        }

        // Idempotency: already paid
        if ($order->getStatus() === 'completed') {
            return [
                'order_id' => $order->getId(),
                'order_ref' => $order->getOrderRef(),
                'status' => 'completed',
                'paid_at' => $order->getPaidAt(),
                'total_amount' => $order->getTotalAmount()
            ];
        }

        // Check if expired
        if (strtotime($order->getExpiresAt()) < time()) {
            $this->orderRepo->updateStatus($order->getId(), 'expired');
            $this->voucherRepo->releaseExpiredVouchers();
            throw new RuntimeException("คำสั่งซื้อนี้หมดเวลาชำระเงินแล้ว (เกิน 15 นาที) กรุณาสั่งซื้อใหม่");
        }

        $paidAt = date('Y-m-d H:i:s');

        $this->db->beginTransaction();
        try {
            // 1. Update order status to completed
            $this->orderRepo->updateStatus($order->getId(), 'completed', $paidAt);

            // 2. Mark vouchers as sold
            $this->voucherRepo->markSoldByOrderId($order->getId());

            $this->db->commit();

            return [
                'order_id' => $order->getId(),
                'order_ref' => $order->getOrderRef(),
                'status' => 'completed',
                'paid_at' => $paidAt,
                'total_amount' => $order->getTotalAmount()
            ];
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Get order details and voucher PINs (masked or unmasked depending on payment status)
     */
    public function getOrderFulfillment(string $orderRef): array
    {
        $order = $this->orderRepo->getOrderByRef($orderRef);
        if (!$order) {
            throw new InvalidArgumentException("ไม่พบคำสั่งซื้อหมายเลข {$orderRef}");
        }

        $isPaid = ($order->getStatus() === 'completed');
        $vouchers = $this->voucherRepo->getVouchersByOrderId($order->getId());
        $items = $this->orderRepo->getOrderItems($order->getId());

        $formattedVouchers = [];
        foreach ($vouchers as $v) {
            $formattedVouchers[] = [
                'id' => $v->getId(),
                'code' => $isPaid ? $v->getCode() : $v->getMaskedCode(),
                'serial' => $v->getSerial(),
                'status' => $v->getStatus(),
                'is_unmasked' => $isPaid
            ];
        }

        return [
            'order' => $order->toArray(),
            'items' => $items,
            'vouchers' => $formattedVouchers,
            'redemption_guide' => $this->getRedemptionGuide($items)
        ];
    }

    private function getRedemptionGuide(array $items): array
    {
        $guides = [
            'Google Play' => 'เข้า Play Store > แตะไอคอนโปรไฟล์ > การชำระเงินและการสมัครใช้บริการ > แลกรับรหัสของขวัญ',
            'Apple Gift Card' => 'เข้า App Store > แตะรูปโปรไฟล์ > แลกบัตรของขวัญหรือรหัส > กรอกรหัส PIN',
            'Netflix' => 'ไปที่ netflix.com/redeem > กรอกรหัส PIN และกดยืนยันเพื่อรับวันรับชมทันที',
            'Steam Wallet' => 'เปิดโปรแกรม Steam > เมนูเกม > เปิดใช้รหัสกระเป๋าเงิน Steam > กรอกรหัส PIN'
        ];

        $matched = [];
        foreach ($items as $item) {
            $platformName = $item['platform_name'] ?? '';
            if (isset($guides[$platformName]) && !isset($matched[$platformName])) {
                $matched[$platformName] = $guides[$platformName];
            }
        }

        return $matched;
    }
}
