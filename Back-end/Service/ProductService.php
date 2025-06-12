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

        var_dump($_SERVER["HTTP_X_IMAGE_DATA"]);

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

        // get image by Header

        $image = $_SERVER['HTTP_X_IMAGE_DATA'];
        if (empty($image) || $image === 'null' || $image === 'undefined' ||  $image === '') {
            $image = null;
        }

        ConvertBase64::processBase64($image, 'Product');
        
        



        var_dump($data);die;


        // if (!$this->supplierRepository->getSupplierById($data['supplier_id'])) {
        //     throw new DomainException("Fornecedor não encontrado");
        // }

        return $ProductModel;
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