<?php

namespace App\Backend\Repository;

use App\Backend\Model\SaleModel;
use App\Backend\Config\Database;
use App\Backend\Repository\OrderRepository;
use App\Backend\Utils\Responses;
use DateTimeInterface;
use PDO;
use PDOException;

class SaleRepository {

    use Responses;

    private PDO $conn;
    private string $table = 'sale';

    private OrderRepository $orderRepository;

    public function __construct(PDO $conn = null) 
    {
        $this->conn = $conn ?: Database::getInstance();
        $this->orderRepository = new OrderRepository();
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

    public function findByDateRange(DateTimeInterface $startDate, DateTimeInterface $endDate, ?int $sellerId = null): array
    {
        $query = "SELECT 
                    s.*, 
                    u.name AS user_name, 
                    u.email AS user_email, 
                    u.created_at AS user_created_at
                FROM {$this->table} s
                LEFT JOIN user u ON s.user_id = u.id
                WHERE s.created_at BETWEEN :start_date AND :end_date";
        
        $params = [
            ':start_date' => $startDate->format('Y-m-d'),
            ':end_date' => $endDate->format('Y-m-d')
        ];

        if ($sellerId !== null) {
            $query .= " AND s.user_id = :seller_id";
            $params[':seller_id'] = $sellerId;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->buildRepositoryResponse(!empty($data), $data);
    }


    public function findByStatus(string $status): array
    {
        $query = "SELECT 
                    s.*, 
                    u.name AS user_name, 
                    u.email AS user_email, 
                    u.created_at AS user_created_at
                FROM {$this->table} s
                LEFT JOIN user u ON s.user_id = u.id
                WHERE s.status = :status 
                ORDER BY s.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([':status' => $status]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->buildRepositoryResponse(!empty($data), $data);
    }


    public function findBySeller(int $sellerId, ?string $status = null): array
{
    $query = "SELECT 
                s.*, 
                u.name AS user_name, 
                u.email AS user_email, 
                u.created_at AS user_created_at
            FROM {$this->table} s
            LEFT JOIN user u ON s.user_id = u.id
            WHERE s.user_id = :seller_id";

    $params = [':seller_id' => $sellerId];

    if ($status !== null) {
        $query .= " AND s.status = :status";
        $params[':status'] = $status;
    }

    $query .= " ORDER BY s.created_at DESC";

    $stmt = $this->conn->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $this->buildRepositoryResponse(!empty($data), $data);
}

    public function findAll(): array 
    {
        $query = "SELECT 
            s.*, 
            u.name AS user_name, 
            u.email AS user_email, 
            u.created_at AS user_created_at
          FROM {$this->table} s
          LEFT JOIN user u ON s.user_id = u.id
          ORDER BY s.created_at DESC";


        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->buildRepositoryResponse(!empty($data), $data);
    }


    public function find(int $id): ?array
    {
        $query = "SELECT 
                    s.*, 
                    u.name AS user_name, 
                    u.email AS user_email, 
                    u.created_at AS user_created_at
                FROM {$this->table} s
                LEFT JOIN user u ON s.user_id = u.id
                WHERE s.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        return $this->buildRepositoryResponse($data !== null, $data);
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

    public function findOpenBySeller(int $sellerId): ?array
    {
        $query = "SELECT * FROM {$this->table}
                 WHERE user_id = :user_id
                 AND status = 'pending'
                 LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $sellerId]);
        if ($stmt->rowCount() > 0) {
            $saleRepository = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
            return $this->buildRepositoryResponse(true, $saleRepository);
        } else {
            return $this->buildRepositoryResponse(false, null);
        }
    }


    public function createSale(SaleModel $sale): array
    {  
        $userId = $sale->getUserId();
        $code = $sale->getCode();
        $status = $sale->getStatus();
        $method = $sale->getMethod();
        $totalAmountSale = $sale->getTotalAmountSale();

        $query = "INSERT INTO {$this->table} 
                  (user_id, code, status, method, total_amount_sale) 
                  VALUES (:user_id, :code, :status, :method, :total_amount_sale)";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':user_id' => $userId,
                ':code' => $code,
                ':status' => $status,
                ':method' => $method,
                ':total_amount_sale' => $totalAmountSale
            ]);

            $saleId = $this->conn->lastInsertId();
            return $this->buildRepositoryResponse(true, $saleId);

        } catch (PDOException $e) {
            return $this->buildRepositoryResponse(false, "Erro ao criar venda: " . $e->getMessage());
        }
    }

    public function completeSale(SaleModel $sale): array
    {
        $id = $sale->getId();
        $img_sale = $sale->getImgSale();
        $status = $sale->getStatus();
        $totalamountsale = $sale->getTotalAmountSale();

        try {
            $this->conn->beginTransaction();

            $query = "UPDATE {$this->table}
                      SET status = :status,
                          total_amount_sale = :total_amount_sale,
                          img_sale = :img_sale
                      WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->execute(
                [
                    ':id' => $id,
                    ':status' => $status,
                    ':total_amount_sale' => $totalamountsale,
                    ':img_sale' => $img_sale,
                ]);

            $this->conn->commit();
            return $this->buildRepositoryResponse(true, $sale);

        } catch (PDOException $e) {
            $this->conn->rollBack();
            return $this->buildRepositoryResponse(false, "Erro ao finalizar venda: " . $e->getMessage());
        }
    }

    public function cancellSale(SaleModel $sale): array
    {
        $id = $sale->getId();
        $status = $sale->getStatus();
        $totalamountsale = $sale->getTotalAmountSale();

        try {
            $this->conn->beginTransaction();

            $query = "UPDATE {$this->table}
                      SET status = :status,
                          total_amount_sale = :total_amount_sale
                      WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->execute(
                [
                    ':id' => $id,
                    ':status' => $status,
                    ':total_amount_sale' => $totalamountsale,

                ]);

            $this->conn->commit();
            return $this->buildRepositoryResponse(true, $sale);

        } catch (PDOException $e) {
            $this->conn->rollBack();
            return $this->buildRepositoryResponse(false, "Erro ao finalizar venda: " . $e->getMessage());
        }
    }

}