<?php
namespace App\Backend\Service;

use App\Backend\Model\Product;
use App\Backend\Repository\ProductRepository;

use Exception;
use DateTime;

class ProductService {
    
    private $repository;

    public function __construct()
    {
        $this->repository = new ProductRepository();
    }

    public function searchProducts($searchTerm) {
        return $this->repository->getByName($searchTerm);
    }

    public function searchByCategory($category) {
        return $this->repository->getByCategory($category);
    }

    public function searchByCost($cost) {
        return $this->repository->getByCost($cost);
    }

    public function searchByFavorite() {
        return $this->repository->getByFavorite();
    }

    public function searchByDonation() {
        return $this->repository->getByDonation();
    }
    
    public function readById($id) {
        return $this->repository->getById($id);
    }

    public function readAll() {
        return $this->repository->getAllProducts();
    }

    public function create($data) {
        $product = new Product();
        $product->setSupplierId($data->supplier_id);
        $product->setName($data->name);
        $product->setCostPrice($data->cost_price);
        $product->setSalePrice($data->sale_price);
        $product->setDescription($data->description);
        $product->setIsFavorite($data->is_favorite);
        $product->setCategory($data->category);
        $product->setIsDonation($data->is_donation);
        $product->setDateCreate(new DateTime());

        if ($this->repository->insertProduct($product)) {
            return true;
        } else {
            return false;
        }
    }

    public function update($id, $data) {
        $product = new Product();
        $product->setId($id);
        $product->setSupplierId($data->supplier_id ?? $product->getSupplierId());
        $product->setName($data->name ?? $product->getName());
        $product->setCostPrice($data->cost_price ?? $product->getCostPrice());
        $product->setSalePrice($data->sale_price ?? $product->getSalePrice());
        $product->setDescription($data->description ?? $product->getDescription());
        $product->setIsFavorite($data->is_favorite ?? $product->getIsFavorite());
        $product->setCategory($data->category ?? $product->getCategory());
        $product->setIsDonation($data->is_donation ?? $product->getIsDonation());

        if ($this->repository->updateProduct($product)) {
            return true;
        } else {
            return false;
        }
    }
}