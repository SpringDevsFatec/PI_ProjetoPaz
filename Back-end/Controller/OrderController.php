<?php

namespace App\Backend\Controller;

use App\Backend\Service\OrderService;

use DomainException;
use Exception;
use InvalidArgumentException;
use Dotenv\Exception\InvalidFileException;

class OrderController {

    private OrderService $service;

    public function __construct(OrderService $service) 
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

    public function listWithItems(int $id): void 
    {
        try {
            $orders = $this->service->getWithItems($id);
            $this->jsonResponse($orders, 200, empty($orders) ? 'Nenhum pedido encontrado' : null);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar pedidos: ' . $e->getMessage());
        }
    }

    public function listByPaymentMethod(string $paymentMethod): void 
    {
        try {
            $orders = $this->service->getByPaymentMethod($paymentMethod);
            $this->jsonResponse($orders, 200, empty($orders) ? 'Nenhum pedido encontrado' : null);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar pedidos');
        }
    }

    public function listAll(): void 
    {
        try {
            $orders = $this->service->getAll();
            $this->jsonResponse($orders, 200, empty($orders) ? 'Nenhum pedido encontrado' : null);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar pedidos');
        }
    }

    public function show(int $id): void 
    {
        try {
            $order = $this->service->getOrder($id);
            if ($order === null) {
                $this->jsonResponse(null, 404, 'Pedido não encontrado');
            }
            $this->jsonResponse($order);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar pedido');
        }
    }

    public function create(): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException('JSON inválido');
            }

            $requiredFields = ['sale_id', 'payment_method'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new InvalidArgumentException("Campo obrigatório faltando: {$field}");
                }
            }

            $order = $this->service->createOrder(
                (int)$data['sale_id'],
                (string)$data['payment_method']
            );

            $this->jsonResponse($order->toArray(), 201, 'Pedido criado com sucesso');
            
        } catch (InvalidArgumentException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());
            
        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());

        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao criar pedido: ' . $e->getMessage());
        }
    }

    public function addItem(int $orderId): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $requiredFields = ['product_id', 'quantity'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new InvalidArgumentException("Campo obrigatório faltando: {$field}");
                }
            }

            $order = $this->service->addItemToOrder(
                $orderId,
                (int)$data['product_id'],
                (int)$data['quantity']
            );

            $this->jsonResponse($order->toArray(), 200, 'Item adicionado ao pedido');
            
        } catch (InvalidArgumentException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());

        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());

        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao adicionar item: ' . $e->getMessage());
        }
    }

    public function updateOrder(int $id, array $data): void 
    {
        try {
            $data = json_decode(file_get_contents('php://input', true));

            if (!isset($data['status'])) {
                throw new InvalidArgumentException('Status não informado');
            }

            $order = $this->service->updateOrderStatus($id, (string)$data['status']);
            $this->jsonResponse($order->toArray(), 200, 'Status atualizado');

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
            $this->service->deleteOrder($id);
            $this->jsonResponse(null, 204, 'Pedido excluído com sucesso.');

        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());

        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao remover pedido: ' . $e->getMessage()); 
        }
    }

}