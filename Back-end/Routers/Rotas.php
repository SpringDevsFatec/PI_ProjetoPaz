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
                '/products/list' => [ProductController::class, 'listProductsByName'],
                '/products/list/{category}' => [ProductController::class, 'listProductsByCategory'],
                '/products/list/{cost}' => [ProductController::class, 'listProductsByCost'],
                '/products/list/favorite' => [ProductController::class, 'listProductsByFavorite'],
                '/products/list/donation' => [ProductController::class, 'listProductsByDonation'],

                // Orders
                '/orders/{id}' => [OrderController::class, 'listWithItems'],
                '/orders/payment-method/{paymentMethod}' => [OrderController::class, 'listByPaymentMethod'],
                '/orders' => [OrderController::class, 'listAll'],
                '/orders/{id}' => [OrderController::class, 'show'],
                '/orders/{id}/items' => [OrderItemController::class, 'listByOrder'],

                // Order Items
                '/order-items' => [OrderItemController::class, 'listItemsWithProductDetails'],
                '/order-items/{id}' => [OrderItemController::class, 'show'],

                // Sales

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

            ],
            'DELETE' => [
                // user
                '/users/{id}' => [UserController::class, 'delete'],

                // products
                '/products/{id}' => [ProductController::class, 'delete'],

                // orderItem
                '/order-items/{id}' => [OrderItemController::class, 'delete'],

                // orders
                '/orders/{id}' => [OrderController::class, 'delete'],
            ],
        ];
    }
}