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

    public function listSalesByDate(DateTimeInterface $date): void 
    {
        try {
            $sales = $this->service->getByDate($date);
            $this->jsonResponse($sales, 200, empty($sales) ? 'Nenhuma venda encontrada' : null);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar vendas: ' . $e->getMessage());
        }
    }

    public function listBySeller(int $sellerId): void
    {
        try {
            $status = $_GET['status'] ?? null;
            $sales = $this->service->getSalesBySeller($sellerId, $status);
            $this->jsonResponse($sales, 200, empty($sales) ? 'Nenhuma venda encontrada' : null);
        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao listar vendas');
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

    public function getSaleById(int $id): void 
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

    public function show(int $id): void 
    {
        try {
            $sale = $this->service->getSaleDetails($id);
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
                throw new InvalidArgumentException('Formato JSON inválido');
            }

            if (empty($data['seller_id'])) {
                throw new InvalidArgumentException('ID do vendedor é obrigatório');
            }

            $sale = $this->service->createSale((int)$data['seller_id']);
            $this->jsonResponse($sale->toArray(), 201, 'Venda criada com sucesso');
            
        } catch (InvalidArgumentException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());
            
        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());

        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao criar venda');
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

            $sale = $this->service->addOrderToSale($saleId, (int)$data['order_id']);
            $this->jsonResponse($sale->toArray(), 200, 'Pedido adicionado a venda');
            
        } catch (InvalidArgumentException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());

        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());

        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao adicionar pedido');
        }
    }

    public function complete(int $id): void 
    {
        try {
            $sale = $this->service->completeSale($id);
            $this->jsonResponse($sale->toArray(), 200, 'Venda concluída com sucesso');

        } catch (DomainException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());

        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao concluir venda');
        }
    }

    public function cancel(int $id): void 
    {
        try {
            $sale = $this->service->cancelSale($id);
            $this->jsonResponse($sale->toArray(), 200, 'Status atualizado');

        } catch (DomainException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());

        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao cancelar venda');
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
            $this->jsonResponse(null, 500, 'Erro ao excluir venda'); 
        }
    }
}