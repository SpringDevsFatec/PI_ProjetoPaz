<?php

namespace App\Backend\Model;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use DomainException;

class Product {
    private ?int $id;
    private ?int $supplierId;
    private string $name;
    private float $costPrice;
    private float $salePrice;
    private string $category;
    private string $description;
    private bool $isFavorite = false;
    private bool $isDonation = false;
    private ?DateTimeInterface $createdAt;
    private ?DateTimeInterface $updatedAt;

    public function __construct(
        string $name,
        float $costPrice,
        float $salePrice,
        string $category,
        string $description,
        bool $isFavorite = false,
        bool $isDonation = false,
        ?int $id = null,
        ?int $supplierId = null,
        ?DateTimeInterface $createdAt = null,
        ?DateTimeInterface $updatedAt = null
    ) {
        $this->id = $id;
        $this->supplierId = $supplierId;
        $this->setName($name);
        $this->setCostPrice($costPrice);
        $this->setSalePrice($salePrice);
        $this->setCategory($category);
        $this->setDescription($description);
        $this->setIsFavorite($isFavorite);
        $this->setIsDonation($isDonation);
        $this->createdAt = $createdAt ?? new DateTime();
        $this->updatedAt = $updatedAt ?? new DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getSupplierId(): ?int { return $this->supplierId; }

    public function getName(): string { return $this->name; }

    public function getCostPrice(): float { return $this->costPrice ? 0.0 : $this->costPrice; }
    public function getSalePrice(): float { return $this->salePrice; }

    public function getCategory(): string { return $this->category; }
    public function getDescription(): string { return $this->description; }

    public function isFavorite(): int { return $this->isFavorite; }
    public function isDonation(): int { return $this->isDonation; }

    public function getCreatedAt(): ?DateTimeInterface { return $this->createdAt; }
    public function getUpdatedAt(): ?DateTimeInterface { return $this->updatedAt; }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function setName(string $name): void {
        if (strlen(trim($name)) === 0) {
            throw new InvalidArgumentException("Nome do produto não pode ser vazio");
        }
        $this->name = trim($name);
        $this->updatedAt = new DateTime();
    }

    public function setCostPrice(float $costPrice): void {
        if ($costPrice <= 0) {
            throw new \InvalidArgumentException("Preço de custo não pode ser negativo.");
        }

        $this->costPrice = $this->isDonation ? 0.0 : round($costPrice, 2);
        $this->updatedAt = new DateTime();
    }

    public function setSalePrice(float $salePrice): void {
        if ($salePrice <= 0) {
            throw new \InvalidArgumentException("Preço de venda deve ser maior que zero.");
        }
        $this->salePrice = round($salePrice, 2);
        $this->updatedAt = new DateTime();
    }

    public function setCategory(string $category): void {
        $this->category = $category;
        $this->updatedAt = new DateTime();
    }

    public function setDescription(string $description): void {
        $this->description = $description;
        $this->updatedAt = new DateTime();
    }

    public function setIsFavorite(bool $isFavorite): void {
        $this->isFavorite = $isFavorite;
        $this->updatedAt = new DateTime();
    }

    public function setIsDonation(bool $isDonation): void {
        $wasDonation = $this->isDonation;
        $this->isDonation = $isDonation;

        if ($this->isDonation && !$wasDonation) {
            $this->costPrice = 0.0;
        }
        
        $this->updatedAt = new DateTime();

    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }

    public function calculateProfitMargin() : float
    {
        if ($this->getCostPrice() <= 0) {
            return 0.0;
        }
        return round((($this->salePrice - $this->getCostPrice()) / $this->getCostPrice()) * 100, 2);
    }

    public function updateFromArray(array $data): void
    {
        if (isset($data['name'])) $this->setName($data['name']);
        
        if (isset($data['cost_price'])) $this->setCostPrice((float)$data['cost_price']);
        
        if (isset($data['sale_price'])) $this->setSalePrice((float)$data['sale_price']);
        
        if (isset($data['category'])) $this->setCategory($data['category']);
        
        if (isset($data['description'])) $this->setDescription($data['description']);
        
        if (isset($data['is_favorite'])) $this->setIsFavorite((bool)$data['is_favorite']);
        
        if (isset($data['is_donation'])) $this->setIsDonation((bool)$data['is_donation']);
    }
    public function toArray(): array {
        return [
            'id' => $this->id,
            'supplier_id' => $this->supplierId,
            'name' => $this->name,
            'cost_price' => $this->getCostPrice(),
            'sale_price' => $this->salePrice,
            'category' => $this->category,
            'description' => $this->description,
            'is_favorite' => $this->isFavorite,
            'is_donation' => $this->isDonation,
            'profit_margin' => $this->calculateProfitMargin(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s')
        ];
    }
}