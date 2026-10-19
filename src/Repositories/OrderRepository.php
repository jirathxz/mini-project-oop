<?php

namespace CardVault\Repositories;

use CardVault\Config\Database;
use CardVault\Contracts\OrderRepositoryInterface;
use CardVault\Models\Order;
use PDO;

class OrderRepository implements OrderRepositoryInterface
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function createOrder(string $orderRef, string $guestEmail, float $totalAmount, string $expiresAt, string $paymentMethod = 'promptpay'): int
    {
        $sql = "INSERT INTO orders (order_ref, guest_email, total_amount, payment_method, status, expires_at) 
                VALUES (?, ?, ?, ?, 'pending', ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderRef, $guestEmail, $totalAmount, $paymentMethod, $expiresAt]);

        return (int)$this->db->lastInsertId();
    }

    public function addOrderItem(int $orderId, int $denominationId, int $quantity, float $unitPrice, float $subtotal): void
    {
        $sql = "INSERT INTO order_items (order_id, denomination_id, quantity, unit_price, subtotal) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId, $denominationId, $quantity, $unitPrice, $subtotal]);
    }

    public function getOrderById(int $orderId): ?Order
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
        $stmt->execute([$orderId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        return $this->hydrate($row);
    }

    public function getOrderByRef(string $orderRef): ?Order
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE order_ref = ? LIMIT 1");
        $stmt->execute([$orderRef]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        return $this->hydrate($row);
    }

    public function updateStatus(int $orderId, string $status, ?string $paidAt = null): bool
    {
        $sql = "UPDATE orders SET status = ?, paid_at = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $paidAt, $orderId]);
    }

    public function getOrderItems(int $orderId): array
    {
        $sql = "SELECT oi.*, d.name as denomination_name, p.name as platform_name, p.region 
                FROM order_items oi
                JOIN denominations d ON d.id = oi.denomination_id
                JOIN platforms p ON p.id = d.platform_id
                WHERE oi.order_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrdersByEmail(string $email): array
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE guest_email = ? ORDER BY id DESC");
        $stmt->execute([$email]);
        $orders = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $orders[] = $this->hydrate($row);
        }
        return $orders;
    }

    public function getAllOrders(int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->db->prepare("SELECT * FROM orders ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        $orders = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $orders[] = $this->hydrate($row);
        }
        return $orders;
    }

    public function getMetrics(): array
    {
        $metrics = [
            'total_revenue' => 0.0,
            'completed_orders' => 0,
            'pending_orders' => 0,
            'total_orders' => 0,
        ];

        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END) as total_revenue
            FROM orders
        ");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $metrics['total_orders'] = (int)($row['total_orders'] ?? 0);
            $metrics['completed_orders'] = (int)($row['completed_orders'] ?? 0);
            $metrics['pending_orders'] = (int)($row['pending_orders'] ?? 0);
            $metrics['total_revenue'] = (float)($row['total_revenue'] ?? 0.0);
        }

        return $metrics;
    }

    private function hydrate(array $row): Order
    {
        return new Order(
            (int)$row['id'],
            $row['order_ref'],
            $row['guest_email'],
            (float)$row['total_amount'],
            $row['payment_method'] ?? 'promptpay',
            $row['status'] ?? 'pending',
            $row['paid_at'] ?? null,
            $row['expires_at'] ?? '',
            $row['created_at'] ?? ''
        );
    }
}
