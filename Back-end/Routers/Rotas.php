<?php
namespace App\Backend\Routers;

use App\Backend\Controller\UserController;

class Rotas {
    public static function fastRotas(){
        return [
            'GET' => [
                '/users' => [UserController::class, 'getAllUsers'],
                '/users/{id}' => [UserController::class, 'getUserById'],
                '/TestUser' => [UserController::class, 'testJWT'],
            ],
            'POST' => [
                '/login' => [UserController::class, 'login'],
                '/users' => [UserController::class, 'createUser'],
            ],
            'PUT' => [
                '/users' => [UserController::class, 'updateUser'],
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