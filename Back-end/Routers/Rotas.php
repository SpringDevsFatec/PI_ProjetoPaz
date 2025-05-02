<?php
namespace App\Backend\Routers;

use App\Backend\Controller\ProductController;
use App\Backend\Controller\UserController;

class Rotas {
    public static function fastRotas(){
        return [
            'GET' => [
                // user
                '/user' => [UserController::class, 'readAll'],
                '/user/{id}' => [UserController::class, 'readById'],
                '/user/login' => [UserController::class, 'login'],
                
                // product
                '/product' => [ProductController::class, 'readAll'],
                '/product/{id}' => [ProductController::class, 'readById'],
                '/product/search' => [ProductController::class, 'searchProducts'],
                '/product/search' => [ProductController::class, 'searchByCategory'],
                '/product/search' => [ProductController::class, 'searchByCost'],
                '/product/search' => [ProductController::class, 'searchByFavorite'],
                '/product/search' => [ProductController::class, 'searchByDonation'],

                // order


                // sale

            ],
            'POST' => [
                // user
                '/user' => [UserController::class, 'create'],

                // product
                '/product' => [ProductController::class, 'create'],

                // order


                // sale

            ],
            'PUT' => [
                // user
                '/user' => [UserController::class, 'put'],

                // product
                '/product' => [ProductController::class, 'put'],

                // order


                // sale

            ],
            'DELETE' => [
                // user
                '/user' => [UserController::class, 'delete'],
            ],
        ];
    }
}