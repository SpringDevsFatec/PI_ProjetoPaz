<?php
namespace App\Backend\Service;

use App\Backend\Model\ProductModel;
use App\Backend\Model\SupplierModel;
use App\Backend\Repository\ProductRepository;
use App\Backend\Service\SupplierService;
use App\Backend\Utils\ConvertBase64;
use App\Backend\Utils\PatternText;
use App\Backend\Utils\Responses;
use Exception;
use DateTime;
use InvalidArgumentException;
use DomainException;

class ProductService {

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
            
            $reponse = $this->repository->searchByName(trim($searchTerm), $limit);
            if ($reponse['status'] == true) {
                return [
                    'status' => true,
                    'message' => 'Conteúdo encontrado.',
                    'content' => $reponse['product']
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Nenhum conteúdo encontrado.',
                    'content' => null
                ];
            }
            
            $this->repository->commitTransaction();
            
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
            
            $reponse = $this->repository->findByCategory($category);
            if ($reponse['status'] == true) {
                return [
                    'status' => true,
                    'message' => 'Conteúdo encontrado.',
                    'content' => $reponse['product']
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Nenhum conteúdo encontrado.',
                    'content' => null
                ];
            }
            
            $this->repository->commitTransaction();
            
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    public function getFavoriteProducts(): array { 
        try {
            $this->repository->beginTransaction();
            
            $reponse = $this->repository->findFavorites();
            if ($reponse['status'] == true) {
                return [
                    'status' => true,
                    'message' => 'Conteúdo encontrado.',
                    'content' => $reponse['product']
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Nenhum conteúdo encontrado.',
                    'content' => null
                ];
            }
            
            $this->repository->commitTransaction();
            
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    public function getDonationProducts(): array { 
        try {
            $this->repository->beginTransaction();
            
            $reponse = $this->repository->findDonations();
            if ($reponse['status'] == true) {
                return [
                    'status' => true,
                    'message' => 'Conteúdo encontrado.',
                    'content' => $reponse['product']
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Nenhum conteúdo encontrado.',
                    'content' => null
                ];
            }
            
            $this->repository->commitTransaction();
            
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
            
            $reponse = $productData;
            if ($reponse['status'] == true) {
                return [
                    'status' => true,
                    'message' => 'Conteúdo encontrado.',
                    'content' => $reponse['product']
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Nenhum conteúdo encontrado.',
                    'content' => null
                ];
            }
            
            $this->repository->commitTransaction();
            
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    public function getAllProducts(string $orderBy = 'name', string $order = 'ASC'): array
    {
        try {
            $this->repository->beginTransaction();
            
            $reponse = $this->repository->findAll($orderBy, $order);
            if ($reponse['status'] == true) {
                return [
                    'status' => true,
                    'message' => 'Conteúdo encontrado.',
                    'content' => $reponse['product']
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Nenhum conteúdo encontrado.',
                    'content' => null
                ];
            }
            
            $this->repository->commitTransaction();
            
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

        // $image = $data['image'] ?? null;

        // $reponseImg = ConvertBase64::processBase64($image, 'Product');
        
        // if ($reponseImg['status'] == false) {
        //     return $this->buildResponse(false, 'Erro ao processar imagem: ' . $reponseImg['message'], null);
        // }

        // $data->link_image = $reponseImg['content'];

        // var_dump($data);die;

        //Link image Test
         $data["link_image"] = 'data:image/jpeg;base64,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';

        
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

    public function updateProduct(int $id, array $data)
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

        // $ProductModel = $updateData;
        // //$ProductModel->setUpdatedAt(new DateTime());

        // if (!$this->repository->update($ProductModel)) {
        //     throw new DomainException("Falha ao atualizar produto");
        // }

        // return $ProductModel;
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

    

    //  private function hydrateProduct(array $productData): ProductModel
    // {
    //     return new ProductModel(
    //         name: $productData['name'],
    //         costPrice: (float)$productData['cost_price'],
    //         salePrice: (float)$productData['sale_price'],
    //         category: $productData['category'],
    //         description: $productData['description'],
    //         isFavorite: (bool)$productData['is_favorite'],
    //         isDonation: (bool)$productData['is_donation'],
    //         id: (int)$productData['id'],
    //         createdAt: new DateTime($productData['created_at']),
    //         updatedAt: new DateTime($productData['updated_at'])
    //     );
    // }

    /**
     * Cleans strings by removing extra spaces and HTML tags
     */
//     private function sanitizeString(string $input): string
//     {
//         return trim(strip_tags($input));
//     }
}