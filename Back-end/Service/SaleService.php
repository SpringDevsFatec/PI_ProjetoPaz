<?php
namespace App\Backend\Service;

use App\Backend\Model\Sale;
use App\Backend\Repository\SaleRepository;

use Exception;
use DateTime;

class SaleService {
    
    private $repository;

    public function __construct(SaleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create($data) {
        if (!isset($data->seller_id)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos"]);
            return;
        }

        $sale = new Sale();
        $sale->setSellerId($data->seller_id);
        $sale->setTotal(0);
        $sale->setStatus(0);
        $sale->setDateCreate(new DateTime());

        if ($this->repository->startSale($sale)) {
            http_response_code(201);
            echo json_encode(["message" => "Venda iniciada com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao iniciar venda."]);
        }
    }

    public function read($id = null) {
        if ($id) {
            $result = $this->repository->getById($id);
            $status = $result ? 200 : 404;
        } else {
            $result = $this->repository->getAllSales();
            unset($sale);
            $status = !empty($result) ? 200 : 404;
        }

        http_response_code($status);
        echo json_encode($result ?: ["message" => "Nenhuma venda encontrada."]);
    }

    public function finishSale($data) {
        if (!isset($data->id, $data->seller_id, $data->total)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos"]);
            return;
        }

        $sale = new Sale();
        $sale->setId($data->id);
        $sale->setSellerId($data->seller_id);
        $sale->setTotal($data->total);
        $sale->setStatus(1);

        if ($this->repository->updateStatusSale($sale)) {
            http_response_code(201);
            echo json_encode(["message" => "Status da Venda atualizada com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao atualizar status."]);
        }
    }

    public function delete($id) {
        if ($this->repository->deleteSale($id)) {
            http_response_code(200);
            echo json_encode(["message" => "Venda excluída com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao excluir venda."]);
        }
    }
}