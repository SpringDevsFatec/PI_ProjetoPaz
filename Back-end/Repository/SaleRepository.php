<?php

namespace App\Backend\Repository;

use App\Backend\Model\Sale;
use App\Backend\Config\Database;
use App\Backend\Repository\OrderRepository;
use DateTimeInterface;
use PDO;
use PDOException;

class SaleRepository {

    private PDO $conn;
    private $table = 'sale';

    private OrderRepository $orderRepository;

    public function __construct() {
        $this->conn = Database::getInstance();
        $this->orderRepository = new OrderRepository();
    }

    // usar View

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

    public function findByDate(DateTimeInterface $createdAt): array
    {
        $query = "SELECT * FROM {$this->table} WHERE created_at = :created_at";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':created_at', $createdAt, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByStatus(string $status): array
    {
        $query = "SELECT * FROM {$this->table} WHERE status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    public function findBySeller(int $sellerId): array
    {
        $query = "SELECT * FROM {$this->table} WHERE seller_id = :seller_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':seller_d', $sellerId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    */

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
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function openSale(Sale $sale): int
    {  
        $query = "INSERT INTO {$this->table} 
                  (total, status, created_at, updated_at) 
                  VALUES (:total, :status, :created_at, :updated_at)";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                //'seller_id' => $sale->getSellerId(),
                'total' => $sale->getTotal(),
                'status' => $sale->getStatus(),
                'created_at' => $sale->getCreatedAt()?->format('Y-m-d H:i:s'),
                'updated_at' => $sale->getUpdatedAt()?->format('Y-m-d H:i:s')
            ]);

            $saleId = $this->conn->lastInsertId();
            return $saleId;

        } catch (PDOException $e) {
            throw new PDOException("Erro ao iniciar a venda: " . $e->getMessage());
        }
    }

    public function update(Sale $sale): bool
    {    
        $query = "UPDATE {$this->table} 
                  SET total = :total, 
                      status = :status,
                      updated_at = :updated_at
                  WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([     
            ':id' => $sale->getId(),
            ':total' => $sale->getTotal(),
            ':status' => $sale->getStatus(),
            ':updated_at' => (new \DateTime)->format('Y-m-d H:i:s')
        ]);

        } catch (PDOException $e) {
            throw new PDOException("Erro ao atualizar venda: " . $e->getMessage());
        }
    }

    public function delete(int $id): bool 
    {
        try {
            $this->orderRepository->deleteBySale($id);

            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $id]);
            
            return true;
        
        } catch (PDOException $e) {
            throw new PDOException("Erro ao deletar venda: " . $e->getMessage());
        }    
    }
}