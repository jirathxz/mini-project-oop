<?php

namespace CardVault\Models;

class Platform
{
    private int $id;
    private int $categoryId;
    private string $name;
    private string $slug;
    private string $region;
    private string $deliveryType;
    private string $description;
    private ?string $logoUrl;

    public function __construct(
        int $id,
        int $categoryId,
        string $name,
        string $slug,
        string $region = 'TH',
        string $deliveryType = 'Instant Digital Key',
        string $description = '',
        ?string $logoUrl = null
    ) {
        $this->id = $id;
        $this->categoryId = $categoryId;
        $this->name = $name;
        $this->slug = $slug;
        $this->region = $region;
        $this->deliveryType = $deliveryType;
        $this->description = $description;
        $this->logoUrl = $logoUrl;
    }

    public function getId(): int { return $this->id; }
    public function getCategoryId(): int { return $this->categoryId; }
    public function getName(): string { return $this->name; }
    public function getSlug(): string { return $this->slug; }
    public function getRegion(): string { return $this->region; }
    public function getDeliveryType(): string { return $this->deliveryType; }
    public function getDescription(): string { return $this->description; }
    public function getLogoUrl(): ?string { return $this->logoUrl; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->categoryId,
            'name' => $this->name,
            'slug' => $this->slug,
            'region' => $this->region,
            'delivery_type' => $this->deliveryType,
            'description' => $this->description,
            'logo_url' => $this->logoUrl,
        ];
    }
}
