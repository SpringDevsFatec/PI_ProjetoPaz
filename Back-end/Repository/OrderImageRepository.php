<?php

namespace App\Backend\Repository;

use App\Backend\Model\OrderImage;
use App\Backend\Config\Database;
use PDO;

class OrderImageRepository {

    private $conn;
    private $table = 'order_images';

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    public function getAllOrderImages() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByOrderId($orderId) {
        $query = "SELECT * FROM " . $this->table . " WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
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
    public function insertOrderImage(OrderImage $orderImage) {
        $orderId = $orderImage->getOrderId();
        $imagePath = $orderImage->getImagePath();
        
        $query = "INSERT INTO " . $this->table . " (order_id, image_path) VALUES (:order_id, :image_path)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':image_path', $imagePath);
        return $stmt->execute();
    }
    public function updateOrderImage(OrderImage $orderImage) {
        $id = $orderImage->getId();
        $imagePath = $orderImage->getImagePath();
        
        $query = "UPDATE " . $this->table . " SET image_path = :image_path WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':image_path', $imagePath);
        return $stmt->execute();
    }
    public function deleteOrderImage($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}