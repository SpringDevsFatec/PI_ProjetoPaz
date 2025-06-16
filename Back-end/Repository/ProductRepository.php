<?php

namespace App\Backend\Repository;

use App\Backend\Model\Product;
use App\Backend\Config\Database;
use App\Backend\Utils\Responses;
use App\Backend\Model\ProductModel;
use PDO;
use PDOException;

class ProductRepository {

    private PDO $conn;
    private string $table = 'product';
    private $tableLog = '';

    use Responses;

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
    $query = "SELECT 
                p.id AS idproduct,
                p.name AS nameproduct,
                p.cost_price,
                p.sale_price,
                p.description,
                p.is_favorite,
                p.category,
                p.donation,
                p.img_product,
                p.status,
                s.id AS idsupplier,
                s.name AS namesupplier,
                s.location
            FROM {$this->table} p
            JOIN projeto_paz.supplier s ON p.supplier_id = s.id
            WHERE LOWER(p.name) LIKE LOWER(:searchTerm) AND p.status = 1
            LIMIT :limit";

    $stmt = $this->conn->prepare($query);
    $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $this->buildRepositoryResponse(!empty($products), $products ?: null);
}


    public function findByCategory(string $category): array
{
    $query = "SELECT 
                p.id AS idproduct,
                p.name AS nameproduct,
                p.cost_price,
                p.sale_price,
                p.description,
                p.is_favorite,
                p.category,
                p.donation,
                p.img_product,
                p.status,
                s.id AS idsupplier,
                s.name AS namesupplier,
                s.location
            FROM {$this->table} p
            JOIN projeto_paz.supplier s ON p.supplier_id = s.id
            WHERE p.category = :category AND p.status = 1
            ORDER BY p.name";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':category', $category, PDO::PARAM_STR);
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $this->buildRepositoryResponse(!empty($products), $products ?: null);
}


    private function findByFlag(string $flagName, bool $value): array
    {
        $query = "SELECT 
                    p.id AS idproduct,
                    p.name AS nameproduct,
                    p.cost_price,
                    p.sale_price,
                    p.description,
                    p.is_favorite,
                    p.category,
                    p.donation,
                    p.img_product,
                    p.status,
                    s.id AS idsupplier,
                    s.name AS namesupplier,
                    s.location
                FROM {$this->table} p
                JOIN projeto_paz.supplier s ON p.supplier_id = s.id
                WHERE p.{$flagName} = :value
                ORDER BY p.name";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':value', $value, PDO::PARAM_BOOL);
        $stmt->execute();

        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->buildRepositoryResponse(!empty($products), $products ?: null);
    }


    public function findFavorites(): array 
    {
        return $this->findByFlag('is_favorite', true);
    }

    public function findNotFavorites(): array 
    {
        return $this->findByFlag('is_favorite', false);
    }

    public function   findDonations(): array
    {
        return $this->findByFlag('donation', true);
    }
    public function   findNotDonations(): array
    {
        return $this->findByFlag('donation', false);
    }

    public function findBySalePriceRange(float $minPrice, float $maxPrice): array
    {
        $query = "SELECT 
                    p.id AS idproduct,
                    p.name AS nameproduct,
                    p.cost_price,
                    p.sale_price,
                    p.description,
                    p.is_favorite,
                    p.category,
                    p.donation,
                    p.img_product,
                    s.id AS idsupplier,
                    s.name AS namesupplier,
                    s.location
                FROM {$this->table} p
                JOIN projeto_paz.supplier s ON p.supplier_id = s.id
                WHERE p.sale_price BETWEEN :min_price AND :max_price
                ORDER BY p.sale_price, p.name";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':min_price', $minPrice);
        $stmt->bindParam(':max_price', $maxPrice);
        $stmt->execute();

        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->buildRepositoryResponse(!empty($products), $products ?: null);
    }


    public function findAllActive(string $orderBy = 'name', string $order = 'ASC'): array
{
    $validOrders = [
        'name' => 'p.name',
        'category' => 'p.category',
        'sale_price' => 'p.sale_price',
        'create_at' => 'p.create_at'
    ];
    $orderByColumn = $validOrders[$orderBy] ?? 'p.name';
    $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

    $query = "SELECT 
                p.id AS idproduct,
                p.name AS nameproduct,
                p.cost_price,
                p.sale_price,
                p.description,
                p.is_favorite,
                p.category,
                p.donation,
                p.img_product,
                p.status,
                s.id AS idsupplier,
                s.name AS namesupplier,
                s.location
            FROM {$this->table} p
            JOIN projeto_paz.supplier s ON p.supplier_id = s.id
            WHERE p.status = 1
            ORDER BY {$orderByColumn} {$order}";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $this->buildRepositoryResponse(!empty($products), $products ?: null);
}

   public function findAll(string $orderBy = 'name', string $order = 'ASC'): ?array
{
    $validOrders = [
        'name' => 'p.name',
        'category' => 'p.category',
        'sale_price' => 'p.sale_price',
        'create_at' => 'p.create_at'
    ];
    $orderByColumn = $validOrders[$orderBy] ?? 'p.name';
    $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

    $query = "SELECT 
                p.id AS idproduct, 
                p.name AS nameproduct, 
                p.cost_price, 
                p.sale_price, 
                p.description, 
                p.is_favorite, 
                p.category, 
                p.donation, 
                p.img_product,
                p.status,
                s.id AS idsupplier, 
                s.name AS namesupplier, 
                s.location 
            FROM projeto_paz.product p 
            JOIN projeto_paz.supplier s ON p.supplier_id = s.id
            ORDER BY {$orderByColumn} {$order}";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();

     if ($stmt->rowCount() > 0) {
            $productRepository = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->buildRepositoryResponse(true, $productRepository);
        }else {
            return $this->buildRepositoryResponse(false, null);
        }
}



    public function find(int $id): ?array
    {
        $query = "SELECT p.id AS idproduct, p.name AS nameproduct, p.cost_price, p. sale_price, p.description, p.is_favorite, p.category, p.donation, p.img_product, p.status,
                        s.id AS idsupplier, s.name AS namesupplier, s.location
                    FROM projeto_paz.product p
                    JOIN projeto_paz.supplier s ON p.supplier_id = s.id
                    WHERE p.id = :id
                    LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $productRepository = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
            return $this->buildRepositoryResponse(true, $productRepository);
        }else {
            return $this->buildRepositoryResponse(false, null);
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

    // This method updates a product in the database
    public function update(ProductModel $product)
    {
        $id = $product->getId();
        $name = $product->getName();
        $cost_price = $product->getCostPrice();
        $sale_price = $product->getSalePrice();
        $category = $product->getCategory();
        $description = $product->getDescription();
        $is_favorite = $product->getFavorite();
        $is_donation = $product->getDonation();
        $status = $product->getStatus();
        
        $query = "UPDATE $this->table
                  SET name = :name,
                      cost_price = :cost_price,
                      sale_price = :sale_price,
                      category = :category,
                      description = :description,
                      is_favorite = :is_favorite,
                      donation = :donation,
                      status = :status
                  WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":cost_price", $cost_price);
            $stmt->bindParam(":sale_price", $sale_price);
            $stmt->bindParam(":category", $category, PDO::PARAM_STR);
            $stmt->bindParam(":description", $description, PDO::PARAM_STR);
            $stmt->bindParam(":is_favorite", $is_favorite, PDO::PARAM_INT);
            $stmt->bindParam(":donation", $is_donation, PDO::PARAM_INT);
            $stmt->bindParam(":status", $status, PDO::PARAM_INT);
            $stmt->execute();

            // Check if the product was updated successfully
            if ($stmt->rowCount() > 0) {
                return $this->buildRepositoryResponse(true, $product);
            }else {
                return $this->buildRepositoryResponse(false, null);
            }
        } catch (PDOException $e) {
            throw new PDOException("Erro ao atualizar produto: " . $e->getMessage());
        }
    }

    // This method checks if a product exists in the database to update
    public function existsToUpdate(ProductModel $product) {
        $id = $product->getId();

        $query = "SELECT * FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $productRepository = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
            return $this->buildRepositoryResponse(false, $productRepository);
        }else {
            return $this->buildRepositoryResponse(true, null);
        }
    }

    // This method updates the status of a product (active or inactive)
    public function updateStatus(ProductModel $product)
    {
        $query = "UPDATE {$this->table} SET status = :status WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $id = $product->getId();
            $status = $product->getStatus();

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $this->buildRepositoryResponse(true, $product);
            }else {
                return $this->buildRepositoryResponse(false, null);
            }

        } catch (PDOException $e) {
            throw new PDOException("Erro ao atualizar status: " . $e->getMessage());
        }
    }

    // This method updates the status of a product (active or inactive)
    public function updateImage(ProductModel $product):array
    {
        $id = $product->getId();
        $img_product = $product->getImgProduct();

        $query = "UPDATE {$this->table} SET img_product = :img_product WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $id = $product->getId();
            $status = $product->getStatus();

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':img_product', $img_product, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $this->buildRepositoryResponse(true, $product);
            }else {
                return $this->buildRepositoryResponse(false, null);
            }

        } catch (PDOException $e) {
            throw new PDOException("Erro ao atualizar status: " . $e->getMessage());
        }
    }
}