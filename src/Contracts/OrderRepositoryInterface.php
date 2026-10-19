<?php

namespace CardVault\Contracts;

use CardVault\Models\Order;

interface OrderRepositoryInterface
{
    public function createOrder(string $orderRef, string $guestEmail, float $totalAmount, string $expiresAt, string $paymentMethod = 'promptpay'): int;

    public function addOrderItem(int $orderId, int $denominationId, int $quantity, float $unitPrice, float $subtotal): void;

    public function getOrderByRef(string $orderRef): ?Order;

    public function getOrderById(int $id): ?Order;

    public function updateStatus(int $orderId, string $status, ?string $paidAt = null): bool;

    public function getOrdersByEmail(string $email): array;

    public function getAllOrders(int $limit = 50, int $offset = 0): array;

    public function getMetrics(): array;
}
