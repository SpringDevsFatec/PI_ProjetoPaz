<?php 

namespace App\Backend\Repository;

use App\Backend\Model\OrderItemModel;
use App\Backend\Config\Database;
use App\Backend\Utils\Responses;
use PDO;

class OrderItemRepository {
    
    private PDO $conn;
    private string $table = 'order_item';

    use Responses;

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
            return $this->buildRepositoryResponse(true, $orderItemRepository);
        } else {
            return $this->buildRepositoryResponse(false, null);
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
            return $this->buildRepositoryResponse(true, $orderItemRepository);
        } else {
            return $this->buildRepositoryResponse(false, null);
        }
    }

    public function save(OrderItemModel $orderItem)
    {
        $query = "INSERT INTO {$this->table}
                  (order_id, product_id, quantity, unit_price, created_at)
                  VALUES (:order_id, :product_id, :quantity, :unit_price, :created_at)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            'product_id' => $orderItem->getProductId(), 
            'order_id' => $orderItem->getOrderId(),
            'quantity' => $orderItem->getQuantity(),
            'unit_price' => $orderItem->getUnitPrice(),
            'created_at' => $orderItem->getCreatedAt()->format('Y-m-d H:i:s')
        ]);

        if ($stmt->rowCount() > 0) {
            $orderItem->setId((int)$this->conn->lastInsertId());
            return $this->buildRepositoryResponse(true, $orderItem);
        } else {
            return $this->buildRepositoryResponse(false, null);
        }
    }
    public function update(OrderItemModel $orderItem)
    {
        $query = "UPDATE {$this->table} 
                  SET quantity = :quantity
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':id' => $orderItem->getId(),
            ':quantity' => $orderItem->getQuantity()
        ]);
        if ($stmt->rowCount() > 0) {
            return $this->buildRepositoryResponse(true, $orderItem);
        } else {
            return $this->buildRepositoryResponse(false, null);
        }
    }
    public function delete(int $id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $this->buildRepositoryResponse(true, null);
        } else {
            return $this->buildRepositoryResponse(false, null);
        }
    }

    public function deleteByOrder(int $orderId)
    {
        $query = "DELETE FROM order_items WHERE order_id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $this->buildRepositoryResponse(true, null);
        } else {
            return $this->buildRepositoryResponse(false, null);
        }
    }
}