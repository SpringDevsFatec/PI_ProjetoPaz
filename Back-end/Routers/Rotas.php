<?php
namespace App\Backend\Routers;

use App\Backend\Controller\UserController;

class Rotas {
    public static function fastRotas(){
        return [
            'GET' => [
                // user
                '/user' => [UserController::class, 'readAll'],
                '/user' => [UserController::class, 'readById'],
                '/user/login' => [UserController::class, 'login'],
                
                // product


                // order


                // sale

            ],
            'POST' => [
                // user
                '/user' => [UserController::class, 'create'],

                // product


                // order


                // sale

            ],
            'PUT' => [
                // user
                '/user' => [UserController::class, 'update'],

                // product


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