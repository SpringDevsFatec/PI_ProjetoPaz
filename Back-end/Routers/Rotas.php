<?php
namespace App\Backend\Routers;

use App\Backend\Controller\OrderItemController;
use App\Backend\Controller\OrderController;
use App\Backend\Controller\ProductController;
use App\Backend\Controller\UserController;
use App\Backend\Controller\SaleController;

class Rotas 
{
    public static function fastRotas(): array 
    {
        return [
            'GET' => [
                // Users
                '/users' => [UserController::class, 'listAll'],
                '/users/{id}' => [UserController::class, 'show'],
                '/users/{id}/orders' => [UserController::class, 'listUserOrders'],
                
                // Products
                '/products' => [ProductController::class, 'listAll'],
                '/products/{id}' => [ProductController::class, 'show'],
                '/products/list/{searchTerm}' => [ProductController::class, 'listProductsByName'],
                '/products/{category}/category' => [ProductController::class, 'listProductsByCategory'],
                '/products/{cost}/cost' => [ProductController::class, 'listProductsByCost'],
                '/products/favorite/{favorite}' => [ProductController::class, 'listProductsByFavorite'],
                '/products/{donation}/donation' => [ProductController::class, 'listProductsByDonation'],

                // Orders
                '/orders/items/{id}' => [OrderController::class, 'listWithItems'],
                '/orders/{paymentMethod}/payment-method' => [OrderController::class, 'listByPaymentMethod'],
                '/orders' => [OrderController::class, 'listAll'],
                '/orders/{id}' => [OrderController::class, 'show'],
                '/orders/{id}/items' => [OrderItemController::class, 'listByOrder'],

                // Order Items
                '/order-items' => [OrderItemController::class, 'listItemsWithProductDetails'],
                '/order-items/{id}' => [OrderItemController::class, 'show'],

                // Sales
                'sales' => [SaleController::class, 'listAll'],
                'sales/{id}' => [SaleController::class, 'getSaleById'],
                'sellers/{id}/sales' => [SaleController::class, 'show'],
                'sales/{date}/date' => [SaleController::class, 'listSalesByDate'],
                'sales/{seller_id}/?{status}/seller' => [SaleController::class, 'listBySeller'],
                'sales/{status}/status' => [SaleController::class, 'listSalesByStatus'],
            ],
            'POST' => [
                // Users
                '/users' => [UserController::class, 'create'],
                '/users/login' => [UserController::class, 'login'],

                // Products
                '/products' => [ProductController::class, 'create'],

                // Orders
                '/orders' => [OrderController::class, 'create'],
                '/orders/{id}/items' => [OrderController::class, 'addItem'],

                // Order Items
                '/order-items' => [OrderItemController::class, 'create'],

                // Sale
                '/sales' => [SaleController::class, 'create'],
                '/sales/{id}/orders' => [SaleController::class, 'addOrder'],
                
            ],
            'PUT' => [
                // Users
                '/users/{id}' => [UserController::class, 'update'],

                // Products
                '/products/{id}' => [ProductController::class, 'update'],

                // Order Items
                '/order-items/{id}/quantity' => [OrderItemController::class, 'updateQuantity'],

                // Orders
                '/orders/{id}' => [OrderController::class, 'updateStatus'],

                // Sales
                '/sales/{id}/complete' => [SaleController::class, 'complete'],
                '/sales/{id}/cancel' => [SaleController::class, 'cancel'],
                
            ],
            'DELETE' => [
                // users
                '/users/{id}' => [UserController::class, 'delete'],

                // products
                '/products/{id}' => [ProductController::class, 'delete'],

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