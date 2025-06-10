<?php
namespace App\Backend\Controller;

use App\Backend\Service\SupplierService;
use App\Backend\Libs\AuthMiddleware;
use App\Backend\Utils\PatternText;
use App\Backend\Model\SupplierModel;
use App\Backend\Utils\Responses;
use Exception;

class SupplierController {
    private $service;
    private $authMiddleware;

    // Use the Responses trait 
    use Responses;

    public function __construct(SupplierService $supplierService) {
        $this->service = $supplierService;
        $this->authMiddleware = new AuthMiddleware();
    }

    public function getSupplierById($id) {
         //verify if the user is authenticated
        $this->authMiddleware->openToken();

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
       //verify if the user is authenticated
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

        if (!isset($data->name) || !isset($data->location)) {
            $this->handleResponse(['status' => false, 'message' => 'Nome e local do fornecedor são obrigatórios.'], "Erro: Dados incompletos.", null, 400);
            return;
        }

        // Check PatternText for data consistency
        $data = PatternText::processText($data);

        // Create a new SupplierModel instance
        $supplierData = new \App\Backend\Model\SupplierModel();
        $supplierData->setName($data->name);
        $supplierData->setLocation($data->location);
        
        // Call the service to create the supplier

        if ($result = $this->service->createSupplier($supplierData)) {
            $this->handleResponse($result, $result['message'], $result['content'], 201);
        } else {
            $this->handleResponse($result, "Erro ao criar fornecedor.", null, 400);
        }
    }

    public function updateSupplier($id) {
        $this->authMiddleware->openToken();

        $data = json_decode(file_get_contents('php://input'));

       if (!isset($data->name) || !isset($data->location)) {
            $this->handleResponse(['status' => false, 'message' => 'Nome e local do fornecedor são obrigatórios.'], "Erro: Dados incompletos.", null, 400);
            return;
        }

        // Check PatternText for data consistency
        $data = PatternText::processText($data);

        // Create a new SupplierModel instance
        $supplierData = new \App\Backend\Model\SupplierModel();
        $supplierData->setId($id);
        $supplierData->setName($data->name);
        $supplierData->setLocation($data->location);
        
        // Call the service to create the supplier


        if ($result = $this->service->updateSupplier($supplierData)) {
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