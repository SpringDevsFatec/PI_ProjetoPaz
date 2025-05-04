<?php 

namespace App\Backend\Repository;

use App\Backend\Model\OrderItem;
use App\Backend\Config\Database;
use PDO;
use PDOException;

class OrderItemRepository {
    
    private PDO $conn;
    private string $table = 'order_items';

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    public function findWithProductDetails(int $orderId): array
    {
        $query = "SELECT oi.*, p.name AS product_name
                  FROM {$this->table} oi
                  JOIN products p ON oi.product_id = p.id
                  WHERE oi.order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByOrderId(int $orderId): array 
    {
        $query = "SELECT * FROM {$this->table} WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function find(int $id): ?array 
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    public function save(OrderItem $orderItem): int 
    {
        $query = "INSERT INTO {$this->table}
                  (order_id, product_id, quantity, unit_price, created_at, updated_at)
                  VALUES (:order_id, :product_id, :quantity, :unit_price, :created_at, :updated_at)";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'product_id' => $orderItem->getProductId(), 
                'order_id' => $orderItem->getOrderId(),
                'quantity' => $orderItem->getQuantity(),
                'unit_price' => $orderItem->getUnitPrice(),
                'created_at' => $orderItem->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $orderItem->getUpdateAt()->format('Y-m-d H:i:s')
            ]);

            return (int)$this->conn->lastInsertId();
        } catch (PDOException $e) {
            throw new PDOException("Error saving order item: " . $e->getMessage());
        }
    }
    public function update(OrderItem $orderItem): bool
    {
        $query = "UPDATE {$this->table} 
                  SET quantity = :quantity, 
                      updated_at = :updated_at 
                  WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                ':id' => $orderItem->getId(),
                ':quantity' => $orderItem->getQuantity(),
                ':updated_at' => (new \DateTime)->format('Y-m-d H:i:s')
            ]);
        } catch (PDOException $e) {
            throw new PDOException("Error updating order item: " . $e->getMessage());
        }
    }
    public function delete(int $id): bool
    {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}