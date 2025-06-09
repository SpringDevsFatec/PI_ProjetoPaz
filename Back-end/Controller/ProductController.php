<?php

namespace App\Backend\Controller;

use App\Backend\Service\ProductService;
use App\Backend\Libs\AuthMiddleware;
use App\Backend\Utils\PatternText;
use InvalidArgumentException;

class ProductController {

    private ProductService $service;

    public function __construct(ProductService $service) 
    {
        $this->service = $service;
    }

    public function searchByName(): void
    {
        $searchTerm = $_GET['q'] ?? '';
        if ($result = $this->service->searchProductsByName($searchTerm)) {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function listByCategory(string $category): void 
    {
        if ($result = $this->service->getProductsByCategory($category)) {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function listFavorites(): void
    {
        if ($result = $this->service->getFavoriteProducts()) {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function listDonations(): void
    {
        if ($result = $this->service->getDonationProducts()) {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function listAll(): void
    {
        $orderBy = $_GET['orderBy'] ?? 'name';
        $order = $_GET['order'] ?? 'ASC';
            
        if ($result = $this->service->getAllProducts($orderBy, $order)) {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function show(int $id): void
    {
        if ($result = $this->service->getProduct($id)) {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function create(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('JSON inválido');
        }
        
        if ($result = $this->service->createProduct($data)) {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 404);
        }

    }

    public function update(int $id): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('JSON inválido');
        }
        
        if ($result = $this->service->updateProduct($id, $data)) {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function delete(int $id): void
    {
        if ($result = $this->service->deleteProduct($id)) {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            PatternText::handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }
}