<?php

namespace App\Backend\Repository;

use App\Backend\Model\ProductImage;
use App\Backend\Config\Database;
use PDO;
use PDOException;

class ProductImageRepository {

    private PDO $conn;
    private string $table = 'product_image';
    private $tableLog = '';

    public function __construct(PDO $conn = null) 
    {
        $this->conn = $conn ?: Database::getInstance();
    }

    public function findByProductId(int $productId): array
    {
        $query = "SELECT * FROM {$this->table} WHERE product_id = :product_id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            throw new PDOException("Erro ao buscar imagens do produto: " . $e->getMessage());
        }
    }

    public function find(int $id): ?array
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            throw new PDOException("Erro ao buscar imagem: " . $e->getMessage());
        }
    }

    public function save(ProductImage $productImage): int
    {
        $query = "INSERT INTO {$this->table} 
                  (product_id, path, alt_text, is_featured, position, created_at, updated_at)
                  VALUES 
                  (:product_id, :path, :alt_text, :created_at, :updated_at)";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'product_id' => $productImage->getProductId(),
                'path' => $productImage->getPath(),
                'alt_text' => $productImage->getAltText(),
                'created_at' => $productImage->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $productImage->getUpdatedAt()->format('Y-m-d H:i:s')
            ]);

            return (int)$this->conn->lastInsertId();

        } catch (PDOException $e) {
            throw new PDOException("Erro ao salvar imagem: " . $e->getMessage());
        }
    }

    public function update(ProductImage $productImage): bool
    {    
        $query = "UPDATE {$this->table} 
                  SET name = :name, 
                      path = :path, 
                      alt_text = :alt_text,
                      updated_at = :updated_at
                  WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                ':id' => $productImage->getId(),
                ':path'=> $productImage->getPath(),
                ':alt_text'=> $productImage->getAltText(),
                ':updated_at' => (new \DateTime)->format('Y-m-d H:i:s')
            ]);

        } catch (PDOException $e) {
            throw new PDOException("Erro ao atualizar imagem: " . $e->getMessage());
        }
    }

    public function delete(int $id): bool
    {
        $query = "DELETE FROM {$this->table} WHERE id = :id";

        try {
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            throw new PDOException("Erro ao remover imagem: " . $e->getMessage());
        }
    }
}