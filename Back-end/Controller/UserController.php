<?php
namespace App\Backend\Controller;
use App\Backend\Service\UserService;

use Exception;

class UserController {

    private $service;

    public function __construct() {
        $this->service = new UserService();
    }

    private function handleResponse($result, $successMessage = "Operação concluída com sucesso.") {
        if (!empty($result)) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(['status' => false, "message" => $successMessage]);
        }
    }

    public function readAll() {
        $result = $this->service->readAll();
        foreach ($result as &$user) {
            unset($user['password']);
        }
        unset($user);
        $this->handleResponse($result, "Nenhum usuário encontrado.");
    }

    public function readById($id) {
        $result = $this->service->readAll($id);
        unset($user['password']);
        $this->handleResponse($result, "Nenhum usuário encontrado.");
    }

    public function login($data) {
        if(!isset($data->email) || !isset($data->password)) {
            http_response_code(400);
            echo json_encode(["error" => "Email e senha são necessários para o login."]);
            return;
        }
        try {
            $user = $this->service->login($data->email);
            
            if($user && password_verify($data->password, $user['password'])) {
                unset($user['password']);
                http_response_code(200);
                echo json_encode([
                    "message" => "Login bem-sucedido.", 
                    "user" => $user
                ]); 
            } else {
                http_response_code(401);
                echo json_encode(["error"=> "Email ou senha incorretos"]);
            }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Erro interno do servidor."]);
            }
    }

    public function create() {
        $data = json_decode(file_get_contents('php://input'));

        if (!isset(
            $data->name,
            $data->email,
            $data->password,
               )) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos para criação de usuário."]);
            exit;
        }
        if ($this->service->create($data)) {
            http_response_code(200);
            echo json_encode(['status' => true, "message" => "Usuário criado com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, "error" => "Erro ao criar usuário."]);
        }
    }

    public function put($id) {
        $data = json_decode(file_get_contents('php://input'));

        if (!isset(
            $data->name,
            $data->email,
               )) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos para atualização de usuário."]);
            exit;
        }
        if ($this->service->update($id, $data)) {
            http_response_code(200);
            echo json_encode(['status' => true, "message" => "Usuário atualizado com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, "error" => "Erro ao atualizar usuário."]);
        }
    }

    public function delete($id) {
        if ($this->service->delete($id)) {
            http_response_code(200);
            echo json_encode(['status' => true, "message" => "Usuário deletado com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, "error" => "Erro ao deletar usuário."]);
        }
    }
}