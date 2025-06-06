<?php
namespace App\Backend\Controller;
use App\Backend\Service\UserService;
use App\Backend\Libs\AuthMiddleware;
use Exception;

class  UserController {
    private $service;

    public function __construct() {
        $this->service = new UserService();
    }

    // code Get, Post, Put, Delete in private functions

    private function handleResponse($result, $successMessage = "Operação concluída com sucesso.", $content = null, $http_response_header = null) {
        if (!empty($result)) {
            http_response_code($http_response_header);
            echo json_encode(["status" => $result,"message" => $successMessage,"content" => $content]);
        } else {
            http_response_code($http_response_header);
            echo json_encode(['status' => false, "message" => $successMessage, "content" => $content]);
        }
    }

    public function login() {
        //Get the Json data
        $data = json_decode(file_get_contents('php://input'));
       
        // Check if the login was successful
        if($result = $this->service->login($data)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 401);
        }
        
    }        
    //Select user by id
    public function getUserById() {

        if($result = $this->service->getUserById()){
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 401);
        }
    }

    //Select all users
    public function getAllUsers() {
        if($result = $this->service->getAllUsers()){
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 401);
        }
    }

    //Create user
    public function createUser() {
        //Get the Json data
        $data = json_decode(file_get_contents('php://input'));
        
        // Check if the user was created successfully
        if($result = $this->service->createUser($data)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 201);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

    //Update user
    public function updateUser() {
        //Get the Json data
        $data = json_decode(file_get_contents('php://input'));
        
        // Check if the user was updated successfully
        if($result = $this->service->updateUser($data)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }

     //Update password user
    public function updateUserPassword() {
        //Get the Json data
        $data = json_decode(file_get_contents('php://input'));
        
        // Check if the user was updated successfully
        if($result = $this->service->updateUserPassword($data)) {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 200);
        } else {
            $this->handleResponse($result['status'], $result['message'], $result['content'], 404);
        }
    }
}