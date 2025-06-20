<?php

namespace App\Backend\Repository;

use App\Backend\Model\OrderModel;
use App\Backend\Config\Database;
use App\Backend\Model\SaleModel;
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

    public function findByCode(string $code): ?array
    {
        $query = "SELECT * FROM {$this->table}
                 WHERE code = :code
                 LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([':code' => $code]);
        if ($stmt->rowCount() > 0) {
            $saleRepository = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
            return $this->buildRepositoryResponse(true, $saleRepository);
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

    public function createOrder(OrderModel $order)
    {
        $saleId = $order->getSaleId();
        $code = $order->getCode();
        $paymentMethod = $order->getPaymentMethod();

        $query = "INSERT INTO {$this->table}
                (sale_id, code, payment_method)
                VALUES
                (:sale_id, :code, :payment_method)";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":sale_id", $saleId, PDO::PARAM_INT);
            $stmt->bindParam(":code", $code, PDO::PARAM_STR);
            $stmt->bindParam(":payment_method", $paymentMethod, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $orderId = (int)$this->conn->lastInsertId();
                $order->setId($orderId);
                return $this->buildRepositoryResponse(true, $order);
            } else {
                return $this->buildRepositoryResponse(false, null);
            }
        } catch (PDOException $e) {
            throw new PDOException("Erro ao salvar pedido: " . $e->getMessage());
        }
    }

    public function updateTotalAmount(OrderModel $order)
    {
        $query = "UPDATE {$this->table} SET total_amount_order = :total WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':total', $order->getTotalAmountOrder());
        $stmt->bindValue(':id', $order->getId());
        return $stmt->execute();
    }


    public function updateStatus(OrderModel $order) 
    {
        $query = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $id = $order->getId();
            $status = $order->getStatus();

            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $this->buildRepositoryResponse(true, $order);
            }else {
                return $this->buildRepositoryResponse(false, null);
            }
            
        } catch (PDOException $e) {
            throw new PDOException("Erro ao atualizar pedido: " . $e->getMessage());
        }
    }

     public function cancellOrders($sale_id) 
    {
        $id = $sale_id;
        $status = 'cancelled';
      
        $query = "UPDATE {$this->table} SET status = :status WHERE sale_id = :sale_id";
        
        try {
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":sale_id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $this->buildRepositoryResponse(true, $sale_id);
            }else {
                return $this->buildRepositoryResponse(false, null);
            }
            
        } catch (PDOException $e) {
            throw new PDOException("Erro ao atualizar pedido: " . $e->getMessage());
        }
    }


    /*
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
    */
}