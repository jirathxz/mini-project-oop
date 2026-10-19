<?php

namespace CardVault\Contracts;

interface CatalogRepositoryInterface
{
    public function getCategories(): array;

    public function getPlatformsByCategory(int $categoryId): array;

    public function getDenominationsByPlatform(int $platformId): array;

    public function getDenominationById(int $id): ?array;

    public function getAllStockOverview(): array;
}
