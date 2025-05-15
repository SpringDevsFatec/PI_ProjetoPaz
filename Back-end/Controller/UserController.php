<?php
namespace App\Backend\Controller;

use App\Backend\Libs\AuthMiddleware;
use App\Backend\Service\UserService;

use DomainException;
use Dotenv\Exception\InvalidFileException;
use Exception;
use InvalidArgumentException;

class UserController {

    private $service;

    public function __construct(
        UserService $service
    ) {
        $this->service = $service;
    }

    private function jsonResponse(
        mixed $data,
        int $statusCode = 200,
        ?string $message = null
    ): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');

        $response = [];
        if ($message) {
            $response['message'] = $message;
        }
        if ($data !== null) {
            $response['data'] = $data;
        }

        echo json_encode($response);
        exit;
    }

    public function listAll(): void
    {
        try {
            $users = $this->service->getAll();
            foreach($users as &$user) {
                unset($result['password']);
            }
            
            $this->jsonResponse($users, 200, empty($sales) ? 'Nenhum usuario encontrado' : null);

        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar usuarios: ' . $e->getMessage());
        }
    }

    public function show(int $id): void
    {
        try {
            $users = $this->service->getById($id);
            if ($users) {
                unset($result['password']);
            }
            
            $this->jsonResponse($users, 200, empty($sales) ? 'Nenhum usuario encontrado' : null);

        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao buscar usuario: ' . $e->getMessage());
        }
    }

    public function login($data) {
        if(!isset($data->email) || !isset($data->password)) {
            http_response_code(400);
            echo json_encode(["error" => "Email e senha são necessários para o login."]);
            return;
        }
        try {
            $user = $this->service->getUserByEmail($data->email);
            
            if($user && password_verify($data->password, $user['password'])) {
                unset($user['password']);
                http_response_code(200);
                echo json_encode([
                    "message" => "Login bem-sucedido.", 
                    "user" => $user
                ]); 
            } 
        } catch (Exception $e) {
            $this->jsonResponse(null, 401, 'Email ou senha incorretos: ' . $e->getMessage());
        
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao fazer login: ' . $e->getMessage());
        }
    }

    public function create() {
        $data = json_decode(file_get_contents('php://input'));

        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException('Formato JSON inválido');
            }

            if (!isset($data['name'])) {
                throw new InvalidArgumentException('Nome não informado');
            }

            if (!isset($data['email'])) {
                throw new InvalidArgumentException('Email não informado');
            }

            if (!isset($data['password'])) {
                throw new InvalidArgumentException('Senha necessaria');
            }

            $sale = $this->service->createUser($data);
            $this->jsonResponse($sale->toArray(), 201, 'Usuario criado com sucesso');
            
        } catch (InvalidArgumentException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());
            
        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());

        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao criar usuario');
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents('php://input'));

        try {
            $data = json_decode(file_get_contents('php://input', true));

            if (!isset($data['name'])) {
                throw new InvalidArgumentException('Nome não informado');
            }

            if (!isset($data['email'])) {
                throw new InvalidArgumentException('Email não informado');
            }

            $user = $this->service->updateUser($id, (string)$data['name'], (string)$data['email']);
            $this->jsonResponse($user->toArray(), 200, 'Usuario atualizado');

        } catch (InvalidFileException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());
        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao atualizar usuario');
        }
    }

    public function updatePassword($id) {
        $data = json_decode(file_get_contents('php://input'));

        try {
            $data = json_decode(file_get_contents('php://input', true));

            if (!isset($data['password'])) {
                throw new InvalidArgumentException('Senha não informada');
            }

            $user = $this->service->updatePassord($id, (string)$data['password']);
            $this->jsonResponse($user->toArray(), 200, 'Usuario atualizado');

        } catch (InvalidFileException $e) {
            $this->jsonResponse(null, 400, $e->getMessage());
        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());
        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao atualizar senha');
        }
    }

    public function delete($id) {
        try {
            $this->service->deleteUser($id);
            $this->jsonResponse(null, 204, 'Usuario excluído com sucesso.');

        } catch (DomainException $e) {
            $this->jsonResponse(null, 404, $e->getMessage());

        } catch (Exception $e) {
            $this->jsonResponse(null, 500, 'Erro ao excluir usuario'); 
        }
    }
}