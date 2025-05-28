<?php
namespace App\Backend\Service;

use App\Backend\Model\ProductImage;
use App\Backend\Repository\ProductImageRepository;
use App\Backend\Repository\ProductRepository;

use DateTime;
use InvalidArgumentException;
use DomainException;
use Exception;

class ProductImageService {
    
    private $repository;
    private $productRepository;

    public function __construct(
        ProductImageRepository $repository,
        productRepository $productRepository
    ) {
        $this->repository = $repository;
        $this->productRepository = $productRepository;
    }

    public function getImagesByProduct(int $productId): array 
    {
        if (!$this->productRepository->find($productId)) {
            throw new DomainException("Produto não encontrado");
        }
        $imagesData = $this->repository->findByProductId($productId);


        return array_map(
            fn(array $data) => $this->hydrateProductImage($data),
            $imagesData
        );
    }

    public function getImage(int $id): ProductImage
    { 
        $productImageData = $this->repository->find($id);
        if (!$productImageData) {
            throw new DomainException("Imagem do Produto não encontrada");
        }

        return $this->hydrateProductImage($productImageData);
    }

    public function createImage(array $data): ProductImage
    {
        $this->validateProductImageData($data);
        
        $productId = (int)$data['product_id'];
        if (!$this->productRepository->find($productId)) {
            throw new DomainException("Produto não encontrado");
        }

        $image = new ProductImage(
            productId: (int)$data['product_id'],
            path: $this->sanitizePath($data['path']),
            altText: $this->sanitizeText($data['alt_text']),
            id: null,
            createdAt: new DateTime(),
            updatedAt: new DateTime()
        );

        $imageId = $this->repository->save($image);
        $image->setId($imageId);

        return $image;
    }

    public function updateImage(int $id, array $data): ProductImage
    {
        $existingImage = $this->getImage($id);

        if (isset($data['path'])) {
            $existingImage->setPath($this->sanitizePath($data['path']));
        }
        if (isset($data['alt_text'])) {
            $existingImage->setAltText($this->sanitizeText($data['alt_text']));
        }

        $existingImage->setUpdatedAt(new DateTime());

        if (!$this->repository->update($existingImage)) {
            throw new DomainException("Falha ao atualizar imagem");
        }

        return $existingImage;
    }

    public function deleteImage(int $id): void 
    {
        $image = $this->getImage($id);
        
        if (!$this->repository->delete($id)) {
            throw new DomainException("Falha ao remover imagem");
        }
    }

    private function validateProductImageData(array $data): void
    {
        $requiredFields = ['path', 'alt_text', 'product_id'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new InvalidArgumentException("Campo obrigatório faltando: {$field}");
            }
        }

        if (empty(trim($data['path']))) {
            throw new InvalidArgumentException("Caminho da imagem não pode ser vazio");
        }

        if (empty(trim($data['alt_text']))) {
            throw new InvalidArgumentException("Texto alternativo não pode ser vazio");
        }

        if (strlen(trim($data['alt_text'])) > 255) {
            throw new InvalidArgumentException("Texto alternativo muito longo");
        }
    }

     private function hydrateProductImage(array $data): ProductImage
    {
        return new ProductImage(
            productId: (int)$data['product_id'],
            path: $data['path'],
            altText: $data['alt_text'],
            id: (int)$data['id'],
            createdAt: new DateTime($data['created_at']),
            updatedAt: new DateTime($data['updated_at'])
        );
    }

    /**
     * Limpa e valida o caminho da imagem
     */
    private function sanitizePath(string $path): string
    {
        $path = trim($path);
        if (empty($path)) {
            throw new InvalidArgumentException("Caminho da imagem não pode ser vazio");
        }
        return $path;
    }

    /**
     * Limpa o texto alternativo
     */
    private function sanitizeText(string $text): string
    {
        $text = trim(strip_tags($text));
        if (empty($text)) {
            throw new InvalidArgumentException("Texto alternativo não pode ser vazio");
        }
        return $text;
    }

    public function ImageConvert($data) {
        if (isset($data->imageSrcBase64)) {
            $imageData = base64_decode($data->imageSrcBase64);
            $uploadDir = __DIR__ . '/../../ImageServer/ProductImage/';
            $fileName = uniqid() . '.jpg';
            $uploadFile = $uploadDir . $fileName;
            
            if (file_put_contents($uploadFile, $imageData)) {
                $data->imgurl = '../../ImageServer/ProductImage/' . $fileName;
            } else {
                throw new Exception("Failed to upload image");
            }
        }
        return $data;
    }
}