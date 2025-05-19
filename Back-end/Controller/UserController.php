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
            echo json_encode([$result,"message" => $successMessage]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => false, "message" => $successMessage]);
        }
    }

    public function Login() {
        //Get the Json data
        $data = json_decode(file_get_contents('php://input'));

        if($this->service->login($data)) {
            $result = $this->service->login($data);
            $this->handleResponse($result, "Login realizado com sucesso.");
        } else {
            http_response_code(401);
            echo json_encode(['status' => false, "message" => "Usuário ou senha inválidos."]);
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