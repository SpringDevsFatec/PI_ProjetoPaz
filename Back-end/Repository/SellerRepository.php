<?php 

namespace App\Backend\Repository;

use App\Backend\Model\Seller;
use App\Backend\Config\Database;
use PDO;

class SellerRepository {

    private $conn;
    private $table = 'sellers';

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    public function getSellerById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllSellers() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertSeller(Seller $seller) {
        $userId = $seller->getUserId();
        $saleId = $seller->getSaleId();
        $dateCreate = $seller->getDateCreate();

        $query = "INSERT INTO " . $this->table . " (user_id, sale_id, date_created) VALUES (:user_id, :sale_id, :date_create)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':sale_id', $saleId);
        $stmt->bindParam(':date_create', $dateCreate);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}