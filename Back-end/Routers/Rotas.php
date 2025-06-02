<?php
namespace App\Backend\Routers;

use App\Backend\Controller\UserController;

class Rotas {
    public static function fastRotas(){
        return [
            'GET' => [
                '/users' => [UserController::class, 'getAllUsers'],
                '/user' => [UserController::class, 'getUserById'],
                '/TestUser' => [UserController::class, 'testJWT'],
            ],
            'POST' => [
                '/login' => [UserController::class, 'login'],
                '/users' => [UserController::class, 'createUser'],
            ],
            'PUT' => [
                '/users' => [UserController::class, 'updateUser'],
                '/users/passworld' => [UserController::class, 'updateUserPassword'],
            ],
            'DELETE' => [

            ],
        ];
    }
}