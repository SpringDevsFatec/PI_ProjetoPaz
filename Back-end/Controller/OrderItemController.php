<?php

namespace App\Backend\Controller;

use App\Backend\Model\OrderItem;
use App\Backend\Service\OrderItemService;

use DomainException;
use Dotenv\Exception\InvalidFileException;
use Exception;
use InvalidArgumentException;

class OrderItemController {

    private OrderItemService $service;

    public function __construct(OrderItemService $service) 
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

    public function listItemsWithProductDetails(int $orderId): void 
    {
        try {
            $items = $this->service->getItemsWithProductDetails($orderId);
            $this->jsonResponse($items, 200, empty($items) ? 'Nenhum item encontrado' : null);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar itens: ' . $e->getMessage());
        }
    }

    public function listByOrder(int $orderId): void 
    {
        try {
            $items = $this->service->getItemsByOrderId($orderId);
            $this->jsonResponse($items, 200, empty($items) ? 'Nenhum item encontrado' : null);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar itens do pedido');
        }
    }

    public function show(int $id): void 
    {
        try {
            $item = $this->service->getItem($id);
            if ($item === null) {
                $this->jsonResponse(null, 404, 'Item não encontrado');
            }
            $this->jsonResponse($item);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar item');
        }
    }

    public function create(): void
    {
        try {
            $data = json_decode(file_get_contents('php://input', true));

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException('JSON inválido');
            }

            $requiredFields = ['product_id', 'order_id', 'quantity'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    throw new InvalidArgumentException("Campo obrigatório faltando: {$field}");
                }
            }

            $item = $this->service->createItem($data);
            $this->jsonResponse($item->toArray(), 201, 'Item criado com sucesso');

        } catch (InvalidFileException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());
        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao criar item');
        }
    }

    public function updateQuantity(int $id): void 
    {
        try {
            $data = json_decode(file_get_contents('php://input', true));

            if (!isset($data['quantity'])) {
                throw new InvalidArgumentException('Quantidade não informada');
            }

            $item = $this->service->updateItemQuantity($id, (int)$data['quantity']);
            $this->jsonResponse($item->toArray(), 200, 'Quantidade atualizada');

        } catch (InvalidFileException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());
        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao atualizar quantidade');
        }
    }

    public function delete(int $id): void 
    {
        try {
            $this->service->deleteItem($id);
            $this->jsonResponse(null, 204);

        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao remover item'); 
        }
    }

}