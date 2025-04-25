<?php

namespace App\Backend\Repository;

use App\Backend\Model\Order;
use App\Backend\Config\Database;
use PDO;

class OrderRepository {
    private $conn;
    private $table = 'orders';

    public function __construct() {

        $this->conn = Database::getInstance();
    }

    public function getOrdersByPaymentMethod($paymentMethod) {
        $query = "SELECT * FROM " . $this->table . " WHERE payment_method= :payment_method";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':payment_method', $paymentMethod);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrdersBySaleId($saleId) {
        $query = "SELECT * FROM " . $this->table . " WHERE sale_id = :sale_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $saleId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllOrders() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertOrder(Order $order) {
        $saleId = $order->getSaleId();
        $paymentMethod = $order->getPaymentMethod();
        $dateCreate = $order->getDateCreate();

        $query = "INSERT INTO " . $this->table . " (sale_id, payment_method, date_created) VALUES (:sale_id, :payment_method, :date_created)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $saleId);
        $stmt->bindParam(':payment_method', $paymentMethod);
        $stmt->bindParam(':date_create', $dateCreate);
        return $stmt->execute();
    }

    public function updateOrder(Order $order) {
        $id = $order->getId();
        $saleId = $order->getSaleId();
        $paymentMethod = $order->getPaymentMethod();

        $query = "UPDATE " . $this->table . " SET sale_id = :sale_id, payment_method = :payment_method WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':sale_id', $saleId);
        $stmt->bindParam(':payment_method', $paymentMethod);
        return $stmt->execute();
    }

    public function deleteOrder($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}