<?php
namespace App\Backend\Service;

use App\Backend\Model\ProductModel;
use App\Backend\Model\SupplierModel;
use App\Backend\Repository\ProductRepository;
use App\Backend\Service\SupplierService;
use App\Backend\Utils\ImageUploader;
use App\Backend\Utils\PatternText;
use App\Backend\Utils\Responses;
use Exception;
use InvalidArgumentException;
use DomainException;


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
            return $this->buildResponse(false, 'Termo de pesquisa não pode ser vazio.', null);
        }

        try {
            $this->repository->beginTransaction();
            $response = $this->repository->searchByName(trim($searchTerm), $limit);
            $this->repository->commitTransaction();
            
            if ($response['status'] == true) {
                $formattedProducts = array_map([$this, 'ResolveProduct'], $response['content']);
                return $this->buildResponse(true, 'Conteúdo encontrado.', $formattedProducts);
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
                $formattedProducts = array_map([$this, 'ResolveProduct'], $response['content']);
                return $this->buildResponse(true, 'Conteúdo encontrado.', $formattedProducts);
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
                $formattedProducts = array_map([$this, 'ResolveProduct'], $response['content']);
                return $this->buildResponse(true, 'Conteúdo encontrado.', $formattedProducts);
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
               $formattedProducts = array_map([$this, 'ResolveProduct'], $response['content']);
                return $this->buildResponse(true, 'Conteúdo encontrado.', $formattedProducts);
            }

            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);

        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }
    public function getNotDonationProducts(): array { 
        try {
            $this->repository->beginTransaction();
            $response = $this->repository->findNotDonations();
            $this->repository->commitTransaction();
            
            if ($response['status'] == true) {
               $formattedProducts = array_map([$this, 'ResolveProduct'], $response['content']);
                return $this->buildResponse(true, 'Conteúdo encontrado.', $formattedProducts);
            }

            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);

        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    public function getNotFavoriteProducts(): array { 
        try {
            $this->repository->beginTransaction();
            $response = $this->repository->findNotFavorites();
            $this->repository->commitTransaction();
            
            if ($response['status'] == true) {
                $formattedProducts = array_map([$this, 'ResolveProduct'], $response['content']);
                return $this->buildResponse(true, 'Conteúdo encontrado.', $formattedProducts);
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
            $this->buildResponse(false, 'Produto não encontrado', null);
        }
        try {
            $this->repository->beginTransaction();
            $response = $productData;
            $this->repository->commitTransaction();
            
            if ($response['status'] == true) {
                // Format the product data
                $formattedProduct = $this->ResolveProduct($response['content']);
                // Return the formatted product data
                return $this->buildResponse(true, 'Conteúdo encontrado.', $formattedProduct);
            }

            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);

        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    public function getAllProductsActives(string $orderBy = 'name', string $order = 'ASC'): array
    {
        try {
            $this->repository->beginTransaction();
            $response = $this->repository->findAllActive($orderBy, $order);
            $this->repository->commitTransaction();
            
            if ($response['status'] == true) {
                $formattedProducts = array_map([$this, 'ResolveProduct'], $response['content']);
                return $this->buildResponse(true, 'Conteúdo encontrado.', $formattedProducts);
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
                $formattedProducts = array_map([$this, 'ResolveProduct'], $response['content']);
                return $this->buildResponse(true, 'Conteúdo encontrado.', $formattedProducts);
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
        $product->setStatus($data['status'] ?? 1);

        $existingData = $this->repository->existsToUpdate($product);
        if ($existingData['status'] === true) {
            return $this->buildResponse(false, 'Produto não existe.', $existingData['content']);
        }

        //update SupplierModel
        $supplier = new SupplierModel($data['idSupplier'], $data['namesupplier'], $data['location'], null);

        // send ProductModel to repository
        $this->repository->beginTransaction();

        try {
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
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            return $this->buildResponse(false, $e->getMessage(), null);
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
            'status' => $product->getStatus(),
            'supplier' => [ 
                "id" => $data['idSupplier'],
                "name" => $data['namesupplier'],
                "location" => $data['location']
            ],
        ]);
    }

    public function updateImgProduct($id, $data)
    {
        $response = $this->repository->find($id);
        if ($response['status'] === false) {
           return $this->buildResponse(false, 'Produto não existe.', null);
        }

        // Process the image
        $reponseImg = ImageUploader::base64ToS3Url($data, 'Product');
        if ($reponseImg['status'] === false) {
            return $this->buildResponse(false, 'Erro ao processar imagem: ' . $reponseImg['message'], null);
        }

        $product = new ProductModel();
        $product->setId($id);
        $product->setImgProduct($reponseImg['content']);
        $this->repository->beginTransaction();

        try {
            
            $result = $this->repository->updateImage($product);

            if ($result['status'] === true) {
                $this->repository->commitTransaction();
                return $this->buildResponse(true, 'Imagem atualizada com sucesso', $product->getImgProduct());
            } else {
                $this->repository->rollBackTransaction();
                return $this->buildResponse(false, 'Erro ao atualizar Image.', null);
            }

        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    public function inactivateProduct($id)
    {
        $response = $this->repository->find($id);
        if ($response['status'] === false) {
            throw new DomainException("Produto não encontrado");
        }

        $product = new ProductModel();
        $product->setId($id);
        $product->setStatus(0);
        $this->repository->beginTransaction();

        try {
            $result = $this->repository->updateStatus($product);

            if ($result['status'] === true) {
                $this->repository->commitTransaction();
                return $this->buildResponse(true, 'Status atualizado com sucesso', null);
            } else {
                $this->repository->rollBackTransaction();
                return $this->buildResponse(false, 'Erro ao atualizar.', null);
            }

        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    private function ResolveProduct(array $product): array
{
    return [
        'id' => (string) $product['idproduct'],
        'name' => $product['nameproduct'],
        'cost_price' => $product['cost_price'],
        'sale_price' => $product['sale_price'],
        'category' => $product['category'],
        'description' => $product['description'],
        'is_favorite' => (string) $product['is_favorite'],
        'is_donation' => (string) $product['donation'],
        'img_product' => $product['img_product'],
        'status' => (string) $product['status'],
        'supplier' => [
            'id' => (int) $product['idsupplier'],
            'name' => $product['namesupplier'],
            'location' => $product['location'],
        ],
    ];
}

}