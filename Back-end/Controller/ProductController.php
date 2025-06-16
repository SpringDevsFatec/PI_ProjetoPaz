<?php

namespace App\Backend\Controller;

use App\Backend\Service\ProductService;
use App\Backend\Libs\AuthMiddleware;
use App\Backend\Utils\Responses;
use InvalidArgumentException;

class ProductController {

    use Responses;

    private ProductService $service;

    public function __construct(ProductService $service) 
    {
        $this->service = $service;
    }

    public function searchByName(): void
    {
        $searchTerm = $_GET['q'] ?? '';
        if ($result = $this->service->searchProductsByName($searchTerm)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function listByCategory(string $category): void 
    {
        if ($result = $this->service->getProductsByCategory($category)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function listFavorites(): void
    {
        if ($result = $this->service->getFavoriteProducts()) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function listDonations(): void
    {
        if ($result = $this->service->getDonationProducts()) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function listAllActive(): void
    {
        $orderBy = $_GET['orderBy'] ?? 'name';
        $order = $_GET['order'] ?? 'ASC';
            
        if ($result = $this->service->getAllProductsActives($orderBy, $order)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function listAll(): void
    {
        $orderBy = $_GET['orderBy'] ?? 'name';
        $order = $_GET['order'] ?? 'ASC';
            
        if ($result = $this->service->getAllProducts($orderBy, $order)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function show(int $id): void
    {
        if ($result = $this->service->getProduct($id)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function createProduct(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('JSON inválido');
        }
        
        if ($result = $this->service->createProduct($data)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }

    }

    public function updateProduct(int $id): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('JSON inválido');
        }
        
        if ($result = $this->service->updateProduct($id, $data)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    public function inactivateProduct($id)
    {
        if ($result = $this->service->inactivateProduct($id)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }
}