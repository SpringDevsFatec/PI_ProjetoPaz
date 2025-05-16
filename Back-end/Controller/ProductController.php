<?php

namespace App\Backend\Controller;

use App\Backend\Service\ProductService;
use App\Backend\Libs\AuthMiddleware;

use DomainException;
use Exception;
use InvalidArgumentException;

class ProductController {

    private ProductService $service;

    public function __construct(ProductService $service) 
    {
        $this->service = $service;
    }

    private function jsonResponse(
        mixed $data,
        int $statusCode = 200,
        ?string $message = null
    ): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');

        $response = [];
        if ($message) {
            $response['message'] = $message;
        }
        if ($data !== null) {
            $response['data'] = $data;
        }

        echo json_encode($response);
        exit;
    }

    public function searchByName(): void
    {
        try {
            $searchTerm = $_GET['q'] ?? '';
            $products = $this->service->searchProductsByName($searchTerm);
            $this->jsonResponse($products, 200, empty($products) ? 'Nenhum produto encontrado' : null);
        } catch (InvalidArgumentException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar produtos');
        }
    }

    public function listByCategory(string $category): void 
    {
        try {
            $products = $this->service->getProductsByCategory($category);
            $this->jsonResponse($products, 200, empty($products) ? 'Nenhum produto encontrado' : null);
        } catch (InvalidArgumentException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar produtos por categoria');
        }
    }

    public function listFavorites(): void
    {
        try {
            $products = $this->service->getFavoriteProducts();
            $this->jsonResponse($products, 200, empty($products) ? 'Nenhum produto favorito encontrado' : null);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar produtos favoritos');
        }
    }

    public function listDonations(): void
    {
        try {
            $products = $this->service->getDonationProducts();
            $this->jsonResponse($products, 200, empty($products) ? 'Nenhum produto de doação encontrado' : null);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar produtos de doação');
        }
    }

    public function listAll(): void
    {
        try {
            $orderBy = $_GET['orderBy'] ?? 'name';
            $order = $_GET['order'] ?? 'ASC';
            
            $products = $this->service->getAllProducts($orderBy, $order);
            $this->jsonResponse($products, 200, empty($products) ? 'Nenhum produto encontrado' : null);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao listar produtos');
        }
    }

    public function show(int $id): void
    {
        try {
            $product = $this->service->getProduct($id);
            $this->jsonResponse($product->toArray());
        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar produto');
        }
    }

    public function create(): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException('JSON inválido');
            }
            
            $product = $this->service->createProduct($data);
            $this->jsonResponse($product->toArray(), 201, 'Produto criado com sucesso');
            
        } catch (InvalidArgumentException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());
        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao criar produto');
        }
    }

    public function update(int $id): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException('JSON inválido');
            }
            
            $product = $this->service->updateProduct($id, $data);
            $this->jsonResponse($product->toArray(), 200, 'Produto atualizado com sucesso');
            
        } catch (InvalidArgumentException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());
        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao atualizar produto');
        }
    }

    public function delete(int $id): void
    {
        try {
            $this->service->deleteProduct($id);
            $this->jsonResponse(null, 204);
        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao remover produto');
        }
    }
}