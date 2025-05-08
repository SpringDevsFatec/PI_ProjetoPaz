<?php
namespace App\Backend\Service;

use App\Backend\Model\Product;
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

    public function getProductsByName(string $searchTerm): array
    {
        if (empty($searchTerm)) {
            return ['success' => false, 'message' => 'Termo de pesquisa vazio'];
        }
        
        return $this->repository->findByName($searchTerm);
    }

    public function getProductByCategory(string $category): array
    {
        return $this->repository->findByCategory($category);
    }

    public function getProductByCost(float $cost): array
    {
        return $this->repository->findByCost($cost);
    }

    public function getProductByFavorite(): array
    {
        return $this->repository->findByFavorite();
    }

    public function getProductByDonation(): array
    {
        return $this->repository->findByDonation();
    }
    
    public function getProduct(int $id): ?array
    {
        return $this->repository->find($id);
    }

    public function getAllProducts(): array
    {
        return $this->repository->findAll();
    }

    public function createProduct(array $data): Product
    {
        if (empty($data['name']) || 
            empty($data['cost_price']) ||
            empty($data['sale_price'])
            ) 
        {
            throw new InvalidArgumentException("Dados incompletos.");
        }

        $supplier = $this->supplierRepository->getSupplierById($data['supplier_id']);
        if(!$supplier) {
            throw new DomainException("Fornecedor não encontrado.");
        }

        $product = new Product(
            name: (string)$data['name'],
            costPrice: (float)$data['cost_price'],
            salePrice: (float)$data['sale_price'],
            category: $data['category'],
            description: $data['description'],
            isFavorite: $data['is_facorite'],
            isDonation: $data['is_donation'],
            id: null,
            supplierId: (int)$data['supplier_id'],
            createdAt: new DateTime(),
            updatedAt: new DateTime()
        );

        $productId = $this->repository->save($product);
        $product->setId($productId);

        return $product;
    }

    public function updateProduct(int $id, array $data): Product
    {
        if ($data['cost_price'] <= 0)
        {
            throw new DomainException("Preço de custo deve ser maior que zero.");
        }

        if ($this->repository->findByDonation($id))
        {
            $data['cost_price'] = 0;
        }

        if ($data['cost_price'] <= 0)
        {
            throw new DomainException("Preço de venda deve ser maior que zero.");
        }

        $existingItem = $this->repository->find($id);
        if (!$existingItem) {
            throw new DomainException("Produto não encontrado.");
        }

        $product = new Product(
            supplierId: (int)$existingItem['supplier_id'],
            name: (string)$existingItem['name'],
            costPrice: (float)$existingItem['cost_price'],
            salePrice: (float)$existingItem['sale_price'],
            category: $$existingItem['category'],
            description: $$existingItem['description'],
            isFavorite: $$existingItem['is_facorite'],
            isDonation: $$existingItem['is_donation'],
            id: (int)$existingItem['id'],
            createdAt: new DateTime($existingItem['created_at']),
            updatedAt: new DateTime()
        );

        $this->repository->update($product);

        return $product;
    }

    public function deleteProduct(int $id): void 
    {
        $product = $this->repository->find($id);
        if (!$product) {
            throw new DomainException("Pedido não encontrado");
        }

        if (!$this->repository->delete($id)) {
            throw new DomainException("Falha ao deletar pedido.");
        } 
    }
}