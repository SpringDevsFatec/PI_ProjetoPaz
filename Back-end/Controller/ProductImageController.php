<?php

namespace App\Backend\Controller;

use App\Backend\Service\ProductImageService;
use App\Backend\Libs\AuthMiddleware;
use App\Backend\Utils\ConvertBase64;

use DomainException;
use Exception;
use InvalidArgumentException;

class ProductImageController {

    private ProductImageService $service;
    private ConvertBase64 $convertBase64;

    public function __construct(
        ProductImageService $service,
        ConvertBase64 $convertBase64
    ) {
        $this->service = $service;
        $this->convertBase64 = $convertBase64;
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

    public function listByProduct(int $productId): void
    {
        try {
            $images = $this->service->getImagesByProduct($productId);
            $this->jsonResponse($images, 200, empty($images) ? 'Nenhuma imagem encontrada' : null);
        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());
        } catch (Exception $e) {
            error_log('Image list error: ' . $e->getMessage());
            $this->jsonResponse(null, 500, 'Erro ao buscar imagens do produto');
        }
    }

    public function show(int $id): void
    {
        try {
            $image = $this->service->getImage($id);
            $this->jsonResponse($image->toArray());
        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());
        } catch (Exception $e) {
            error_log('Image fetch error: ' . $e->getMessage());
            $this->jsonResponse(null, 500, 'Erro ao buscar imagem');
        }
    }

    public function create(): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException('JSON inválido');
            }

            // Processa upload da imagem
            if (isset($data['image'])) {
                $data = $this->service->ImageConvert($data, 'products');
            }
            
            $image = $this->service->createImage($data);
            $this->jsonResponse($image->toArray(), 201, 'Imagem criada com sucesso');
            
        } catch (InvalidArgumentException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());
        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());
        } catch (Exception $e) {
            error_log('Image creation error: ' . $e->getMessage());
            $this->jsonResponse(null, 500, 'Erro ao criar imagem');
        }
    }

    public function update(int $id): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException('JSON inválido');
            }

            // Processa atualização da imagem se necessário
            if (isset($data['image'])) {
                $data = $this->service->ImageConvert($data, 'products');
            }
            
            $image = $this->service->updateImage($id, $data);
            $this->jsonResponse($image->toArray(), 200, 'Imagem atualizada com sucesso');
            
        } catch (InvalidArgumentException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());
        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());
        } catch (Exception $e) {
            error_log('Image update error: ' . $e->getMessage());
            $this->jsonResponse(null, 500, 'Erro ao atualizar imagem');
        }
    }

    public function delete(int $id): void
    {
        try {
            // Primeiro obtém a imagem para ter o caminho do arquivo
            $image = $this->service->getImage($id);
            
            // Remove do banco de dados
            $this->service->deleteImage($id);
            
            // Tenta remover o arquivo físico
            //$this->service->deleteImageFile($image->getPath());
            
            $this->jsonResponse(null, 204);
        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());
        } catch (Exception $e) {
            error_log('Image deletion error: ' . $e->getMessage());
            $this->jsonResponse(null, 500, 'Erro ao remover imagem');
        }
    }

    private function ImageConvert($dataImg){

        $data = ConvertBase64::processBase64($dataImg,'ProductImage');
        
        return $data;
    }
}