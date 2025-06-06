<?php

namespace App\Backend\Model;

use InvalidArgumentException;

class ProductImage {
    private $id;
    private $productId;
    private $path;
    private $altText;
    private $createdAt;
    
    public function getId() {
        return $this->id;
    }

    public function getProductId() {
        return $this->productId;
    }

    public function getPath() {
        return $this->path;
    }

    public function getAltText() {
        return $this->altText;
    }

    public function getCreatedAt() { 
        return $this->createdAt; 
    }

    public function setId(?int $id) {
        $this->id = $id;
    }

    public function setPath(string $path) {
        if (!filter_var($path, FILTER_VALIDATE_URL) && !is_file($path)) {
            throw new InvalidArgumentException('Caminho/URL da imagem inválido');
        }
        $this->path = $path;
    }

    public function setAltText(string $altText) {
        if (empty(trim($altText))) {
            throw new InvalidArgumentException("Texto alternativo não pode ser vazio");
        }
        $this->altText = $altText;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'product_id' => $this->productId,
            'path' => $this->path,
            'alt_text' => $this->altText,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s')
        ];
    }
}