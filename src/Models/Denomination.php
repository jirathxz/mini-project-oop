<?php

namespace CardVault\Models;

class Denomination
{
    private int $id;
    private int $platformId;
    private string $name;
    private float $price;
    private float $faceValue;
    private string $currency;
    private int $stockCount;

    public function __construct(
        int $id,
        int $platformId,
        string $name,
        float $price,
        float $faceValue,
        string $currency = 'THB',
        int $stockCount = 0
    ) {
        $this->id = $id;
        $this->platformId = $platformId;
        $this->name = $name;
        $this->price = $price;
        $this->faceValue = $faceValue;
        $this->currency = $currency;
        $this->stockCount = $stockCount;
    }

    public function getId(): int { return $this->id; }
    public function getPlatformId(): int { return $this->platformId; }
    public function getName(): string { return $this->name; }
    public function getPrice(): float { return $this->price; }
    public function getFaceValue(): float { return $this->faceValue; }
    public function getCurrency(): string { return $this->currency; }
    public function getStockCount(): int { return $this->stockCount; }
    public function isAvailable(): bool { return $this->stockCount > 0; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'platform_id' => $this->platformId,
            'name' => $this->name,
            'price' => $this->price,
            'face_value' => $this->faceValue,
            'currency' => $this->currency,
            'stock_count' => $this->stockCount,
            'is_available' => $this->isAvailable(),
        ];
    }
}
