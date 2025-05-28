<?php

namespace App\Backend\Model;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;

class ProductImage {
    private ?int $id;
    private int $productId;
    private string $path;
    private string $altText;
    private ?DateTimeInterface $createdAt;
    private ?DateTimeInterface $updatedAt;

    function __construct(
        int $productId, 
        string $path,
        string $altText,
        ?int $id = null,
        ?DateTimeInterface $createdAt = null,
        ?DateTimeInterface $updatedAt = null
    ){
        $this->id = $id;
        $this->productId = $productId;
        $this->setPath($path);
        $this->setAltText($altText);
        $this->createdAt = $createdAt ?? new DateTime();
        $this->updatedAt = $updatedAt ?? new DateTime();
    }
    
    public function getId(): ?int {
        return $this->id;
    }

    public function getProductId(): int {
        return $this->productId;
    }

    public function getPath(): string {
        return $this->path;
    }

    public function getAltText(): string {
        return $this->altText;
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

    public function setPath(string $path): void {
        if (!filter_var($path, FILTER_VALIDATE_URL) && !is_file($path)) {
            throw new InvalidArgumentException('Caminho/URL da imagem inválido');
        }
        $this->path = $path;
        $this->updatedAt = new DateTime();
    }

    public function setAltText(string $altText): void {
        if (empty(trim($altText))) {
            throw new InvalidArgumentException("Texto alternativo não pode ser vazio");
        }
        $this->altText = $altText;
        $this->updatedAt = new DateTime();
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): void 
    {
        $this->updatedAt = $updatedAt;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'product_id' => $this->productId,
            'path' => $this->path,
            'alt_text' => $this->altText,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s')
        ];
    }
}