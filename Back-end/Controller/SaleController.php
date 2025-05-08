<?php

namespace App\Backend\Controller;

use App\Backend\Service\SaleService;
use DateTimeInterface;
use DomainException;
use Exception;
use InvalidArgumentException;
use Dotenv\Exception\InvalidFileException;

class SaleController {

    private SaleService $service;

    public function __construct(SaleService $service) 
    {
        $this->service = $service;
    }

    private function jsonResponse(
        mixed $data,
        int $statusCode = 200,
        ?string $message = null
    ): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');

        $response = [];
        if ($message) {
            $response['message'] = $message;
        }
        if ($data !== null) {
            $response['data'] = $data;
        }

        echo json_encode($response);
        exit;
    }

    public function listWithOrders(int $id): void 
    {
        try {
            $sales = $this->service->getWithOrders($id);
            $this->jsonResponse($sales, 200, empty($sales) ? 'Nenhuma venda encontrada' : null);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar vendas: ' . $e->getMessage());
        }
    }

    public function listSalesByDate(DateTimeInterface $createdAt): void 
    {
        try {
            $sales = $this->service->getByDate($createdAt);
            $this->jsonResponse($sales, 200, empty($sales) ? 'Nenhuma venda encontrada' : null);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar vendas: ' . $e->getMessage());
        }
    }

    public function listSalesByStatus(string $status): void 
    {
        try {
            $sales = $this->service->getByStatus($status);
            $this->jsonResponse($sales, 200, empty($sales) ? 'Nenhuma venda encontrada' : null);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar vendas: ' . $e->getMessage());
        }
    }

    public function listAllSales(): void 
    {
        try {
            $sales = $this->service->getAll();
            $this->jsonResponse($sales, 200, empty($sales) ? 'Nenhuma venda encontrada' : null);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar vendas: ' . $e->getMessage());
        }
    }

    public function show(int $id): void 
    {
        try {
            $sale = $this->service->getSale($id);
            if ($sale === null) {
                $this->jsonResponse(null, 404, 'Venda não encontrada');
            }
            $this->jsonResponse($sale);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar venda');
        }
    }

    public function create(): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException('JSON inválido');
            }

            $requiredFields = ['status'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new InvalidArgumentException("Campo obrigatório faltando: {$field}");
                }
            }

            $order = $this->service->createSale(
                (string)$data['status']
            );

            $this->jsonResponse($order->toArray(), 201, 'Venda iniciada com sucesso');
            
        } catch (InvalidArgumentException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());
            
        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());

        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao iniciar venda: ' . $e->getMessage());
        }
    }

    public function addOrder(int $saleId): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $requiredFields = ['sale_id', 'status', 'payment_method', 'total_amount'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new InvalidArgumentException("Campo obrigatório faltando: {$field}");
                }
            }

            $sale = $this->service->addOrderToSale(
                $saleId,
                (string)$data['status'],
                (string)$data['payment_method'],
                (float)$data['total_amount']
            );

            $this->jsonResponse($sale->toArray(), 200, 'Pedido adicionado a venda');
            
        } catch (InvalidArgumentException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());

        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());

        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao adicionar pedido: ' . $e->getMessage());
        }
    }

    public function update(int $id, array $data): void 
    {
        try {
            $data = json_decode(file_get_contents('php://input', true));

            if (!isset($data['status'])) {
                throw new InvalidArgumentException('Status não informado');
            }

            $sale = $this->service->updateSaleStatus($id, (string)$data['status']);
            $this->jsonResponse($sale->toArray(), 200, 'Status atualizado');

        } catch (InvalidFileException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());

        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());

        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao atualizar status');
        }
    }

    public function delete(int $id): void 
    {
        try {
            $this->service->deleteSale($id);
            $this->jsonResponse(null, 204, 'Venda excluída com sucesso.');

        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());

        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao excluir venda: ' . $e->getMessage()); 
        }
    }
}