<?php

namespace App\Backend\Controller;

use App\Backend\Service\OrderService;
use App\Backend\Utils\PatternText;
use InvalidArgumentException;

class OrderController {

    private OrderService $service;

    public function __construct(OrderService $service) 
    {
        $this->service = $service;
    }

    public function listWithItems(int $id): void 
    {
        if ($result = $this->service->getWithItems($id)) {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function listByPaymentMethod(string $paymentMethod): void 
    {
        if ($result = $this->service->getByPaymentMethod($paymentMethod)) {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function listAll(): void 
    {
        if ($result = $this->service->getAll()) {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function show(int $id): void 
    {
        if ($result = $this->service->getOrder($id)) {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function create(): void
    {
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

        if ($result = $this->service->createOrder(
            (int)$data['sale_id'],
            (string)$data['payment_method']
        )) {
            PatternText::handleResponse($result['status'], $result['message'], $result->toArray()['content'], 200);
        } else {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 404);
        }   
    }

    public function addItem(int $orderId): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $requiredFields = ['product_id', 'quantity'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("Campo obrigatório faltando: {$field}");
            }
        }

        if ($result = $this->service->addItemToOrder(
            $orderId,
            (int)$data['product_id'],
            (int)$data['quantity']
        )) {
            PatternText::handleResponse($result['status'], $result['message'], $result->toArray()['content'], 200);
        } else {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function updateOrder(int $id, array $data): void 
    {
        $data = json_decode(file_get_contents('php://input', true));

        if (!isset($data['status'])) {
            throw new InvalidArgumentException('Status não informado');
        }

        if ($result = $this->service->updateOrderStatus($id, (string)$data['status'])) {
            PatternText::handleResponse($result['status'], $result['message'], $result->toArray()['content'], 200);
        } else {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function delete(int $id): void 
    {
        if ($result = $this->service->deleteOrder($id)) {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

}