<?php

namespace App\Backend\Repository;

use App\Backend\Model\Product;
use App\Backend\Config\Database;
use PDO;
use PDOException;

class ProductRepository {

    private PDO $conn;
    private string $table = 'product';
    private $tableLog = '';

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    /* Getters */
    public function findByName(string $searchTerm): array
    {
        $query = "SELECT * FROM {{$this->table}} WHERE name LIKE :searchTerm LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%');
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function findByCategory(string $category): array
    {
        $query = "SELECT * FROM {{$this->table}} WHERE category = :category";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":category", $category, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByFavorite(): array 
    {
        $query = "SELECT * FROM {$this->table} WHERE is_favorite = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByDonation(): array {
        $query = "SELECT * FROM {$this->table} WHERE is_donation = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findBySalePrice(float $salePrice): array
    {
        $query = "SELECT * FROM {$this->table} WHERE sale_price = :sale_price";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":sale_price", $salePrice, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByCost(float $costPrice): array
    {
        $query = "SELECT * FROM {$this->table} WHERE cost_price = :cost_price";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cost_price", $costPrice, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ------------------------------------------- */

    public function findAll(): array
    {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save(Product $product): int
    {
        $query = "INSERT INTO {$this->table} 
                  (supplier_id, name, cost_price, sale_price, description, is_favorite, category, is_donation, date_create)
                  VALUES (sale_price, :description, :is_favorite, :category, :is_donation, :date_create)";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'supplier_id' => $product->getSupplierId(),
                'name'=> $product->getName(),
                'cost_price'=> $product->getCostPrice(),
                'sale_price' => $product->getSalePrice(),
                'category'=> $product->getCategory(),
                'description' => $product->getDescription(),
                'is_favorite' => $product->getIsFavorite(),
                'is_donation'=> $product->getIsDonation(),
                'created_at' => $product->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $product->getUpdatedAt()->format('Y-m-d H:i:s')
            ]);

            return (int)$this->conn->lastInsertId();

        } catch (PDOException $e) {
            throw new PDOException("Erro ao salvar produto: " . $e->getMessage());
        }
    }

    public function update(Product $product): bool
    {    
        $query = "UPDATE {$this->table} 
                  SET name = :name, 
                      cost_price = :cost_price, 
                      sale_price = :sale_price, 
                      description = :description, 
                      is_favorite = :is_favorite, 
                      category = :category, 
                      is_donation = :is_donation,
                      updated_at = :updated_at
                  WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                'name'=> $product->getName(),
                'cost_price'=> $product->getCostPrice(),
                'sale_price' => $product->getSalePrice(),
                'category'=> $product->getCategory(),
                'description' => $product->getDescription(),
                'is_favorite' => $product->getIsFavorite(),
                'is_donation'=> $product->getIsDonation(),
                'updated_at' => (new \DateTime)->format('Y-m-d H:i:s')
            ]);

        } catch (PDOException $e) {
            throw new PDOException("Erro ao atualizar produto: " . $e->getMessage());
        }
    }

    public function delete(int $id): bool
    {
        try {
            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            throw new PDOException("Erro ao deletar produto: " . $e->getMessage());
        }
    }
}