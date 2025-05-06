<?php
namespace App\Backend\Service;

use App\Backend\Model\UserModel;
use App\Backend\Repository\UserRepository;
use App\Backend\Libs\AuthMiddleware;
use Exception;


class UserService {
    
    private $repository;

    public function __construct() {
        $this->repository = new UserRepository();
    }

    
    //public functions...
    //Get all users
    public function getAllUsers() {
        //check the token
        $check = new AuthMiddleware();
        $check->openToken();
        
        try {
            $this->repository->beginTransaction();
            
            // Get all users
            return $this->repository->getAllUsers();
            
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
            return  $this->repository->getContentId($id);
            
            $this->repository->commitTransaction();
        } catch (Exception $e) {
            $this->repository->rollBackTransaction();
            throw $e;
        }
    }

    // create user
    public function createUser($data) {
         //check the token
         $check = new AuthMiddleware();
         $check->openToken();
 
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