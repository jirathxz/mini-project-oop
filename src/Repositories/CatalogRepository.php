<?php

namespace CardVault\Repositories;

use CardVault\Config\Database;
use CardVault\Contracts\CatalogRepositoryInterface;
use CardVault\Models\Category;
use CardVault\Models\Platform;
use CardVault\Models\Denomination;
use PDO;

class CatalogRepository implements CatalogRepositoryInterface
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
        $this->lazySweepExpiredReservations();
    }

    /**
     * ADR-0003: Lazy On-Demand Voucher Reservation Sweep
     * Reclaim expired reservations back to available pool.
     */
    public function lazySweepExpiredReservations(): int
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
     * @return Category[]
     */
    public function getAllCategories(): array
    {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY id ASC");
        $categories = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = new Category((int)$row['id'], $row['name'], $row['slug']);
        }
        return $categories;
    }

    public function getCategoryBySlug(string $slug): ?Category
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        return new Category((int)$row['id'], $row['name'], $row['slug']);
    }

    /**
     * @return Platform[]
     */
    public function getPlatformsByCategoryId(int $categoryId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM platforms WHERE category_id = ? ORDER BY id ASC");
        $stmt->execute([$categoryId]);
        $platforms = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $platforms[] = new Platform(
                (int)$row['id'],
                (int)$row['category_id'],
                $row['name'],
                $row['slug'],
                $row['region'] ?? 'TH',
                $row['delivery_type'] ?? 'Instant Digital Key',
                $row['description'] ?? '',
                $row['logo_url'] ?? null
            );
        }
        return $platforms;
    }

    /**
     * @return Denomination[]
     */
    public function getDenominationsByPlatformId(int $platformId): array
    {
        $sql = "SELECT d.*, 
                       (SELECT COUNT(*) FROM vouchers v WHERE v.denomination_id = d.id AND v.status = 'available') as stock_count
                FROM denominations d
                WHERE d.platform_id = ?
                ORDER BY d.price ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$platformId]);
        $denominations = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $denominations[] = new Denomination(
                (int)$row['id'],
                (int)$row['platform_id'],
                $row['name'],
                (float)$row['price'],
                (float)$row['face_value'],
                $row['currency'] ?? 'THB',
                (int)$row['stock_count']
            );
        }

        return $denominations;
    }

    public function getCategories(): array
    {
        return $this->getAllCategories();
    }

    public function getPlatformsByCategory(int $categoryId): array
    {
        return $this->getPlatformsByCategoryId($categoryId);
    }

    public function getDenominationsByPlatform(int $platformId): array
    {
        return $this->getDenominationsByPlatformId($platformId);
    }

    public function getDenominationById(int $id): ?array
    {
        $sql = "SELECT d.*, p.name as platform_name, p.slug as platform_slug,
                       (SELECT COUNT(*) FROM vouchers v WHERE v.denomination_id = d.id AND v.status = 'available') as stock_count
                FROM denominations d
                JOIN platforms p ON p.id = d.platform_id
                WHERE d.id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getAllStockOverview(): array
    {
        $sql = "SELECT d.id as denomination_id, d.name as denomination_name, d.price, 
                       p.id as platform_id, p.name as platform_name, p.slug as platform_slug,
                       c.name as category_name,
                       (SELECT COUNT(*) FROM vouchers v WHERE v.denomination_id = d.id AND v.status = 'available') as available_stock,
                       (SELECT COUNT(*) FROM vouchers v WHERE v.denomination_id = d.id AND v.status = 'sold') as sold_count
                FROM denominations d
                JOIN platforms p ON p.id = d.platform_id
                JOIN categories c ON c.id = p.category_id
                ORDER BY p.name ASC, d.price ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
