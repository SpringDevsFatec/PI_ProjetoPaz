<?php

namespace App\Backend\Controller;

use App\Backend\Service\OrderItemService;
use App\Backend\Utils\Responses;
use InvalidArgumentException;

class OrderItemController {

    private OrderItemService $service;

    use Responses;

    public function __construct(OrderItemService $service) 
    {
        $this->service = $service;
    }

    public function listItemsWithProductDetails(int $orderId): void 
    {
        if ($result = $this->service->getItemsWithProductDetails($orderId)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function show(int $id): void 
    {
        if ($result = $this->service->getItem($id)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function create(): void
    {
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

        if ($result = $this->service->createItem($data)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function updateQuantity(int $id): void 
    {
        $data = json_decode(file_get_contents('php://input', true));

        if (!isset($data['quantity'])) {
            throw new InvalidArgumentException('Quantidade não informada');
        }

        if ($result = $this->service->updateItemQuantity($id, (int)$data['quantity'])) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function delete(int $id): void 
    {
        if ($result = $this->service->deleteItem($id)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }
}