<?php

namespace CardVault\Repositories;

use CardVault\Config\Database;
use CardVault\Contracts\VoucherRepositoryInterface;
use CardVault\Models\Voucher;
use PDO;
use RuntimeException;

class VoucherRepository implements VoucherRepositoryInterface
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    /**
     * Reserve available vouchers with row-level selection
     * @return int[] Reserved voucher IDs
     */
    public function reserveVouchers(int $denominationId, int $quantity, int $orderId, string $expiresAt): array
    {
        // 1. Select available voucher IDs
        $selectSql = "SELECT id FROM vouchers 
                      WHERE denomination_id = ? AND status = 'available' 
                      LIMIT " . (int)$quantity;
        
        $stmt = $this->db->prepare($selectSql);
        $stmt->execute([$denominationId]);
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (count($ids) < $quantity) {
            throw new RuntimeException("สินค้าในสต็อกมีไม่เพียงพอตามจำนวนที่ต้องการ");
        }

        // 2. Lock them as 'reserved'
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $updateSql = "UPDATE vouchers 
                      SET status = 'reserved', order_id = ?, reserved_until = ? 
                      WHERE id IN ({$placeholders})";
        
        $params = array_merge([$orderId, $expiresAt], $ids);
        $updateStmt = $this->db->prepare($updateSql);
        $updateStmt->execute($params);

        return $ids;
    }

    /**
     * Release expired vouchers back to 'available'
     */
    public function releaseExpiredVouchers(): int
    {
        $isSqlite = (Database::getDriver() === 'sqlite');
        $nowExpr = $isSqlite ? "datetime('now')" : "NOW()";

        $sql = "UPDATE vouchers 
                SET status = 'available', order_id = NULL, reserved_until = NULL 
                WHERE status = 'reserved' AND reserved_until IS NOT NULL AND reserved_until < {$nowExpr}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Transition reserved vouchers to 'sold' upon payment confirmation
     */
    public function markSoldByOrderId(int $orderId): bool
    {
        return $this->markAsSold($orderId) > 0;
    }

    public function markAsSold(int $orderId): int
    {
        $stmt = $this->db->prepare("UPDATE vouchers SET status = 'sold', reserved_until = NULL WHERE order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->rowCount();
    }

    public function getAvailableStock(int $denominationId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM vouchers WHERE denomination_id = ? AND status = 'available'");
        $stmt->execute([$denominationId]);
        return (int)$stmt->fetchColumn();
    }

    public function bulkInsertVouchers(int $denominationId, array $codes): int
    {
        if (empty($codes)) {
            return 0;
        }

        $stmt = $this->db->prepare("INSERT INTO vouchers (denomination_id, code, serial, status) VALUES (?, ?, ?, 'available')");
        $count = 0;

        foreach ($codes as $index => $code) {
            $trimmedCode = trim((string)$code);
            if ($trimmedCode === '') {
                continue;
            }
            $serial = 'SN' . date('ymd') . str_pad((string)($denominationId * 1000 + $index + 1), 7, '0', STR_PAD_LEFT);
            $stmt->execute([$denominationId, $trimmedCode, $serial]);
            $count++;
        }

        return $count;
    }

    /**
     * @return Voucher[]
     */
    public function getVouchersByOrderId(int $orderId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM vouchers WHERE order_id = ? ORDER BY id ASC");
        $stmt->execute([$orderId]);
        $vouchers = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $vouchers[] = new Voucher(
                (int)$row['id'],
                (int)$row['denomination_id'],
                $row['code'],
                $row['serial'] ?? null,
                $row['status'],
                isset($row['order_id']) ? (int)$row['order_id'] : null,
                $row['reserved_until'] ?? null
            );
        }

        return $vouchers;
    }
}
