<?php

namespace App\Backend\Repository;

use App\Backend\Model\Sale;
use App\Backend\Config\Database;
use PDO;

class SaleRepository {
    private $conn;
    private $table = 'sales';

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    // usar View
    public function getByDate($dateCreate) {
        $query = "SELECT * FROM " . $this->table . " WHERE date_create = :date_create";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date_create', $dateCreate, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllSales() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function startSale(Sale $sale) {
        $sellerId = $sale->getSellerId();
        $total = $sale->getTotal();
        $status = $sale->getStatus();
        $dateCreate = $sale->getDateCreate();
        
        $query = "INSERT INTO " . $this->table . " (seller_id, total, status, date_created) VALUES (:seller_id, :total, :status, :date_created)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':seller_id', $sellerId);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':date_created', $dateCreate);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateStatusSale(Sale $sale) {
        $id = $sale->getId();
        $total = $sale->getTotal();
        $status = $sale->getStatus();
        
        $query = "UPDATE " . $this->table . " SET total = :total, status = :status WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':status', $status);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function deleteSale($id) {
        
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}