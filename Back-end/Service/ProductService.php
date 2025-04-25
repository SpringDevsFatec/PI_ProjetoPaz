<?php
namespace App\Backend\Service;

use App\Backend\Model\Product;
use App\Backend\Repository\ProductRepository;

use Exception;
use DateTime;

class ProductService {
    
    private $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create($data) {
        if (!isset($data->supplier_id, $data->name, $data->cost_price, $data->sale_price, $data->description, $data->is_favorite, $data->category, $data->is_donation)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos"]);
            return;
        }

        $product = new Product();
        $product->setSupplierId($data->supplier_id);
        $product->setName($data->name);
        $product->setCostPrice($data->cost_price);
        $product->setSalePrice($data->sale_price);
        $product->setDescription($data->Description);
        $product->setIsFavorite($data->is_favorite);
        $product->setCategory($data->category);
        $product->setIsDonation($data->is_donation);
        $product->setDateCreate(new DateTime());

        if ($this->repository->insertProduct($product)) {
            http_response_code(201);
            echo json_encode(["message" => "Produto criado com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao criar produto."]);
        }
    }

    public function read($id = null) {
        if ($id) {
            $result = $this->repository->getProductById($id);
            $status = $result ? 200 : 404;
        } else {
            $result = $this->repository->getAllProducts();
            unset($product);
            $status = !empty($result) ? 200 : 404;
        }

        http_response_code($status);
        echo json_encode($result ?: ["message" => "Nenhum produto encontrado."]);
    }

    public function update($data) {
        if (!isset($data->name, $data->email)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos"]);
            return;
        }

        $product = new Product();
        $product->setId($data->id);
        $product->setSupplierId($data->supplier_id);
        $product->setName($data->name);
        $product->setCostPrice($data->cost_price);
        $product->setSalePrice($data->sale_price);
        $product->setDescription($data->Description);
        $product->setIsFavorite($data->is_favorite);
        $product->setCategory($data->category);
        $product->setIsDonation($data->is_donation);

        if ($this->repository->updateProduct($product)) {
            http_response_code(201);
            echo json_encode(["message" => "Produto atualizado com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao atualizar produto."]);
        }
    }

    public function delete($id) {
        if ($this->repository->deleteProduct($id)) {
            http_response_code(200);
            echo json_encode(["message" => "Produto excluído com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao excluir produto."]);
        }
    }
}