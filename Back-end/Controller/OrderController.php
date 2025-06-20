<?php

namespace App\Backend\Controller;

use App\Backend\Service\OrderService;
use App\Backend\Utils\Responses;
use InvalidArgumentException;

class OrderController {

    use Responses;

    private OrderService $service;

    public function __construct(OrderService $service) 
    {
        $this->service = $service;
    }

    public function listWithItems(int $id): void 
    {
        if ($result = $this->service->getWithItems($id)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function listByPaymentMethod(string $paymentMethod): void 
    {
        if ($result = $this->service->getByPaymentMethod($paymentMethod)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function listAll(): void 
    {
        if ($result = $this->service->getAll()) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function show(int $id): void 
    {
        if ($result = $this->service->getOrder($id)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function createOrder($saleId): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('JSON inválido');
        }

        if ($result = $this->service->createOrder($saleId, $data)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function cancelOrder(int $id): void
    {
        if ($result = $this->service->cancelOrder($id)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

}