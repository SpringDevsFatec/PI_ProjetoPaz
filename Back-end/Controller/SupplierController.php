<?php
namespace App\Backend\Controller;

use App\Backend\Service\SupplierService;
use App\Backend\Libs\AuthMiddleware;
use Exception;

class SupplierController {
    private $service;
    private $authMiddleware;

    public function __construct(SupplierService $supplierService) {
        $this->service = $supplierService;
        $this->authMiddleware = new AuthMiddleware();
    }

    private function handleResponse($result, $successMessage = "Operação concluída com sucesso.", $content = null, $http_response_header = null) {
        if ($result['status']) {
            http_response_code($http_response_header);
            echo json_encode(["status" => true, "message" => $successMessage, "content" => $content]);
        } else {
            http_response_code($http_response_header);
            echo json_encode(['status' => false, "message" => $result['message'], "content" => $content]);
        }
    }

    public function getSupplierById() {
        var_dump("Chegou!"); die;
        $this->authMiddleware->openToken();

        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->handleResponse(['status' => false, 'message' => 'ID do fornecedor não fornecido.'], "Erro: ID do fornecedor ausente.", null, 400);
            return;
        }

        if ($result = $this->service->getSupplierById($id)) {
            $this->handleResponse($result, $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse(['status' => false, 'message' => 'Fornecedor não encontrado.'], "Erro ao buscar fornecedor.", null, 404);
        }
    }

    public function getAllSuppliers() {
        $this->authMiddleware->openToken();

        if ($result = $this->service->getAllSuppliers()) {
            $this->handleResponse($result, $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse(['status' => false, 'message' => 'Nenhum fornecedor encontrado.'], "Erro ao buscar fornecedores.", null, 404);
        }
    }

    public function createSupplier() {
        $this->authMiddleware->openToken();

        $data = json_decode(file_get_contents('php://input'));

        if (!isset($data->name) || !isset($data->place)) {
            $this->handleResponse(['status' => false, 'message' => 'Nome e local do fornecedor são obrigatórios.'], "Erro: Dados incompletos.", null, 400);
            return;
        }

        if ($result = $this->service->createSupplier($data)) {
            $this->handleResponse($result, $result['message'], $result['content'], 201);
        } else {
            $this->handleResponse($result, "Erro ao criar fornecedor.", null, 400);
        }
    }

    public function updateSupplier() {
        $this->authMiddleware->openToken();

        $data = json_decode(file_get_contents('php://input'));

        if (!isset($data->id)) {
            $this->handleResponse(['status' => false, 'message' => 'ID do fornecedor é obrigatório para atualização.'], "Erro: ID do fornecedor ausente.", null, 400);
            return;
        }

        if ($result = $this->service->updateSupplier($data)) {
            $this->handleResponse($result, $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result, "Erro ao atualizar fornecedor.", null, 400);
        }
    }

    public function deleteSupplier() {
        $this->authMiddleware->openToken();

        $data = json_decode(file_get_contents('php://input'));
        $id = $data->id ?? null;

        if (!$id) {
            $this->handleResponse(['status' => false, 'message' => 'ID do fornecedor não fornecido para exclusão.'], "Erro: ID do fornecedor ausente.", null, 400);
            return;
        }

        if ($result = $this->service->deleteSupplier($id)) {
            $this->handleResponse($result, $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result, "Erro ao excluir fornecedor.", null, 400);
        }
    }
}