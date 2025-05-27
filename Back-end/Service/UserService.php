<?php
namespace App\Backend\Service;

use App\Backend\Model\UserModel;
use App\Backend\Repository\UserRepository;
use App\Backend\Libs\AuthMiddleware;
use App\Backend\Utils\PatternText;
use Exception;


class UserService {
    
    private $repository;

    public function __construct() {
        $this->repository = new UserRepository();
    }

    
    //public functions...

    //Login
    public function login($data) {

       $data = PatternText::processText($data);

        $email = $data->email;
        $password = $data->password;

        try {
            $this->repository->beginTransaction();
            // Check if user exists
            $user = $this->repository->verifyLogin($email, $password);
            if ($user['status'] == true) {
                // Generate JWT token
                $token = (new AuthMiddleware())->createToken(
                    [
                        'id' => $user['user']['id'],
                        'name' => $user['user']['name']
                    ]
                );
                
                return [
                    'status' => true,
                    'message' => 'Login realizado com sucesso',
                    'content' => $token
                ];
            } else if ($user['status'] == false && $user['user'] == 'false') {
                return [
                    'status' => false,
                    'message' => 'senha inválida!',
                    'content' => null
                ];
            }else {
                return [
                    'status' => false,
                    'message' => 'Usuário não encontrado!',
                    'content' => null
                ];
            }
            
            $this->repository->commitTransaction();
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }    
    }


    //Get all users
    public function getAllUsers() {
        //check the token
        $check = new AuthMiddleware();
        $check->openToken();
        
        try {
            $this->repository->beginTransaction();
            
            // Get all users
             $reponse = $this->repository->getAllUsers();
            if ($reponse['status'] == true) {
                return [
                    'status' => true,
                    'message' => 'Conteúdo encontrado.',
                    'content' => $reponse['user']
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Nenhum conteúdo encontrado.',
                    'content' => null
                ];
            }
            
            $this->repository->commitTransaction();
            
         
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }


    //Get user by id
    public function getUserById($id) {
        //check the token
        $check = new AuthMiddleware();
        $check->openToken();
        
        try {
            $this->repository->beginTransaction();
            
            // Get user by id
            $reponse = $this->repository->getContentId($id);
            if ($reponse['status'] == true) {
                return [
                    'status' => true,
                    'message' => 'Conteúdo encontrado.',
                    'content' => $reponse['user']
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Nenhum conteúdo encontrado.',
                    'content' => null
                ];
            }
            $this->repository->commitTransaction();
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }



    // create user
    public function createUser($data) {
         
         
 
         // set UserModel

        $user = new UserModel();
        $user->setName($data->nome);
        $user->setEmail($data->email);
        $user->setPassword($data->password);
        try {
            $this->repository->beginTransaction();
            
            // Check if user already exists
            if ($this->repository->userExists($user) > 0) {
                throw new Exception("User already exists.");
            }
            
            // Create user            
           return $this->repository->createUser($user);
            
            $this->repository->commitTransaction();
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    // update user
    public function updateUser($data) {
        //check the token
        $check = new AuthMiddleware();
        $check->openToken();

        $user = new UserModel();
        $user->setName($data->nome);
        $user->setEmail($data->email);
        $user->setPassword($data->password);
        
        if ($this->repository->userExists($user) > 0) {
            throw new Exception("User already exists.");
        }

        try {
            $this->repository->beginTransaction();
            
            // Update user
            return $this->repository->updateUser($user);
            
            $this->repository->commitTransaction();
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    // update user password
    public function updateUserPassword($data) {
        //check the token
        $check = new AuthMiddleware();
        $check->openToken();

        $user = new UserModel();
        $user->setName($data->nome);
        $user->setEmail($data->email);
        $user->setPassword($data->password);

        if ($this->repository->userExists($user) > 0) {
            throw new Exception("User already exists.");
        }
        
        try {
            $this->repository->beginTransaction();
            
            // Update user password
            return $this->repository->updateUserPassword($user->getId(), $user->getPassword());
            
            $this->repository->commitTransaction();
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }
}