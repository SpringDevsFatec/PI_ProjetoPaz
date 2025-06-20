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
                '/products/notfavorites' => [ProductController::class, 'listNotFavorites'],
                '/products/donations' => [ProductController::class, 'listDonations'],
                '/products/notdonations' => [ProductController::class, 'listNotDonations'],
                '/products/active' => [ProductController::class, 'listAllActive'],
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
                '/sales/seller{id}' => [SaleController::class, 'listBySeller'],
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
            
                // Sale
                '/sales' => [SaleController::class, 'createSale'], 

                // Order
                '/orders/{saleId}' => [OrderController::class, 'createOrder'],
                
                // Order Item
                '/order-items/{orderId}' => [OrderItemController::class, 'createOrderItem'],
            ],
            'PUT' => [
                // User
                '/users' => [UserController::class, 'updateUser'],
                '/users/passworld' => [UserController::class, 'updateUserPassword'],

                // Supplier
                '/supplier/{id}' => [SupplierController::class, 'updateSupplier'],

                // Product
                '/products/{id}' => [ProductController::class, 'updateProduct'],
                '/products/inactivate/{id}' => [ProductController::class, 'inactivateProduct'],
                '/products/img/{id}' => [ProductController::class, 'updateImgProduct'],

                // Sale
                '/sales/completed/{id}' => [SaleController::class, 'completeSale'],
                '/sales/cancelled/{id}' => [SaleController::class, 'cancelSale'],

                // Order
                '/orders/cancelled/{id}' => [OrderController::class, 'cancelOrder'],
            ],
            'DELETE' => [
                // users
                '/users/{id}' => [UserController::class, 'delete'],
                
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