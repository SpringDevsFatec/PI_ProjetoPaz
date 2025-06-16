<?php
namespace App\Backend\Service;

use App\Backend\Model\ProductModel;
use App\Backend\Model\SupplierModel;
use App\Backend\Repository\ProductRepository;
use App\Backend\Service\SupplierService;
use App\Backend\Utils\ConvertBase64;
use App\Backend\Utils\ImageUploader;
use App\Backend\Utils\PatternText;
use App\Backend\Utils\Responses;
use Exception;
use DateTime;
use InvalidArgumentException;
use DomainException;
use GuzzleHttp\Psr7\UploadedFile;

class ProductService {
    
    use Responses;


    // use Trait Responses;
    use Responses;
    
    private $repository;
    private $supplierService;

    public function __construct(
        ProductRepository $repository,
        SupplierService $supplierService
    ) {
        $this->repository = $repository;
        $this->supplierService = $supplierService;
    }

    public function searchProductsByName(string $searchTerm, int $limit = 10): array
    {
        if (empty(trim($searchTerm))) {
            throw new InvalidArgumentException("Termo de pesquisa não pode ser vazio");
        }

        try {
            $this->repository->beginTransaction();
            $response = $this->repository->searchByName(trim($searchTerm), $limit);
            $this->repository->commitTransaction();
            
            if ($response['status'] == true) {
                return $this->buildResponse(true, 'Conteúdo encontrado.', $response['content']);
            }

            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);

        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    public function getProductsByCategory(string $category): array
    {
        $validCategories = ['Alimento', 'Bebida', 'Cozinha', 'Livros', 'Outros'];
        if (!in_array($category, $validCategories)) {
            throw new InvalidArgumentException("Categoria inválida");
        }

        try {
            $this->repository->beginTransaction();
            $response = $this->repository->findByCategory($category);
            $this->repository->commitTransaction();
            
            if ($response['status'] == true) {
                return $this->buildResponse(true, 'Conteúdo encontrado.', $response['content']);
            }

            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);

        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    public function getFavoriteProducts(): array { 
        try {
            $this->repository->beginTransaction();
            $response = $this->repository->findFavorites();
            $this->repository->commitTransaction();
            
            if ($response['status'] == true) {
                return $this->buildResponse(true, 'Conteúdo encontrado.', $response['content']);
            }

            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);

        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    public function getDonationProducts(): array { 
        try {
            $this->repository->beginTransaction();
            $response = $this->repository->findDonations();
            $this->repository->commitTransaction();
            
            if ($response['status'] == true) {
                return $this->buildResponse(true, 'Conteúdo encontrado.', $response['content']);
            }

            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);

        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }
    
    public function getProduct(int $id): array
    { 
        $productData = $this->repository->find($id);
        if (!$productData) {
            throw new DomainException("Produto não encontrado");
        }
        try {
            $this->repository->beginTransaction();
            $response = $productData;
            $this->repository->commitTransaction();
            
            if ($response['status'] == true) {
                return $this->buildResponse(true, 'Conteúdo encontrado.', $response['content']);
            }

            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);

        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    public function getAllProducts(string $orderBy = 'name', string $order = 'ASC'): array
    {
        try {
            $this->repository->beginTransaction();
            $response = $this->repository->findAll($orderBy, $order);
            $this->repository->commitTransaction();
            
            if ($response['status'] == true) {
                return $this->buildResponse(true, 'Conteúdo encontrado.', $response['content']);
            }

            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);

        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    public function createProduct( $data)
    {
        //Patterned data
        PatternText::validateProductData($data);
        PatternText::processText($data);


        //create SupplierModel
        $supplier = new SupplierModel(null, $data['namesupplier'],$data['location'],null);
        $ReponseSupplier = $this->supplierService->createSupplier($supplier);

        //verify if supplier was created
        if ($ReponseSupplier['status'] == true) {
            if (isset($ReponseSupplier['content']['id'])) {
                $data['supplier_id'] = $ReponseSupplier['content']['id'];
            } else {
            return $this->buildResponse(false, 'Id não retornado do Supplier! ', null);
            }
        }else {
            return $this->buildResponse(false, 'erro ao criar Supplier', null);
        }

        // get image by Body       

        $reponseImg = ImageUploader::base64ToS3Url($data, 'Product');
        
         if ($reponseImg['status'] == false) {
             return $this->buildResponse(false, 'Erro ao processar imagem: ' . $reponseImg['message'], null);
         }

        $data['link_image'] = $reponseImg['content'];

        // Finally create Product of fact
         //create ProductModel
    
        $ProductModel = new ProductModel();
        $ProductModel->setName($data['nameproduct']);
        $ProductModel->setCostPrice($data['cost_price']);
        $ProductModel->setSalePrice($data['sale_price']);
        $ProductModel->setCategory($data['category']);
        $ProductModel->setDescription($data['description'] ?? null);
        $ProductModel->setIsFavorite(($data['is_favorite'] ?? 0));
        $ProductModel->setIsDonation(($data['donation'] ?? 0));
        $ProductModel->setSupplierId($data['supplier_id']);
        $ProductModel->setImgProduct($data['link_image']);

        // send ProductModel to repository
        try {
            $this->repository->beginTransaction();
             $product = $this->repository->createProduct($ProductModel);
             if ($product['status'] === true) {
                $ProductModel->setId($product['content']->getId());
            } else {
                return $this->buildResponse(false, 'Erro ao criar produto', null);
            }
            $this->repository->commitTransaction();
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }

        // Return array conform data waited
        return $this->buildResponse(true, 'Produto criado com sucesso', [
            'id' => $ProductModel->getId(),
            'name' => $ProductModel->getName(),
            'cost_price' => $ProductModel->getCostPrice(),
            'sale_price' => $ProductModel->getSalePrice(),
            'category' => $ProductModel->getCategory(),
            'description' => $ProductModel->getDescription(),
            'is_favorite' => $ProductModel->getFavorite(),
            'is_donation' => $ProductModel->getDonation(),
            'img_product' => $ProductModel->getImgProduct(),
            'supplier' => [ 
                "id" => $ProductModel->getSupplierId(),
                "name" => $data['namesupplier'],
                "location" => $data['location']
            ],
        ]); 

    }

    public function updateProduct($id, $data)
    {
        // Validate data
        PatternText::validateProductData($data);

        // Standardize data
        PatternText::processText($data);

        // check if product exists
        $product = new ProductModel();
        $product->setId($id);
        $product->setName($data['nameproduct']);
        $product->setCostPrice($data['cost_price']);
        $product->setSalePrice($data['sale_price']);
        $product->setCategory($data['category']);
        $product->setDescription($data['description'] ?? null);
        $product->setIsFavorite(($data['is_favorite'] ?? 0));
        $product->setIsDonation(($data['donation'] ?? 0));

        $existingData = $this->repository->existsToUpdate($product);
        if ($existingData['status'] === true) {
            return $this->buildResponse(false, 'Produto não existe.', $existingData['content']);
        }

        //update SupplierModel
        $supplier = new SupplierModel($data['idSupplier'], $data['namesupplier'], $data['location'], null);

        // send ProductModel to repository
        $this->repository->beginTransaction();

        $updatedProduct = $this->repository->update($product);
        
        if ($updatedProduct['status'] === true) {

            $updatedSupplier = $this->supplierService->updateSupplier($supplier);
            if ($updatedSupplier['status'] === false) {
                return $this->buildResponse(false, 'Erro ao atualizar fornecedor.', null);
            }
            $this->repository->commitTransaction();
        } else {
            $this->repository->rollBackTransaction();
            return $this->buildResponse(false, 'Erro ao Atualizar.', null);
        }

        // Return array conform data waited
        return $this->buildResponse(true, 'Atualizado com sucesso', [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'cost_price' => $product->getCostPrice(),
            'sale_price' => $product->getSalePrice(),
            'category' => $product->getCategory(),
            'description' => $product->getDescription(),
            'is_favorite' => $product->getFavorite(),
            'is_donation' => $product->getDonation(),
            'img_product' => $product->getImgProduct(),
            'supplier' => [ 
                "id" => $data['idSupplier'],
                "name" => $data['namesupplier'],
                "location" => $data['location']
            ],
        ]);
    }

    public function deleteProduct(int $id): void 
    {
        $product = $this->repository->find($id);
        if (!$product) {
            throw new DomainException("Produto não encontrado");
        }

        if (!$this->repository->delete($id)) {
            throw new DomainException("Falha ao remover produto.");
        } 
    }
}