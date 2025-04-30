<?php
namespace App\Backend\Service;

use App\Backend\Model\Order;
use App\Backend\Repository\OrderRepository;

use Exception;
use DateTime;

class OrderService {
    
    private $repository;

    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create($data) {
        if (!isset($data->sale_id, $data->payment_method, $data->date_create)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos"]);
            return;
        }

        $order = new Order();
        $order->setSaleId($data->sale_id);
        $order->setPaymentMethod($data->payment_method);
        $order->setDateCreate(new DateTime());

        if ($this->repository->insertOrder($order)) {
            http_response_code(201);
            echo json_encode(["message" => "Pedido criado com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao criar pedido."]);
        }
    }

    public function read($id = null) {
        if ($id) {
            $result = $this->repository->getById($id);
            $status = $result ? 200 : 404;
        } else {
            $result = $this->repository->getAllOrders();
            unset($order);
            $status = !empty($result) ? 200 : 404;
        }

        http_response_code($status);
        echo json_encode($result ?: ["message" => "Nenhum pedido encontrado."]);
    }

    public function update($data) {
        if (!isset($data->id, $data->sale_id, $data->payment_method)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos"]);
            return;
        }

        $order = new Order();
        $order->setId($data->id);
        $order->setSaleId($data->sale_id);
        $order->setPaymentMethod($data->payment_method);

        if ($this->repository->updateOrder($order)) {
            http_response_code(201);
            echo json_encode(["message" => "Pedido atualizado com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao atualizar pedido."]);
        }
    }

    public function delete($id) {
        if ($this->repository->deleteOrder($id)) {
            http_response_code(200);
            echo json_encode(["message" => "Pedido excluído com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao excluir pedido."]);
        }
    }
}