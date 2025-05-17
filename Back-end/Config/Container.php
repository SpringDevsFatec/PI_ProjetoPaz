<?php

namespace App\Backend\Config;

use App\Backend\Repository\ProductRepository;
use App\Backend\Repository\SupplierRepository;
use App\Backend\Service\ProductService;
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
        
        $this->instances[SupplierRepository::class] = new SupplierRepository(
            $this->get(PDO::class)
        );
        
        // Services
        $this->instances[ProductService::class] = new ProductService(
            $this->get(ProductRepository::class),
            $this->get(SupplierRepository::class)
        );
        
        // Controllers
        $this->instances[\App\Backend\Controller\ProductController::class] = 
            new \App\Backend\Controller\ProductController(
                $this->get(ProductService::class)
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