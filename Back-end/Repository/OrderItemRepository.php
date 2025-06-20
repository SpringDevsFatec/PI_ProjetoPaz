<?php 

namespace App\Backend\Repository;

use App\Backend\Model\OrderItemModel;
use App\Backend\Config\Database;
use App\Backend\Utils\Responses;
use PDO;
use PDOException;

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

    public function commitTransaction() {
        $this->conn->commit();
    }

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
        $orderItem = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->buildRepositoryResponse(!empty($orderItem), $orderItem ?: null);

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

    public function createOrderItem(OrderItemModel $item, int $orderId): array
    {
        $orderId = $item->getOrderId();
        $productId = $item->getProductId();
        $quantity = $item->getQuantity();
        $unitPrice = $item->getUnitPrice();

        $query = "INSERT INTO {$this->table}
                  (product_id, order_id, quantity, unit_price)
                  VALUES 
                  (:product_id, :order_id, :quantity, :unit_price)";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":product_id", $productId, PDO::PARAM_INT); 
            $stmt->bindParam(":order_id", $orderId, PDO::PARAM_INT);
            $stmt->bindParam(":quantity", $quantity, PDO::PARAM_INT);
            $stmt->bindParam(":unit_price", $unitPrice);
            $stmt->execute();

            $item->setId((int)$this->conn->lastInsertId());
            return $this->buildRepositoryResponse(!empty($item), $item ?: null);

        } catch (PDOException $e) {
            throw new PDOException("Erro ao inserir item do pedido: " . $e->getMessage());
        }        
    }
    
    /*
    no update and delete method for OrderItem

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
    */
}