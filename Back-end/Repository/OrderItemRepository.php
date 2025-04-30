<?php 

namespace App\Backend\Repository;

use App\Backend\Model\OrderItem;
use App\Backend\Config\Database;
use PDO;

class OrderItemRepository {
    
    private $conn;
    private $table = 'order_items';

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    public function getAllOrderItems() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getOrderItemById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function insertOrderItem(OrderItem $orderItem) {
        $orderId = $orderItem->getOrderId();
        $productId = $orderItem->getProductId();
        $quantity = $orderItem->getQuantity();
        $unitPrice = $orderItem->getUnitPrice();
        
        $query = "INSERT INTO " . $this->table . " (order_id, product_id, quantity, unit_price) VALUES (:order_id, :product_id, :quantity, :unit_price)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':unit_price', $unitPrice);
        return $stmt->execute();
    }
    public function updateOrderItem(OrderItem $orderItem) {
        $id = $orderItem->getId();
        $productId = $orderItem->getProductId();
        $quantity = $orderItem->getQuantity();
        $unitPrice = $orderItem->getUnitPrice();
        
        $query = "UPDATE " . $this->table . " SET product_id = :product_id, quantity = :quantity, unit_price = :unit_price WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':unit_price', $unitPrice);
        return $stmt->execute();
    }

}