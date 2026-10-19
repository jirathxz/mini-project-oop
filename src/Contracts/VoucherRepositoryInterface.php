<?php

namespace CardVault\Contracts;

interface VoucherRepositoryInterface
{
    /**
     * Reserve available vouchers with row-level selection
     * @return int[] Reserved voucher IDs
     */
    public function reserveVouchers(int $denominationId, int $quantity, int $orderId, string $expiresAt): array;

    /**
     * Release expired vouchers back to 'available'
     */
    public function releaseExpiredVouchers(): int;

    /**
     * Mark vouchers as sold upon payment confirmation
     */
    public function markAsSold(int $orderId): int;

    /**
     * Retrieve all vouchers attached to an order
     * @return \CardVault\Models\Voucher[]
     */
    public function getVouchersByOrderId(int $orderId): array;

    /**
     * Retrieve current available inventory count for a denomination
     */
    public function getAvailableStock(int $denominationId): int;

    /**
     * Bulk insert new vouchers into inventory
     */
    public function bulkInsertVouchers(int $denominationId, array $codes): int;
}
