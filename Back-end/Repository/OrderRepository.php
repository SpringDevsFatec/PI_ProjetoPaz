<?php

namespace App\Backend\Repository;

use App\Backend\Model\OrderModel;
use App\Backend\Config\Database;
use App\Backend\Repository\OrderItemRepository;
use App\Backend\Utils\Responses;
use PDO;
use PDOException;

class OrderRepository {

    use Responses;

    private PDO $conn;
    private $table = 'projeto_paz.order';
    private OrderItemRepository $itemRepository;

    public function __construct() {
        $this->conn = Database::getInstance();
        $this->itemRepository = new OrderItemRepository();
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

    public function findWithItems(int $id): ?array
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        
        if ($stmt->rowCount() > 0) {
            $orderRepository = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
            // Busca os itens
            $orderRepository['items'] = $this->itemRepository->findWithProductDetails($id);
            return $this->buildRepositoryResponse(true, $orderRepository);
        } else {
            return $this->buildRepositoryResponse(false, null);
        }
    }

    public function findByPaymentMethod(string $paymentMethod): array 
    {
        $query = "SELECT * FROM {$this->table} WHERE payment_method= :payment_method";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':payment_method', $paymentMethod, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $orderRepository = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->buildRepositoryResponse(true, $orderRepository);
        } else {
            return $this->buildRepositoryResponse(false, null);
        }
    }

    public function findBySaleId(int $saleId): array 
    {
        $query = "SELECT * FROM {$this->table} WHERE sale_id = :sale_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $orderRepository = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->buildRepositoryResponse(true, $orderRepository);
        } else {
            return $this->buildRepositoryResponse(false, null);
        }
    }

    public function findAll(): array
    {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $orderRepository = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->buildRepositoryResponse(true, $orderRepository);
        } else {
            return $this->buildRepositoryResponse(false, null);
        }
    }

    public function find(int $id): ?array
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $orderRepository = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
            return $this->buildRepositoryResponse(true, $orderRepository);
        } else {
            return $this->buildRepositoryResponse(false, null);
        }
    }

    public function save(OrderModel $order): bool 
    {
        $query = "INSERT INTO {$this->table}
                  (sale_id, status, payment_method, total_amount, created_at) 
                  VALUES (:sale_id, :status, :payment_method, :total_amount, :created_at)";
    
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                //'saller_id' => $this->sallerId,
                'sale_id' => $order->getSaleId(),
                'status' => $order->getStatus(),
                'payment_method' => $order->getPaymentMethod(),
                'total_amount' => $order->getTotalAmount(),
                'created_at' => $order->getCreatedAt()?->format('Y-m-d H:i:s')
            ]);

            $orderId = (int)$this->conn->lastInsertId();
            
            foreach ($order->getItems() as $item) {
                $item->setOrderId($orderId);
                $this->itemRepository->save($item);
            }
            
            return $orderId;
        } catch (PDOException $e) {
            throw new PDOException("Erro ao salvar pedido: " . $e->getMessage());
        }
    }

    public function update(OrderModel $order): bool 
    {
        $query = "UPDATE {$this->table} 
                  SET status = :status, 
                      payment_method = :payment_method
                  WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                ':id' => $order->getId(),
                ':status' => $order->getStatus(),
                ':payment_method' => $order->getPaymentMethod()
            ]);
        } catch (PDOException $e) {
            throw new PDOException("Erro ao atualizar o pedido: " . $e->getMessage());
        }
    }

    /**
     * Atualiza a venda associada a vários pedidos
     */
    public function assignToSale(array $orderIds, int $saleId): bool
    {
        try {
            $this->conn->beginTransaction();
            
            $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
            $query = "UPDATE {$this->table} 
                      SET sale_id = ?
                      WHERE id IN ($placeholders)";
            
            $params = array_merge(
                $saleId,
                $orderIds
            );
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            
            $this->conn->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw new PDOException("Erro ao vincular pedidos à venda: " . $e->getMessage());
        }
    }

    public function delete(int $id): bool
    {
        try {
            
            $this->itemRepository->deleteByOrder($id);
            
            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $id]);
            
            return true;
            
        } catch (PDOException $e) {
            throw new PDOException("Erro ao remover pedido: " . $e->getMessage());
        }
    }

    public function deleteBySale(int $saleId): bool
    {

        $query = "DELETE FROM order WHERE sale_id = :sale_id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new PDOException("Erro ao remover pedidos da venda: " . $e->getMessage());
        }
    }
}