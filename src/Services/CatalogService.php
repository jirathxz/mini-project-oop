<?php

namespace CardVault\Services;

use CardVault\Repositories\CatalogRepository;

class CatalogService
{
    private CatalogRepository $repository;

    public function __construct(?CatalogRepository $repository = null)
    {
        $this->repository = $repository ?? new CatalogRepository();
    }

    public function getCatalog(): array
    {
        $categories = $this->repository->getAllCategories();
        $result = [];

        foreach ($categories as $category) {
            $catData = $category->toArray();
            $platforms = $this->repository->getPlatformsByCategoryId($category->getId());
            $catData['platforms'] = [];

            foreach ($platforms as $platform) {
                $platData = $platform->toArray();
                $denominations = $this->repository->getDenominationsByPlatformId($platform->getId());
                $platData['denominations'] = array_map(fn($d) => $d->toArray(), $denominations);
                $catData['platforms'][] = $platData;
            }

            $result[] = $catData;
        }

        return $result;
    }

    public function getCatalogByCategory(string $categorySlug): array
    {
        $category = $this->repository->getCategoryBySlug($categorySlug);
        if (!$category) {
            return [];
        }

        $catData = $category->toArray();
        $platforms = $this->repository->getPlatformsByCategoryId($category->getId());
        $catData['platforms'] = [];

        foreach ($platforms as $platform) {
            $platData = $platform->toArray();
            $denominations = $this->repository->getDenominationsByPlatformId($platform->getId());
            $platData['denominations'] = array_map(fn($d) => $d->toArray(), $denominations);
            $catData['platforms'][] = $platData;
        }

        return [$catData];
    }
}
