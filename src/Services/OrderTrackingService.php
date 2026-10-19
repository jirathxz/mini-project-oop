<?php

namespace CardVault\Services;

use CardVault\Config\Database;
use CardVault\Repositories\OrderRepository;
use InvalidArgumentException;
use PDO;

class OrderTrackingService
{
    private PDO $db;
    private OrderRepository $orderRepo;
    private PaymentService $paymentService;

    public function __construct(
        ?OrderRepository $orderRepo = null,
        ?PaymentService $paymentService = null,
        ?PDO $db = null
    ) {
        $this->db = $db ?? Database::getConnection();
        $this->orderRepo = $orderRepo ?? new OrderRepository($this->db);
        $this->paymentService = $paymentService ?? new PaymentService($this->orderRepo, null, $this->db);
    }

    /**
     * Dual-key order lookup: requires both Order Ref and matching Guest Email
     */
    public function trackOrder(string $orderRef, string $email): array
    {
        $ref = strtoupper(trim($orderRef));
        $cleanEmail = strtolower(trim($email));

        if (empty($ref)) {
            throw new InvalidArgumentException("กรุณากรอกหมายเลขคำสั่งซื้อ (Order Ref)");
        }
        if (!filter_var($cleanEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("กรุณากรอกอีเมลที่ถูกต้อง");
        }

        $order = $this->orderRepo->getOrderByRef($ref);
        if (!$order) {
            throw new InvalidArgumentException("ไม่พบคำสั่งซื้อหมายเลข {$ref}");
        }

        // Dual-Key check: verify email matches
        if (strtolower(trim($order->getGuestEmail())) !== $cleanEmail) {
            throw new InvalidArgumentException("อีเมลที่ระบุไม่ตรงกับข้อมูลในคำสั่งซื้อนี้ เพื่อความปลอดภัยของรหัสบัตร");
        }

        return $this->paymentService->getOrderFulfillment($ref);
    }
}
