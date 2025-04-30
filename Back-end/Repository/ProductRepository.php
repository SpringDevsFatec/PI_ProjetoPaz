<?php

namespace App\Backend\Repository;

use App\Backend\Model\Product;
use App\Backend\Config\Database;
use PDO;

class ProductRepository {

    private $conn;
    private $table = 'products';
    private $tableLog = '';

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    /* usar view */
    // view para pesquisa por nome dinamicamente

    public function getByCategory($category){
        $query = "SELECT * FROM $this->table WHERE category = :category";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":category", $category, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByFavorite() {
        $query = "SELECT * FROM " . $this->table . " WHERE is_favorite = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByDonation() {
        $query = "SELECT * FROM " . $this->table . " WHERE is_donation = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBySale($salePrice) {
        $query = "SELECT * FROM " . $this->table . " WHERE sale_price = :sale_price";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":sale_price", $salePrice, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCost($costPrice) {
        $query = "SELECT * FROM " . $this->table . " WHERE cost_price = :cost_price";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cost_price", $costPrice, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ------------------------------------------- */

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 
    public function getAllProducts() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertProduct(Product $product) {
        $supplierId = $product->getSupplierId();
        $name = $product->getName();
        $costPrice = $product->getCostPrice();
        $salePrice = $product->getSalePrice();
        $description = $product->getDescription();
        $isFavorite = $product->getIsFavorite();
        $category = $product->getCategory();
        $isDonation = $product->getIsDonation();
        $dateCreate = $product->getDateCreate();
        
        $query = "INSERT INTO " . $this->table . " (supplier_id, name, cost_price, sale_price, description, is_favorite, category, is_donation, date_create)
        VALUES (sale_price, :description, :is_favorite, :category, :is_donation, :date_create)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':supplier_id', $supplierId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':cost_price', $costPrice);
        $stmt->bindParam(':sale_price', $salePrice);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':is_favorite', $isFavorite);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':is_donation', $isDonation);
        $stmt->bindParam(':date_create', $dateCreate);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateProduct(Product $product) {
        $id = $product->getId();
        $supplierId = $product->getSupplierId();
        $name = $product->getName();
        $costPrice = $product->getCostPrice();
        $salePrice = $product->getSalePrice();
        $description = $product->getDescription();
        $isFavorite = $product->getIsFavorite();
        $category = $product->getCategory();
        $isDonation = $product->getIsDonation();
        
        $query = "UPDATE " . $this->table . "SET (supplier_id, name, cost_price, sale_price, description, is_favorite, category, is_donation)
        VALUES (sale_price, :description, :is_favorite, :category, :is_donation)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':supplier_id', $supplierId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':cost_price', $costPrice);
        $stmt->bindParam(':sale_price', $salePrice);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':is_favorite', $isFavorite);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':is_donation', $isDonation);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}