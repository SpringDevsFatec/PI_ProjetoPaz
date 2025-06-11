<?php
namespace App\Backend\Service;

use App\Backend\Model\UserModel;
use App\Backend\Repository\UserRepository;
use App\Backend\Libs\AuthMiddleware;
use App\Backend\Utils\PatternText;
use App\Backend\Utils\Responses;
use Exception;

class UserService {
    use Responses;

    private $repository;

    public function __construct() {
        $this->repository = new UserRepository();
    }

    public function login($data) {
        $data = PatternText::processText($data);
        $email = $data->email;
        $password = $data->password;

        try {
            $this->repository->beginTransaction();
            $user = $this->repository->verifyLogin($email, $password);
            $this->repository->commitTransaction();

            if ($user['status'] === true) {
                $token = (new AuthMiddleware())->createToken([
                    'id' => $user['user']['id'],
                    'name' => $user['user']['name']
                ]);
                return $this->buildResponse(true, 'Login realizado com sucesso.', $token);
            } elseif ($user['status'] === false && $user['user'] === 'false') {
                return $this->buildResponse(false, 'Senha inválida!', null);
            } else {
                return $this->buildResponse(false, 'Usuário não encontrado!', null);
            }
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    public function getAllUsers() {
        (new AuthMiddleware())->openToken();

        try {
            $this->repository->beginTransaction();
            $response = $this->repository->getAllUsers();
            $this->repository->commitTransaction();

            if ($response['status'] === true) {
                return $this->buildResponse(true, 'Conteúdo encontrado.', $response['user']);
            }
            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    public function getUserById() {
        $decodedToken = (new AuthMiddleware())->openToken();
        $userId = $decodedToken->id;

        try {
            $this->repository->beginTransaction();
            $response = $this->repository->getContentId($userId);
            $this->repository->commitTransaction();

            if ($response['status'] === true) {
                return $this->buildResponse(true, 'Conteúdo encontrado.', $response['user']);
            }
            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    public function createUser($data) {
        $dataPadronizado = PatternText::processText($data);

        $user = new UserModel();
        $user->setName($dataPadronizado->name);
        $user->setEmail($dataPadronizado->email);
        $user->setPassword($dataPadronizado->password);

        try {
            $this->repository->beginTransaction();

            $userExists = $this->repository->userExists($user);
            if ($userExists['status'] === false) {
                return $this->buildResponse(false, 'User já cadastrado.', $userExists['user']);
            }

            $userCreated = $this->repository->createUser($user);
            if ($userCreated['status'] === true) {
                $data->id = $userCreated['user']->getId();
                $this->repository->commitTransaction();
                return $this->buildResponse(true, 'Usuário criado com sucesso.', $data);
            }

            $this->repository->rollBackTransaction();
            return $this->buildResponse(false, 'Erro ao criar usuário.', null);
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    public function updateUser($data) {
        $decodedToken = (new AuthMiddleware())->openToken();
        $userId = $decodedToken->id;

        $dataPadronizado = PatternText::processText($data);

        $user = new UserModel();
        $user->setId($userId);
        $user->setName($dataPadronizado->name);
        $user->setEmail($dataPadronizado->email);
        $user->setPassword($dataPadronizado->password);

        $userExists = $this->repository->userExistsUpdate($user);
        if ($userExists['status'] === true) {
            return $this->buildResponse(false, 'User não existe.', $userExists['user']);
        }

        try {
            $this->repository->beginTransaction();

            $userUpdated = $this->repository->updateUser($user);
            if ($userUpdated['status'] === true) {
                $this->repository->commitTransaction();
                return $this->buildResponse(true, 'Usuário Atualizado com sucesso.', $data);
            }

            $this->repository->rollBackTransaction();
            return $this->buildResponse(false, 'Erro ao Atualizar usuário.', null);
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    public function updateUserPassword($data) {
        (new AuthMiddleware())->openToken();

        $user = new UserModel();
        $user->setEmail($data->email);
        $user->setPassword($data->password);

        $userExists = $this->repository->userExists($user);
        if ($userExists['status'] === true) {
            return $this->buildResponse(false, 'User não existe.', $userExists['user']);
        }

        try {
            $this->repository->beginTransaction();

            $passwordNew = $this->repository->updateUserPassword($user);
            if ($passwordNew['status'] === true) {
                $this->repository->commitTransaction();
                return $this->buildResponse(true, 'Senha Atualizada com sucesso.', $data);
            }

            $this->repository->rollBackTransaction();
            return $this->buildResponse(false, 'Erro ao Atualizar senha.', null);
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }
}
