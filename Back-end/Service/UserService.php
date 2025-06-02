<?php
namespace App\Backend\Service;

use App\Backend\Model\UserModel;
use App\Backend\Repository\UserRepository;
use App\Backend\Libs\AuthMiddleware;
use App\Backend\Utils\PatternText;
use Directory;
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

            $this->repository->commitTransaction();

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
    public function getUserById() {
        //check the token
        $check = new AuthMiddleware();
        $decodedToken =  $check->openToken();
        // Get user ID from the token
        $userId = $decodedToken->id;

        try {
            $this->repository->beginTransaction();
            
            // Get user by id
            $reponse = $this->repository->getContentId($userId);
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

        // Process the data
        $dataPadronizado = PatternText::processText($data);
       // var_dump($dataPadronizado->password);die;
        // Create a new UserModel instance

        $user = new UserModel();
        $user->setName($dataPadronizado->name);
        $user->setEmail($dataPadronizado->email);
        $user->setPassword($dataPadronizado->password); // Password is already hashed in the model

        try {
            $this->repository->beginTransaction();
            
            // Check if user already exists
            $userExists = $this->repository->userExists($user);
            if ($userExists['status'] == false) {
                return [
                    'status' => false,
                    'message' => 'User já cadastrado.',
                    'content' => $userExists['user']
                ];
            }
            
            // Create user
            $userCreated = $this->repository->createUser($user); 
            if($userCreated['status'] == true) {
                $data->id = $userCreated['user']->getId();
                 $this->repository->commitTransaction(); //action that make all things happen
                return [
                    'status' => true,
                    'message' => 'Usuário criado com sucesso.',
                    'content' => $data
                ];
            } else {
                $this->repository->rollBackTransaction(); // action that make all things not happen
                return [
                    'status' => false,
                    'message' => 'Erro ao criar usuário.',
                    'content' => null
                ];
            }
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
        $decodedToken =  $check->openToken();
        // Get user ID from the token
        $userId = $decodedToken->id;
        // Process the data
        $dataPadronizado = PatternText::processText($data);

        $user = new UserModel();
        $user->setId($userId); // Id by token
        $user->setName($dataPadronizado->name);
        $user->setEmail($dataPadronizado->email);
        $user->setPassword($dataPadronizado->password); // Password is already hashed in the model
        
        // Check if user already exists
            $userExists = $this->repository->userExists($user);
            if ($userExists['status'] == true) {
                return [
                    'status' => false,
                    'message' => 'User não existe.',
                    'content' => $userExists['user']
                ];
            }

        try {
            $this->repository->beginTransaction();
            
            // Update user
            $userUpdated = $this->repository->updateUser($user); 
            if($userUpdated['status'] == true) {
                 $this->repository->commitTransaction(); //action that make all things happen
                return [
                    'status' => true,
                    'message' => 'Usuário Atualizado com sucesso.',
                    'content' => $data
                ];
            } else {
                $this->repository->rollBackTransaction(); // action that make all things not happen
                return [
                    'status' => false,
                    'message' => 'Erro ao Atualizar usuário.',
                    'content' => null
                ];
            }
            $this->repository->commitTransaction();
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }


    // update user password
    public function updateUserPassword($data) {

        $user = new UserModel();
        $user->setEmail($data->email);
        $user->setPassword($data->password);

         // Check if user already exists
            $userExists = $this->repository->userExists($user);
            if ($userExists['status'] == true) {
                return [
                    'status' => false,
                    'message' => 'User não existe.',
                    'content' => $userExists['user']
                ];
            }
        
        try {
            $this->repository->beginTransaction();
            
            // Update user password
            $passwordNew = $this->repository->updateUserPassword($user);
            if($passwordNew['status'] == true) {
                 $this->repository->commitTransaction(); //action that make all things happen
                return [
                    'status' => true,
                    'message' => 'Senha Atualizada com sucesso.',
                    'content' => $data
                ];
            } else {
                $this->repository->rollBackTransaction(); // action that make all things not happen
                return [
                    'status' => false,
                    'message' => 'Erro ao Atualizar senha.',
                    'content' => null
                ];
            }
            
            $this->repository->commitTransaction();
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }
}