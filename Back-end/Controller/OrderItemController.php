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

    public function createOrderItem($orderId): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('JSON inválido');
        }
        if ($result = $this->service->createItem($data, $orderId)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }
}