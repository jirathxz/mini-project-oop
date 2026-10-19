<?php

namespace CardVault\Services;

use CardVault\Config\Database;
use CardVault\Contracts\CatalogRepositoryInterface;
use CardVault\Contracts\OrderRepositoryInterface;
use CardVault\Contracts\VoucherRepositoryInterface;
use CardVault\Repositories\CatalogRepository;
use CardVault\Repositories\OrderRepository;
use CardVault\Repositories\VoucherRepository;
use InvalidArgumentException;
use PDO;

class AdminService
{
    private PDO $db;
    private OrderRepositoryInterface $orderRepo;
    private VoucherRepositoryInterface $voucherRepo;
    private CatalogRepositoryInterface $catalogRepo;

    public function __construct(
        ?OrderRepositoryInterface $orderRepo = null,
        ?VoucherRepositoryInterface $voucherRepo = null,
        ?CatalogRepositoryInterface $catalogRepo = null,
        ?PDO $db = null
    ) {
        $this->db = $db ?? Database::getConnection();
        $this->orderRepo = $orderRepo ?? new OrderRepository($this->db);
        $this->voucherRepo = $voucherRepo ?? new VoucherRepository($this->db);
        $this->catalogRepo = $catalogRepo ?? new CatalogRepository($this->db);
    }

    /**
     * Authenticate store administrator
     */
    public function authenticate(string $username, string $password): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
        $stmt->execute([trim($username)]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            return null;
        }

        return [
            'id' => (int)$admin['id'],
            'username' => $admin['username'],
            'name' => $admin['name'] ?? 'Store Manager',
            'role' => $admin['role'] ?? 'admin'
        ];
    }

    /**
     * Get real-time dashboard metrics (Revenue, Orders count, Stock status)
     */
    public function getDashboardMetrics(): array
    {
        $orderMetrics = $this->orderRepo->getMetrics();
        $stockOverview = $this->catalogRepo->getAllStockOverview();

        $totalAvailableVouchers = 0;
        $totalSoldVouchers = 0;
        $lowStockItems = [];

        foreach ($stockOverview as $item) {
            $avail = (int)$item['available_stock'];
            $totalAvailableVouchers += $avail;
            $totalSoldVouchers += (int)$item['sold_count'];

            if ($avail <= 2) {
                $lowStockItems[] = [
                    'denomination_id' => (int)$item['denomination_id'],
                    'denomination_name' => $item['denomination_name'],
                    'platform_name' => $item['platform_name'],
                    'available_stock' => $avail
                ];
            }
        }

        return [
            'revenue' => [
                'total_amount' => $orderMetrics['total_revenue'],
                'currency' => 'THB'
            ],
            'orders' => [
                'total' => $orderMetrics['total_orders'],
                'completed' => $orderMetrics['completed_orders'],
                'pending' => $orderMetrics['pending_orders'],
            ],
            'inventory' => [
                'total_available' => $totalAvailableVouchers,
                'total_sold' => $totalSoldVouchers,
                'low_stock_count' => count($lowStockItems),
                'low_stock_items' => $lowStockItems
            ]
        ];
    }

    /**
     * Get stock overview per platform and denomination
     */
    public function getStockOverview(): array
    {
        return $this->catalogRepo->getAllStockOverview();
    }

    /**
     * Bulk import voucher PIN codes into inventory
     */
    public function bulkImportVouchers(int $denominationId, array $codes): int
    {
        $denom = $this->catalogRepo->getDenominationById($denominationId);
        if (!$denom) {
            throw new InvalidArgumentException("ไม่พบแพ็กเกจสินค้า ID: {$denominationId}");
        }

        // Clean and filter empty codes
        $validCodes = array_values(array_filter(array_map('trim', $codes), fn($c) => $c !== ''));
        if (empty($validCodes)) {
            throw new InvalidArgumentException("กรุณากรอกรหัสบัตรอย่างน้อย 1 รหัส");
        }

        return $this->voucherRepo->bulkInsertVouchers($denominationId, $validCodes);
    }

    /**
     * Get paginated orders list
     */
    public function getOrders(int $limit = 50, int $offset = 0): array
    {
        $orders = $this->orderRepo->getAllOrders($limit, $offset);
        return array_map(fn($o) => $o->toArray(), $orders);
    }

    /**
     * Get complete order audit details with vouchers
     */
    public function getOrderDetails(string $orderRef): ?array
    {
        $order = $this->orderRepo->getOrderByRef($orderRef);
        if (!$order) {
            return null;
        }

        $items = $this->orderRepo->getOrderItems($order->getId());
        $vouchers = $this->voucherRepo->getVouchersByOrderId($order->getId());

        $voucherList = [];
        foreach ($vouchers as $v) {
            $voucherList[] = [
                'id' => $v->getId(),
                'code' => $v->getCode(), // Admin sees unmasked code for auditing
                'serial' => $v->getSerial(),
                'status' => $v->getStatus(),
            ];
        }

        return [
            'order' => $order->toArray(),
            'items' => $items,
            'vouchers' => $voucherList
        ];
    }
}
