<?php

namespace App\Backend\Repository;

use App\Backend\Model\SaleModel;
use App\Backend\Config\Database;
use App\Backend\Repository\OrderRepository;
use DateTimeInterface;
use PDO;
use PDOException;

class SaleRepository {

    private PDO $conn;
    private string $table = 'sale';

    private OrderRepository $orderRepository;

    public function __construct(PDO $conn = null) 
    {
        $this->conn = $conn ?: Database::getInstance();
        $this->orderRepository = new OrderRepository();
    }

    public function findWithOrders(int $id): ?array
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        $sale = $stmt->fetch(PDO::FETCH_ASSOC);
             
        // Busca os pedidos
        $sale['orders'] = $this->orderRepository->findBySaleId($id);
             
        return $sale;
    }

    public function findByDateRange(DateTimeInterface $startDate, DateTimeInterface $endDate, ?int $sellerId = null): array
    {
        $query = "SELECT * FROM {$this->table} WHERE date BETWEEN :start_date AND :end_date";
        
        $params = [
            ':start_date' => $startDate->format('Y-m-d'),
            ':end_date' => $endDate->format('Y-m-d')
        ];

        if ($sellerId !== null) {
            $query .= " AND seller_id = :seller_id";
            $params[':seller_id'] = $sellerId;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function findOpenBySeller(int $sellerId): ?array
    {
        $query = "SELECT * FROM {$this->table}
                 WHERE seller_id = :seller_id
                 AND status = 'open'
                 AND date = CURRENT_DATE
                 LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([':seller_id' => $sellerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByStatus(string $status): array
    {
        $query = "SELECT * FROM {$this->table} WHERE status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findBySeller(int $sellerId, ?string $status = null): array
    {
        $query = "SELECT * FROM {$this->table} 
                  WHERE seller_id = :seller_id AND (status = :status OR status IS NULL)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':seller_id' => $sellerId, ':status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAll(): array 
    {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(SaleModel $sale): int
    {  
        $query = "INSERT INTO {$this->table} 
                  (seller_id, date, status, created_at) 
                  VALUES (:seller_id, :date, :status, :created_at)";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'seller_id' => $sale->getSellerId(),
                'date' => $sale->getDate()->format('Y-m-d'),
                'status' => $sale->getStatus(),
                'created_at' => $sale->getCreatedAt()?->format('Y-m-d H:i:s')
            ]);

            $saleId = $this->conn->lastInsertId();
            return $saleId;

        } catch (PDOException $e) {
            throw new PDOException("Erro ao criar venda: " . $e->getMessage());
        }
    }

    public function update(SaleModel $sale): bool
    {    
        $query = "UPDATE {$this->table} 
                  SET total = :total, 
                      status = :status
                  WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([     
            ':id' => $sale->getId(),
            ':status' => $sale->getStatus(),
            ':total' => $sale->getTotal()
        ]);

        } catch (PDOException $e) {
            throw new PDOException("Erro ao atualizar venda: " . $e->getMessage());
        }
    }

    public function completeSale(int $saleId): bool
    {
        try {
            $this->conn->beginTransaction();

            $orderRepository = new OrderRepository($this->conn);
            $orders = $orderRepository->findBySaleId($saleId);

            $total = array_reduce($orders, fn($sum, $order) => $sum + $order['total_amount']);

            $query = "UPDATE {$this->table}
                      SET staus = 'completed',
                          total = :total
                      WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $saleId, ':total' => $total]);

            $this->conn->commit();
            return true;

        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw new PDOException("Erro ao finalizar venda: " . $e->getMessage());
        }
    }

    public function delete(int $id): bool 
    {
        try {
            $this->conn->beginTransaction();

            $orderRepository = new OrderRepository($this->conn);
            $orderRepository->deleteBySale($id);

            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $id]);
            
            $this->conn->commit();
            return true;
        
        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw new PDOException("Erro ao deletar venda: " . $e->getMessage());
        }    
    }
}