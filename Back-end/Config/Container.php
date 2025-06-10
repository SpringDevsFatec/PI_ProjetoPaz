<?php

namespace App\Backend\Config;

use App\Backend\Model\Product;
use App\Backend\Repository\ProductRepository;
use App\Backend\Repository\ProductImageRepository;
use App\Backend\Repository\SupplierRepository;
use App\Backend\Repository\OrderItemRepository;
use App\Backend\Repository\OrderRepository;
use App\Backend\Repository\SaleRepository;
use App\Backend\Repository\UserRepository;
use App\Backend\Service\ProductService;
use App\Backend\Service\ProductImageService;
use App\Backend\Service\OrderItemService;
use App\Backend\Service\OrderService;
use App\Backend\Service\SaleService;
use App\Backend\Service\UserService;
use App\Backend\Service\SupplierService;
//use App\Backend\Utils\ConvertBase64;
use PDO;

class Container
{
    private array $instances = [];

    public function __construct()
    {
        $this->setup();
    }

    private function setup(): void
    {
        // Configuração do banco de dados
        $this->instances[PDO::class] = Database::getInstance();
        
        // Repositórios
        $this->instances[ProductRepository::class] = new ProductRepository(
            $this->get(PDO::class)
        );
        $this->instances[ProductImageRepository::class] = new ProductImageRepository(
            $this->get(PDO::class)
        );
        $this->instances[SupplierRepository::class] = new SupplierRepository(
            $this->get(PDO::class)
        );
        $this->instances[OrderItemRepository::class] = new OrderItemRepository(
            $this->get(PDO::class)
        );
        $this->instances[OrderRepository::class] = new OrderRepository(
            $this->get(PDO::class)
        );
        $this->instances[SaleRepository::class] = new SaleRepository(
            $this->get(PDO::class)
        );
        $this->instances[UserRepository::class] = new UserRepository(
            $this->get(PDO::class)
        );

        // Services
        $this->instances[ProductService::class] = new ProductService(
            $this->get(ProductRepository::class),
            $this->get(SupplierRepository::class)
        );

        $this->instances[ProductImageService::class] = new ProductImageService(
            $this->get(ProductImageRepository::class),
            $this->get(ProductRepository::class)
        );

        $this->instances[OrderItemService::class] = new OrderItemService(
            $this->get(OrderItemRepository::class),
            $this->get(ProductRepository::class)
        );

        $this->instances[OrderService::class] = new OrderService(
            $this->get(OrderRepository::class),
            $this->get(SaleRepository::class),
            $this->get(ProductRepository::class)
        );

        $this->instances[SaleService::class] = new SaleService(
            $this->get(SaleRepository::class),
            $this->get(OrderRepository::class),
            $this->get(UserRepository::class)
        );

        $this->instances[UserService::class] = new UserService(
            $this->get(UserRepository::class)
        );
        $this->instances[SupplierService::class] = new SupplierService(
            $this->get(SupplierRepository::class)
        );
        
        // Controllers

        $this->instances[\App\Backend\Controller\UserController::class] = 
            new \App\Backend\Controller\UserController(
                $this->get(UserService::class)
            );

        $this->instances[\App\Backend\Controller\ProductController::class] = 
            new \App\Backend\Controller\ProductController(
                $this->get(ProductService::class)
            );
   
            // $this->instances[\App\Backend\Controller\ProductImageController::class] = 
        //     new \App\Backend\Controller\ProductImageController(
        //         $this->get(ConvertBase64::class),
        //         $this->get(ProductImageService::class)
        //     );
   
        $this->instances[\App\Backend\Controller\OrderItemController::class] = 
            new \App\Backend\Controller\OrderItemController(
                $this->get(OrderItemService::class)
            );
        $this->instances[\App\Backend\Controller\OrderController::class] = 
            new \App\Backend\Controller\OrderController(
                $this->get(OrderService::class)
            );
        $this->instances[\App\Backend\Controller\SaleController::class] = 
            new \App\Backend\Controller\SaleController(
                $this->get(SaleService::class)
            );
        $this->instances[\App\Backend\Controller\SaleController::class] = 
            new \App\Backend\Controller\SaleController(
                $this->get(SaleService::class)
            );
        $this->instances[\App\Backend\Controller\SupplierController::class] = 
            new \App\Backend\Controller\SupplierController(
                $this->get(SupplierService::class)
            );
    }

    public function get(string $class): object
    {
        if (!isset($this->instances[$class])) {
            throw new \RuntimeException("Class {$class} not found in container");
        }
        
        return $this->instances[$class];
    }
}