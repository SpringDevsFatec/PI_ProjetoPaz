<?php

namespace App\Backend\Controller;

use App\Backend\Service\ProductService;
use App\Backend\Libs\AuthMiddleware;

use Exception;

class ProductController {

    private $service;

    public function __construct() {
        $this->service = new ProductService();
    }

    private function handleResponse($result, $successMessage = "Operação concluída com sucesso.") {
        if (!empty($result)) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(['status' => false, "message" => $successMessage]);
        }
    }

    public function searchProducts($request) {
        $searchTerm = $request['searchTerm'] ?? '';
        
        if (empty($searchTerm)) {
            return ['success' => false, 'message' => 'Termo de pesquisa vazio'];
        }

        try {
            $products = $this->service->searchProducts($searchTerm);
            return ['success' => true, 'data' => $products];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function searchByCategory($category) {
        $result = $this->service->searchByCategory($category);
        $this->handleResponse($result, "Nenhum produto encontrado.");
    }

    public function searchByCost($cost) {
        $result = $this->service->searchByCost($cost);
        $this->handleResponse($result, "Nenhum produto encontrado.");
    }

    public function searchByFavorite() {
        $result = $this->service->searchByFavorite();
        $this->handleResponse($result, "Nenhum produto encontrado.");
    }

    public function searchByDonation() {
        $result = $this->service->searchByDonation();
        $this->handleResponse($result, "Nenhum produto encontrado.");
    }

    public function readAll() {
        $result = $this->service->readAll();
        $this->handleResponse($result, "Nenhum produto encontrado.");
    }

    public function readById($id) {
        $result = $this->service->readById($id);
        $this->handleResponse($result, "Nenhum produto encontrado.");
    }

    public function create() {
        $data = json_decode(file_get_contents('php://input'));
        if (!isset(
            $data->supplier_id, 
            $data->name, 
            $data->cost_price, 
            $data->sale_price, 
            $data->description, 
            $data->is_favorite, 
            $data->category, 
            $data->is_donation
                )) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos"]);
            exit;
        }

        if ($this->service->create($data)) {
            http_response_code(201);
            echo json_encode(["message" => "Produto criado com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao criar produto."]);
        }
    }

    public function put($id) {
        if (!isset(
            $data->supplier_id,
            $data->name,
            $data->cost_price,
            $data->sale_price,
            $data->description,
            $data->is_favorite,
            $data->category,
            $data->is_donation,
                )) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos"]);
            exit;
        }

        if ($this->service->update($id, $data)) {
            http_response_code(201);
            echo json_encode(["message" => "Produto atualizado com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao atualizar produto."]);
        }
    }

}