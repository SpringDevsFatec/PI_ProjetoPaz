<?php

namespace App\Backend\Repository;

use App\Backend\Model\Product;
use App\Backend\Config\Database;
use App\Backend\Model\ProductModel;
use PDO;
use PDOException;

class ProductRepository {

    private PDO $conn;
    private string $table = 'product';
    private $tableLog = '';

    public function __construct(PDO $conn = null) 
    {
        $this->conn = $conn ?: Database::getInstance();
    }

    public function beginTransaction() {
        if (!$this->conn->inTransaction()) {
            $this->conn->beginTransaction();
        }
    }

    public function commitTransaction() {
        $this->conn->commit();
    }

    public function rollBackTransaction() {
        $this->conn->rollBack();
    }

    public function searchByName(string $searchTerm, int $limit = 10): array
    {
        $query = "SELECT * FROM {$this->table} 
                 WHERE LOWER(name) LIKE LOWER(:searchTerm) 
                 LIMIT :limit";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $productRepository = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($stmt->rowCount() > 0) {
                return ['status' => true,'product' => $productRepository];
            }else {
                return ['status' => false, 'product' => null];
            }
        } catch (PDOException $e) {
            throw new PDOException("Erro ao buscar produtos: " . $e->getMessage());
        }
    }

    public function findByCategory(string $category): array
    {
        $query = "SELECT * FROM {$this->table} WHERE category = :category ORDER BY name";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":category", $category, PDO::PARAM_STR);
            $stmt->execute();
            $productRepository = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($stmt->rowCount() > 0) {
                return ['status' => true,'product' => $productRepository];
            }else {
                return ['status' => false, 'product' => null];
            }
        } catch (PDOException $e) {
            throw new PDOException("Erro ao buscar por categoria: " . $e->getMessage());
        }
    }

    private function findByFlag(string $flagName, bool $value): array
    {
        $query = "SELECT * FROM {$this->table} 
                 WHERE {$flagName} = :value 
                 ORDER BY name";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':value', $value, PDO::PARAM_BOOL);
            $stmt->execute();
            $productRepository = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($stmt->rowCount() > 0) {
                return ['status' => true,'product' => $productRepository];
            }else {
                return ['status' => false, 'product' => null];
            }
        } catch (PDOException $e) {
            throw new PDOException("Erro ao buscar produtos: " . $e->getMessage());
        }
    }

    public function findFavorites(): array 
    {
        return $this->findByFlag('is_favorite', true);
    }

    public function findDonations(): array
    {
        return $this->findByFlag('donation', true);
    }

    public function findBySalePriceRange(float $minPrice, float $maxPrice): array
    {
        $query = "SELECT * FROM {$this->table} 
                 WHERE sale_price BETWEEN :min_price AND :max_price
                 ORDER BY sale_price, name";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":min_price", $minPrice);
            $stmt->bindParam(":max_price", $maxPrice);
            $stmt->execute();
            $productRepository = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($stmt->rowCount() > 0) {
                return ['status' => true,'product' => $productRepository];
            }else {
                return ['status' => false, 'product' => null];
            }
        } catch (PDOException $e) {
            throw new PDOException("Erro ao buscar por faixa de preço: " . $e->getMessage());
        }
    }

    public function findAll(string $orderBy = 'name', string $order = 'ASC'): array
    {
        $validOrders = ['name', 'category', 'sale_price', 'create_at'];
        $orderBy = in_array($orderBy, $validOrders) ? $orderBy : 'name';
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
 
        $query = "SELECT * FROM {$this->table} ORDER BY {$orderBy} {$order}";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $productRepository = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($stmt->rowCount() > 0) {
                return ['status' => true,'product' => $productRepository];
            }else {
                return ['status' => false, 'product' => null];
            }
        } catch (PDOException $e) {
            throw new PDOException("Erro ao buscar produtos: " . $e->getMessage());
        }
    }

    public function find(int $id): ?array
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $productRepository = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
            if ($stmt->rowCount() > 0) {
                return ['status' => true,'product' => $productRepository];
            }else {
                return ['status' => false, 'product' => null];
            }
        } catch (PDOException $e) {
            throw new PDOException("Erro ao buscar produto: " . $e->getMessage());
        }
    }

    public function createProduct(ProductModel $product)
    {
        $name = $product->getName();
        $cost_price = $product->getCostPrice();
        $sale_price = $product->getSalePrice();
        $category = $product->getCategory();
        $description = $product->getDescription();
        $is_favorite = $product->getFavorite();
        $is_donation = $product->getDonation();
        $status = $product->getStatus();
        $supplier_id = $product->getSupplierId();   
        $img_product = $product->getImgProduct();

        $query = "INSERT INTO {$this->table} 
                  (name, cost_price, sale_price, category, 
                  description, is_favorite, donation, status,
                  supplier_id, img_product)
                  VALUES 
                  (:name, :cost_price, :sale_price, :category, 
                  :description, :is_favorite, :donation, :status,
                  :supplier_id, :img_product)";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":cost_price", $cost_price);
            $stmt->bindParam(":sale_price", $sale_price);
            $stmt->bindParam(":category", $category, PDO::PARAM_STR);
            $stmt->bindParam(":description", $description, PDO::PARAM_STR);
            $stmt->bindParam(":is_favorite", $is_favorite, PDO::PARAM_INT);
            $stmt->bindParam(":donation", $is_donation, PDO::PARAM_INT);
            $stmt->bindParam(":status", $status, PDO::PARAM_INT);
            $stmt->bindParam(":supplier_id", $supplier_id, PDO::PARAM_INT);
            $stmt->bindParam(":img_product", $img_product, PDO::PARAM_STR);
            $stmt->execute();

        // Check if the product was created successfully
        if ($stmt->rowCount() > 0) {
            $product->setId($this->conn->lastInsertId());
            return ['status' => true, 'content' => $product];
        } else {
            return ['status' => false, 'content' => null];
        }
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
                      category = :category,
                      description = :description,
                      is_favorite = :is_favorite, 
                      is_donation = :is_donation,
                      updated_at = :updated_at
                  WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                ':id' => $product->getId(),
                ':name'=> $product->getName(),
                ':cost_price'=> $product->getCostPrice(),
                ':sale_price' => $product->getSalePrice(),
                ':category'=> $product->getCategory(),
                ':description' => $product->getDescription(),
                ':is_favorite' => (int)$product->isFavorite(),
                ':is_donation'=> (int)$product->isDonation(),
                ':updated_at' => (new \DateTime)->format('Y-m-d H:i:s')
            ]);

        } catch (PDOException $e) {
            throw new PDOException("Erro ao atualizar produto: " . $e->getMessage());
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
            throw new PDOException("Erro ao remover produto: " . $e->getMessage());
        }
    }
}