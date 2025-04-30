<?php
namespace App\Backend\Service;

use App\Backend\Model\OrderImage;
use App\Backend\Repository\OrderImageRepository;

use Exception;

class OrderImageService {
    
    private $repository;

    public function __construct(OrderImageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create($data) {
        if (!isset($data->order_id, $data->image_path)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos"]);
            return;
        }

        $orderImage = new OrderImage();
        $orderImage->setOrderId($data->order_id);
        $orderImage->setImagePath($data->image_path);

        if ($this->repository->insertOrderImage($orderImage)) {
            http_response_code(201);
            echo json_encode(["message" => "Imagem inserida com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao inserir imagem."]);
        }
    }

    public function read($id = null) {
        if ($id) {
            $result = $this->repository->getById($id);
            $status = $result ? 200 : 404;
        } else {
            $result = $this->repository->getAllOrderImages();
            unset($orderImage);
            $status = !empty($result) ? 200 : 404;
        }

        http_response_code($status);
        echo json_encode($result ?: ["message" => "Nenhuma imagem encontrado."]);
    }

    public function update($data) {
        if (!isset($data->id, $data->order_id, $data->image_path)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos"]);
            return;
        }

        $orderImage = new OrderImage();
        $orderImage->setId($data->id);
        $orderImage->setOrderId($data->order_id);
        $orderImage->setImagePath($data->image_path);

        if ($this->repository->updateOrderImage($orderImage)) {
            http_response_code(201);
            echo json_encode(["message" => "Imagem atualizada com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao atualizar imagem."]);
        }
    }

    public function delete($id) {
        if ($this->repository->deleteOrderImage($id)) {
            http_response_code(200);
            echo json_encode(["message" => "Imagem excluída com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao excluir imagem."]);
        }
    }
}