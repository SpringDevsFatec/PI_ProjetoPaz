<?php
namespace App\Backend\Service;

use App\Backend\Model\User;
use App\Backend\Repository\UserRepository;

use Exception;
use DateTime;

class UserService {
    
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function login($data) {
        if(!isset($data->email) || !isset($data->password)) {
            http_response_code(400);
            echo json_encode(["error" => "Email e senha são necessários para o login."]);
            return;
        }
        try {
            $user = $this->repository->getUserByEmail($data->email);
            
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

    public function create($data) {
        if (!isset($data->name, $data->email, $data->password)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos"]);
            return;
        }

        //função de enviar email para vendedor

        $user = new User();
        $user->setName($data->name);
        $user->setEmail($data->email);
        $user->setPassword($data->password);
        $user->setDateCreate(new DateTime());

        if ($this->repository->insertUser($user)) {
            http_response_code(201);
            echo json_encode(["message" => "Usuário criado com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao criar usuário."]);
        }
    }

    public function read($id = null) {
        if ($id) {
            $result = $this->repository->getUserById($id);
            unset($result['password']);
            $status = $result ? 200 : 404;
        } else {
            $result = $this->repository->getAllUsers();
            foreach ($result as &$user) {
                unset($user['password']);
            }
            unset($user);
            $status = !empty($result) ? 200 : 404;
        }

        http_response_code($status);
        echo json_encode($result ?: ["message" => "Nenhum usuário encontrado."]);
    }

    public function update($data) {
        if (!isset($data->id, $data->name, $data->email)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos"]);
            return;
        }

        //função de enviar email para vendedor

        $user = new User();
        $user->setId($data->id);
        $user->setName($data->name);
        $user->setEmail($data->email);

        if ($this->repository->updateUser($user)) {
            http_response_code(201);
            echo json_encode(["message" => "Usuário atualizado com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao atualizar usuário."]);
        }
    }

    public function updatePassord($data) {
        if (!isset($data->id, $data->password)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos"]);
            return;
        }

        $user = new User();
        $user->setId($data->id);
        $user->setPassword($data->passord);

        if ($this->repository->updatePassword($user)) {
            http_response_code(201);
            echo json_encode(["message" => "Senha atualizada com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao atualizar senha."]);
        }
    }

    public function delete($id) {
        if ($this->repository->deleteUser($id)) {
            http_response_code(200);
            echo json_encode(["message" => "Usuário excluído com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao excluir usuario."]);
        }
    }
}