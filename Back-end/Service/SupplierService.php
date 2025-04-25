<?php
namespace App\Backend\Service;

use App\Backend\Model\Supplier;
use App\Backend\Repository\SupplierRepository;

use Exception;
use DateTime;

class SupplierService {
    
    private $repository;

    public function __construct(SupplierRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create($data) {
        if (!isset($data->name, $data->address, $data->date_create)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos"]);
            return;
        }

        $supplier = new Supplier();
        $supplier->setName($data->name);
        $supplier->setAddress($data->address);
        $supplier->setDateCreate(new DateTime());

        if ($this->repository->insertSupplier($supplier)) {
            http_response_code(201);
            echo json_encode(["message" => "Fornecedor criado com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao criar fornecedor."]);
        }
    }

    public function read($id = null) {
        if ($id) {
            $result = $this->repository->getSupplierById($id);
            $status = $result ? 200 : 404;
        } else {
            $result = $this->repository->getAllSuppliers();
            unset($supplier);
            $status = !empty($result) ? 200 : 404;
        }

        http_response_code($status);
        echo json_encode($result ?: ["message" => "Nenhum fornecedor encontrado."]);
    }

    public function update($data) {
        if (!isset($data->id, $data->name, $data->address, $data->date_create)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos"]);
            return;
        }

        $supplier = new Supplier();
        $supplier->setId($data->id);
        $supplier->setName($data->name);
        $supplier->setAddress($data->address);

        if ($this->repository->updateSupplier($supplier)) {
            http_response_code(201);
            echo json_encode(["message" => "Fornecedor atualizado com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao atualizar fornecedor."]);
        }
    }

    public function delete($id) {
        if ($this->repository->deleteSupplier($id)) {
            http_response_code(200);
            echo json_encode(["message" => "Fornecedor excluído com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao excluir fornecedor."]);
        }
    }
}