<?php
namespace App\Backend\Service;

use App\Backend\Model\ProductModel;
use App\Backend\Repository\ProductRepository;
use App\Backend\Repository\SupplierRepository;

use DateTime;
use InvalidArgumentException;
use DomainException;

class ProductService {
    
    private $repository;
    private $supplierRepository;

    public function __construct(
        ProductRepository $repository,
        SupplierRepository $supplierRepository
    ) {
        $this->repository = $repository;
        $this->supplierRepository = $supplierRepository;
    }

    public function searchProductsByName(string $searchTerm, int $limit = 10): array
    {
        if (empty(trim($searchTerm))) {
            throw new InvalidArgumentException("Termo de pesquisa não pode ser vazio");
        }

        return $this->repository->searchByName(trim($searchTerm), $limit);
    }

    public function getProductsByCategory(string $category): array
    {
        $validCategories = ['Alimento', 'Bebida', 'Cozinha', 'Livros', 'Outros'];
        if (!in_array($category, $validCategories)) {
            throw new InvalidArgumentException("Categoria inválida");
        }

        return $this->repository->findByCategory($category);
    }

    public function getFavoriteProducts(): array { return $this->repository->findFavorites(); }

    public function getDonationProducts(): array { return $this->repository->findDonations(); }
    
    public function getProduct(int $id): ProductModel
    { 
        $productData = $this->repository->find($id);
        if (!$productData) {
            throw new DomainException("Produto não encontrado");
        }

        return $this->hydrateProduct($productData);
    }

    public function getAllProducts(string $orderBy = 'name', string $order = 'ASC'): array
    {
        return $this->repository->findAll($orderBy, $order);
    }

    public function createProduct(array $data): ProductModel
    {
        $this->validateProductData($data);

        if (!$this->supplierRepository->getSupplierById($data['supplier_id'])) {
            throw new DomainException("Fornecedor não encontrado");
        }

        $ProductModel = new ProductModel(
            name: $this->sanitizeString($data['name']),
            costPrice: (float)$data['cost_price'],
            salePrice: (float)$data['sale_price'],
            category: $this->sanitizeString($data['category']),
            description: $this->sanitizeString($data['description'] ?? ''),
            isFavorite: (bool)($data['is_favorite'] ?? false),
            isDonation: (bool)($data['is_donation'] ?? false),
            id: null,
            supplierId: (int)$data['supplier_id'],
            createdAt: new DateTime(),
            updatedAt: new DateTime()
        );

        $productId = $this->repository->save($ProductModel);
        $ProductModel->setId($productId);

        return $ProductModel;
    }

    public function updateProduct(int $id, array $data): ProductModel
    {
        $existingData = $this->repository->find($id);
        if (!$existingData) {
            throw new DomainException("Produto não encontrado");
        }

        if (isset($data['cost_price']) && $data['cost_price'] < 0) {
            throw new InvalidArgumentException("Preço de custo não pode ser negativo");
        }

        if (isset($data['sale_price']) && $data['sale_price'] <= 0) {
            throw new InvalidArgumentException("Preço de venda deve ser maior que zero");
        }

        $updateData = array_merge($existingData, $data);
        
        if (($updateData['is_donation'] ?? false)) {
            $updateData['cost_price'] = 0;
        }

        $ProductModel = $this->hydrateProduct($updateData);
        $ProductModel->setUpdatedAt(new DateTime());

        if (!$this->repository->update($ProductModel)) {
            throw new DomainException("Falha ao atualizar produto");
        }

        return $ProductModel;
    }

    public function deleteProduct(int $id): void 
    {
        $ProductModel = $this->repository->find($id);
        if (!$ProductModel) {
            throw new DomainException("Produto não encontrado");
        }

        if (!$this->repository->delete($id)) {
            throw new DomainException("Falha ao remover produto.");
        } 
    }

    private function validateProductData(array $data): void
    {
        $requiredFields = ['name', 'cost_price', 'sale_price', 'category', 'supplier_id'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new InvalidArgumentException("Campo obrigatório faltando: {$field}");
            }
        }

        if (strlen(trim($data['name'])) === 0) {
            throw new InvalidArgumentException("Nome do produto não pode ser vazio");
        }

        if ($data['cost_price'] < 0) {
            throw new InvalidArgumentException("Preço de custo não pode ser negativo");
        }

        if ($data['sale_price'] <= 0) {
            throw new InvalidArgumentException("Preço de venda deve ser maior que zero");
        }

        if (!$data['is_donation'] && $data['sale_price'] < $data['cost_price']) {
            throw new DomainException("Preço de venda não pode ser menor que o custo");
        }
    }

     private function hydrateProduct(array $productData): ProductModel
    {
        return new ProductModel(
            name: $productData['name'],
            costPrice: (float)$productData['cost_price'],
            salePrice: (float)$productData['sale_price'],
            category: $productData['category'],
            description: $productData['description'],
            isFavorite: (bool)$productData['is_favorite'],
            isDonation: (bool)$productData['is_donation'],
            id: (int)$productData['id'],
            supplierId: (int)$productData['supplier_id'],
            createdAt: new DateTime($productData['created_at']),
            updatedAt: new DateTime($productData['updated_at'])
        );
    }

    /**
     * Cleans strings by removing extra spaces and HTML tags
     */
    private function sanitizeString(string $input): string
    {
        return trim(strip_tags($input));
    }
}