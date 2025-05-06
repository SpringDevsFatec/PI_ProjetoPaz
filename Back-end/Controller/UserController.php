<?php
namespace App\Backend\Controller;
use App\Backend\Service\UserService;
use App\Backend\Libs\AuthMiddleware;
use Exception;

class UserController {
    private $service;

    public function __construct() {
        $this->service = new UserService();
    }

    // code Get, Post, Put, Delete in private functions

    private function handleResponse($result, $successMessage = "Operação concluída com sucesso.") {
        if (!empty($result)) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(['status' => false, "message" => $successMessage]);
        }
    }

    public function getUserById($id) {
        $result = $this->service->getUserById($id);
        $this->handleResponse($result, "Nenhum conteúdo encontrado.");
    }

    public function getAllUsers() {
        $result = $this->service->getAllUsers();
        $this->handleResponse($result, "Nenhum conteúdo encontrado.");
    }

}