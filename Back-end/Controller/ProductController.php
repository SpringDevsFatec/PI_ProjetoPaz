<?php

namespace App\Backend\Controller;

use App\Backend\Service\ProductService;
use App\Backend\Libs\AuthMiddleware;
use DomainException;
use Dotenv\Exception\InvalidFileException;
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

    public function listProductsByName(string $request): void
    {
        $searchTerm = $request['searchTerm'] ?? '';

        try {
            $products = $this->service->getProductsByName($searchTerm);
            $this->jsonResponse($products, 200, empty($products) ? 'Nenhum produto encontrado' : null);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar produtos: ' . $e->getMessage());
        }
    }

    public function listProductsByCategory(string $category): void 
    {
        try {
            $products = $this->service->getProductByCategory($category);
            $this->jsonResponse($products, 200, empty($products) ? 'Nenhum produto encontrado' : null);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar produtos: ' . $e->getMessage());
        }
    }

    public function listProductsByCost(float $cost): void
    {
        try {
            $products = $this->service->getProductByCost($cost);
            $this->jsonResponse($products, 200, empty($products) ? 'Nenhum produto encontrado' : null);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar produtos: ' . $e->getMessage());
        }
    }

    public function listProductsByFavorite(): void
    {
        try {
            $products = $this->service->getProductByFavorite();
            $this->jsonResponse($products, 200, empty($products) ? 'Nenhum produto encontrado' : null);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar produtos: ' . $e->getMessage());
        }
    }

    public function searchByDonation(): void
    {
        try {
            $products = $this->service->getProductByDonation();
            $this->jsonResponse($products, 200, empty($products) ? 'Nenhum produto encontrado' : null);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar produtos: ' . $e->getMessage());
        }
    }

    public function listAll() {
        try {
            $products = $this->service->getAllProducts();
            $this->jsonResponse($products, 200, empty($products) ? 'Nenhum produto encontrado' : null);
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar produtos: ' . $e->getMessage());
        }
    }

    public function show(int $id): void
    {
        try {
            $product = $this->service->getProduct($id);
            if ($product === null) {
                $this->jsonResponse(null, 404, "Nenhum produto encontrado.");
            }
            $this->jsonResponse($product);

        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar produto: ' . $e->getMessage());
        }
    }

    public function create(): void
    {
        try {
            $data = json_decode(file_get_contents('php://input', true));

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException('JSON inválido');
            }

            $requiredFields = [
                'name',
                'cost_price',
                'sale_price',
                'category',
                'description',
                'is_donation'
            ];
            foreach ($requiredFields as $field) {
                if (!isset($data->field)) {
                    throw new InvalidArgumentException("Campo obrigatório faltando: {$field}");
                }
            }

            $product = $this->service->createProduct($data);
            $this->jsonResponse($product, 201, 'Produto criado com sucesso.');
            
        } catch (InvalidFileException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());

        } catch (DomainException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());

        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao criar produto: ' . $e->getMessage());
        }
    }

    public function update(int $id, array $data): void
    {
        try {
            $data = json_decode(file_get_contents('php://input', true));

            if (!isset($data['name'], $data['cost_price'], $data['sale_price'])) {
                throw new InvalidArgumentException('Campos obrigatórios não informados');
            }

            $product = $this->service->updateProduct($id, $data);
            $this->jsonResponse($product->toArray(), 200, 'Produto atualizado com sucesso.');

        } catch (InvalidFileException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());

        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());

        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao atualizar produto: ' . $e->getMessage());
        }
    }

    public function delete(int $id): void
    {
        try {
            $this->service->deleteProduct($id);
            $this->jsonResponse(null, 204, 'Produto excluído com sucesso.');
        
        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());

        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao excluir produto.');
        }
    }
}