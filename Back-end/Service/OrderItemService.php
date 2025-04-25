<?php
namespace App\Backend\Service;

use App\Backend\Model\OrderItem;
use App\Backend\Repository\OrderItemRepository;

use Exception;

class OrderItemService {
    
    private $repository;

    public function __construct(OrderItemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create($data) {
        if (!isset($data->product_id, $data->order_id, $data->quantity, $data->unit_price)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos"]);
            return;
        }

        $orderItem = new OrderItem();
        $orderItem->setProductId($data->product_id);
        $orderItem->setOrderId($data->order_id);
        $orderItem->setQuantity($data->quantity);
        $orderItem->setUnitPrice($data->unit_price);

        if ($this->repository->insertOrderItem($orderItem)) {
            http_response_code(201);
            echo json_encode(["message" => "Item inserido com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao inserir item."]);
        }
    }

    public function read($id = null) {
        if ($id) {
            $result = $this->repository->getOrderItemById($id);
            $status = $result ? 200 : 404;
        } else {
            $result = $this->repository->getAllOrderItems();
            unset($orderItem);
            $status = !empty($result) ? 200 : 404;
        }

        http_response_code($status);
        echo json_encode($result ?: ["message" => "Nenhuma item encontrado."]);
    }

    public function update($data) {
        if (!isset($data->id, $data->product_id, $data->order_id, $data->quantity, $data->unit_price)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos"]);
            return;
        }

        $orderItem = new OrderItem();
        $orderItem->setId($data->id);
        $orderItem->setProductId($data->product_id);
        $orderItem->setOrderId($data->order_id);
        $orderItem->setQuantity($data->quantity);
        $orderItem->setUnitPrice($data->unit_price);

        if ($this->repository->updateOrderItem($orderItem)) {
            http_response_code(201);
            echo json_encode(["message" => "Item atualizado com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao atualizar item."]);
        }
    }
}