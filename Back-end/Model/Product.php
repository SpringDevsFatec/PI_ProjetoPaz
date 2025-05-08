<?php

namespace App\Backend\Model;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;

class Product {
    private ?int $id;
    private ?int $supplierId;
    private string $name;
    private float $costPrice;
    private float $salePrice;
    private string $category;
    private string $description;
    private int $isFavorite = 0; //0 - is not, 1 - is
    private int $isDonation = 0; //0 - is not, 1 - is
    private ?DateTimeInterface $createdAt;
    private ?DateTimeInterface $updatedAt;

    public function __construct(
        string $name,
        float $costPrice,
        float $salePrice,
        string $category,
        string $description,
        int $isFavorite = 0,
        int $isDonation = 0,
        ?int $id = null,
        ?int $supplierId = null,
        ?DateTimeInterface $createdAt = null,
        ?DateTimeInterface $updatedAt = null
    ) {
        $this->id = $id;
        $this->setSupplierId($supplierId);
        $this->setName($name);
        $this->setCostPrice($costPrice);
        $this->setSalePrice($salePrice);
        $this->setCategory($category);
        $this->setDescription($description);
        $this->setIsFavorite();
        $this->setIsDonation();
        $this->createdAt = $createdAt ?? new DateTime();
        $this->updatedAt = $updatedAt ?? new DateTime();
    }

    public function getId(): ?int {
        return $this->id;
    }
    public function getSupplierId(): ?int {
        return $this->supplierId;
    }
    public function getName(): string {
        return $this->name;
    }
    public function getCostPrice(): float {
        return $this->costPrice;
    }
    public function getSalePrice(): float {
        return $this->salePrice;
    }
    public function getCategory(): string {
        return $this->category;
    }
    public function getDescription(): string {
        return $this->description;
    }
    public function getIsFavorite(): int {
        return $this->isFavorite;
    }
    public function getIsDonation(): int {
        return $this->isDonation;
    }
    public function getCreatedAt(): ?DateTimeInterface {
        return $this->createdAt;
    }
    public function getUpdatedAt(): ?DateTimeInterface {
        return $this->updatedAt;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }
    public function setSupplierId(?int $supplierId): void {
        $this->supplierId = $supplierId;
    }
    public function setName(string $name): void {
        $this->name = $name;
    }
    public function setCostPrice(float $costPrice): void {
        if ($costPrice <= 0) {
            throw new \InvalidArgumentException("Preço de custo deve ser maior que zero.");
        }

        if ($this->isDonation) {
            $this->costPrice = 0;
        }

        $this->costPrice = $costPrice;
    }
    public function setSalePrice(float $salePrice): void {
        if ($salePrice <= 0) {
            throw new \InvalidArgumentException("Preço de venda deve ser maior que zero.");
        }
        $this->salePrice = $salePrice;
    }
    public function setCategory(string $category): void {
        $this->category = $category;
    }
    public function setDescription(string $description): void {
        $this->description = $description;
    }
    public function setIsFavorite(): void {
        if ($this->isFavorite != 0) 
        {
            $this->isFavorite = 1;
        }
        if ($this->isFavorite != 1) 
        {
            $this->isFavorite = 0;
        }
    }
    public function setIsDonation(): void {
        if ($this->isDonation != 0) 
        {
            $this->isDonation = 1;
        }
        if ($this->isDonation != 1) 
        {
            $this->isDonation = 0;
        }
    }

    public function updateFromArray(array $data): void {
        if (isset(
                $data['name'], 
                $data['cost_price'], 
                $data['sale_price'], 
                $data['category'], 
                $data['description'], 
                $data['is_favorite'], 
                $data['is_donation'])) 
        {
            $this->setName($data['name']);
            $this->setCostPrice($data['cost_price']); 
            $this->setSalePrice($data['sale_price']); 
            $this->setCategory($data['category']);
            $this->setDescription($data['description']); 
            $this->setIsFavorite($data['is_favorite']);
            $this->setIsDonation($data['is_donation']);
        }

        $this->updatedAt = new DateTime();
    }
    public function toArray(): array {
        return [
            'id' => $this->id,
            'supplier_id' => $this->supplierId,
            'name' => $this->name,
            'cost_price' => $this->costPrice,
            'sale_price' => $this->salePrice,
            'category' => $this->category,
            'description' => $this->description,
            'is_favorite' => $this->isFavorite,
            'is_donation' => $this->isDonation,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s')
        ];
    }
}