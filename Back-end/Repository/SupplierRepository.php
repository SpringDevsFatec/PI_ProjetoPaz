<?php 

namespace App\Backend\Repository;

use App\Backend\Model\Supplier;
use App\Backend\Config\Database;
use PDO;

class SupplierRepository {
    private $conn;
    private $table = 'supplier';

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    public function getAllSuppliers() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSupplierById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertSupplier(Supplier $supplier) {
        $name = $supplier->getName();
        $address = $supplier->getAddress();
        $dateCreate = $supplier->getDateCreate();

        $query = "INSERT INTO " . $this->table . " (name, address, data_create) VALUES (:name, :address, :date_create)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':date_create', $dateCreate);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateSupplier(Supplier $supplier) {
        $id = $supplier->getId();
        $name = $supplier->getName();
        $address = $supplier->getAddress();

        $query = "UPDATE " . $this->table . " SET name = :name, address = :address WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':address', $address);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function deleteSupplier($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}