<?php
namespace App\Backend\Service;

use App\Backend\Model\Seller;
use App\Backend\Repository\SellerRepository;

use Exception;
use DateTime;

class SellerService {
    
    private $repository;

    public function __construct(SellerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create($data) {
        if (!isset($data->user_id, $data->sale_id, $data->date_create)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos"]);
            return;
        }

        $seller = new Seller();
        $seller->setUserId($data->user_id);
        $seller->setSaleId($data->sale_id);
        $seller->setDateCreate(new DateTime());

        if ($this->repository->insertSeller($seller)) {
            http_response_code(201);
            echo json_encode(["message" => "Vendedor criado com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao criar vendedor."]);
        }
    }

    public function read($id = null) {
        if ($id) {
            $result = $this->repository->getSellerById($id);
            $status = $result ? 200 : 404;
        } else {
            $result = $this->repository->getAllSellers();
            unset($seller);
            $status = !empty($result) ? 200 : 404;
        }

        http_response_code($status);
        echo json_encode($result ?: ["message" => "Nenhum vendedor encontrado."]);
    }
}