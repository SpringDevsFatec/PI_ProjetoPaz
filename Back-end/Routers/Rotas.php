<?php
namespace App\Backend\Routers;

use App\Backend\Controller\UserController;
use App\Backend\Controller\ProductController;
use App\Backend\Controller\ProductImageController;
use App\Backend\Controller\OrderItemController;
use App\Backend\Controller\OrderController;
use App\Backend\Controller\SaleController;
use App\Backend\Controller\SupplierController;

class Rotas {
    public static function fastRotas(){
        return [
            'GET' => [
                // User
                '/users' => [UserController::class, 'getAllUsers'],
                '/user' => [UserController::class, 'getUserById'],
                '/TestUser' => [UserController::class, 'testJWT'],

                // Product
                // Rotas específicas primeiro
                '/products/search' => [ProductController::class, 'searchByName'],
                '/products/favorites' => [ProductController::class, 'listFavorites'],
                '/products/donations' => [ProductController::class, 'listDonations'],
                // Rotas com parâmetros depois
                '/products/category/{category}' => [ProductController::class, 'listByCategory'],
                '/products/{id}' => [ProductController::class, 'show'],
                // Rota genérica por último
                '/products' => [ProductController::class, 'listAll'],

                // Product Image

                // Order Item
                '/order-items/details/{orderId}' => [OrderItemController::class, 'listItemsWithProductDetails'],
                '/order-items/{id}' => [OrderItemController::class, 'show'],

                // Order
                '/orders/with-items/{id}' => [OrderController::class, 'listWithItems'],
                '/orders/payment-method/{paymentMethod}' => [OrderController::class, 'listByPaymentMethod'],
                '/orders/items/{id}' => [OrderItemController::class, 'listItemsWithProductDetails'],
                '/orders/{id}' => [OrderController::class, 'show'],
                '/orders' => [OrderController::class, 'listAll'],

                // Sale
                //'/sales/{seller_id}/?{status}/seller' => [SaleController::class, 'listBySeller'],
                '/sales/date' => [SaleController::class, 'listByDate'],
                '/sales/status/{status}' => [SaleController::class, 'listSalesByStatus'],
                '/sales/details/{id}' => [SaleController::class, 'show'],
                '/sales/{id}' => [SaleController::class, 'getSaleById'],
                '/sales' => [SaleController::class, 'listAllSales'],

                //Supplier
                 '/suppliers' => [SupplierController::class, 'getAllSuppliers'],
                 '/supplier/{id}' => [SupplierController::class, 'getSupplierById'],
            ],
            'POST' => [
            
                // User
                '/login' => [UserController::class, 'login'],
                '/users' => [UserController::class, 'createUser'],
            
                // Supplier
                '/supplier' => [SupplierController::class, 'createSupplier'],
            
                // Product
                '/product' => [ProductController::class, 'createProduct'],
            ],
            'PUT' => [
                // User
                '/users' => [UserController::class, 'updateUser'],
                '/users/passworld' => [UserController::class, 'updateUserPassword'],

                // Supplier
                '/supplier/{id}' => [SupplierController::class, 'updateSupplier'],

                // Product
            ],
            'DELETE' => [
                // users
                '/users/{id}' => [UserController::class, 'delete'],

                // products
                '/products/{id}' => [ProductController::class, 'delete'],
                
                // product-images

                // orderItem
                '/order-items/{id}' => [OrderItemController::class, 'delete'],

                // orders
                '/orders/{id}' => [OrderController::class, 'delete'],

                // sales
                '/sales/{id}' => [SaleController::class, 'delete'],
            ],
        ];
    }
}