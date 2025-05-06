<?php
namespace App\Backend\Routers;

use App\Backend\Controller\UserController;

class Rotas {
    public static function fastRotas(){
        return [
            'GET' => [
                '/users' => [UserController::class, 'getAllUsers'],
                '/users/{id}' => [UserController::class, 'getUserById'],
            ],
            'POST' => [

            ],
            'PUT' => [

            ],
            'DELETE' => [

            ],
        ];
    }
}