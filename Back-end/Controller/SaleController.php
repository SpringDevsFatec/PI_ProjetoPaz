<?php

namespace App\Backend\Controller;

use App\Backend\Service\SaleService;
use App\Backend\Utils\Responses;
use DateTime;
use DomainException;
use Exception;
use InvalidArgumentException;

class SaleController {

    private SaleService $service;
    use Responses;

    public function __construct(SaleService $service) 
    {
        $this->service = $service;
    }

    public function listByDate(): void
    {

        // Obtém parâmetros da query string
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        $sellerId = $_GET['seller_id'] ?? null;

        // Validação básica
        if (!$startDate || !$endDate) {
            throw new InvalidArgumentException("As datas inicial e final são obrigatórias");
        }

        // Converte as strings para objetos DateTime
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        
        // Busca as vendas
        if ($result = $this->service->getSalesByPeriod(
            $start,
            $end,
            $sellerId ? (int)$sellerId : null
        )) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 401);
        }
    }

    public function listBySeller(int $sellerId): void
    {
        $status = $_GET['status'] ?? null;
        if ($result = $this->service->getSalesBySeller($sellerId, $status)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 401);
        }
    }

    public function listSalesByStatus(string $status): void 
    {
        if ($result = $this->service->getByStatus($status)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 401);
        }
    }

    public function listAllSales(): void 
    {
        if ($result = $this->service->getAll()) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 401);
        }
    }

    public function getSaleById(int $id): void 
    {
        if ($result = $this->service->getSale($id)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 401);
        }
    }

    public function show(int $id): void 
    {
        if ($result = $this->service->getSaleDetails($id)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 401);
        }
    }

    public function createSale(): void
    {
        // this method needs of Token JWT to run
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->handleResponse(false, "Formato JSON inválido", null, 200);
        }

        if ($result = $this->service->createSale($data)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 401);
        }
    }

    public function addOrder(int $saleId): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $requiredFields = ['sale_id', 'status', 'payment_method', 'total_amount'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("Campo obrigatório faltando: {$field}");
            }
        }

        if ($result = $this->service->addOrderToSale($saleId, (int)$data['order_id'])) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 401);
        } 
    }

    public function complete(int $id): void 
    {
        if ($result = $this->service->completeSale($id)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 401);
        }
    }

    public function cancel(int $id): void 
    {
        if ($result = $this->service->cancelSale($id)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 401);
        }
    }

    public function delete(int $id): void 
    {
        if ($result = $this->service->deleteSale($id)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 401);
        }
    }
}