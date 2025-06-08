<?php 

namespace App\Backend\Repository;

use App\Backend\Model\OrderItemModel;
use App\Backend\Config\Database;
use PDO;
use PDOException;

class OrderItemRepository {
    
    private PDO $conn;
    private string $table = 'order_item';

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    public function beginTransaction() {
        if (!$this->conn->inTransaction()) {
            $this->conn->beginTransaction();
        }
    }

    // commit transaction
    public function commitTransaction() {
        $this->conn->commit();
    }

    // roll back transaction
    public function rollBackTransaction() {
        $this->conn->rollBack();
    }

    public function findWithProductDetails(int $orderId): array
    {
        $query = "SELECT oi.*, p.name AS product_name, p.description AS product_description, p.category AS product_category, p.img_product
                  FROM {$this->table} oi
                  JOIN product p ON oi.product_id = p.id
                  WHERE oi.order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();

        // Check if any order items were found
        if ($stmt->rowCount() > 0) {
            $orderItemRepository = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ['status' => true, 'order_item' => $orderItemRepository];
        } else {
            return ['status' => true, 'order_item' => null];
        }
    }
    
    public function find(int $id): ?array 
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $orderItemRepository = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
            return ['status' => true, 'order_item' => $orderItemRepository];
        } else {
            return ['status' => true, 'order_item' => null];
        }
    }

    public function save(OrderItemModel $orderItem): int 
    {
        $query = "INSERT INTO {$this->table}
                  (order_id, product_id, quantity, unit_price, created_at)
                  VALUES (:order_id, :product_id, :quantity, :unit_price, :created_at)";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'product_id' => $orderItem->getProductId(), 
                'order_id' => $orderItem->getOrderId(),
                'quantity' => $orderItem->getQuantity(),
                'unit_price' => $orderItem->getUnitPrice(),
                'created_at' => $orderItem->getCreatedAt()->format('Y-m-d H:i:s')
            ]);

            return (int)$this->conn->lastInsertId();
        } catch (PDOException $e) {
            throw new PDOException("Erro ao salvar item do pedido: " . $e->getMessage());
        }
    }
    public function update(OrderItemModel $orderItem): bool
    {
        $query = "UPDATE {$this->table} 
                  SET quantity = :quantity
                  WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                ':id' => $orderItem->getId(),
                ':quantity' => $orderItem->getQuantity()
            ]);
        } catch (PDOException $e) {
            throw new PDOException("Erro ao atualizar item do pedido: " . $e->getMessage());
        }
    }
    public function delete(int $id): bool
    {
        try {
            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException("Erro ao deletar item do pedido: " . $e->getMessage());
        }
    }

    public function deleteByOrder(int $orderId): int
    {
        $query = "DELETE FROM order_items WHERE order_id = :order_id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new PDOException("Erro ao remover itens do pedido: " . $e->getMessage());
        }
    }
}