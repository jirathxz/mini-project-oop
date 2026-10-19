<?php

namespace CardVault\Services;

use CardVault\Config\Database;
use CardVault\Repositories\OrderRepository;
use CardVault\Repositories\VoucherRepository;
use InvalidArgumentException;
use PDO;

class OtpService
{
    private PDO $db;
    private OrderRepository $orderRepo;
    private VoucherRepository $voucherRepo;

    public function __construct(
        ?OrderRepository $orderRepo = null,
        ?VoucherRepository $voucherRepo = null,
        ?PDO $db = null
    ) {
        $this->db = $db ?? Database::getConnection();
        $this->orderRepo = $orderRepo ?? new OrderRepository($this->db);
        $this->voucherRepo = $voucherRepo ?? new VoucherRepository($this->db);
    }

    /**
     * Generate 6-digit OTP code for email session
     */
    public function requestOtp(string $email): array
    {
        $cleanEmail = strtolower(trim($email));
        if (!filter_var($cleanEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("กรุณากรอกอีเมลที่ถูกต้อง");
        }

        $otp = sprintf('%06d', mt_rand(100000, 999999));
        $expiresAt = date('Y-m-d H:i:s', time() + 10 * 60); // 10 minutes lifespan

        $stmt = $this->db->prepare("INSERT INTO otp_sessions (email, otp_code, is_verified, expires_at) VALUES (?, ?, 0, ?)");
        $stmt->execute([$cleanEmail, $otp, $expiresAt]);

        return [
            'email' => $cleanEmail,
            'otp_code' => $otp,
            'expires_at' => $expiresAt
        ];
    }

    /**
     * Verify OTP code submitted by user
     */
    public function verifyOtp(string $email, string $otpCode): bool
    {
        $cleanEmail = strtolower(trim($email));
        $code = trim($otpCode);

        $isSqlite = (Database::getDriver() === 'sqlite');
        $nowExpr = $isSqlite ? "datetime('now')" : "NOW()";

        $sql = "SELECT id FROM otp_sessions 
                WHERE email = ? AND otp_code = ? AND expires_at > {$nowExpr}
                ORDER BY id DESC LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cleanEmail, $code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        }

        // Mark verified
        $updateStmt = $this->db->prepare("UPDATE otp_sessions SET is_verified = 1 WHERE id = ?");
        $updateStmt->execute([(int)$row['id']]);

        return true;
    }

    /**
     * Get all purchased vouchers and orders for a verified buyer email
     */
    public function getBuyerOrderHistory(string $email): array
    {
        $cleanEmail = strtolower(trim($email));

        $stmt = $this->db->prepare("SELECT * FROM orders WHERE guest_email = ? ORDER BY id DESC");
        $stmt->execute([$cleanEmail]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $history = [];
        foreach ($orders as $o) {
            $orderId = (int)$o['id'];
            $isPaid = ($o['status'] === 'completed');

            $items = $this->orderRepo->getOrderItems($orderId);
            $vouchers = $this->voucherRepo->getVouchersByOrderId($orderId);

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

            $history[] = [
                'order_id' => $orderId,
                'order_ref' => $o['order_ref'],
                'total_amount' => (float)$o['total_amount'],
                'status' => $o['status'],
                'paid_at' => $o['paid_at'],
                'created_at' => $o['created_at'],
                'items' => $items,
                'vouchers' => $formattedVouchers
            ];
        }

        return $history;
    }
}
